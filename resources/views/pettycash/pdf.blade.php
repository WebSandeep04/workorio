<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 15px 20px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #434afa;
            padding-bottom: 12px;
        }
        .header h2 {
            margin: 0 0 6px 0;
            color: #1e293b;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 11px;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin-bottom: 20px;
            margin-left: -12px;
            margin-right: -12px;
        }
        .summary-card-cell {
            width: 33.33%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .summary-val {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-val.expense {
            color: #dc2626;
        }
        .summary-val.remaining {
            color: #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
        }
        th {
            background-color: #434afa;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge-approved {
            color: #047857;
            font-weight: bold;
        }
        .badge-pending {
            color: #b45309;
            font-weight: bold;
        }
        .total-row {
            background-color: #f1f5f9 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>{{ $title }}</h2>
    <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<table class="summary-table">
    <tr>
        <td class="summary-card-cell">
            <div class="summary-label">Total Opening Balance</div>
            <div class="summary-val">Rs. {{ number_format($totalOpeningBalance, 2) }}</div>
        </td>
        <td class="summary-card-cell">
            <div class="summary-label">Total Filtered Expense</div>
            <div class="summary-val expense">Rs. {{ number_format($totalExpense, 2) }}</div>
        </td>
        <td class="summary-card-cell">
            <div class="summary-label">Remaining Balance</div>
            <div class="summary-val remaining">Rs. {{ number_format($remainingBalance, 2) }}</div>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 35px;" class="text-center">#</th>
            <th style="width: 85px;">Date</th>
            <th>Wallet / Department</th>
            <th>Expense Name</th>
            <th class="text-end" style="width: 100px;">Price (Rs.)</th>
            <th>Remark</th>
            <th style="width: 75px;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; @endphp
        @forelse($items as $index => $item)
            @php $grandTotal += (float)$item->price; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                <td>{{ $item->department ? $item->department->name : '-' }}</td>
                <td>{{ $item->expense ? $item->expense->name : 'N/A' }}</td>
                <td class="text-end">{{ number_format((float)$item->price, 2) }}</td>
                <td>{{ $item->remark ?? '-' }}</td>
                <td class="text-center">
                    @if($item->is_approved)
                        <span class="badge-approved">Approved</span>
                    @else
                        <span class="badge-pending">Pending</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">No expenses found for the selected criteria.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-end">Total:</td>
            <td class="text-end">Rs. {{ number_format($grandTotal, 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    Petty Cash Management System • Page 1
</div>

</body>
</html>

