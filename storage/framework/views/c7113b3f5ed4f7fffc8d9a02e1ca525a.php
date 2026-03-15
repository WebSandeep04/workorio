<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 2px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6;">

<?php
function renderTable($items, $title, $titleColor = '#111827', $isOverdue = false) {
    if (empty($items)) return "";

    $rows = "";
    foreach ($items as $idx => $item) {
        $bg = ($idx % 2 == 0) ? '#ffffff' : '#f9fafb';
        $amount = number_format($item['amount'], 2);
        
        $dateDisplay = $item['due_date'] ? date('d M Y', strtotime($item['due_date'])) : '-';
        
        $dateStyle = "color:#4b5563;";
        if ($isOverdue) {
            $dateStyle = "color:#dc2626;font-weight:700;";
        } elseif (isset($item['notes']) && strpos($item['notes'], 'Due in') !== false) {
             $dateStyle = "color:#d97706;font-weight:700;";
             $dateDisplay .= "<div style='font-size:9px;'>{$item['notes']}</div>";
        }
        
        $statusStyle = "background:#f3f4f6;color:#374151;";
        $s = strtolower($item['status']);

        // Quick Visual Coding
        if (strpos($s, 'paid') !== false || strpos($s, 'received') !== false) {
             $statusStyle = "background:#ecfdf5;color:#059669;";
        } elseif (strpos($s, 'pending') !== false) {
             $statusStyle = "background:#fffbeb;color:#d97706;";
        } elseif (strpos($s, 'invoice') !== false || strpos($s, 'sent') !== false) {
             $statusStyle = "background:#eff6ff;color:#2563eb;";
        } elseif (strpos($s, 'overdue') !== false) {
             $statusStyle = "background:#fef2f2;color:#dc2626;";
        }

        $rows .= "
        <tr style='background:{$bg};border-bottom:1px solid #e5e7eb;'>
            <td style='padding:8px;font-size:11px;color:#111827;'>".htmlspecialchars($item['customer'], ENT_QUOTES, 'UTF-8')."</td>
            <td style='padding:8px;font-size:11px;color:#4b5563;'>".htmlspecialchars($item['product'], ENT_QUOTES, 'UTF-8')."</td>
            <td style='padding:8px;font-size:11px;color:#4b5563;text-align:right;'><b>{$amount}</b></td>
            <td style='padding:8px;font-size:11px;{$dateStyle};text-align:center;'>{$dateDisplay}</td>
            <td style='padding:8px;text-align:center;'><span style='font-size:10px;padding:2px 6px;border-radius:4px;font-weight:600;{$statusStyle}'>".htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8')."</span></td>
        </tr>";
    }

    return "
    <div style='margin-top:20px;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;overflow:hidden;'>
        <div style='background:#f3f4f6;padding:8px 12px;font-weight:700;color:{$titleColor};font-size:13px;border-bottom:1px solid #e5e7eb;'>
            {$title} <span style='font-weight:400;color:#6b7280;margin-left:5px;'>(" . count($items) . ")</span>
        </div>
        <div style='overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;'>
        <table width='100%' cellspacing='0' cellpadding='0' style='border-collapse:collapse; min-width: 600px; white-space: nowrap;'>
            <thead style='background:#ffffff;border-bottom:1px solid #e5e7eb;'>
                <tr>
                    <th style='padding:8px;text-align:left;font-size:10px;color:#6b7280;text-transform:uppercase;'>Customer</th>
                    <th style='padding:8px;text-align:left;font-size:10px;color:#6b7280;text-transform:uppercase;'>Product</th>
                    <th style='padding:8px;text-align:right;font-size:10px;color:#6b7280;text-transform:uppercase;'>Amount</th>
                    <th style='padding:8px;text-align:center;font-size:10px;color:#6b7280;text-transform:uppercase;'>Due Date</th>
                    <th style='padding:8px;text-align:center;font-size:10px;color:#6b7280;text-transform:uppercase;'>Status</th>
                </tr>
            </thead>
            <tbody>{$rows}</tbody>
        </table>
        </div>
    </div>";
}

$tablesHtml = "";

if (!empty($payload['overdueItems'])) {
    $tablesHtml .= renderTable($payload['overdueItems'], "⚠️ Overdue Subscriptions", "#dc2626", true);
}

foreach ($payload['statusGroups'] as $statusName => $items) {
    if (empty($items)) continue;
    $tablesHtml .= renderTable($items, "➤ Status: " . htmlspecialchars($statusName, ENT_QUOTES, 'UTF-8'));
}

if(empty($tablesHtml)) {
    $tablesHtml = "<div style='padding:20px;text-align:center;color:#6b7280;'>No subscriptions to display.</div>";
}
?>

<div style="width: 100%; max-width: 1400px; margin: 0 auto; background-color: #ffffff; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background:linear-gradient(135deg, #4f46e5, #6366f1); padding:20px; color:white;">
        <table width="100%">
            <tr>
                <td style="font-size:18px;font-weight:bold;">Subscription Report</td>
                <td style="text-align:right;font-size:12px;opacity:0.9;"><?php echo e($payload['dateDisplay']); ?></td>
            </tr>
        </table>
    </div>
    
    <div style="padding:5px 2px 20px 2px;">
        <?php echo $tablesHtml; ?>

    </div>
    
    <div style="background:#f9fafb;padding:15px;text-align:center;font-size:10px;color:#6b7280;border-top:1px solid #e5e7eb;">
        Workorio Automated Report &bull; Total Active: <?php echo e($payload['totalActive']); ?> &bull; Pending: ₹<?php echo e(number_format($payload['totalReceivable'], 2)); ?>

    </div>
</div>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/subscription_report.blade.php ENDPATH**/ ?>