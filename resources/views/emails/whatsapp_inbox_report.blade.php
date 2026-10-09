<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:2px; background-color:#f4f4f4; font-family: Arial, sans-serif;">
    
<div style="width: 100%; max-width: 1400px; margin: 0 auto; background-color: #ffffff; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
    <!-- Header -->
    <div style="background-color: #0d6efd; padding:30px 20px; text-align:center;">
        <h1 style="margin:0; color:white; font-size:28px; font-weight:600;">📱 WhatsApp Inbox Report</h1>
        <p style="margin:10px 0 0 0; color:white; font-size:14px;">{{ $payload['dateDisplay'] }}</p>
    </div>
    
    <!-- Greeting -->
    <div style="padding:20px 15px 10px 15px;">
        <p style="margin:0; font-size:16px; color:#212529;">Hello <strong style="color:#0d6efd;">{{ $payload['recipient_name'] }}</strong>,</p>
        <p style="margin:15px 0; font-size:14px; color:#495057; line-height:1.6;">
            Here is the summary of your WhatsApp Inbox (All active chats). You have <strong style="color:#dc3545;">{{ count($payload['messages']) }} unread {{ count($payload['messages']) == 1 ? 'chat' : 'chats' }}</strong>.
        </p>
    </div>
    
    <!-- Tasks Table -->
    <div style="padding:0 2px 30px 2px;">
        <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; font-family: Arial, sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.1); min-width: 600px; white-space: nowrap;">
                <thead style="background-color:#f1f3f5; color:#495057;">
                    <tr>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:25px;">#</th>
                        <th style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:20%;">Contact</th>
                        <th style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; font-size:11px; font-weight:600; white-space: normal;">Latest Message</th>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:75px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse ($payload['messages'] as $msg)
                        @php
                            $rowColor = ($counter % 2 == 0) ? '#f8f9fa' : '#ffffff';
                            $contactName = $msg['sender_name'] ?: $msg['sender_number'];
                            $timeStr = \Carbon\Carbon::parse($msg['received_at'])->format('M d, Y');
                            
                            $messageText = '';
                            if(!empty($msg['media_url']) || in_array($msg['message_type'], ['image', 'document', 'image_reply', 'document_reply'])) {
                                $messageText .= '📷 ';
                            }
                            $messageText .= \Illuminate\Support\Str::limit($msg['message_text'] ?: 'No text', 60);
                        @endphp
                        
                        <tr style="background-color:{{ $rowColor }};">
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-weight:600; color:#495057; font-size:11px;">{{ $counter }}</td>
                            <td style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; color:#0d6efd; font-size:12px; font-weight:bold;">{{ $contactName }}</td>
                            <td style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:12px; white-space: normal;">{{ $messageText }}</td>
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:10px; color:#6c757d;">{{ $timeStr }}</td>
                        </tr>
                        @php $counter++; @endphp
                    @empty
                        <tr>
                            <td colspan="4" style="padding:15px; text-align:center; border:1px solid #dee2e6; color:#6c757d; font-size:12px;">No active messages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer -->
    <div style="background-color:#f8f9fa; padding:20px 15px; border-top:1px solid #dee2e6;">
        <p style="margin:0; font-size:12px; color:#6c757d; text-align:center;">
            This is an automated report from <strong>Workorio</strong>.<br>
            Please do not reply to this email.
        </p>
        <p style="margin:10px 0 0 0; font-size:11px; color:#adb5bd; text-align:center;">
            © {{ $payload['year'] }} Workorio. All rights reserved.
        </p>
    </div>

</div>

</body>
</html>
