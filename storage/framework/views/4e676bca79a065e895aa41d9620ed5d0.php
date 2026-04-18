<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 0px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6;">

<div style="width: 100%; max-width: 800px; margin: 0 auto; background-color: #ffffff; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #4f46e5; padding:20px; color:white;">
        <table width="100%">
            <tr>
                <td style="font-size:18px;font-weight:bold;">New Lead Added</td>
                <td style="text-align:right;font-size:12px;opacity:0.9;"><?php echo e(\Carbon\Carbon::now()->format('d M Y')); ?></td>
            </tr>
        </table>
    </div>
    
    <div style="padding:15px 15px 0 15px; font-size:12px; color:#374151;">
        A new lead has been successfully entered into the system. Below are the details:
    </div>

    <div style="padding:15px;">
        <div style="border:1px solid #e5e7eb; overflow:hidden;">
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;width:35%;">Lead Name</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($lead->leads_name ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#ffffff;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Contact Person</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($lead->contact_person ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Contact Number</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($lead->contact_number ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#ffffff;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Email</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($lead->email ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Next Follow-up</td>
                    <td style="padding:6px 8px;font-size:11px;color:#dc2626;font-weight:700;"><?php echo e($lead->next_follow_up_date ? \Carbon\Carbon::parse($lead->next_follow_up_date)->format('d M Y') : 'N/A'); ?></td>
                </tr>
                <tr style="background:#ffffff;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Address</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;white-space:normal;"><?php echo e($lead->address ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Website</td>
                    <td style="padding:6px 8px;font-size:11px;color:#2563eb;"><?php echo e($lead->website_link ?? 'N/A'); ?></td>
                </tr>
                <tr style="background:#ffffff;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Created By</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($creator ? $creator->name : 'Unknown User'); ?></td>
                </tr>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Assigned To</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;"><?php echo e($assignedTo ? $assignedTo->name : 'Unassigned'); ?></td>
                </tr>
                <tr style="background:#ffffff;">
                    <td style="padding:6px 8px;font-size:11px;color:#6b7280;font-weight:700;">Initial Remark</td>
                    <td style="padding:6px 8px;font-size:11px;color:#111827;white-space:normal;"><?php echo e($remarkText ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div style="background:#f9fafb;padding:15px;text-align:center;font-size:10px;color:#6b7280;border-top:1px solid #e5e7eb;">
        Workorio Automated Report &bull; Lead Management Notification
    </div>
</div>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/new_lead_notification.blade.php ENDPATH**/ ?>