<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6;">

<div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #ef4444; padding: 25px; color: white; text-align: center;">
        <h2 style="margin: 0; font-size: 24px;">Attendance Rejected</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    </div>
    
    <div style="padding: 25px; color: #374151;">
        <p style="margin-top: 0; font-size: 16px;">Hello <strong>{{ $user->name }}</strong>,</p>
        <p style="font-size: 14px; line-height: 1.5;">
            Your attendance for the particular date has been <strong>rejected</strong> by the administrator. Below are the details:
        </p>

        <div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 15px; margin-top: 20px;">
            <table width="100%" cellspacing="0" cellpadding="0" style="font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; width: 40%;"><strong>Attendance Date</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;"><strong>Punch In</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ $attendance->in_time ? \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;"><strong>Punch Out</strong></td>
                    <td style="padding: 8px 0; color: #111827;">{{ $attendance->out_time ? \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') : 'N/A' }}</td>
                </tr>
                <tr style="border-top:1px solid #fee2e2;">
                    <td style="padding: 8px 0; color: #b91c1c;"><strong>Reason for Rejection</strong></td>
                    <td style="padding: 8px 0; color: #b91c1c; font-weight: 500;">{{ $reason }}</td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 25px; font-size: 14px; color: #6b7280;">
            Please contact your HR or administrator to resolve this issue.
        </p>
    </div>
    
    <div style="background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb;">
        This is an automated notification from Workorio.
    </div>
</div>

</body>
</html>
