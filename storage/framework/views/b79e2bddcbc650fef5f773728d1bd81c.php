<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Date Wise Attendance Report</title>
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
    </style>
</head>
<body>

<div class="header">
    <h2>Date Wise Attendance Report</h2>
    <p>Date: <?php echo e($data['date']); ?></p>
</div>

<div class="summary-box">
    <strong>Summary:</strong>
    <?php $s = $data['summary']; ?>
    <table style="margin-top: 10px;">
        <tr>
            <td>Total Employees: <?php echo e($s['total_users']); ?></td>
            <td>Present: <span class="text-success"><?php echo e($s['present']); ?></span></td>
            <td>Half Day: <span class="text-warning"><?php echo e($s['halfday']); ?></span></td>
        </tr>
        <tr>
            <td>Absent: <span class="text-danger"><?php echo e($s['absent']); ?></span></td>
            <td>Leave: <span class="text-warning"><?php echo e($s['leave']); ?></span></td>
            <td>Holiday Working: <span class="text-info"><?php echo e($s['holiday_working']); ?></span></td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Status</th>
            <th>First In</th>
            <th>Last Out</th>
            <th>Total (H:MM)</th>
            <th>Office</th>
            <th>Field</th>
            <th>Break</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($data['data']) > 0): ?>
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
            <?php $__currentLoopData = $data['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="<?php echo e(($d['status'] == 'holiday' || $d['status'] == 'sunday') ? ($d['hours'] > 0 ? 'background-color: #f0fff4;' : 'background-color: #f8f9fa;') : ($d['status'] == 'absent' ? 'background-color: #fff5f5;' : '')); ?>">
                    <td class="text-left"><strong><?php echo e($d['user']['name']); ?></strong></td>
                    <td><?php echo e(ucfirst($d['status'])); ?> <?php echo e($d['holiday_name'] ? "({$d['holiday_name']})" : ''); ?></td>
                    <td><?php echo e($d['first_in']); ?></td>
                    <td><?php echo e($d['last_out']); ?></td>
                    <td><?php echo e(formatH($d['hours'])); ?></td>
                    <td><?php echo e(formatH($d['office_hours'])); ?></td>
                    <td><?php echo e(formatH($d['field_hours'])); ?></td>
                    <td><?php echo e(formatH($d['break_time'])); ?></td>
                    <td><?php echo e($d['description'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No data found for this date.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/attendance/date-report-pdf.blade.php ENDPATH**/ ?>