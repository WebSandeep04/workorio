<h3 style='font-family: Arial, sans-serif; color:#ffffff; background:#007bff; padding:10px; border-radius:5px;'>📋 Follow-ups Due for <?php echo e($user->name); ?> (till <?php echo e($today); ?>)</h3>
<table border='0' cellpadding='5' cellspacing='0' style='border-collapse:collapse; font-size:12px; font-family: Arial, sans-serif; width:100%; box-shadow:0 1px 3px rgba(0,0,0,0.1);'>
    <tr style='background:#17a2b8; color:#fff;'>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Lead</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Contact Person</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Contact Number</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">City</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">State</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Email</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Status</th>
        <th style="padding: 5px; border: 1px solid #dee2e6;">Product</th>
    </tr>
    <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $row_color = $index % 2 == 0 ? '#f9f9f9' : '#ffffff';
            
            $status_name = $lead->status ? $lead->status->status_name : 'Unknown';
            $status_color = '#fd7e14'; 
            if (strtolower($status_name) == 'completed') $status_color = '#28a745';
            if (strtolower($status_name) == 'rejected') $status_color = '#dc3545';
        ?>
        <tr style='background:<?php echo e($row_color); ?>;'>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->leads_name); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->contact_person); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->contact_number); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->city ? $lead->city->city_name : ''); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->state ? $lead->state->state_name : ''); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->email); ?></td>
            <td style='color:#fff; background:<?php echo e($status_color); ?>; text-align:center; padding: 5px; border: 1px solid #dee2e6;'><?php echo e($status_name); ?></td>
            <td style="padding: 5px; border: 1px solid #dee2e6;"><?php echo e($lead->product ? $lead->product->product_name : ''); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<br>
<p style='font-family: Arial, sans-serif; font-size:13px; color:#555;'>Regards,<br><b>HR Team</b></p>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/followup_report.blade.php ENDPATH**/ ?>