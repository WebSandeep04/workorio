<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Today's Attendance Summary</title>
</head>
<body style="margin: 0; padding: 1px; font-family: Arial, sans-serif; background-color: #ffffff;">

    <h2 style="font-family: Arial, sans-serif; color: #2c3e50; margin:0 0 8px;">📌 Today's Attendance Report</h2>
    <p style="font-family: Arial, sans-serif; font-size:13px; color:#555; margin:0 0 12px;">
        Here is the summary of the first punch-in/field-in for all employees today:
    </p>

    <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
    <table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:13px; border:1px solid #ddd; white-space: nowrap;">
        <thead>
            <tr style="background-color:#34495e; color:#fff;">
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">User</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Attendance Type</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Mode</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Place</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Time (IST)</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Late Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $user = $data['user'];
                    $firstMovement = $data['first_movement'];
                    $isOnLeave = $data['is_on_leave'];
                ?>

                <?php if($firstMovement): ?>
                    <?php
                        $movementType = htmlspecialchars($firstMovement->movement_type, ENT_QUOTES, 'UTF-8');
                        $mode = htmlspecialchars($firstMovement->mode ?? '-', ENT_QUOTES, 'UTF-8');
                        $place = htmlspecialchars($firstMovement->place ?? '-', ENT_QUOTES, 'UTF-8');
                        
                        $dt = new DateTime($firstMovement->time, new DateTimeZone("UTC"));
                        $dt->setTimezone(new DateTimeZone("Asia/Kolkata"));
                        $timeIST = $dt->format("h:i A");

                        // Extract late reason
                        $desc = $firstMovement->description ?? null;
                        $lateReason = '-';
                        if (!empty($desc)) {
                            $prefix = "Late punch-in: ";
                            if (stripos($desc, $prefix) === 0) {
                                $lateReason = trim(substr($desc, strlen($prefix)));
                            } else {
                                $lateReason = trim($desc);
                            }
                        }
                        $lateReasonHtml = htmlspecialchars($lateReason, ENT_QUOTES, 'UTF-8');
                        $lateReasonStyle = ($lateReason !== '-') ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
                    ?>
                    <tr style="background-color:#f9f9f9;">
                        <td style="padding:8px; border:1px solid #ddd;"><?php echo e($user->name); ?></td>
                        <td style="padding:8px; border:1px solid #ddd; text-transform:capitalize;"><?php echo e($movementType); ?></td>
                        <td style="padding:8px; border:1px solid #ddd;"><?php echo e($mode); ?></td>
                        <td style="padding:8px; border:1px solid #ddd;"><?php echo e($place); ?></td>
                        <td style="padding:8px; border:1px solid #ddd; color:#27ae60; font-weight:bold;"><?php echo e($timeIST); ?></td>
                        <td style="padding:8px; border:1px solid #ddd; <?php echo e($lateReasonStyle); ?>"><?php echo e($lateReasonHtml); ?></td>
                    </tr>
                <?php else: ?>
                    <?php if($isOnLeave): ?>
                        <tr style="background-color:#fff9e6;">
                            <td style="padding:8px; border:1px solid #ddd;"><?php echo e($user->name); ?></td>
                            <td style="padding:8px; border:1px solid #ddd; color:#e67e22; font-weight:bold;">Leave</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd; color:#95a5a6;">-</td>
                        </tr>
                    <?php else: ?>
                        <tr style="background-color:#ffe6e6;">
                            <td style="padding:8px; border:1px solid #ddd;"><?php echo e($user->name); ?></td>
                            <td style="padding:8px; border:1px solid #ddd; color:#e74c3c; font-weight:bold;">Absent</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd;">-</td>
                            <td style="padding:8px; border:1px solid #ddd; color:#95a5a6;">-</td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="padding:1px; text-align:center; border:1px solid #ddd;">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

    <br>
    <p style="font-family: Arial, sans-serif; font-size:12px; color:#555;">
        Regards,<br>
        <b>HR Team</b>
    </p>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/morning_attendance.blade.php ENDPATH**/ ?>