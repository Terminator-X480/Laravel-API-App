<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Helpers\TimezoneHelper;

class CallLogsController extends Controller
{
    public function handleCallLogs(Request $request)
    {
        // Accept GET and POST data
        $params = $request->all();

        // Define valid call types from the ENUM
        $validCallTypes = ['incoming', 'outgoing', 'missed', 'declined', 'no_response']; // Add others if needed

        // Validate fields
        $validated = $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
            'trek_id' => 'nullable|string',
            'msg' => 'nullable|string',
            'duration' => 'nullable|string',
            'callType' => 'nullable|string',
            'trekDate' => 'nullable|string',
            'groupSize' => 'nullable|string',
            'device' => 'nullable|string',
            'note' => 'nullable|string',
            'leadid' => 'nullable|string',
            'recording' => 'nullable|file|max:20480', // max 20MB
        ]);

        // Assign values from request
        $phone     = $validated['phone'];
        $name      = $validated['name'] ?? null;
        $trek_id   = $validated['trek_id'] ?? null;
        $msg       = $validated['msg'] ?? null;
        $duration  = $validated['duration'] ?? null;
        $callType  = $validated['callType'] ?? 'outgoing'; // fallback default
        $trekDate  = $validated['trekDate'] ?? null;
        $groupSize = $validated['groupSize'] ?? null;
        $device    = $validated['device'] ?? null;
        $note      = $validated['note'] ?? null;
        $leadid    = $validated['leadid'] ?? null;

        // Validate and sanitize callType value
        if (!in_array($callType, $validCallTypes)) {
            $callType = 'outgoing'; // default fallback if invalid
        }

        // Check if lead exists
        $existingLead = $this->leadExists($phone);

        // Handle recording file upload
        $file_url = '';
        if ($request->hasFile('recording') && $request->file('recording')->isValid()) {
            $path = $request->file('recording')->store('lead-attachments/recordings', 'public');
            $file_url = $path;
        }

        // Prepare data for insertion
        $data = [
            'name' => $name,
            'phone' => $phone,
            'lead_id' => $existingLead ? $existingLead->id : null,
            'leadid' => $leadid,
            'note' => $note,
            'trek_id' => $trek_id,
            'call' => $callType,
            'trek_date' => $trekDate,
            'duration' => $duration,
            'no_of_people' => $groupSize,
            'msg' => $msg,
            'device' => $device,
            'recording_file' => $file_url,
            'created_at' => now(),
        ];

        // Insert into the database
        $inserted = DB::table('wp_whatsapp_numbers_calls')->insert($data);

        if (!$inserted) {
            return response()->json(['success' => false, 'error' => 'Failed to insert call data'], 500);
        }

        $phone_id = DB::getPdo()->lastInsertId();

        return response()->json(['success' => true, 'phone_id' => $phone_id]);
    }

    private function leadExists(string $phone)
    {
        return DB::table('wp_mt_leads')->where('phone', $phone)->first();
    }

    private function getDeviceName($deviceId)
    {
        return DB::table('wp_whatsapp_devices')->where('id', $deviceId)->first() ?? false;
    }

    public function callListById(Request $request, $id){
        $lead_id = (int) $id;

        if (!$lead_id) {
            return response()->json([
                'success' => false,
                'html' => '<tr><td colspan="5">Invalid Lead ID</td></tr>'
            ]);
        }

        $lead = DB::table('wp_mt_leads')->where('id', $lead_id)->first();

        if (!$lead) {
            return response()->json([
                'success' => false,
                'html' => '<tr><td colspan="5">Lead not found</td></tr>'
            ]);
        }

        $calls = DB::table('wp_whatsapp_numbers_calls')
            ->where('phone', 'LIKE', '%' . $lead->phone . '%')
            ->orderByDesc('id')
            ->get();

        $html = '';

        if (count($calls)) {
            foreach ($calls as $call) {
                $duration = !empty($call->duration)
                    ? $call->duration . 's (' . $call->call . ')'
                    : $call->call;

                $deviceName = $this->getDeviceName($call->device)->name ?? false;

                $formattedTime = TimezoneHelper::get_formatted_time($call->created_at);

                $html .= "<tr>
                    <td>{$call->phone}</td>
                    <td>{$duration}</td>
                    <td>{$formattedTime}</td>
                    <td>";
                if (!empty($call->recording_file)) {
                    $html .= "<audio controls style='width:236px;'>
                                <source src='" ."https://madtrek.com/wp-content/lead-attachments/recordings/{$call->recording_file}" . "' type='audio/mp4'>
                                Your browser does not support the audio element.
                            </audio>";
                }

                $html .= "</td>
                    <td>{$deviceName}</td>
                </tr>";
            }
        } else {
            $html = "<tr><td colspan='5'>No Calls Found</td></tr>";
        }

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);

    }
}
