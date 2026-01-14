<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// ================== CONFIG ==================
$servername = "localhost";
$username   = "u976774818_triserv360";
$password   = "g/OdVW0e8R";
$dbname     = "u976774818_triserv360";

// ================== DB CONNECT ==================
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit('DB connect failed');
}

$conn->set_charset('utf8mb4');

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ================== FETCH RECIPIENTS ==================
$recipientEmails = [
    'sandeep@triserv360.com',
];

if (empty($recipientEmails)) {
    echo "⚠️ No recipients entered.<br>";
    $conn->close();
    exit;
}

echo "📧 Preparing report for " . count($recipientEmails) . " recipients...<br>";

// ================== FETCH ACTIVE SUBSCRIPTIONS & LATEST HISTORY ==================
// Logic: Get Active Subscriptions + Their LATEST History record.
$sql = "
    SELECT 
        s.id,
        s.subscription_name,
        s.amount,
        s.recurrence_type,
        s.billing_type,
        s.created_at as sub_created_at,
        c.name AS customer_name, 
        c.company_name, 
        c.phone,
        c.email,
        p.product_name,
        h.period_start,
        h.period_end,
        h.due_date,
        h.status AS history_status,
        h.id AS history_id,
        h.amount AS history_amount,
        h.created_at AS history_created_at,
        h.updated_at AS history_updated_at
    FROM subscriptions s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN sales_products p ON s.product_id = p.id 
    LEFT JOIN subscription_histories h ON h.id = (
        SELECT id FROM subscription_histories 
        WHERE subscription_id = s.id 
        ORDER BY due_date DESC, id DESC 
        LIMIT 1
    )
    WHERE s.is_active = 1 
    ORDER BY c.name ASC
";

$result = $conn->query($sql);

$groups = [
    'Overdue' => [],
    'Due Soon' => [],
    'Pending' => [],
    'Other' => []
];

$totalReceivable = 0;
$totalActive = 0;

$today = date('Y-m-d');
$next7Days = date('Y-m-d', strtotime('+7 days'));

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalActive++;
        $status = strtolower($row['history_status'] ?? 'pending');
        $dueDate = $row['due_date'];
        
        // Sanitize & Format Data
        $item = [
            'customer' => $row['customer_name'] ?: ($row['company_name'] ?: 'Unknown Customer'),
            'product' => $row['product_name'] ?: ($row['subscription_name'] ?: 'Subscription #' . $row['id']),
            'amount' => $row['amount'],
            'due_date' => $dueDate,
            'status' => ucwords($status),
            'recurrence' => ucfirst($row['recurrence_type'] ?? 'One Time'),
            'billing' => ucfirst($row['billing_type'] ?? '-'),
            'contact' => $row['phone'] ?: $row['email'] ?: '-'
        ];

        // Classification Logic
        $isPaid = in_array($status, ['payment received', 'paid', 'completed']);
        
        if ($isPaid) {
            $groups['Other'][] = $item;
        } else {
            // Unpaid calculation
            $totalReceivable += $row['amount'];
            
            if ($dueDate && $dueDate < $today) {
                $daysOver = (strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24);
                $item['notes'] =  floor($daysOver) . " days overdue";
                $groups['Overdue'][] = $item;
            } elseif ($dueDate && $dueDate <= $next7Days) {
                 $daysLeft = (strtotime($dueDate) - strtotime($today)) / (60 * 60 * 24);
                 $item['notes'] = "Due in " . ceil($daysLeft) . " days";
                 $groups['Due Soon'][] = $item;
            } else {
                $item['notes'] = "Pending";
                $groups['Pending'][] = $item;
            }
        }
    }
}

// Custom Sort for Overdue: Pending -> Invoice -> Other
if (!empty($groups['Overdue'])) {
    usort($groups['Overdue'], function($a, $b) {
        $p = function($s) {
            if (stripos($s, 'pending') !== false) return 1;
            if (stripos($s, 'invoice') !== false || stripos($s, 'pi') !== false) return 2;
            return 3;
        };
        $pA = $p($a['status']);
        $pB = $p($b['status']);
        return $pA <=> $pB;
    });
}

// ================== HELPER: RENDER COMPACT TABLE ==================
function renderCompactTable($subscriptions, $title = '') {
    if (empty($subscriptions)) {
        return "";
    }

    $rows = "";
    foreach ($subscriptions as $idx => $item) {
        $bg = ($idx % 2 == 0) ? '#ffffff' : '#f8f9fa';
        $amount = number_format($item['amount'], 2);
        
        // Handle dates
        $dueDateRaw = $item['due_date'];
        $dueDateDisplay = $dueDateRaw ? date('d M Y', strtotime($dueDateRaw)) : '-';
        
        $dueDateStyle = "color:#4b5563;";
        if (isset($item['notes']) && strpos($item['notes'], 'overdue') !== false) {
             $dueDateStyle = "color:#dc2626;font-weight:700;";
        }
        
        $customerName = h($item['customer']);
        $productName = h($item['product']);
        $Recur = h($item['recurrence']);
        $Billing = h($item['billing']);
        $status = h($item['status']);
        $sn = $idx + 1;
        
        $statusColor = '#374151';
        $statusBg = 'transparent';
        if (stripos($status, 'paid') !== false || stripos($status, 'received') !== false) {
             $statusColor = '#059669'; $statusBg = '#ecfdf5';
        } elseif (stripos($status, 'pending') !== false || stripos($status, 'due') !== false) {
             $statusColor = '#d97706'; $statusBg = '#fffbeb';
        } elseif (stripos($status, 'overdue') !== false) {
             $statusColor = '#dc2626'; $statusBg = '#fef2f2';
        }

        $rows .= "
        <tr style='background:{$bg};border-bottom:1px solid #e5e7eb;'>
            <td style='padding:6px 8px;color:#6b7280;font-size:10px;text-align:center;'>{$sn}</td>
            <td style='padding:6px 8px;color:#111827;font-weight:600;'>{$customerName}</td>
            <td style='padding:6px 8px;color:#374151;'>{$productName}</td>
            <td style='padding:6px 8px;color:#374151;'>{$Recur}</td>
            <td style='padding:6px 8px;color:#374151;'>{$Billing}</td>
            <td style='padding:6px 8px;text-align:right;color:#111827;font-weight:600;'>₹{$amount}</td>
            <td style='padding:6px 8px;text-align:center;{$dueDateStyle}'>{$dueDateDisplay}</td>
            <td style='padding:6px 8px;text-align:center;'><span style='color:{$statusColor};background:{$statusBg};padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;'>{$status}</span></td>
        </tr>";
    }

    $header = $title ? "<div style='background:#f3f4f6;padding:8px 10px;font-weight:700;color:#111827;font-size:12px;text-transform:uppercase;border:1px solid #e5e7eb;border-bottom:none;margin-top:15px;'>{$title}</div>" : "";

    return "
    {$header}
    <table width='100%' cellspacing='0' cellpadding='0' style='font-size:11px;border-collapse:collapse;border:1px solid #e5e7eb;margin-bottom:0;'>
        <thead>
            <tr style='background:#ffffff;border-bottom:2px solid #e5e7eb;'>
                <th style='padding:8px;text-align:center;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;width:30px;'>#</th>
                <th style='padding:8px;text-align:left;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Customer</th>
                <th style='padding:8px;text-align:left;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Product</th>
                <th style='padding:8px;text-align:left;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Type</th>
                <th style='padding:8px;text-align:left;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Billing</th>
                <th style='padding:8px;text-align:right;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Amount</th>
                <th style='padding:8px;text-align:center;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Next Due</th>
                <th style='padding:8px;text-align:center;color:#4b5563;font-weight:700;text-transform:uppercase;font-size:10px;'>Status</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>";
}

$tablesHtml = "";
if (empty($groups['Overdue']) && empty($groups['Due Soon']) && empty($groups['Pending']) && empty($groups['Other'])) {
    $tablesHtml = "<div style='padding:20px;text-align:center;color:#666;'>No active subscriptions found.</div>";
} else {
    // Render Sections
    if (!empty($groups['Overdue'])) {
        $tablesHtml .= renderCompactTable($groups['Overdue'], "⚠️ Overdue Subscriptions");
    }
    if (!empty($groups['Due Soon'])) {
        $tablesHtml .= renderCompactTable($groups['Due Soon'], "⏰ Due Soon (Next 7 Days)");
    }
    if (!empty($groups['Pending'])) {
        $tablesHtml .= renderCompactTable($groups['Pending'], "⏳ Pending");
    }
    if (!empty($groups['Other'])) {
        $tablesHtml .= renderCompactTable($groups['Other'], "✅ Paid / Up to Date");
    }
}

$dateDisplay = date('d M Y');
$receivableDisplay = number_format($totalReceivable, 2);

$bodyHtml = "
<!DOCTYPE html>
<html>
<head>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; margin: 0; padding: 0; }
  .wrapper { width: 100%; table-layout: fixed; background-color: #ffffff; padding: 10px; }
  .main-table { background-color: #ffffff; margin: 0 auto; width: 100%; }
</style>
</head>
<body>
<div class='wrapper'>
  <table align='center' class='main-table' cellpadding='0' cellspacing='0'>
    <tr>
      <td style='padding-bottom: 10px; border-bottom: 2px solid #434AFA; margin-bottom: 10px;'>
         <table width='100%'>
             <tr>
                 <td style='color: #434AFA; font-size: 16px; font-weight: 700;'>Subscription Report</td>
                 <td style='text-align:right; color: #6b7280; font-size: 11px;'>{$dateDisplay} &bull; Active: <strong>{$totalActive}</strong> &bull; Pending: <strong>₹{$receivableDisplay}</strong></td>
             </tr>
         </table>
      </td>
    </tr>
    <tr>
      <td style='padding-top: 10px;'>
        {$tablesHtml}
         <div style='text-align:center; color:#9ca3af; font-size:10px; margin-top:15px; border-top:1px solid #f3f4f6; padding-top:5px;'>
            Workorio Auto-Report
         </div>
      </td>
    </tr>
  </table>
</div>
</body>
</html>
";

$plainText = "Subscription Report - $dateDisplay\n\n";
$plainText .= "Total Pending Receivable: ₹$receivableDisplay\n";
$plainText .= "Total Active: $totalActive\n\n";
foreach ($groups as $name => $items) {
    if (!empty($items)) {
        $plainText .= "[$name]: " . count($items) . " subscriptions\n";
    }
}
$plainText .= "\nPlease verify details on the dashboard.";

// ================== SEND EMAIL ==================
foreach ($recipientEmails as $to) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'triserv360businesssolutions@gmail.com';
        $mail->Password   = 'mqyjzlbdxekoupqy';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('triserv360businesssolutions@gmail.com', 'Workorio Alerts');
        $mail->addAddress($to);
        $mail->isHTML(true);
        
        // Dynamic subject line
        $subjectPrefix = count($groups['Overdue']) > 0 ? " Alert: " . count($groups['Overdue']) . " Overdue" : "✅ Daily Report";
        $mail->Subject = "$subjectPrefix | Subscription Summary - $dateDisplay";
        
        $mail->Body    = $bodyHtml;
        $mail->AltBody = $plainText;

        $mail->send();
        echo "✅ Sent to $to<br>";
    } catch (Exception $e) {
        echo "❌ Failed to send to $to: {$mail->ErrorInfo}<br>";
    }
}

$conn->close();
echo "<br><strong>Report Generation Complete.</strong>";
