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

        // MSG91 inbound webhook payload parsing
        $payload = $request->all();

        // MSG91 sometimes sends inbound messages in an array or direct object
        // Let's assume standard format or similar to Meta
        
        $sender = $request->input('sender'); // or $request->input('from')
        $receiver = $request->input('receiver'); // or $request->input('to')
        $message = $request->input('message');
        
        // If the structure is nested inside 'data' or similar
        if (!$sender && $request->has('data')) {
            $data = $request->input('data');
            $sender = $data['sender'] ?? null;
            $receiver = $data['receiver'] ?? null;
            $message = $data['message'] ?? null;
        }

        if ($sender && $message) {
            $messageType = $message['type'] ?? 'text';
            $messageText = null;
            $mediaUrl = null;

            if ($messageType === 'text') {
                $messageText = $message['text']['body'] ?? ($message['text'] ?? '');
            } elseif (in_array($messageType, ['image', 'document', 'audio', 'video'])) {
                // MSG91 might provide media URL or ID
                $mediaUrl = $message['media_url'] ?? ($message[$messageType]['link'] ?? null);
                $messageText = $message['caption'] ?? null;
            }

            WhatsappInbox::create([
                'sender_number' => $sender,
                'receiver_number' => $receiver,
                'message_text' => is_string($messageText) ? $messageText : json_encode($messageText),
                'media_url' => $mediaUrl,
                'message_type' => $messageType,
                'msg91_message_id' => $request->input('message_id') ?? $request->input('id'),
                'is_read' => false,
                'received_at' => Carbon::now(),
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'ignored']);
    }
}
