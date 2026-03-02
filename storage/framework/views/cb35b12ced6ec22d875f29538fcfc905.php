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
    <p>Month: <?php echo e(Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?></p>
</div>

<table>
    <thead>
        <tr>
            <th class="user-col" rowspan="2">User</th>
            <?php $__currentLoopData = $data['month']['dates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isSunday = $d['is_sunday'];
                    $style = $isSunday ? 'sunday' : '';
                ?>
                <th class="date-col <?php echo e($style); ?>"><?php echo e($d['day']); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th colspan="10">Summary</th>
        </tr>
        <tr>
            <?php $__currentLoopData = $data['month']['dates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isSunday = $d['is_sunday'];
                    $style = $isSunday ? 'sunday' : '';
                ?>
                <th class="date-col <?php echo e($style); ?>" style="font-size: 8px;"><?php echo e(substr($d['day_name'], 0, 1)); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="summary-col">WD</th>
            <th class="summary-col">TP</th>
            <th class="summary-col">FD</th>
            <th class="summary-col">HD</th>
            <th class="summary-col">HW</th>
            <th class="summary-col">L</th>
            <th class="summary-col">A</th>
            <th class="summary-col">&lt;8:30</th>
            <th class="summary-col">&gt;8:30</th>
            <th class="summary-col">Late</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($data['data']) > 0): ?>
            <?php $__currentLoopData = $data['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $s = $item['summary'];
                ?>
                <tr>
                    <td class="user-col"><strong><?php echo e($item['user']['name']); ?></strong></td>
                    
                    <?php if(isset($item['daily_statuses'])): ?>
                        <?php $__currentLoopData = $item['daily_statuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $cellClass = '';
                                if($dayStat['code'] === 'S') $cellClass = 'sunday';
                                elseif($dayStat['code'] === 'H') $cellClass = 'holiday';
                                
                                $textClass = '';
                                if($dayStat['code'] === 'P') $textClass = 'text-success';
                                elseif($dayStat['code'] === 'P2') $textClass = 'text-warning';
                                elseif($dayStat['code'] === 'A') $textClass = 'text-danger';
                                elseif($dayStat['code'] === 'H/W') $textClass = 'text-info';
                            ?>
                            <td class="date-col <?php echo e($cellClass); ?> <?php echo e($textClass); ?>">
                                <strong><?php echo e($dayStat['code']); ?></strong>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    
                    <td><?php echo e($s['total_working_days']); ?></td>
                    <td class="text-success"><strong><?php echo e($s['total_present_combined']); ?></strong></td>
                    <td class="text-success"><?php echo e($s['total_present']); ?></td>
                    <td class="text-warning"><?php echo e($s['total_halfday']); ?></td>
                    <td class="text-info"><?php echo e($s['total_holidays_worked']); ?></td>
                    <td class="text-secondary"><?php echo e($s['days_on_leave']); ?></td>
                    <td class="text-danger"><?php echo e($s['days_absent']); ?></td>
                    <td><?php echo e($s['total_less_8_30']); ?></td>
                    <td><?php echo e($s['total_more_8_30']); ?></td>
                    <td class="text-danger"><?php echo e($s['late_count'] ?? 0); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="100%">No data found for this month.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/attendance/monthly-report-pdf.blade.php ENDPATH**/ ?>