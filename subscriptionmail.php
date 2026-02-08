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
   'shamshad@triserv360.com'
];

if (empty($recipientEmails)) {
    echo "⚠️ No recipients entered.<br>";
    $conn->close();
    exit;
}

// ================== DEBUG MODE ==================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "📧 Preparing report for " . count($recipientEmails) . " recipients...<br>";

// ================== FETCH ACTIVE SUBSCRIPTIONS & LATEST HISTORY ==================
echo "🔍 status: Connecting to Database...<br>";

// ... (previous logic) ...

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
    LEFT JOIN subscription_histories h ON s.id = h.subscription_id
    WHERE s.is_active = 1 
    ORDER BY c.name ASC, h.due_date DESC
";

echo "🔍 status: Running SQL Query...<br>";
$result = $conn->query($sql);
if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}
echo "✅ SQL Success. Found " . $result->num_rows . " rows.<br>";

// ================== DATA PROCESSING ==================
$overdueItems = [];
$statusGroups = []; 

$totalActive = 0;
$totalReceivable = 0;
$today = date('Y-m-d');

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalActive++;
        // Validating data 
        
        $statusRaw = $row['history_status'] ?? 'Pending'; // Default to Pending if null
        $statusKey = ucwords(strtolower($statusRaw));     // Normalize for grouping
        $dueDate = $row['due_date'];
        
        if (in_array(strtolower($statusRaw), ['payment received', 'last payment received'])) {
            continue;
        }

        // Item Data
        $item = [
            'customer' => $row['customer_name'] ?: ($row['company_name'] ?: 'Unknown'),
            'product' => $row['product_name'] ?: ($row['subscription_name'] ?: 'Sub #' . $row['id']),
            'amount' => (float)$row['amount'],
            'due_date' => $dueDate,
            'status' => $statusKey, 
            'recurrence' => ucfirst($row['recurrence_type'] ?? 'One Time'),
            'billing' => ucfirst($row['billing_type'] ?? '-'),
        ];

        // Classification Logic
        // 1. Check strict payment status
        $isPaid = in_array(strtolower($statusKey), ['paid', 'payment received', 'completed']);
        
        // 2. Check Overdue (Priority Rule: Unpaid + Past Due)
        $isOverdue = (!$isPaid && $dueDate && $dueDate < $today);

        if ($isOverdue) {
            $daysOver = (strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24);
            $item['notes'] = floor($daysOver) . " days overdue";
            $overdueItems[] = $item;
            $totalReceivable += $item['amount'];
        } else {
            // 3. Group by Database Status
            if (!$isPaid) {
                 $totalReceivable += $item['amount'];
                 // Optional: Add "Due Soon" note for context only, NOT grouping
                 if ($dueDate) {
                     $daysLeft = (strtotime($dueDate) - strtotime($today)) / (60 * 60 * 24);
                     if ($daysLeft >= 0 && $daysLeft <= 7) {
                         $item['notes'] = "Due in " . ceil($daysLeft) . " days";
                     }
                 }
            }
            $statusGroups[$statusKey][] = $item;
        }
    }
}
echo "✅ Processed $totalActive subscriptions.<br>";

// ================== HELPER: RENDER TABLE ==================
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
            <td style='padding:8px;font-size:11px;color:#111827;'>{$item['customer']}</td>
            <td style='padding:8px;font-size:11px;color:#4b5563;'>{$item['product']}</td>
            <td style='padding:8px;font-size:11px;color:#4b5563;text-align:right;'><b>{$amount}</b></td>
            <td style='padding:8px;font-size:11px;{$dateStyle};text-align:center;'>{$dateDisplay}</td>
            <td style='padding:8px;text-align:center;'><span style='font-size:10px;padding:2px 6px;border-radius:4px;font-weight:600;{$statusStyle}'>{$item['status']}</span></td>
        </tr>";
    }

    return "
    <div style='margin-top:20px;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;'>
        <div style='background:#f3f4f6;padding:8px 12px;font-weight:700;color:{$titleColor};font-size:13px;border-bottom:1px solid #e5e7eb;'>
            {$title} <span style='font-weight:400;color:#6b7280;margin-left:5px;'>(" . count($items) . ")</span>
        </div>
        <table width='100%' cellspacing='0' cellpadding='0' style='border-collapse:collapse;'>
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
    </div>";
}

// ================== BUILD REPORT HTML ==================
$tablesHtml = "";

// 1. Overdue Table (Priority)
if (!empty($overdueItems)) {
    $tablesHtml .= renderTable($overdueItems, "⚠️ Overdue Subscriptions", "#dc2626", true);
}

// 2. Status Tables (Sorted by Name)
ksort($statusGroups); // A-Z Sort of Statuses
foreach ($statusGroups as $statusName => $items) {
    if (empty($items)) continue;
    $tablesHtml .= renderTable($items, "➤ Status: " . $statusName);
}

if(empty($tablesHtml)) {
    $tablesHtml = "<div style='padding:20px;text-align:center;color:#6b7280;'>No subscriptions to display.</div>";
}

$dateDisplay = date('d M Y');
$receivableDisplay = number_format($totalReceivable, 2);

$bodyHtml = "
<!DOCTYPE html>
<html>
<head>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
  .wrapper { width: 100%; margin: 0 auto; background-color: #ffffff; padding: 0; border-radius:8px; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  .header { background:#4f46e5; padding:20px; color:white; }
  .content { padding:20px; }
</style>
</head>
<body>
<div class='wrapper'>
    <div class='header'>
        <table width='100%'>
            <tr>
                <td style='font-size:18px;font-weight:bold;'>Subscription Report</td>
                <td style='text-align:right;font-size:12px;opacity:0.9;'>{$dateDisplay}</td>
            </tr>
        </table>
    </div>
    <div class='content'>
        {$tablesHtml}
    </div>
    <div style='background:#f9fafb;padding:15px;text-align:center;font-size:10px;color:#6b7280;border-top:1px solid #e5e7eb;'>
        Workorio Automated Report &bull; Total Active: {$totalActive} &bull; Pending: ₹{$receivableDisplay}
    </div>
</div>
</body>
</html>
";

$plainText = "Subscription Report - $dateDisplay\n\n";
if (!empty($overdueItems)) {
    $plainText .= "⚠️ OVERDUE: " . count($overdueItems) . " items\n";
}
foreach ($statusGroups as $name => $items) {
    $plainText .= "[$name]: " . count($items) . " items\n";
}
$plainText .= "\nTotal Pending: ₹$receivableDisplay\n";
$plainText .= "Please check dashboard for details.";

echo "📝 HTML Report Generated. Sending emails...<br>";

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
        $subjectPrefix = count($overdueItems) > 0 ? " Alert: " . count($overdueItems) . " Overdue" : "✅ Daily Report";
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
