<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Wise Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .summary-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }
        .summary-box table { width: 100%; border: none; }
        .summary-box th, .summary-box td { border: none; padding: 5px; text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: center; }
        th { background-color: #f4f5f7; font-weight: bold; }
        .text-left { text-align: left; }
        .text-success { color: #10b981; }
        .text-warning { color: #f59e0b; }
        .text-danger { color: #ef4444; }
        .text-info { color: #0ea5e9; }
        .badge { padding: 2px 5px; border-radius: 3px; color: white; }
        .bg-office { background-color: #434afa; }
        .bg-field { background-color: #10b981; }
    </style>
</head>
<body>

<div class="header">
    <h2>User Wise Attendance Report</h2>
    <p>User: <?php echo e($data['user']['name']); ?> | Month: <?php echo e($data['month']['display']); ?></p>
</div>

<div class="summary-box">
    <strong>Summary:</strong>
    <?php $s = $data['summary']; ?>
    <table style="margin-top: 10px;">
        <tr>
            <td>Working Days: <?php echo e($s['total_working_days']); ?></td>
            <td>Present: <span class="text-success"><?php echo e($s['total_present']); ?></span></td>
            <td>Half Day: <span class="text-warning"><?php echo e($s['total_halfday']); ?></span></td>
            <td>Absent: <span class="text-danger"><?php echo e($s['days_absent']); ?></span></td>
        </tr>
        <tr>
            <td>Leave: <?php echo e($s['days_on_leave']); ?></td>
            <td>Holiday Working: <span class="text-info"><?php echo e($s['total_holidays_worked']); ?></span></td>
            <td>Less 8:30: <?php echo e($s['total_less_8_30']); ?></td>
            <td>More 8:30: <?php echo e($s['total_more_8_30']); ?></td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>First In</th>
            <th>Last Out</th>
            <th>Total (H:MM)</th>
            <th>Office</th>
            <th>Field</th>
            <th>Break</th>
            <th>Late Reason</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($data['daily_breakdown']) > 0): ?>
            <?php
                if (!function_exists('formatH')) {
                    function formatH($decimal) {
                        $min = (int)round(floatval($decimal) * 60);
                        $h = floor($min / 60);
                        $m = $min % 60;
                        return $h . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                    }
                }
            ?>
            <?php $__currentLoopData = $data['daily_breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $firstIn = '-';
                    $lastOut = '-';
                    if(!empty($d['movements'])){
                        $f = collect($d['movements'])->firstWhere(fn($m) => in_array($m['movement_type'] ?? $m['type'] ?? '', ['Office', 'Field', 'office', 'field']) && in_array($m['movement_action'] ?? $m['action'] ?? '', ['In', 'Start', 'in', 'start']));
                        if($f) $firstIn = \Carbon\Carbon::parse($f['time'])->format('H:i');
                        $l = collect($d['movements'])->reverse()->firstWhere(fn($m) => in_array($m['movement_type'] ?? $m['type'] ?? '', ['Office', 'Field', 'office', 'field']) && in_array($m['movement_action'] ?? $m['action'] ?? '', ['Out', 'End', 'out', 'end']));
                        if($l) $lastOut = \Carbon\Carbon::parse($l['time'])->format('H:i');
                    }
                ?>
                <tr style="<?php echo e($d['status'] == 'sunday' ? 'background-color: #fff0f0;' : ''); ?>">
                    <td class="text-left"><?php echo e($d['display_date']); ?></td>
                    <td><?php echo e(ucfirst($d['status'])); ?> <?php echo e($d['holiday_name'] ? "({$d['holiday_name']})" : ''); ?></td>
                    <td><?php echo e($firstIn); ?></td>
                    <td><?php echo e($lastOut); ?></td>
                    <td><?php echo e(formatH($d['hours'])); ?></td>
                    <td><?php echo e(formatH($d['office_hours'])); ?></td>
                    <td><?php echo e(formatH($d['field_hours'])); ?></td>
                    <td><?php echo e(formatH($d['break_time'])); ?></td>
                    <td><?php echo e($d['description'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No data found for this period.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/attendance/user-report-pdf.blade.php ENDPATH**/ ?>