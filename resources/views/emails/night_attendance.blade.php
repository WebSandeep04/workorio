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
                <th style='padding:6px; border:1px solid #ddd;'>Punch-In</th>
                <th style='padding:6px; border:1px solid #ddd;'>Punch-Out</th>
                <th style='padding:6px; border:1px solid #ddd;'>Mode</th>
                <th style='padding:6px; border:1px solid #ddd;'>Place</th>
                <th style='padding:6px; border:1px solid #ddd;'>Total Hours</th>
                <th style='padding:6px; border:1px solid #ddd;'>Late Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['today'] as $row)
                @if($row['status'] === 'leave')
                    <tr>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['user_name'] }}</td>
                        <td colspan='6' style='padding:5px; border:1px solid #ddd; text-align:center; color:#3498db; font-weight:bold;'>Leave</td>
                    </tr>
                @elseif($row['status'] === 'absent')
                    <tr>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['user_name'] }}</td>
                        <td colspan='6' style='padding:5px; border:1px solid #ddd; text-align:center; color:#e74c3c; font-weight:bold;'>Absent</td>
                    </tr>
                @else
                    @php
                        $lrStyle = $row['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                    @endphp
                    <tr style='background-color:#f9f9f9;'>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['user_name'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['punch_in'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['punch_out'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['mode'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd;'>{{ $row['place'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{{ $row['total_hours'] }}</td>
                        <td style='padding:5px; border:1px solid #ddd; {{ $lrStyle }}'>{{ $row['late_reason'] }}</td>
                    </tr>
                @endif
            @empty
                <tr><td colspan="7" class="text-center" style='padding:5px; border:1px solid #ddd; text-align:center;'>No data</td></tr>
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
                    <th style='padding:0; border:1px solid #ddd;'>Punch-In</th>
                    <th style='padding:0; border:1px solid #ddd;'>Punch-Out</th>
                    <th style='padding:0; border:1px solid #ddd;'>Mode</th>
                    <th style='padding:0; border:1px solid #ddd;'>Place</th>
                    <th style='padding:0; border:1px solid #ddd;'>Total Hours</th>
                    <th style='padding:0; border:1px solid #ddd;'>Late Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userMonthly['records'] as $record)
                    @if($record['status'] === 'leave')
                        <tr>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['date'] }}</td>
                            <td colspan='6' style='padding:0; border:1px solid #ddd; text-align:center; color:#3498db; font-weight:bold;'>Leave</td>
                        </tr>
                    @else
                        @php
                            $lrStyle = $record['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                        @endphp
                        <tr>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['date'] }}</td>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['punch_in'] }}</td>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['punch_out'] }}</td>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['mode'] }}</td>
                            <td style='padding:0; border:1px solid #ddd;'>{{ $record['place'] }}</td>
                            <td style='padding:0; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{{ $record['total_hours'] }}</td>
                            <td style='padding:0; border:1px solid #ddd; {{ $lrStyle }}'>{{ $record['late_reason'] }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr style='background-color:#f8f9fa; font-weight:bold;'>
                    <td style='padding:0; border:1px solid #ddd;'><strong>Monthly Total</strong></td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd; color:#8e44ad;'>{{ $userMonthly['monthly_total'] }}</td>
                    <td style='padding:0; border:1px solid #ddd; color:#95a5a6;'>-</td>
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
