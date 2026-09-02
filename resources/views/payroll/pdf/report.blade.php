<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; margin: 0; padding: 0; }
        h2 { text-align: center; color: #434AFA; margin-bottom: 5px; }
        p.subtitle { text-align: center; margin-top: 0; color: #666; font-size: 11px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: center; }
        th { background-color: #f4f6f9; color: #333; font-weight: bold; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <h2>Salary & Attendance Summary</h2>
    <p class="subtitle">For the month of {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>

    <table>
        <thead>
            <tr>
                <th class="text-start nowrap">Emp Code</th>
                <th class="text-start nowrap">Employee Name</th>
                @foreach($uniqueComponents as $comp)
                <th class="text-end">{{ $comp }}</th>
                @endforeach
                <th>Working Days</th>
                <th>Total Present</th>
                <th>Full Day</th>
                <th>Half Day</th>
                <th>Leave</th>
                <th>Unpaid Leave</th>
                <th>Absent</th>
                <th>Total Weekly Off</th>
                <th>Total Holidays</th>
                <th>Total Deduction Days</th>
                <th class="text-end">Days Deduction</th>
                <th class="text-end">Advance Deduction</th>
                <th class="text-end">Loan Deduction</th>
                <th class="text-end">Total Deduction</th>
                <th>Payable Days</th>
                <th class="text-end">Paid Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summaries as $summary)
                @php
                    $paid = $paidSalaries[$summary->employee_id] ?? null;
                    $comps = $employeeComponents[$summary->employee_id] ?? [];
                    $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
                    $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
                    $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
                    $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
                    
                    $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
                    $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;
                @endphp
                <tr>
                    <td class="text-start nowrap">{{ $summary->employee ? $summary->employee->employee_code : '-' }}</td>
                    <td class="text-start nowrap">{{ $summary->employee ? $summary->employee->name : 'Unknown' }}</td>
                    
                    @foreach($uniqueComponents as $comp)
                        <td class="text-end">Rs. {{ isset($comps[$comp]) ? round($comps[$comp]) : 0 }}</td>
                    @endforeach
                    
                    <td>{{ $summary->total_working_days ?? 0 }}</td>
                    <td>{{ $summary->total_present_combined ?? 0 }}</td>
                    <td>{{ $summary->total_present ?? 0 }}</td>
                    <td>{{ $summary->total_halfday ?? 0 }}</td>
                    <td>{{ $summary->days_on_leave ?? 0 }}</td>
                    <td>{{ $summary->total_unpaid_leaves ?? 0 }}</td>
                    <td>{{ $summary->days_absent ?? 0 }}</td>
                    <td>{{ $summary->total_weekly_offs ?? 0 }}</td>
                    <td>{{ $summary->total_holidays ?? 0 }}</td>
                    <td>{{ $deductionDays }}</td>
                    <td class="text-end">Rs. {{ $lopDeduction }}</td>
                    <td class="text-end">Rs. {{ $advanceDeduction }}</td>
                    <td class="text-end">Rs. {{ $loanDeduction }}</td>
                    <td class="text-end">Rs. {{ $deductionAmount }}</td>
                    <td>{{ $payableDays }}</td>
                    <td class="text-end nowrap">{{ $paid !== null ? 'Rs. ' . number_format($paid, 0, '', '') : 'Not Generated' }}</td>
                </tr>
            @endforeach
            @if($summaries->isEmpty())
                <tr>
                    <td colspan="{{ 15 + count($uniqueComponents) }}">No data available for this month.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
