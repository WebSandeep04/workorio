<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $employee->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #434AFA; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #434AFA; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .emp-details { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .emp-details td { padding: 5px; border-bottom: 1px solid #eee; }
        .emp-details td:nth-child(odd) { font-weight: bold; width: 20%; color: #555; }
        .emp-details td:nth-child(even) { width: 30%; }
        .salary-breakdown { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .salary-breakdown th { background: #434AFA; color: white; padding: 8px; text-align: left; }
        .salary-breakdown td { padding: 8px; border: 1px solid #eee; }
        .salary-breakdown .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f9f9f9; }
        .net-pay { text-align: right; margin-top: 30px; font-size: 16px; font-weight: bold; padding: 15px; background: #f0f4ff; border: 1px solid #c8d9ff; border-radius: 4px; }
        .net-pay span { color: #434AFA; font-size: 20px; }
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Payslip</h1>
        <p>For the month of {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</p>
    </div>

    <table class="emp-details">
        <tr>
            <td>Employee Name:</td>
            <td>{{ $employee->name }}</td>
            <td>Employee Code:</td>
            <td>{{ $employee->employee_code ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Designation:</td>
            <td>{{ $employee->designation->name ?? 'N/A' }}</td>
            <td>Department:</td>
            <td>{{ $employee->department->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="salary-breakdown">
        <thead>
            <tr>
                <th style="width: 50%;">Earnings</th>
                <th class="amount" style="width: 50%;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalEarnings = 0; @endphp
            @foreach($earnings as $earning)
            <tr>
                <td>{{ $earning->salaryComponent->name }}</td>
                <td class="amount">{{ number_format($earning->amount, 2) }}</td>
            </tr>
            @php $totalEarnings += $earning->amount; @endphp
            @endforeach
            <tr class="total-row">
                <td>Total Earnings</td>
                <td class="amount">{{ number_format($totalEarnings, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="salary-breakdown">
        <thead>
            <tr>
                <th style="width: 50%;">Deductions</th>
                <th class="amount" style="width: 50%;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDeductions = 0; @endphp
            @foreach($deductions as $deduction)
            <tr>
                <td>{{ $deduction->salaryComponent->name }}</td>
                <td class="amount">{{ number_format($deduction->amount, 2) }}</td>
            </tr>
            @php $totalDeductions += $deduction->amount; @endphp
            @endforeach
            @if($deductions->isEmpty())
            <tr>
                <td>No Deductions</td>
                <td class="amount">0.00</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Deductions</td>
                <td class="amount">{{ number_format($totalDeductions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="net-pay">
        Net Salary: <span>Rs. {{ number_format($detail->net_salary, 2) }}</span>
    </div>

    <div class="footer">
        This is a system generated payslip and does not require a signature.
    </div>

</body>
</html>
