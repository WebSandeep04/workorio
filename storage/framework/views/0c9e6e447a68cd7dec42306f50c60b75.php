<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 10px; font-family: Arial, sans-serif; background-color: #ffffff;">

<h2 style="font-family: Arial, sans-serif; color: #2c3e50;">📌 Last Day Worklog Summary</h2>
<p style="font-family: Arial, sans-serif; font-size:14px; color:#555;">
    Here is the summary of worklogs for all users yesterday:
</p>
<div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
    <table width="100%" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:12px; border:1px solid #ddd; min-width: 800px; white-space: nowrap;">
        <thead>
            <tr style="background-color:#34495e; color:#fff;">
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">User</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Company</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Service</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Project</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Module</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Hours</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Minutes</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Description</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Rating</th>
                <th style="padding:3px; border:1px solid #ddd; text-align:left;">Rating Remark</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $colors = ['#f9f9f9', '#e8f6f3', '#fff3e0', '#fde2e2', '#f0f0f0', '#e6e6fa'];
                $color_index = 0;
                $last_user = null;
            ?>
            
            <?php $__empty_1 = true; $__currentLoopData = $payload['worklogs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    if ($last_user !== $row['user_id']) {
                        $bg_color = $colors[$color_index % count($colors)];
                        $color_index++;
                        $last_user = $row['user_id'];
                    }

                    $company_name = htmlspecialchars($row['company_name'] ?? '-');
                    $rating = htmlspecialchars($row['rating'] ?? '-'); 
                    $rating_remark = htmlspecialchars($row['rating_remark'] ?? '-');
                    $desc = nl2br(htmlspecialchars($row['description'] ?? '-'));
                ?>
                <tr style="background-color:<?php echo e($bg_color); ?>;">
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top;"><?php echo e(htmlspecialchars($row['user_name'])); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top; white-space:normal; min-width:120px;"><?php echo e($company_name); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top;"><?php echo e(htmlspecialchars($row['service_name'] ?? '-')); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top;"><?php echo e(htmlspecialchars($row['customer_project_name'] ?? '-')); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top;"><?php echo e(htmlspecialchars($row['module_name'] ?? '-')); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top; text-align:center;"><?php echo e(htmlspecialchars($row['hours'] ?? '0')); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top; text-align:center;"><?php echo e(htmlspecialchars($row['minutes'] ?? '0')); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top; white-space:normal; min-width:150px;"><?php echo $desc; ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top;"><?php echo e($rating); ?></td>
                    <td style="padding:3px; border:1px solid #ddd; vertical-align:top; white-space:normal; min-width:100px;"><?php echo e($rating_remark); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" style="padding:12px; text-align:center; border:1px solid #ddd;">No worklogs recorded yesterday.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<br>
<p style="font-family: Arial, sans-serif; font-size:13px; color:#555;">
    Regards,<br>
    <b>HR Team</b>
</p>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/worklog_report_yesterday.blade.php ENDPATH**/ ?>