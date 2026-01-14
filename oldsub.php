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
// Logic: Get Active Subscriptions + Their LATEST History record to determine actual due date and status.
// We use a LEFT JOIN on a derived table that picks the latest history per subscription.

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
    ORDER BY c.name ASC, s.id ASC, h.due_date ASC
";

$result = $conn->query($sql);

$overdue = [];
$dueSoon = [];
$pending = [];
$upToDate = [];
$totalReceivable = 0;

$today = date('Y-m-d');
$next7Days = date('Y-m-d', strtotime('+7 days'));

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
            $upToDate[] = $item;
        } else {
            // Unpaid calculation
            $totalReceivable += $row['amount'];
            
            if ($dueDate && $dueDate < $today) {
                $daysOver = (strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24);
                $item['notes'] =  floor($daysOver) . " days overdue";
                $overdue[] = $item;
            } elseif ($dueDate && $dueDate <= $next7Days) {
                 $daysLeft = (strtotime($dueDate) - strtotime($today)) / (60 * 60 * 24);
                 $item['notes'] = "Due in " . ceil($daysLeft) . " days";
                 $dueSoon[] = $item;
            } else {
                $item['notes'] = "Pending";
                $pending[] = $item;
            }
        }
    }
}

// ================== HELPER: RENDER COMPACT TABLE ==================
function renderCompactTable($subscriptions) {
    if (empty($subscriptions)) {
        return "<div style='padding:20px;text-align:center;color:#666;'>No active subscriptions found.</div>";
    }

    $rows = "";
    foreach ($subscriptions as $idx => $item) {
        $bg = ($idx % 2 == 0) ? '#ffffff' : '#f8f9fa';
        $amount = number_format($item['amount'], 2);
        
        // Handle dates
        $dueDateRaw = $item['due_date'];
        $dueDateDisplay = $dueDateRaw ? date('d M Y', strtotime($dueDateRaw)) : '-';
        
        // Logic for highlighting overdue
        $dueDateStyle = "color:#4b5563;";
        // Removed red color and icon as requested
        
        $customerName = h($item['customer']);
        $productName = h($item['product']);
        $Recur = h($item['recurrence']);
        $Billing = h($item['billing']);
        $status = h($item['status']);
        $sn = $idx + 1;
        
        // Status color logic (simple inline styles)
        $statusColor = '#374151'; // default gray
        $statusBg = 'transparent';
        if (stripos($status, 'paid') !== false || stripos($status, 'received') !== false || stripos($status, 'completed') !== false) {
             $statusColor = '#059669'; // green
             $statusBg = '#ecfdf5';
        } elseif (stripos($status, 'pending') !== false || stripos($status, 'due') !== false) {
             $statusColor = '#d97706'; // amber
             $statusBg = '#fffbeb';
        } elseif (stripos($status, 'overdue') !== false) {
             $statusColor = '#dc2626'; // red
             $statusBg = '#fef2f2';
        }

        // Compact Row
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

    return "
    <table width='100%' cellspacing='0' cellpadding='0' style='font-size:11px;border-collapse:collapse;border:1px solid #e5e7eb;'>
        <thead>
            <tr style='background:#f3f4f6;border-bottom:2px solid #e5e7eb;'>
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

// Combine all subscription types into one list for the single table (sorted by due date usually)
// The database query already sorts by due date (ASC), so we can just iterate the result list.
// However, we processed them into arrays ($overdue, $dueSoon, etc). Let's merge them or just re-iterate.
// Re-merging in logical order: Overdue -> Due Soon -> Pending -> Paid
$allDisplayItems = array_merge($overdue, $dueSoon, $pending, $upToDate);

$tableHtml = renderCompactTable($allDisplayItems);
$dateDisplay = date('d M Y');
$totalCount = count($allDisplayItems);

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
                 <td style='text-align:right; color: #6b7280; font-size: 11px;'>{$dateDisplay} &bull; Total: <strong>{$totalCount}</strong></td>
             </tr>
         </table>
      </td>
    </tr>
    <tr>
      <td style='padding-top: 10px;'>
        {$tableHtml}
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
$plainText .= "Total Active Subscriptions: $totalActive\n\n";
$plainText .= "Please verify details on the dashboard.";

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
        $subjectPrefix = count($overdue) > 0 ? " Alert: " . count($overdue) . " Overdue" : "✅ Daily Report";
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
