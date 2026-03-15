<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 2px; font-family: Arial, sans-serif; background-color: #f4f4f4;">

<div style="width: 100%; max-width: 1400px; margin: 0 auto; background-color: #ffffff; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    
    <div style="background-color: #0d6efd; padding:20px; text-align:center;">
        <h1 style="margin:0; color:white; font-size:20px;">📊 Pending Tasks Summary</h1>
        <p style="margin:5px 0 0 0; color:white; font-size:12px;">Workorio Task Management System</p>
    </div>
    
    <div style="padding:15px 2px 25px 2px;">
        <p style="font-size:12px; color:#495057; margin:0 0 20px 0;">Below is a summary of all pending tasks, grouped by assigned users:</p>
        
        <?php $__currentLoopData = $payload['tasksByUser']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uid => $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="margin-bottom:20px;">
                <div style="background-color: #6c757d; color:white; padding:8px 12px;">
                    <h3 style="margin:0; font-size:13px; font-weight:600;">
                        👤 <?php echo e($ud['user_name']); ?>

                        <span style="background-color:rgba(255,255,255,0.2); padding:2px 6px; border-radius:8px; font-size:10px; margin-left:8px;"><?php echo e(count($ud['tasks'])); ?> tasks</span>
                    </h3>
                </div>
                
                <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; font-family: Arial, sans-serif; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom:10px; min-width: 800px; white-space: nowrap;">
                    <thead style="background-color:#f1f3f5; color:#495057;">
                        <tr>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:20px;">#</th>
                            <th style="padding:5px 4px; text-align:left; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:15%;">Customer</th>
                            <th style="padding:5px 4px; text-align:left; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:18%;">Task Name</th>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:50px;">Type</th>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:50px;">Priority</th>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:50px;">Status</th>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:50px;">Created</th>
                            <th style="padding:5px 4px; text-align:left; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:60px;">Created By</th>
                            <th style="padding:5px 4px; text-align:left; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:22%;">Remark</th>
                            <th style="padding:5px 4px; text-align:center; border:1px solid #dee2e6; font-size:9px; font-weight:600; width:80px;">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php $__currentLoopData = $ud['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $rowColor = ($i % 2 == 0) ? '#f8f9fa' : '#ffffff';
                                $customer = htmlspecialchars($t['customer_company'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                
                                $taskName = $t['task_name'] ?? '';
                                $taskNameHtml = $taskName !== '' ? htmlspecialchars($taskName, ENT_QUOTES, 'UTF-8') : '<span style="color:#adb5bd; font-style:italic; font-size:9px;">No Name</span>';

                                $taskType = strtoupper($t['task_type'] ?? 'task');
                                $typeColor = ($taskType === 'QC') ? '#6f42c1' : '#0d6efd';
                                $typeBadge = "<span style='background-color:{$typeColor}; color:#fff; padding:1px 4px; border-radius:6px; font-size:8px; font-weight:600;'>" . htmlspecialchars($taskType, ENT_QUOTES, 'UTF-8') . "</span>";

                                if (!empty($t['priority_name'])) {
                                    $pColor = $t['priority_color'] ?? '#6c757d';
                                    $priority = "<span style='background-color:{$pColor}; color:#fff; padding:1px 4px; border-radius:6px; font-size:8px; font-weight:600;'>" . htmlspecialchars($t['priority_name'], ENT_QUOTES, 'UTF-8') . "</span>";
                                } else {
                                    $priority = "<span style='background-color:#e9ecef; color:#6c757d; padding:1px 4px; border-radius:6px; font-size:8px;'>None</span>";
                                }

                                $sColor = $t['status_color'] ?? '#6c757d';
                                $statusName = $t['status_name'] ?? 'Pending';
                                $status = "<span style='background-color:{$sColor}; color:#fff; padding:1px 4px; border-radius:6px; font-size:8px; font-weight:600;'>" . htmlspecialchars($statusName, ENT_QUOTES, 'UTF-8') . "</span>";

                                $createdDate = date('d M', strtotime($t['created_at']));
                                $createdBy = htmlspecialchars($t['created_by_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');

                                $lr = trim((string)($t['latest_remark'] ?? ''));
                                if ($lr === '') {
                                    $lrShort = '-';
                                } else {
                                    $plain = strip_tags($lr);
                                    $lrShort = function_exists('mb_strlen') ? (mb_strlen($plain) > 160 ? mb_substr($plain, 0, 160) . '…' : $plain) : (strlen($plain) > 160 ? substr($plain, 0, 160) . '…' : $plain);
                                    $lrShort = htmlspecialchars($lrShort, ENT_QUOTES, 'UTF-8');
                                }

                                $remarkDate = '-';
                                if (!empty($t['latest_remark_date'])) {
                                    $remarkDate = date('d M Y', strtotime($t['latest_remark_date']));
                                }
                            ?>

                            <tr style="background-color:<?php echo e($rowColor); ?>;">
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6; font-weight:600; color:#495057; font-size:9px;"><?php echo e($i); ?></td>
                                <td style="padding:4px 3px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:9px;"><?php echo e($customer); ?></td>
                                <td style="padding:4px 3px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:9px;"><?php echo $taskNameHtml; ?></td>
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6;"><?php echo $typeBadge; ?></td>
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6;"><?php echo $priority; ?></td>
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6;"><?php echo $status; ?></td>
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6; font-size:8px; color:#6c757d;"><?php echo e($createdDate); ?></td>
                                <td style="padding:4px 3px; text-align:left; border:1px solid #dee2e6; font-size:8px; color:#495057;"><?php echo e($createdBy); ?></td>
                                <td style="padding:4px 3px; text-align:left; border:1px solid #dee2e6; font-size:9px; color:#495057;"><?php echo e($lrShort); ?></td>
                                <td style="padding:4px 3px; text-align:center; border:1px solid #dee2e6; font-size:8px; color:#64748b;"><?php echo e($remarkDate); ?></td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

    <div style="background-color:#f8f9fa; padding:15px; text-align:center; font-size:11px; color:#6c757d; border-top:1px solid #dee2e6;">
        Report generated on <?php echo e($payload['timeDisplay']); ?><br>
        © <?php echo e(date('Y')); ?> Workorio. All rights reserved.<br>
        Total Pending Tasks: <?php echo e($payload['totalTasks']); ?> across <?php echo e($payload['totalUsers']); ?> users.
    </div>

</div>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/all_task_report.blade.php ENDPATH**/ ?>