<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Calling Analytic Report</title>
    <style>
        body { margin: 0; padding: 0; background-color: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; }
        .email-container { width: 100%; margin: 0 auto; color: #000000; padding: 2px; }
        
        .header { border-bottom: 2px solid #000; padding: 4px 0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; color: #000; }
        .header p { margin: 0; font-size: 12px; font-weight: bold; color: #000; }
        
        .summary-grid { width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 20px; }
        .summary-card { padding: 12px 2px; text-align: center; background-color: #ffffff; border: 2px solid #000; color: #000; }
        .card-label { font-size: 12px; font-weight: bold; display: block; margin-bottom: 4px; color: #000; }
        .card-value { font-size: 24px; font-weight: bold; line-height: 1; }
        
        .text-blue    { color: #0000FF; }
        .text-amber   { color: #FF8C00; }
        .text-emerald { color: #008000; }
        .text-rose    { color: #FF0000; }
        .text-violet  { color: #8A2BE2; }
        .text-dark    { color: #000000; }
        
        .section-header { font-size: 15px; font-weight: bold; background: #EEE; color: #000; padding: 6px 10px; margin: 25px 0 10px 0; border: 2px solid #000; border-left: 6px solid #000; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; border: 2px solid #000; }
        th { text-align: left; background-color: #EEE; color: #000; border: 2px solid #000; padding: 10px; font-size: 14px; font-weight: bold; }
        td { border: 2px solid #000; padding: 10px; font-size: 13px; color: #000; vertical-align: top; word-wrap: break-word; }
        
        .user-name-cell { color: #000; background-color: #FFF; font-weight: bold; }
        .count-active { color: #0000FF; text-align: center; font-weight: bold; }
        .count-zero { color: #000; text-align: center; }
        .status-red { color: #FF0000; font-weight: bold; background: transparent !important; border: none !important; }
        
        .remark-text { font-size: 13px; color: #000; line-height: 1.4; }
        .user-title { font-size: 15px; font-weight: bold; color: #000; margin: 20px 0 5px 0; padding: 8px 10px; background-color: #FFF; border: 2px solid #000; border-left: 6px solid #000; display: block; }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h1>Calling Analytic Report</h1>
            <p><?php echo e($tenantName); ?> | <?php echo e($today); ?></p>
        </div>
        
        <table class="summary-grid">
            <tr>
                <td class="summary-card"><span class="card-label">Follow Ups</span><div class="card-value text-blue"><?php echo e(collect($userData)->sum('todayFollowups')); ?></div></td>
                <td class="summary-card"><span class="card-label">In Process</span><div class="card-value text-amber"><?php echo e(collect($userData)->sum('underProcess')); ?></div></td>
                <td class="summary-card"><span class="card-label">Completed</span><div class="card-value text-emerald"><?php echo e(collect($userData)->sum('todayCompleted')); ?></div></td>
                <td class="summary-card"><span class="card-label">Pending</span><div class="card-value text-rose"><?php echo e(collect($userData)->sum('todayPending')); ?></div></td>
                <td class="summary-card"><span class="card-label">New Leads</span><div class="card-value text-violet"><?php echo e(collect($userData)->sum('todayNew')); ?></div></td>
                <td class="summary-card"><span class="card-label">Locked Leads</span><div class="card-value text-dark"><?php echo e(collect($userData)->sum('allLeads')); ?></div></td>
            </tr>
        </table>

        <div class="section-header">User Wise Daily Metrics</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">User Name</th>
                    <th style="text-align: center;">Total Locked</th>
                    <th style="text-align: center;">Follow Ups</th>
                    <th style="text-align: center; color: #FF0000;">Pending Now</th>
                    <th style="text-align: center; color: #008000;">Completed Now</th>
                    <th style="text-align: center;">New Leads</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $userData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="user-name-cell"><?php echo e($user['name']); ?></td>
                    <td style="text-align: center;"><?php echo e($user['allLeads']); ?></td>
                    <td class="count-active"><?php echo e($user['todayFollowups']); ?></td>
                    <td style="text-align: center; color: #FF0000; font-weight: bold;"><?php echo e($user['todayPending']); ?></td>
                    <td style="text-align: center; color: #008000; font-weight: bold;"><?php echo e($user['todayCompleted']); ?></td>
                    <td style="text-align: center;"><?php echo e($user['todayNew']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="section-header">User Wise Remark Matrix</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">User Name</th>
                    <?php $__currentLoopData = $allCallingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th style="text-align: center;"><?php echo e($typeName); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $userStatusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName => $counts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="user-name-cell"><?php echo e($userName); ?></td>
                    <?php $__currentLoopData = $allCallingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $c = $counts[$typeName] ?? 0; ?>
                        <td class="<?php echo e($c > 0 ? 'count-active' : 'count-zero'); ?>"><?php echo e($c); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="section-header">Detailed Lead Activity</div>
        <?php $__currentLoopData = $userLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName => $leads): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="user-title">Team Member: <?php echo e($userName); ?></div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Lead Name</th>
                        <th style="width: 12%;">Contact No</th>
                        <th style="width: 12%;">City Location</th>
                        <th style="width: 10%;">Source Campaign</th>
                        <th style="width: 18%;">Lead Status</th>
                        <th style="width: 23%;">Remark Details</th>
                        <th style="width: 10%; text-align: center;">Next Call</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($lead->lead_name); ?></td>
                        <td><?php echo e($lead->phone); ?></td>
                        <td><?php echo e($lead->city); ?></td>
                        <td><?php echo e($lead->campaign_name); ?></td>
                        <td class="status-red"><?php echo e($lead->status_name); ?></td>
                        <td class="remark-text"><?php echo e($lead->latest_remark); ?></td>
                        <td style="text-align: center;"><?php echo e($lead->next_followup_date ? \Carbon\Carbon::parse($lead->next_followup_date)->format('d-M-Y') : '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/calling_analytic_report.blade.php ENDPATH**/ ?>