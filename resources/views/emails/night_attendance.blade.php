<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Today's Attendance Summary</title>
</head>
<body style="margin: 0; padding: 1px; font-family: Arial, sans-serif; background-color: #ffffff;">

    <h2 style='font-family: Arial, sans-serif; color: #2c3e50; margin-top: 0;'>📊 Attendance Summary</h2>
    <p style='font-family: Arial, sans-serif; font-size:14px; color:#555;'>
        Today's attendance summary and complete monthly breakdown for {{ $monthYear }}:
    </p>

    <h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📅 Today's Attendance Summary - {{ \Carbon\Carbon::parse($today)->format('F j, Y') }}</h3>
    
    <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
    <table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:14px; border:1px solid #ddd; margin-bottom: 30px; white-space: nowrap;'>
        <thead>
            <tr style='background-color:#34495e; color:#fff;'>
                <th style='padding:6px; border:1px solid #ddd;'>Employee</th>
                <th style='padding:6px; border:1px solid #ddd;'>In Time</th>
                <th style='padding:6px; border:1px solid #ddd;'>Out Time</th>
                <th style='padding:6px; border:1px solid #ddd;'>Mode</th>
                <th style='padding:6px; border:1px solid #ddd;'>Place</th>
                <th style='padding:6px; border:1px solid #ddd;'>Hours</th>
                <th style='padding:6px; border:1px solid #ddd;'>Late By</th>
                <th style='padding:6px; border:1px solid #ddd;'>Late Reason</th>
                <th style='padding:6px; border:1px solid #ddd;'>Grace Balance</th>
                <th style='padding:6px; border:1px solid #ddd;'>Final Status</th>
                <th style='padding:6px; border:1px solid #ddd;'>Status Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['today'] as $row)
                @php
                    $lrStyle = $row['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                    $statusColor = '#2c3e50';
                    if (str_contains(strtolower($row['status']), 'absent')) $statusColor = '#e74c3c';
                    elseif (str_contains(strtolower($row['status']), 'leave')) $statusColor = '#3498db';
                    elseif (str_contains(strtolower($row['status']), 'halfday')) $statusColor = '#f39c12';
                    elseif (str_contains(strtolower($row['status']), 'present')) $statusColor = '#27ae60';
                    elseif (str_contains(strtolower($row['status']), 'holiday') || str_contains(strtolower($row['status']), 'off')) $statusColor = '#9b59b6';
                @endphp
                <tr style='background-color:#f9f9f9;'>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['user_name'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['punch_in'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['punch_out'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['mode'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['place'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{{ $row['total_hours'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['late_by'] ?? '-' }}</td>
                    <td style='padding:5px; border:1px solid #ddd; {{ $lrStyle }}'>{{ $row['late_reason'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['grace_balance'] ?? '-' }}</td>
                    <td style='padding:5px; border:1px solid #ddd; color:{{ $statusColor }}; font-weight:bold; text-transform:capitalize;'>{{ $row['status'] }}</td>
                    <td style='padding:5px; border:1px solid #ddd;'>{{ $row['status_reason'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center" style='padding:5px; border:1px solid #ddd; text-align:center;'>No data</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📊 Monthly Attendance Breakdown</h3>
    
    @foreach($reportData['monthly'] as $userMonthly)
        <h4 style='font-family: Arial, sans-serif; color: #2c3e50; margin-top: 25px; margin-bottom: 10px;'>👤 {{ $userMonthly['user_name'] }}</h4>
        
        <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
        <table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:13px; border:1px solid #ddd; margin-bottom: 20px; white-space: nowrap;'>
            <thead>
                <tr style='background-color:#ecf0f1;'>
                    <th style='padding:0; border:1px solid #ddd;'>Date</th>
                    <th style='padding:0; border:1px solid #ddd;'>In Time</th>
                    <th style='padding:0; border:1px solid #ddd;'>Out Time</th>
                    <th style='padding:0; border:1px solid #ddd;'>Mode</th>
                    <th style='padding:0; border:1px solid #ddd;'>Place</th>
                    <th style='padding:0; border:1px solid #ddd;'>Hours</th>
                    <th style='padding:0; border:1px solid #ddd;'>Late By</th>
                    <th style='padding:0; border:1px solid #ddd;'>Late Reason</th>
                    <th style='padding:0; border:1px solid #ddd;'>Grace Balance</th>
                    <th style='padding:0; border:1px solid #ddd;'>Final Status</th>
                    <th style='padding:0; border:1px solid #ddd;'>Status Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userMonthly['records'] as $record)
                    @php
                        $lrStyle = $record['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                        $statusColor = '#2c3e50';
                        if (str_contains(strtolower($record['status']), 'absent')) $statusColor = '#e74c3c';
                        elseif (str_contains(strtolower($record['status']), 'leave')) $statusColor = '#3498db';
                        elseif (str_contains(strtolower($record['status']), 'halfday')) $statusColor = '#f39c12';
                        elseif (str_contains(strtolower($record['status']), 'present')) $statusColor = '#27ae60';
                        elseif (str_contains(strtolower($record['status']), 'holiday') || str_contains(strtolower($record['status']), 'off')) $statusColor = '#9b59b6';
                    @endphp
                    <tr>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['date'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['punch_in'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['punch_out'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['mode'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['place'] }}</td>
                        <td style='padding:0; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{{ $record['total_hours'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['late_by'] ?? '-' }}</td>
                        <td style='padding:0; border:1px solid #ddd; {{ $lrStyle }}'>{{ $record['late_reason'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['grace_balance'] ?? '-' }}</td>
                        <td style='padding:0; border:1px solid #ddd; color:{{ $statusColor }}; font-weight:bold; text-transform:capitalize;'>{{ $record['status'] }}</td>
                        <td style='padding:0; border:1px solid #ddd;'>{{ $record['status_reason'] ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr style='background-color:#f8f9fa; font-weight:bold;'>
                    <td style='padding:0; border:1px solid #ddd; text-align:right; padding-right:10px;'><strong>Monthly Total</strong></td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd; color:#8e44ad;'>{{ $userMonthly['monthly_total'] }}</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd; color:#95a5a6;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                </tr>
            </tbody>
        </table>
        </div>
    @endforeach

    <br>
    <p style="font-family: Arial, sans-serif; font-size:13px; color:#555;">
        Regards,<br>
        <b>HR Team</b>
    </p>

</body>
</html>
