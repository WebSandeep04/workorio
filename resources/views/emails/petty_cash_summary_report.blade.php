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

            <!-- Section 1: Department-wise Summary -->
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
                                <td>{{ $dept['name'] }}</td>
                                <td align="right">₹{{ number_format($dept['opening_balance'], 2) }}</td>
                                <td align="right" style="color: #dc2626;">₹{{ number_format($dept['total_expense'], 2) }}</td>
                                <td align="right" style="color: #2563eb;">₹{{ number_format($dept['remaining_balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" align="center" style="padding: 8px; color: #64748b;">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f1f5f9; font-weight: 600;">
                            <td style="text-transform: uppercase;">Total:</td>
                            <td align="right">₹{{ number_format($payload['overall_opening_balance'], 2) }}</td>
                            <td align="right" style="color: #dc2626;">₹{{ number_format($payload['overall_total_expense'], 2) }}</td>
                            <td align="right" style="color: #2563eb;">₹{{ number_format($payload['overall_remaining_balance'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Section 2: Last 10 Days Complete Table (Status removed, Remark added, small font, non-bold, fits without scroll) -->
            <div class="section-title">📋 Last 10 Days Expense History</div>
            <div class="section-sub">
                Entries from {{ $payload['start_date'] }} to {{ $payload['end_date'] }} ({{ count($payload['recent_entries']) }} entries)
            </div>

            <div class="table-wrap">
                <table class="data-table table-dark-header">
                    <thead>
                        <tr>
                            <th align="center" style="width: 6%;">#</th>
                            <th align="left" style="width: 20%;">Date</th>
                            <th align="left" style="width: 20%;">Department</th>
                            <th align="left" style="width: 25%;">Expense</th>
                            <th align="right" style="width: 14%;">Price</th>
                            <th align="left" style="width: 15%;">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payload['recent_entries'] as $idx => $entry)
                            <tr style="background: {{ $idx % 2 === 1 ? '#f8fafc' : '#ffffff' }};">
                                <td align="center" style="color: #64748b;">{{ $idx + 1 }}</td>
                                <td>{{ $entry['date'] }}</td>
                                <td>{{ $entry['department'] }}</td>
                                <td>{{ $entry['expense_name'] }}</td>
                                <td align="right">₹{{ number_format($entry['price'], 2) }}</td>
                                <td style="color: #64748b;">{{ $entry['remark'] ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" align="center" style="padding: 10px; color: #64748b;">
                                    No petty cash expense entries recorded in the last 10 days.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($payload['recent_entries']) > 0)
                    <tfoot>
                        <tr style="background: #f1f5f9; font-weight: 600;">
                            <td colspan="4" align="right" style="text-transform: uppercase;">
                                Total (Last 10 Days):
                            </td>
                            <td align="right" style="color: #dc2626;">
                                ₹{{ number_format($payload['recent_total_price'], 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

        </div>

        <div class="footer-box">
            Workorio Petty Cash Management System
        </div>

    </div>

</body>
</html>
