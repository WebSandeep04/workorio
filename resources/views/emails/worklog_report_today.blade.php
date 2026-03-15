<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 10px; font-family: Arial, sans-serif; background-color: #ffffff;">

<h2 style="font-family: Arial, sans-serif; color: #2c3e50;">📌 Today's Worklog Summary</h2>
<div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
    <table width="100%" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:12px; border:1px solid #ddd; min-width: 800px; white-space: nowrap;">
        <thead>
            <tr style="background-color:#34495e; color:#fff;">
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">User</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Company</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Service</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Project</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Module</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Hours</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Minutes</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Description</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Rating</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Rating Remark</th>
            </tr>
        </thead>
        <tbody>
            @php
                $colors = ['#f9f9f9', '#e8f6f3', '#fff3e0', '#fde2e2', '#f0f0f0', '#e6e6fa'];
                $color_index = 0;
                $last_user = null;
            @endphp
            
            @forelse($payload['worklogs'] as $row)
                @php
                    if ($last_user !== $row['user_id']) {
                        $bg_color = $colors[$color_index % count($colors)];
                        $color_index++;
                        $last_user = $row['user_id'];
                    }

                    $company_name = htmlspecialchars($row['company_name'] ?? '-');
                    $rating = htmlspecialchars($row['rating'] ?? '-'); 
                    $rating_remark = htmlspecialchars($row['rating_remark'] ?? '-');
                    $desc = nl2br(htmlspecialchars($row['description'] ?? '-'));
                @endphp
                <tr style="background-color:{{ $bg_color }};">
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#0f172a; font-weight:600; vertical-align:top;">{{ htmlspecialchars($row['user_name']) }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; vertical-align:top; white-space:normal; min-width:120px;">{{ $company_name }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; vertical-align:top;">{{ htmlspecialchars($row['service_name'] ?? '-') }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; vertical-align:top;">{{ htmlspecialchars($row['customer_project_name'] ?? '-') }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; vertical-align:top;">{{ htmlspecialchars($row['module_name'] ?? '-') }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#0f172a; text-align:center; font-weight:600; vertical-align:top;">{{ htmlspecialchars($row['hours'] ?? '0') }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#0f172a; text-align:center; font-weight:600; vertical-align:top;">{{ htmlspecialchars($row['minutes'] ?? '0') }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; white-space: normal; line-height: 1.4; vertical-align:top; min-width:150px;">{!! $desc !!}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; text-align:center; vertical-align:top;">{{ $rating }}</td>
                    <td style="padding:3px; border-right:1px solid #e2e8f0; color:#334155; vertical-align:top;">{{ $rating_remark }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding:12px; text-align:center; border:1px solid #ddd;">No worklogs recorded today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<br>
<p style="font-family: Arial, sans-serif; font-size:13px; color:#555;">
    Regards,<br>
    <b>HR Team</b>
</p>

</body>
</html>
