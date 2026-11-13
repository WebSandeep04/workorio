<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash Summary Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }
        .wrapper {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        .header-box {
            background: #434afa;
            padding: 12px 10px;
            color: #ffffff;
        }
        .header-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }
        .header-sub {
            margin: 3px 0 0;
            font-size: 11px;
            color: #e0e7ff;
        }
        .content-body {
            padding: 8px 4px;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            margin-bottom: 12px;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 4px;
            text-align: center;
        }
        .card-ob { border-top: 3px solid #8b5cf6; }
        .card-exp { border-top: 3px solid #f43f5e; }
        .card-rem { border-top: 3px solid #f97316; }
        
        .card-label {
            font-size: 8px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .card-val {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.1;
        }
        .section-title {
            font-size: 12px;
            color: #1e293b;
            margin: 12px 0 6px;
            font-weight: 700;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-sub {
            font-size: 10px;
            color: #64748b;
            margin: 0 0 6px;
        }
        .table-wrap {
            width: 100%;
            margin-bottom: 12px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        .data-table th {
            background: #434afa;
            color: #ffffff;
            padding: 4px 5px;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            font-size: 9.5px;
            text-align: left;
        }
        .data-table td {
            padding: 4px 5px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-weight: normal;
            font-size: 10px;
            line-height: 1.25;
        }
        .table-dark-header th {
            background: #334155;
            border-color: #334155;
        }
        .footer-box {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        
        <!-- Header -->
        <div class="header-box">
            <h2 class="header-title">💰 PETTY CASH SUMMARY REPORT</h2>
            <div class="header-sub">{{ $payload['tenant_name'] ?? 'Organization' }}</div>
        </div>

        <div class="content-body">

            <!-- Summary Cards in 1 Row -->
            <table class="summary-table" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="summary-card card-ob" width="33%">
                        <div class="card-label">Total Opening Balance</div>
                        <div class="card-val" style="color: #0f172a;">
                            ₹{{ number_format($payload['overall_opening_balance'], 2) }}
                        </div>
                    </td>
                    <td class="summary-card card-exp" width="33%">
                        <div class="card-label">Total Expense</div>
                        <div class="card-val" style="color: #dc2626;">
                            ₹{{ number_format($payload['overall_total_expense'], 2) }}
                        </div>
                    </td>
                    <td class="summary-card card-rem" width="34%">
                        <div class="card-label">Remaining Op-Expense</div>
                        <div class="card-val" style="color: #2563eb;">
                            ₹{{ number_format($payload['overall_remaining_balance'], 2) }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- 1. Department-wise Summary -->
            <div class="section-title">🏢 Department-wise Summary</div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th align="left" style="width: 28%;">Department</th>
                            <th align="right" style="width: 24%;">Opening Bal.</th>
                            <th align="right" style="width: 24%;">Expense</th>
                            <th align="right" style="width: 24%;">Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payload['department_summary'] as $dept)
                            <tr style="background: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                                <td style="font-weight: 500;">{{ $dept['name'] }}</td>
                                <td align="right">₹{{ number_format($dept['opening_balance'], 2) }}</td>
                                <td align="right" style="color: #dc2626;">₹{{ number_format($dept['total_expense'], 2) }}</td>
                                <td align="right" style="color: #2563eb; font-weight: 600;">₹{{ number_format($dept['remaining_balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" align="center" style="padding: 8px; color: #64748b;">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- 2. Overall Total right after department summary -->
                    <tfoot>
                        <tr style="background: #f1f5f9; font-weight: 600;">
                            <td style="text-transform: uppercase;">Overall Total:</td>
                            <td align="right">₹{{ number_format($payload['overall_opening_balance'], 2) }}</td>
                            <td align="right" style="color: #dc2626;">₹{{ number_format($payload['overall_total_expense'], 2) }}</td>
                            <td align="right" style="color: #2563eb; font-weight: 700;">₹{{ number_format($payload['overall_remaining_balance'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 3. Last Top-Up: Separate Table -->
            <div class="section-title">💳 Latest Top-Up Details</div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th align="left" style="width: 40%;">Department</th>
                            <th align="right" style="width: 30%;">Top-Up Amount</th>
                            <th align="center" style="width: 30%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $hasAnyTopUp = false; @endphp
                        @foreach($payload['department_summary'] as $dept)
                            @if(!empty($dept['last_top_up']))
                                @php $hasAnyTopUp = true; @endphp
                                <tr style="background: #ffffff;">
                                    <td style="font-weight: 500;">{{ $dept['name'] }}</td>
                                    <td align="right" style="color: #15803d; font-weight: 600;">
                                        ₹{{ number_format($dept['last_top_up']['amount'], 2) }}
                                    </td>
                                    <td align="center" style="color: #475569;">
                                        {{ $dept['last_top_up']['date'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @if(!$hasAnyTopUp)
                            <tr>
                                <td colspan="3" align="center" style="padding: 8px; color: #64748b;">No recent top-up records found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- 4. Department-wise Expense History (Last 10 Days) -->
            <!-- Each Department: Department Summary followed by its Last 10 Days Expense History -->
            <div class="section-title" style="margin-top: 16px; border-bottom: 2px solid #334155;">
                📋 Department-wise Expense History (Last 10 Days)
            </div>
            <div class="section-sub">
                Reporting period: {{ $payload['start_date'] }} to {{ $payload['end_date'] }}
            </div>

            @foreach($payload['department_summary'] as $dept)
                <div style="margin-top: 14px; margin-bottom: 6px; padding: 6px 8px; background: #f1f5f9; border-left: 3px solid #434afa; font-size: 11px; font-weight: 700; color: #1e293b;">
                    📁 {{ $dept['name'] }} Department (Last 10 Days)
                </div>

                <!-- Last 10 Days Expense History for this department -->
                <div class="table-wrap" style="margin-bottom: 14px;">
                    <table class="data-table table-dark-header">
                        <thead>
                            <tr>
                                <th align="center" style="width: 10%;">#</th>
                                <th align="left" style="width: 22%;">Date</th>
                                <th align="left" style="width: 32%;">Expense Name</th>
                                <th align="right" style="width: 16%;">Price</th>
                                <th align="left" style="width: 20%;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dept['recent_entries'] as $idx => $entry)
                                <tr style="background: {{ $idx % 2 === 1 ? '#f8fafc' : '#ffffff' }};">
                                    <td align="center" style="white-space: nowrap;">
                                        <span style="color: #64748b;">{{ $idx + 1 }}</span>
                                        @if($entry['is_approved'])
                                            <span title="Approved" style="display: inline-block; padding: 1px 3px; font-size: 8px; font-weight: bold; border-radius: 2px; background: #dcfce7; color: #15803d; margin-left: 2px; line-height: 1;">A</span>
                                        @else
                                            <span title="Pending" style="display: inline-block; padding: 1px 3px; font-size: 8px; font-weight: bold; border-radius: 2px; background: #fef3c7; color: #b45309; margin-left: 2px; line-height: 1;">P</span>
                                        @endif
                                    </td>
                                    <td>{{ $entry['date'] }}</td>
                                    <td>{{ $entry['expense_name'] }}</td>
                                    <td align="right">₹{{ number_format($entry['price'], 2) }}</td>
                                    <td style="color: #64748b;">{{ $entry['remark'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" align="center" style="padding: 8px; color: #64748b;">
                                        No expenses recorded in the last 10 days for {{ $dept['name'] }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($dept['recent_entries']) > 0)
                        <tfoot>
                            <tr style="background: #f1f5f9; font-weight: 600;">
                                <td colspan="3" align="right" style="text-transform: uppercase;">
                                    Total (Last 10 Days - {{ $dept['name'] }}):
                                </td>
                                <td align="right" style="color: #dc2626;">
                                    ₹{{ number_format($dept['recent_total_price'], 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            @endforeach

        </div>

        <div class="footer-box">
            Workorio Petty Cash Management System
        </div>

    </div>

</body>
</html>
