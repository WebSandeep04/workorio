<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Follow-up Report - <?php echo e($today); ?></title>
    <style>
        .email-container {
            max-width: 800px;
            margin: 0 auto;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a1a;
            padding: 5px;
        }
        
        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .header p {
            margin: 2px 0 0 0;
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .summary-section {
            margin-bottom: 20px;
        }
        
        .summary-title, .details-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            border-left: 3px solid #000;
            padding: 0 5px;
            margin-bottom: 8px;
        }
        
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        th {
            text-align: left;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 2px 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #444;
        }
        
        td {
            border: 1px solid #eee;
            padding: 1px 4px;
            font-size: 9px;
            color: #333;
        }
        
        .count-badge {
            font-weight: 700;
            font-size: 9px;
        }
        
        .user-section {
            margin-top: 15px;
            border: 1px solid #eee;
            padding: 0;
        }
        
        .user-header {
            background: #f8f9fa;
            padding: 2px 5px;
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            border-bottom: 1px solid #eee;
        }
        
        .date-header {
            background: #fff;
            color: #666;
            padding: 2px 5px;
            margin: 0;
            font-size: 9px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        
        .status-badge {
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .status-completed { color: #1e7e34; }
        .status-rejected { color: #bd2130; }
        .status-pending { color: #856404; }
        .status-default { color: #545b62; }
        
        .footer {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 9px;
            color: #888;
        }
        
        .contact-info {
            margin-top: 5px;
            line-height: 1.4;
        }

        @media (max-width: 600px) {
            .email-container { padding: 2px; }
            td, th { padding: 1px 2px; font-size: 8px; }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h1>Follow-up Report</h1>
            <p>Daily Sales Follow-up Summary - <?php echo e($today); ?></p>
        </div>
        
        <div class='summary-section'>
            <h2 class='summary-title'>Follow-up Summary - <?php echo e($today); ?></h2>
            <div class='table-container'>
                <table class='summary-table'>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th style='text-align: center; width: 120px;'>Pending Follow-ups</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($user); ?></strong></td>
                            <td style='text-align: center;'><span class='count-badge'><?php echo e($count); ?></span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class='details-section'>
            <h2 class='details-title'>New Leads Added Since Yesterday</h2>
            <div class='table-container'>
                <table class='details-table'>
                    <thead>
                        <tr>
                            <th style='width: 20%;'>Lead Name</th>
                            <th style='width: 15%;'>User</th>
                            <th style='width: 15%;'>Phone</th>
                            <th style='width: 15%;'>Location</th>
                            <th style='width: 15%;'>Status</th>
                            <th style='width: 20%;'>Added At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $newLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $nl_date = \Carbon\Carbon::parse($nl->createdat)->format('d M, Y');
                                $nl_user = $nl->user ? $nl->user->name : 'Unknown';
                                $nl_city = $nl->city ? $nl->city->city_name : '';
                                $nl_status = $nl->status ? $nl->status->status_name : 'Unknown';
                            ?>
                            <tr>
                                <td><strong><?php echo e($nl->leads_name); ?></strong></td>
                                <td><?php echo e($nl_user); ?></td>
                                <td><?php echo e($nl->contact_number); ?></td>
                                <td><?php echo e($nl_city); ?></td>
                                <td><span class='status-badge'><?php echo e($nl_status); ?></span></td>
                                <td><?php echo e($nl_date); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan='6' style='text-align:center; padding:10px;'>No new leads added since yesterday.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class='details-section'>
            <h2 class='details-title'>Detailed Follow-up Report</h2>
            
            <?php 
                $groupedRecords = $records->groupBy(function($item) {
                    return $item->user ? $item->user->name : 'Unknown';
                });
            ?>

            <?php $__currentLoopData = $groupedRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName => $userRecords): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class='user-section'>
                    <h3 class='user-header'>USER: <?php echo e($userName); ?></h3>
                    
                    <?php 
                        $dateRecords = $userRecords->groupBy(function($item) {
                            return $item->next_follow_up_date ? $item->next_follow_up_date->format('Y-m-d') : '';
                        });
                    ?>
                    
                    <?php $__currentLoopData = $dateRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $dayRecords): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <h4 class='date-header'>DATE: <?php echo e($date); ?></h4>
                        <div class='table-container'>
                            <table class='details-table'>
                                <thead>
                                    <tr>
                                        <th style='width: 18%;'>Lead Name</th>
                                        <th style='width: 15%;'>Contact Person</th>
                                        <th style='width: 12%;'>Phone</th>
                                        <th style='width: 15%;'>Location</th>
                                        <th style='width: 15%;'>Email</th>
                                        <th style='width: 10%;'>Status</th>
                                        <th style='width: 15%;'>Product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $dayRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $status_name = $rec->status ? $rec->status->status_name : 'Unknown';
                                            $status_class = 'status-default';
                                            if (strtolower($status_name) == 'completed') $status_class = 'status-completed';
                                            if (strtolower($status_name) == 'rejected') $status_class = 'status-rejected';
                                            if (strtolower($status_name) == 'pending') $status_class = 'status-pending';

                                            $city = $rec->city ? $rec->city->city_name : '';
                                            $state = $rec->state ? $rec->state->state_name : '';
                                            $location = trim($city . ', ' . $state, ', ');
                                        ?>
                                        <tr>
                                            <td><strong><?php echo e($rec->leads_name); ?></strong></td>
                                            <td><?php echo e($rec->contact_person); ?></td>
                                            <td><?php echo e($rec->contact_number); ?></td>
                                            <td><?php echo e($location); ?></td>
                                            <td><?php echo e($rec->email); ?></td>
                                            <td><span class='status-badge <?php echo e($status_class); ?>'><?php echo e($status_name); ?></span></td>
                                            <td><?php echo e($rec->product ? $rec->product->product_name : ''); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class='footer'>
            <p><strong>Best Regards,</strong><br>HR Team - Triserv360</p>
            <div class='contact-info'>
                <strong>Contact:</strong> sandeep@triserv360.com<br>
                <strong>System:</strong> Workorio Lead Management
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/admin_followup_report.blade.php ENDPATH**/ ?>