<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function handleWhatsappMessage(Request $request)
    {
        $phone   = trim($request->query('phone', ''));
        $message = trim($request->query('message', ''));
        $device  = trim($request->query('device', ''));

        $lowercaseMessage = strtolower($message);

        // ✅ Skip unwanted messages
        if (strpos($lowercaseMessage, 'messages') !== false && strpos($lowercaseMessage, 'chats') !== false) {
            return response()->json([
                'success' => true,
                'message' => 'skipped'
            ], 200);
        }

        if (empty($phone) || empty($message)) {
            return response()->json([
                'success' => false,
                'error' => 'Missing phone or message'
            ], 400);
        }

        // 🗂 Tables
        $phoneTable   = 'wp_whatsapp_numbers';
        $messageTable = 'wp_whatsapp_messages';

        // ✅ Check if phone already exists
        $existing = DB::table($phoneTable)->where('phone', $phone)->first();

        if ($existing) {
            $phone_id = $existing->id;

            // Laravel event hook placeholder (can be implemented using custom events if needed)
            Log::info('whatsapp-old-number-message-added', [
                'Phone' => $phone,
                'Message' => $message,
                'Device' => $device,
                'phone_id' => $phone_id
            ]);
        } else {
            // ✅ Insert new number
            $phone_id = DB::table($phoneTable)->insertGetId([
                'phone' => $phone
            ]);

            if (!$phone_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to insert phone number'
                ], 500);
            }

            // Laravel event hook placeholder
            Log::info('whatsapp-new-number-added', [
                'Phone' => $phone,
                'Message' => $message,
                'Device' => $device,
                'phone_id' => $phone_id
            ]);
        }

        // ✅ Insert the message
        $message_id = DB::table($messageTable)->insertGetId([
            'phone_id' => $phone_id,
            'message'  => $message,
            'source'   => 'mobile',
            'device'   => $device
        ]);

        if (!$message_id) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to insert message'
            ], 500);
        }

        return response()->json([
            'success'    => true,
            'phone_id'   => $phone_id,
            'message_id' => $message_id,
            'received'   => [
                'phone'   => $phone,
                'message' => $message
            ]
        ], 200);
    }

    public function printWhatsappMessages($lead)
    {
        $formattedPhone = '+' . $lead->country_code . ' ' . substr($lead->phone, 0, 5) . ' ' . substr($lead->phone, 5);

        $messages = DB::table('wp_whatsapp_messages as m')
            ->join('wp_whatsapp_devices as d', 'm.device', '=', 'd.id')
            ->join('wp_whatsapp_numbers as n', function ($join) use ($formattedPhone) {
                $join->on('n.id', '=', 'm.phone_id')
                    ->where('n.phone', '=', $formattedPhone);
            })
            ->select('m.message', 'm.created_on', 'd.name')
            ->orderBy('m.created_on', 'asc')
            ->get();

        return $messages;
    }

    public static function printAllWhatsappMessages($lead)
    {
        $phone = $lead->country_code . $lead->phone;

        $messages = DB::table('wp_mt_whatsapp_fullchat as m')
            ->join('wp_whatsapp_devices as d', function ($join) use ($phone) {
                $join->on('m.device', '=', 'd.id')
                    ->where('m.phone', '=', $phone);
            })
            ->select('m.*', 'd.name as dname')
            ->orderBy('m.time', 'asc')
            ->get();
           
        return $messages;
    }
}
