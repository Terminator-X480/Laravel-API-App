<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

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
}
