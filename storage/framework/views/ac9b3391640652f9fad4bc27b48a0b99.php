<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Today's Attendance Summary</title>
</head>
<body style="margin: 0; padding: 1px; font-family: Arial, sans-serif; background-color: #ffffff;">

    <h2 style='font-family: Arial, sans-serif; color: #2c3e50; margin-top: 0;'>📊 Attendance Summary</h2>
    <p style='font-family: Arial, sans-serif; font-size:14px; color:#555;'>
        Today's attendance summary and complete monthly breakdown for <?php echo e($monthYear); ?>:
    </p>

    <h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📅 Today's Attendance Summary - <?php echo e(\Carbon\Carbon::parse($today)->format('F j, Y')); ?></h3>
    
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
                <th style='padding:6px; border:1px solid #ddd;'>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reportData['today']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $lrStyle = $row['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                    $statusColor = '#2c3e50';
                    if (str_contains(strtolower($row['status']), 'absent')) $statusColor = '#e74c3c';
                    elseif (str_contains(strtolower($row['status']), 'leave')) $statusColor = '#3498db';
                    elseif (str_contains(strtolower($row['status']), 'halfday')) $statusColor = '#f39c12';
                    elseif (str_contains(strtolower($row['status']), 'present')) $statusColor = '#27ae60';
                    elseif (str_contains(strtolower($row['status']), 'holiday') || str_contains(strtolower($row['status']), 'off')) $statusColor = '#9b59b6';
                ?>
                <tr style='background-color:#f9f9f9;'>
                    <td style='padding:5px; border:1px solid #ddd;'><?php echo e($row['user_name']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd;'><?php echo e($row['punch_in']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd;'><?php echo e($row['punch_out']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd;'><?php echo e($row['mode']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd;'><?php echo e($row['place']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'><?php echo e($row['total_hours']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd; <?php echo e($lrStyle); ?>'><?php echo e($row['late_reason']); ?></td>
                    <td style='padding:5px; border:1px solid #ddd; color:<?php echo e($statusColor); ?>; font-weight:bold; text-transform:capitalize;'><?php echo e($row['status']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center" style='padding:5px; border:1px solid #ddd; text-align:center;'>No data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

    <h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📊 Monthly Attendance Breakdown</h3>
    
    <?php $__currentLoopData = $reportData['monthly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userMonthly): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h4 style='font-family: Arial, sans-serif; color: #2c3e50; margin-top: 25px; margin-bottom: 10px;'>👤 <?php echo e($userMonthly['user_name']); ?></h4>
        
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
                    <th style='padding:0; border:1px solid #ddd;'>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $userMonthly['records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $lrStyle = $record['late_reason'] !== '-' ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                        $statusColor = '#2c3e50';
                        if (str_contains(strtolower($record['status']), 'absent')) $statusColor = '#e74c3c';
                        elseif (str_contains(strtolower($record['status']), 'leave')) $statusColor = '#3498db';
                        elseif (str_contains(strtolower($record['status']), 'halfday')) $statusColor = '#f39c12';
                        elseif (str_contains(strtolower($record['status']), 'present')) $statusColor = '#27ae60';
                        elseif (str_contains(strtolower($record['status']), 'holiday') || str_contains(strtolower($record['status']), 'off')) $statusColor = '#9b59b6';
                    ?>
                    <tr>
                        <td style='padding:0; border:1px solid #ddd;'><?php echo e($record['date']); ?></td>
                        <td style='padding:0; border:1px solid #ddd;'><?php echo e($record['punch_in']); ?></td>
                        <td style='padding:0; border:1px solid #ddd;'><?php echo e($record['punch_out']); ?></td>
                        <td style='padding:0; border:1px solid #ddd;'><?php echo e($record['mode']); ?></td>
                        <td style='padding:0; border:1px solid #ddd;'><?php echo e($record['place']); ?></td>
                        <td style='padding:0; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'><?php echo e($record['total_hours']); ?></td>
                        <td style='padding:0; border:1px solid #ddd; <?php echo e($lrStyle); ?>'><?php echo e($record['late_reason']); ?></td>
                        <td style='padding:0; border:1px solid #ddd; color:<?php echo e($statusColor); ?>; font-weight:bold; text-transform:capitalize;'><?php echo e($record['status']); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr style='background-color:#f8f9fa; font-weight:bold;'>
                    <td style='padding:0; border:1px solid #ddd; text-align:right; padding-right:10px;'><strong>Monthly Total</strong></td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                    <td style='padding:0; border:1px solid #ddd; color:#8e44ad;'><?php echo e($userMonthly['monthly_total']); ?></td>
                    <td style='padding:0; border:1px solid #ddd; color:#95a5a6;'>-</td>
                    <td style='padding:0; border:1px solid #ddd;'>-</td>
                </tr>
            </tbody>
        </table>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <br>
    <p style="font-family: Arial, sans-serif; font-size:13px; color:#555;">
        Regards,<br>
        <b>HR Team</b>
    </p>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/night_attendance.blade.php ENDPATH**/ ?>