<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappInbox;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsappWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('MSG91 Webhook Received:', $request->all());

        // The MSG91 Inbound Request Received payload uses different keys:
        // customerNumber (sender)
        // integratedNumber (receiver)
        // text (message content)
        // contentType / messageType

        $sender = $request->input('customerNumber') ?? $request->input('sender') ?? $request->input('from');
        $receiver = $request->input('integratedNumber') ?? $request->input('receiver') ?? $request->input('to');
        
        // Try to get message content from 'text', or fallback to 'message' array
        $messageText = $request->input('text');
        $messageArray = $request->input('message'); 
        
        // If the structure is nested inside 'data'
        if (!$sender && $request->has('data')) {
            $data = $request->input('data');
            $sender = $data['customerNumber'] ?? $data['sender'] ?? null;
            $receiver = $data['integratedNumber'] ?? $data['receiver'] ?? null;
            $messageText = $data['text'] ?? null;
            $messageArray = $data['message'] ?? null;
        }

        if ($sender && ($messageText || $messageArray)) {
            $messageType = $request->input('contentType') ?? $request->input('messageType') ?? 'text';
            $mediaUrl = $request->input('url') ?? null;
            
            // If it's a complex message array from older MSG91 versions
            if (!$messageText && is_array($messageArray)) {
                $msgType = $messageArray['type'] ?? 'text';
                if ($msgType === 'text') {
                    $messageText = $messageArray['text']['body'] ?? ($messageArray['text'] ?? '');
                } elseif (in_array($msgType, ['image', 'document', 'audio', 'video'])) {
                    $mediaUrl = $messageArray['media_url'] ?? ($messageArray[$msgType]['link'] ?? null);
                    $messageText = $messageArray['caption'] ?? null;
                }
                $messageType = $msgType;
            }

            WhatsappInbox::create([
                'sender_number' => $sender,
                'receiver_number' => $receiver,
                'message_text' => is_string($messageText) ? $messageText : json_encode($messageText),
                'media_url' => $mediaUrl,
                'message_type' => $messageType,
                'msg91_message_id' => $request->input('uuid') ?? $request->input('message_id') ?? $request->input('id'),
                'is_read' => false,
                'received_at' => Carbon::now(),
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'ignored']);
    }
}
