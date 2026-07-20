<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
        }
        th {
            background-color: #f4f5f7;
            font-weight: bold;
        }
        .user-col {
            text-align: left;
            white-space: nowrap;
        }
        .sunday {
            background-color: #fff0f0;
            color: red;
        }
        .holiday {
            background-color: #e0f7fa;
        }
        .text-success { color: #10b981; }
        .text-warning { color: #f59e0b; }
        .text-danger { color: #ef4444; }
        .text-info { color: #0ea5e9; }
        .text-secondary { color: #64748b; }
    </style>
</head>
<body>

<div class="header">
    <h2>Monthly Attendance Report</h2>
    <p>Month: {{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th class="user-col" rowspan="2">User</th>
            @foreach($data['month']['dates'] as $d)
                @php
                    $isSunday = $d['is_sunday'];
                    $style = $isSunday ? 'sunday' : '';
                @endphp
                <th class="date-col {{ $style }}">{{ $d['day'] }}</th>
            @endforeach
            <th colspan="14">Summary</th>
        </tr>
        <tr>
            @foreach($data['month']['dates'] as $d)
                @php
                    $isSunday = $d['is_sunday'];
                    $style = $isSunday ? 'sunday' : '';
                @endphp
                <th class="date-col {{ $style }}" style="font-size: 8px;">{{ substr($d['day_name'], 0, 1) }}</th>
            @endforeach
            <th class="summary-col">WD</th>
            <th class="summary-col">TP</th>
            <th class="summary-col">FD</th>
            <th class="summary-col">HD</th>
            <th class="summary-col">SL</th>
            <th class="summary-col">SW</th>
            <th class="summary-col">HW</th>
            <th class="summary-col">L</th>
            <th class="summary-col">UL</th>
            <th class="summary-col">A</th>
            <th class="summary-col">&lt;Shift</th>
            <th class="summary-col">&gt;Shift</th>
            <th class="summary-col">Late</th>
            <th class="summary-col" title="Late Minutes">LM</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['data']) > 0)
            @foreach($data['data'] as $item)
                @php
                    $s = $item['summary'];
                @endphp
                <tr>
                    <td class="user-col"><strong>{{ $item['user']['name'] }}</strong></td>
                    
                    @if(isset($item['daily_statuses']))
                        @foreach($item['daily_statuses'] as $dayStat)
                            @php
                                $cellClass = '';
                                if($dayStat['code'] === 'S') $cellClass = 'sunday';
                                elseif($dayStat['code'] === 'H') $cellClass = 'holiday';
                                
                                $textClass = '';
                                if($dayStat['code'] === 'P') $textClass = 'text-success';
                                elseif($dayStat['code'] === 'P2') $textClass = 'text-warning';
                                elseif($dayStat['code'] === 'A') $textClass = 'text-danger';
                                elseif($dayStat['code'] === 'H/W' || $dayStat['code'] === 'S/W') $textClass = 'text-info';
                            @endphp
                            <td class="date-col {{ $cellClass }} {{ $textClass }}">
                                <strong>{{ $dayStat['code'] }}</strong>
                            </td>
                        @endforeach
                    @endif
                    
                    <td>{{ $s['total_working_days'] }}</td>
                    <td class="text-success"><strong>{{ $s['total_present_combined'] }}</strong></td>
                    <td class="text-success">{{ $s['total_present'] }}</td>
                    <td class="text-warning">{{ $s['total_halfday'] }}</td>
                    <td class="text-primary">{{ $s['total_short_leaves'] ?? 0 }}</td>
                    <td class="text-info">{{ $s['total_sundays_worked'] }}</td>
                    <td class="text-info">{{ $s['total_holidays_worked'] }}</td>
                    <td class="text-secondary">{{ $s['days_on_leave'] }}</td>
                    <td class="text-danger">{{ $s['total_unpaid_leaves'] ?? 0 }}</td>
                    <td class="text-danger">{{ $s['days_absent'] }}</td>
                    <td>{{ $s['total_less_8_30'] }}</td>
                    <td>{{ $s['total_more_8_30'] }}</td>
                    <td class="text-danger">{{ $s['late_count'] ?? 0 }}</td>
                    <td class="text-danger">{{ $s['total_late_minutes'] ?? 0 }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="100%">No data found for this month.</td>
            </tr>
        @endif
    </tbody>
</table>

</body>
</html>
