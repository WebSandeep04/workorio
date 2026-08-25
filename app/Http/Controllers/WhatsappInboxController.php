<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappInbox;

class WhatsappInboxController extends Controller
{
    public function index()
    {
        return view('whatsapp_inbox.index');
    }

    public function fetch(Request $request)
    {
        $messages = WhatsappInbox::orderBy('received_at', 'desc')->paginate(20);
        return response()->json($messages);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'recipient_number' => 'required',
            'message' => 'required'
        ]);

        $setting = \App\Models\Msg91Setting::first();
        if (!$setting || !$setting->auth_key || !$setting->whatsapp_number) {
            return response()->json(['success' => false, 'message' => 'MSG91 Settings not configured.'], 400);
        }

        $recipientNumber = preg_replace('/[^0-9]/', '', $request->recipient_number);
        // MSG91 requires 91 prefix for India if not present
        if (strlen($recipientNumber) == 10) {
            $recipientNumber = '91' . $recipientNumber;
        }

        $payload = [
            "integrated_number" => $setting->whatsapp_number,
            "recipient_number" => $recipientNumber,
            "content_type" => "text",
            "text" => $request->message
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $setting->auth_key,
                'content-type' => 'application/json'
            ])->post("https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/", $payload);

            if ($response->successful() && isset($response->json()['hasError']) && $response->json()['hasError'] === false) {
                // Log reply to inbox
                WhatsappInbox::create([
                    'sender_number' => $setting->whatsapp_number,
                    'receiver_number' => $recipientNumber,
                    'message_text' => $request->message,
                    'message_type' => 'reply',
                    'received_at' => now(),
                    'is_read' => 1
                ]);

                return response()->json(['success' => true, 'message' => 'Reply sent successfully.']);
            } else {
                $errorMsg = $response->json()['message'] ?? 'Failed to send reply.';
                return response()->json(['success' => false, 'message' => $errorMsg], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
