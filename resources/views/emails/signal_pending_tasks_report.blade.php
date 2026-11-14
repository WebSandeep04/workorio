<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signal Pending Tasks Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }
        .wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .header-box {
            background: #434afa;
            padding: 12px 10px;
            color: #ffffff;
            text-align: center;
        }
        .header-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }
        .header-sub {
            margin: 3px 0 0;
            font-size: 11px;
            color: #e0e7ff;
        }
        .content-body {
            padding: 6px 4px 12px 4px;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            margin-bottom: 8px;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 4px;
            text-align: center;
        }
        .card-pending { border-top: 3px solid #434afa; }
        .card-ai { border-top: 3px solid #10b981; }
        .card-manual { border-top: 3px solid #f59e0b; }
        
        .card-label {
            font-size: 8.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .card-val {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
        }
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            white-space: nowrap;
        }
        .data-table th {
            background: #434afa;
            color: #ffffff;
            padding: 5px 6px;
            border: 1px solid #3730a3;
            font-weight: 600;
            font-size: 9.5px;
            text-align: left;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.25;
            vertical-align: middle;
        }
        .footer-box {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="wrapper">
    
    <!-- Top Header Banner -->
    <div class="header-box">
        <h2 class="header-title">📋 SIGNAL TASKS REPORT</h2>
        <div class="header-sub">{{ $payload['tenant_name'] }} &bull; {{ $payload['dateDisplay'] }}</div>
    </div>

    <div class="content-body">
        
        <!-- Summary Cards in 1 Row -->
        <table class="summary-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="summary-card card-pending" width="33%">
                    <div class="card-label">Total Pending</div>
                    <div class="card-val" style="color: #434afa;">
                        {{ $payload['total_pending'] }}
                    </div>
                </td>
                <td class="summary-card card-ai" width="33%">
                    <div class="card-label">AI Detected</div>
                    <div class="card-val" style="color: #10b981;">
                        {{ $payload['ai_count'] }}
                    </div>
                </td>
                <td class="summary-card card-manual" width="34%">
                    <div class="card-label">Manual / Immediate</div>
                    <div class="card-val" style="color: #f59e0b;">
                        {{ $payload['manual_count'] }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Tasks Table (Compact, legible font, horizontal scroll enabled) -->
        <div class="table-wrap">
            <table class="data-table" cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 25px;">#</th>
                        <th style="text-align: center; width: 45px;">Type</th>
                        <th style="text-align: left;">Title</th>
                        <th style="text-align: left;">Description</th>
                        <th style="text-align: left;">Chat / Source</th>
                        <th style="text-align: left;">Assigned To</th>
                        <th style="text-align: left;">Requested By</th>
                        <th style="text-align: center; width: 75px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse ($payload['tasks'] as $task)
                        @php
                            $rowBg = ($counter % 2 == 0) ? '#f8fafc' : '#ffffff';
                            $isAi = ($task['task_type'] ?? 'ai') === 'ai';
                            $typeBadgeBg = $isAi ? '#e0f2fe' : '#fef3c7';
                            $typeBadgeColor = $isAi ? '#0369a1' : '#b45309';
                            $typeLabel = $isAi ? 'AI' : 'Manual';
                            
                            $titleLimited = \Illuminate\Support\Str::limit($task['title'] ?: 'N/A', 20, '...');
                            $descLimited = \Illuminate\Support\Str::limit($task['description'] ?: '-', 20, '...');
                        @endphp
                        <tr style="background-color: {{ $rowBg }};">
                            <td style="text-align: center; color: #64748b; font-weight: 600;">
                                {{ $counter }}
                            </td>
                            <td style="text-align: center;">
                                <span style="background-color: {{ $typeBadgeBg }}; color: {{ $typeBadgeColor }}; font-weight: 700; font-size: 8.5px; padding: 2px 4px; border-radius: 3px; display: inline-block;">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td style="color: #0f172a; font-weight: 600;" title="{{ $task['title'] }}">
                                {{ $titleLimited }}
                            </td>
                            <td style="color: #334155;" title="{{ $task['description'] }}">
                                {{ $descLimited }}
                            </td>
                            <td style="color: #475569;">
                                {{ $task['chat_name'] ?: 'N/A' }}
                            </td>
                            <td style="color: #475569;">
                                {{ $task['assigned_to'] ?: 'Unassigned' }}
                            </td>
                            <td style="color: #475569;">
                                {{ $task['requested_by'] ?: '-' }}
                            </td>
                            <td style="text-align: center; color: #64748b; font-size: 9px;">
                                {{ $task['created_at_formatted'] }}
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 16px; text-align: center; color: #64748b; font-size: 11px;">
                                🎉 No pending Signal tasks found. Everything is converted or processed!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer-box">
        <p style="margin: 0;">This is an automated notification from <strong>Workorio</strong> for Administrator (Role ID: 1).</p>
        <p style="margin: 3px 0 0 0;">&copy; {{ date('Y') }} Workorio. All rights reserved.</p>
    </div>

</div>

</body>
</html>
