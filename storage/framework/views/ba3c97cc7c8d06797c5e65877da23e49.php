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
    <p class="subtitle">For the month of <?php echo e(date('F Y', mktime(0, 0, 0, $month, 1, $year))); ?></p>

    <table>
        <thead>
            <tr>
                <th class="text-start nowrap">Emp Code</th>
                <th class="text-start nowrap">Employee Name</th>
                <?php $__currentLoopData = $uniqueComponents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th class="text-end"><?php echo e($comp); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <th class="text-end">Deduction Amount</th>
                <th>Payable Days</th>
                <th class="text-end">Paid Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $summaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $paid = $paidSalaries[$summary->employee_id] ?? null;
                    $comps = $employeeComponents[$summary->employee_id] ?? [];
                    $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
                    
                    $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
                    $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;
                ?>
                <tr>
                    <td class="text-start nowrap"><?php echo e($summary->employee ? $summary->employee->employee_code : '-'); ?></td>
                    <td class="text-start nowrap"><?php echo e($summary->employee ? $summary->employee->name : 'Unknown'); ?></td>
                    
                    <?php $__currentLoopData = $uniqueComponents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-end">Rs. <?php echo e(isset($comps[$comp]) ? round($comps[$comp]) : 0); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <td><?php echo e($summary->total_working_days ?? 0); ?></td>
                    <td><?php echo e($summary->total_present_combined ?? 0); ?></td>
                    <td><?php echo e($summary->total_present ?? 0); ?></td>
                    <td><?php echo e($summary->total_halfday ?? 0); ?></td>
                    <td><?php echo e($summary->days_on_leave ?? 0); ?></td>
                    <td><?php echo e($summary->total_unpaid_leaves ?? 0); ?></td>
                    <td><?php echo e($summary->days_absent ?? 0); ?></td>
                    <td><?php echo e($summary->total_weekly_offs ?? 0); ?></td>
                    <td><?php echo e($summary->total_holidays ?? 0); ?></td>
                    <td><?php echo e($deductionDays); ?></td>
                    <td class="text-end">Rs. <?php echo e($deductionAmount); ?></td>
                    <td><?php echo e($payableDays); ?></td>
                    <td class="text-end nowrap"><?php echo e($paid !== null ? 'Rs. ' . number_format($paid, 0, '', '') : 'Not Generated'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($summaries->isEmpty()): ?>
                <tr>
                    <td colspan="<?php echo e(15 + count($uniqueComponents)); ?>">No data available for this month.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/pdf/report.blade.php ENDPATH**/ ?>