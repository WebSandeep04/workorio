<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappInbox;
use App\Traits\TenantAwareStorage;

class WhatsappInboxController extends Controller
{
    use TenantAwareStorage;

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
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240'
        ]);

        if (empty($request->message) && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Please provide a message or attach a file.'], 400);
        }

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
        ];

        $mediaUrl = null;
        $messageType = 'reply'; // Text reply by default

        if ($request->hasFile('file')) {
            $path = $this->storeTenantFile($request->file('file'), 'whatsapp_media');
            // Force HTTPS if in production, otherwise use asset default
            $isProduction = config('app.env') === 'production' || str_contains(url('/'), 'app.workorio.com');
            $mediaUrl = asset('storage/' . $path, $isProduction);
            
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            
            $msg91Type = $isImage ? 'image' : 'document';
            $messageType = $isImage ? 'image_reply' : 'document_reply';

            $payload["content_type"] = $msg91Type;
            $payload["attachment_url"] = $mediaUrl;
            
            if (!$isImage) {
                $payload["filename"] = $file->getClientOriginalName();
            }
            
            // Only add caption if message is provided
            if (!empty($request->message)) {
                $payload["caption"] = $request->message;
            }
        } else {
            $payload["content_type"] = "text";
            $payload["text"] = $request->message;
        }

        try {
            \Illuminate\Support\Facades\Log::info('MSG91 Outbound Payload', $payload);

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $setting->auth_key,
                'content-type' => 'application/json'
            ])->post("https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/", $payload);

            \Illuminate\Support\Facades\Log::info('MSG91 Outbound Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful() && isset($response->json()['hasError']) && $response->json()['hasError'] === false) {
                // Log reply to inbox
                WhatsappInbox::create([
                    'sender_number' => $setting->whatsapp_number,
                    'receiver_number' => $recipientNumber,
                    'message_text' => $request->message,
                    'media_url' => $mediaUrl,
                    'message_type' => $messageType,
                    'received_at' => now(),
                    'is_read' => 1
                ]);

                return response()->json(['success' => true, 'message' => 'Reply sent successfully.']);
            } else {
                $rawBody = $response->body();
                $status = $response->status();
                return response()->json([
                    'success' => false, 
                    'message' => "MSG91 Error ($status): " . $rawBody, 
                    'debug' => $rawBody
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
