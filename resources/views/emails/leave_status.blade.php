<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6;">

<div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background-color: {{ $status === 'approved' ? '#10b981' : '#ef4444' }}; padding: 25px; color: white; text-align: center;">
        <h2 style="margin: 0; font-size: 24px;">Leave Request {{ ucfirst($status) }}</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    </div>
    
    <div style="padding: 25px; color: #374151;">
        <p style="margin-top: 0; font-size: 16px;">Hello <strong>{{ $user->name }}</strong>,</p>
        <p style="font-size: 14px; line-height: 1.5;">
            Your leave request has been <strong>{{ $status }}</strong>. Below are the details of your application:
        </p>

        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-top: 20px;">
            <table width="100%" cellspacing="0" cellpadding="0" style="font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; width: 40%;"><strong>Leave Type</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ $leaveRequest->leaveType ? $leaveRequest->leaveType->name : ($leaveRequest->is_rh ? 'Restricted Holiday' : ($leaveRequest->is_sl ? 'Short Leave' : 'N/A')) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;"><strong>Duration</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;"><strong>Total Days</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ $leaveRequest->total_days }} day(s)</td>
                </tr>
                @if($leaveRequest->reason)
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;"><strong>Reason</strong></td>
                    <td style="padding: 8px 0; color: #111827; white-space: pre-wrap;">{{ $leaveRequest->reason }}</td>
                </tr>
                @endif
            </table>
        </div>

        <p style="margin-top: 25px; font-size: 14px; color: #6b7280;">
            If you have any questions, please contact your manager or HR.
        </p>
    </div>
    
    <div style="background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb;">
        This is an automated notification from Workorio.
    </div>
</div>

</body>
</html>
