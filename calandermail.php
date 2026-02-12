<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// ================== CONFIG ==================
$servername = "localhost";
$username   = "u976774818_triserv360";
$password   = "g/OdVW0e8R";
$dbname     = "u976774818_triserv360";

// Optional filter (?date=YYYY-MM-DD). Default: today
$targetDate = isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])
    ? $_GET['date']
    : date('Y-m-d');

// Get current month for monthly summary
$currentMonth = date('Y-m', strtotime($targetDate));
$monthStart = $currentMonth . '-01';
$monthEnd = date('Y-m-t', strtotime($monthStart));

// Date range: from 1st of current month to today + 2 days
$startDate = $monthStart;
$endDate = date('Y-m-d', strtotime($targetDate . ' +2 days'));

// ================== DB CONNECT ==================
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit('DB connect failed');
}

$conn->set_charset('utf8mb4');

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ================== HELPER FUNCTION: GET STATUS COLOR ==================
function getStatusColor($statusName) {
    if (empty($statusName)) {
        return '#ffc107'; // Default: yellow for pending
    }
    
    $statusLower = strtolower(trim($statusName));
    
    // Color mapping based on common status names
    $colorMap = [
        'pending' => '#ffc107',      // Yellow/Warning
        'created' => '#28a745',      // Green/Success
        'completed' => '#28a745',    // Green/Success
        'done' => '#28a745',         // Green/Success
        'missed' => '#dc3545',       // Red/Danger
        'cancelled' => '#dc3545',    // Red/Danger
        'canceled' => '#dc3545',     // Red/Danger
        'in progress' => '#17a2b8',  // Cyan/Info
        'in-progress' => '#17a2b8',  // Cyan/Info
        'on hold' => '#6c757d',      // Gray
        'on-hold' => '#6c757d',      // Gray
    ];
    
    return isset($colorMap[$statusLower]) ? $colorMap[$statusLower] : '#6c757d'; // Default gray
}


// ================== FETCH RECIPIENTS FROM DATABASE ==================
$recipientEmails = [];

// Check which column exists (is_calander or is_calendar)
$checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'is_calander'");
$hasIsCalander = ($checkColumn && $checkColumn->num_rows > 0);

if ($hasIsCalander) {
    $sqlUsers = "SELECT email, name FROM users WHERE is_calander = 1 AND email IS NOT NULL AND email != ''";
} else {
    // Try is_calendar as fallback
    $checkColumn2 = $conn->query("SHOW COLUMNS FROM users LIKE 'is_calendar'");
    $hasIsCalendar = ($checkColumn2 && $checkColumn2->num_rows > 0);
    
    if ($hasIsCalendar) {
        $sqlUsers = "SELECT email, name FROM users WHERE is_calendar = 1 AND email IS NOT NULL AND email != ''";
    } else {
        echo "⚠️ Column 'is_calander' or 'is_calendar' not found in users table<br>";
        $conn->close();
        exit;
    }
}

$resUsers = $conn->query($sqlUsers);

if ($resUsers && $resUsers->num_rows > 0) {
    while ($row = $resUsers->fetch_assoc()) {
        $email = trim($row['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $recipientEmails[] = $email;
        }
    }
}

// If no recipients found, show message
if (empty($recipientEmails)) {
    echo "⚠️ No recipients found with is_calander = 1 or is_calendar = 1<br>";
    $conn->close();
    exit;
}

echo "📧 Sending to " . count($recipientEmails) . " recipients<br>";
echo "📅 Date range: " . h($startDate) . " to " . h($endDate) . " (month start to today + 2 days)<br>";

// ================== QUERY FOR EVENTS (WITH MISSED REASONS) ==================
$sqlTomorrow = "
SELECT 
  e.name AS item_name,
  c.name AS client_name,
  e.event_date AS event_date,
  dcs.status_id AS status_id,
  cs.name AS status_name,
  dcs.missed_reason_id AS missed_reason_id,
  mr.name AS missed_reason_name,
  0 AS alert_before_days
FROM calendar_events e
JOIN calendar_event_client ec ON ec.event_id = e.id
JOIN calendar_clients c ON c.id = ec.client_id
LEFT JOIN calendar_date_client_statuses dcs
  ON dcs.event_date = e.event_date AND dcs.client_id = ec.client_id
LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
WHERE e.event_date BETWEEN ? AND ?
  AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

UNION ALL

SELECT
  ce.name AS item_name,
  c.name AS client_name,
  ccce.event_date AS event_date,
  dcs.status_id AS status_id,
  cs.name AS status_name,
  dcs.missed_reason_id AS missed_reason_id,
  mr.name AS missed_reason_name,
  ce.alert_before_days AS alert_before_days
FROM calendar_client_common_events ccce
JOIN common_events ce ON ce.id = ccce.common_event_id
JOIN calendar_clients c ON c.id = ccce.client_id
LEFT JOIN calendar_date_client_statuses dcs
  ON dcs.event_date = ccce.event_date AND dcs.client_id = ccce.client_id
LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
WHERE (ccce.event_date BETWEEN ? AND ? 
       OR (ccce.event_date > ? AND ? >= DATE_SUB(ccce.event_date, INTERVAL ce.alert_before_days DAY)))
  AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

ORDER BY event_date DESC, client_name, item_name
";

$stTomorrow = $conn->prepare($sqlTomorrow);
$stTomorrow->bind_param('ssssss', $startDate, $endDate, $startDate, $endDate, $endDate, $targetDate);
$stTomorrow->execute();
$resTomorrow = $stTomorrow->get_result();

// ================== BUILD TOMORROW'S TABLE ROWS ==================
$rowsHtml = "";
$totalTomorrow = 0;
if ($resTomorrow && $resTomorrow->num_rows > 0) {
    $pendingRows = [];
    $i = 1;
    while ($r = $resTomorrow->fetch_assoc()) {
        $totalTomorrow++;
        $rowBg = ($i % 2 == 0) ? "#fafafa" : "#ffffff";

        if (is_null($r['status_id']) || empty($r['status_name'])) {
            // Pending status
            $evtDate = $r['event_date'];
            if ($evtDate == $targetDate) {
                $statusText = "Due Today";
                $statusColor = getStatusColor('pending');
            } elseif ($evtDate > $targetDate) {
                $statusText = "Upcoming" . ($r['alert_before_days'] > 0 ? " (Alert)" : "");
                $statusColor = '#cff4fc'; // Light blue
            } else {
                $statusText = "Pending";
                $statusColor = getStatusColor('pending');
            }
            
            $statusBadge = "<span style='background:{$statusColor};color:#212529;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:500;'>$statusText</span>";
        } else {
            // Has status - check if missed and show reason
            $statusName = $r['status_name'];
            $statusColor = getStatusColor($statusName);
            $statusText = h($statusName);
            
            // Check if status is "missed" and has a reason
            $isMissed = (strtolower(trim($statusName)) === 'missed');
            $missedReason = !empty($r['missed_reason_name']) ? h($r['missed_reason_name']) : '';
            
            if ($isMissed && $missedReason) {
                $statusText .= "<br><span style='font-size:10px;opacity:0.85;font-weight:400;'>({$missedReason})</span>";
            }
            
            // Determine text color based on background
            $textColor = in_array($statusColor, ['#ffc107', '#17a2b8']) ? '#212529' : '#fff';
            
            $statusBadge = "<span style='background:{$statusColor};color:{$textColor};padding:3px 8px;border-radius:12px;font-size:11px;font-weight:500;display:inline-block;'>$statusText</span>";
        }

        $pendingRows[] = "
            <tr style='background:{$rowBg};'>
                <td style='padding:10px 12px;border-bottom:1px solid #e5e5e5;font-weight:600;color:#0f172a;font-size:13px;'>" . h($r['item_name']) . "</td>
                <td style='padding:10px 12px;border-bottom:1px solid #e5e5e5;color:#334155;font-size:13px;'>" . h($r['client_name']) . "</td>
                <td style='padding:10px 12px;text-align:center;border-bottom:1px solid #e5e5e5;color:#64748b;font-size:12px;'>" . h($r['event_date']) . "</td>
                <td style='padding:10px 12px;text-align:center;border-bottom:1px solid #e5e5e5;'>" . $statusBadge . "</td>
            </tr>";
        $i++;
    }

    $half = (int)ceil(count($pendingRows) / 2);
    $firstHalf = array_slice($pendingRows, 0, $half);
    $secondHalf = array_slice($pendingRows, $half);

    $renderTable = function(array $rows) {
        if (empty($rows)) {
            return "<div style='flex:1;margin:0 12px;min-width:280px;'></div>";
        }

        $tableHead = "<thead>
                <tr style='background:#f8fafc;border-bottom:2px solid #e2e8f0;'>
                  <th style='padding:12px;text-align:left;font-weight:700;color:#334155;font-size:12px;'>Event Name</th>
                  <th style='padding:12px;text-align:left;font-weight:700;color:#334155;font-size:12px;'>Client</th>
                  <th style='padding:12px;text-align:center;font-weight:700;color:#334155;font-size:12px;width:120px;'>Date</th>
                  <th style='padding:12px;text-align:center;font-weight:700;color:#334155;font-size:12px;width:140px;'>Status</th>
                </tr>
              </thead>";

        return "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:13px;border:1px solid #eef2f7;border-radius:10px;overflow:hidden;'>
              " . $tableHead . "
              <tbody>" . implode('', $rows) . "</tbody>
            </table>";
    };

    $rowsHtml = "<table width='100%' cellpadding='0' cellspacing='12' style='table-layout:fixed;'>
        <tr>
            <td valign='top' style='width:50%;padding-right:6px;'>" . $renderTable($firstHalf) . "</td>
            <td valign='top' style='width:50%;padding-left:6px;'>" . $renderTable($secondHalf) . "</td>
        </tr>
    </table>";
} else {
    $rowsHtml = "<div style='padding:20px;text-align:center;color:#6c757d;font-size:13px;background:#f8fafc;border-radius:8px;'>No pending items from " . h($startDate) . " up to " . h($endDate) . ".</div>";
}

// ================== QUERY FOR MONTHLY SUMMARY (WITH MISSED REASONS) ==================
$sqlMonthly = "
SELECT 
  c.name AS client_name,
  e.name AS item_name,
  e.event_date AS event_date,
  dcs.status_id AS status_id,
  cs.name AS status_name,
  dcs.missed_reason_id AS missed_reason_id,
  mr.name AS missed_reason_name,
  'Event' AS source_type,
  0 AS alert_before_days
FROM calendar_events e
JOIN calendar_event_client ec ON ec.event_id = e.id
JOIN calendar_clients c ON c.id = ec.client_id
LEFT JOIN calendar_date_client_statuses dcs
  ON dcs.event_date = e.event_date AND dcs.client_id = ec.client_id
LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
WHERE e.event_date >= ? AND e.event_date <= ?
  AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

UNION ALL

SELECT
  c.name AS client_name,
  ce.name AS item_name,
  ccce.event_date AS event_date,
  dcs.status_id AS status_id,
  cs.name AS status_name,
  dcs.missed_reason_id AS missed_reason_id,
  mr.name AS missed_reason_name,
  'Common Event' AS source_type,
  ce.alert_before_days AS alert_before_days
FROM calendar_client_common_events ccce
JOIN common_events ce ON ce.id = ccce.common_event_id
JOIN calendar_clients c ON c.id = ccce.client_id
LEFT JOIN calendar_date_client_statuses dcs
  ON dcs.event_date = ccce.event_date AND dcs.client_id = ccce.client_id
LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
WHERE ccce.event_date >= ? AND ccce.event_date <= ?
  AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

ORDER BY client_name, event_date, item_name
";

$stMonthly = $conn->prepare($sqlMonthly);
$stMonthly->bind_param('ssss', $monthStart, $monthEnd, $monthStart, $monthEnd);
$stMonthly->execute();
$resMonthly = $stMonthly->get_result();

// ================== BUILD MONTHLY SUMMARY (CLIENT-WISE) ==================
$monthlySummaryHtml = "";
$monthlyByClient = [];

if ($resMonthly && $resMonthly->num_rows > 0) {
    while ($r = $resMonthly->fetch_assoc()) {
        $clientName = $r['client_name'];
        if (!isset($monthlyByClient[$clientName])) {
            $monthlyByClient[$clientName] = [];
        }
        $monthlyByClient[$clientName][] = $r;
    }
}

if (!empty($monthlyByClient)) {
    // Build date headers for the month
    $dates = [];
    $periodStart = new DateTime($monthStart);
    $periodEnd = (new DateTime($monthEnd))->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($periodStart, $interval, $periodEnd);
    foreach ($period as $dt) {
        $dates[] = $dt->format('Y-m-d');
    }

    $monthlySummaryHtml .= "<div style='overflow-x:auto;'>
        <table style='width:100%;border-collapse:collapse;font-size:11px;border:1px solid #dde6f6;'>
            <thead>
                <tr style='background:#f1f5ff;border-bottom:1px solid #d6e4ff;'>
                    <th style='padding:10px 12px;text-align:left;font-weight:700;color:#334155;min-width:140px;'>Client</th>";
    foreach ($dates as $date) {
        $dayNum = date('d', strtotime($date));
        $monthlySummaryHtml .= "<th style='padding:8px 6px;text-align:center;font-weight:600;color:#64748b;border-left:1px solid #d6e4ff;min-width:40px;'>$dayNum</th>";
    }
    $monthlySummaryHtml .= "</tr>
            </thead>
            <tbody>";

    foreach ($monthlyByClient as $clientName => $events) {
        // Map events by date for quick lookup
        $eventsByDate = [];
        foreach ($events as $event) {
            $eventsByDate[$event['event_date']][] = $event;
        }

        $monthlySummaryHtml .= "<tr style='background:#ffffff;border-bottom:1px solid #e6efff;'>
            <td style='padding:8px 10px;font-weight:600;color:#1f2a44;border-right:1px solid #d6e4ff;'>" . h($clientName) . "</td>";

        foreach ($dates as $date) {
            if (isset($eventsByDate[$date])) {
                $cellContent = "";
                foreach ($eventsByDate[$date] as $event) {
                    $isPending = is_null($event['status_id']) || empty($event['status_name']);
                    
                    if ($isPending) {
                        $badgeColor = getStatusColor('pending');
                        $textColor = '#212529';
                        
                        $evtDate = $event['event_date'];
                        if ($evtDate == $targetDate) {
                            $statusText = "Due Today";
                            $badgeColor = getStatusColor('pending');
                        } elseif ($evtDate > $targetDate) {
                            $statusText = "Upcoming" . ($event['alert_before_days'] > 0 ? " (Alert)" : "");
                            $badgeColor = '#0dcaf0'; // Cyan for light blue tint
                        } else {
                            $statusText = "Pending";
                            $badgeColor = getStatusColor('pending');
                        }
                    } else {
                        $statusName = $event['status_name'];
                        $badgeColor = getStatusColor($statusName);
                        $textColor = in_array($badgeColor, ['#ffc107', '#17a2b8']) ? '#212529' : '#fff';
                        $statusText = h($statusName);
                        
                        // Check if missed and has reason
                        $isMissed = (strtolower(trim($statusName)) === 'missed');
                        $missedReason = !empty($event['missed_reason_name']) ? h($event['missed_reason_name']) : '';
                        
                        if ($isMissed && $missedReason) {
                            $statusText .= "<br><span style='font-size:9px;opacity:0.85;'>({$missedReason})</span>";
                        }
                    }
                    
                    $cellContent .= "<div style='margin:2px 0;padding:4px 6px;border-radius:8px;font-size:10px;background:" . $badgeColor . "1a;color:" . $badgeColor . ";font-weight:500;'>" . $statusText . "</div>";
                }
                $monthlySummaryHtml .= "<td style='padding:6px;border-left:1px solid #dbe4f7;vertical-align:top;background:#fbfdff;'>$cellContent</td>";
            } else {
                $monthlySummaryHtml .= "<td style='padding:6px;text-align:center;color:#c1ccde;font-size:10px;border-left:1px solid #dbe4f7;'>—</td>";
            }
        }
        $monthlySummaryHtml .= "</tr>";
    }

    $monthlySummaryHtml .= "</tbody></table></div>";
} else {
    $monthlySummaryHtml = "<div style='padding:20px;text-align:center;color:#6c757d;font-size:13px;background:#f8fafc;border-radius:8px;'>No events found for " . h($currentMonth) . ".</div>";
}

// ================== BEAUTIFUL EMAIL HTML ==================
$html = "
<table width='100%' cellspacing='0' cellpadding='0' style='background:#f6f9fc;padding:24px 0;font-family:Arial,Helvetica,sans-serif;'>
  <tr>
    <td align='center'>
      <table width='94%' cellpadding='0' cellspacing='0' style='max-width:900px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.08);'>
        
        <!-- Header -->
        <tr>
          <td style='background:linear-gradient(135deg,#0d6efd,#1e90ff);color:blue;padding:22px 24px;'>
            <div style='font-size:12px;opacity:.9;'>Pending Events — " . h($startDate) . " to " . h($endDate) . "</div>
          </td>
        </tr>

        <!-- Summary Badge -->
        <tr>
          <td style='padding:16px 24px 8px 24px;background:#ffffff;'>
            <div style='display:inline-block;background:#e7f1ff;color:#0d6efd;border:1px solid #cfe2ff;border-radius:10px;padding:6px 12px;font-size:12px;font-weight:600;'>
              Total: " . h($totalTomorrow) . " pending items up to " . h($endDate) . "
            </div>
          </td>
        </tr>

        <!-- Tomorrow's Table -->
        <tr>
          <td style='padding:8px 24px 20px 24px;background:#ffffff;'>
            {$rowsHtml}
          </td>
        </tr>

        <!-- Monthly Summary Section -->
        <tr>
          <td style='padding:20px 24px;background:#f8fafc;border-top:2px solid #e2e8f0;'>
            <div style='font-size:18px;font-weight:700;color:#334155;margin-bottom:16px;'>
              📅 Monthly Summary - " . h($currentMonth) . "
            </div>
            {$monthlySummaryHtml}
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style='padding:14px 24px;background:#ffffff;color:#64748b;font-size:12px;border-top:1px solid #eef2f7;text-align:center;'>
            © " . date('Y') . " Workorio • Automated notification
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
";

// ================== Plain Text Fallback ==================
$plain = "Pending Events (status != 3) — $startDate to $endDate\n";
$plain .= "Total: $totalTomorrow pending items\n\n";

if ($resTomorrow && $resTomorrow->num_rows > 0) {
    $stTomorrow->execute();
    $res2 = $stTomorrow->get_result();
    $i = 1;
    while ($r = $res2->fetch_assoc()) {
        if (is_null($r['status_id']) || empty($r['status_name'])) {
            $evtDate = $r['event_date'];
            if ($evtDate == $targetDate) $statusText = "Due Today";
            elseif ($evtDate > $targetDate) $statusText = "Upcoming" . ($r['alert_before_days'] > 0 ? " (Alert)" : "");
            else $statusText = "Pending";
        } else {
            $statusText = $r['status_name'];
        }
        
        // Add missed reason if applicable
        $isMissed = !empty($r['status_name']) && strtolower(trim($r['status_name'])) === 'missed';
        if ($isMissed && !empty($r['missed_reason_name'])) {
            $statusText .= " ({$r['missed_reason_name']})";
        }
        
        $plain .= sprintf(
            "%d) %s | %s | %s | Status: %s\n",
            $i++,
            $r['item_name'],
            $r['client_name'],
            $r['event_date'],
            $statusText
        );
    }
} else {
    $plain .= "- none -\n";
}

$plain .= "\n\n=== Monthly Summary - $currentMonth ===\n\n";
if (!empty($monthlyByClient)) {
    foreach ($monthlyByClient as $clientName => $events) {
        $plain .= "CLIENT: $clientName (" . count($events) . " events)\n";
        foreach ($events as $event) {
            if (is_null($event['status_id']) || empty($event['status_name'])) {
                $evtDate = $event['event_date'];
                if ($evtDate == $targetDate) $statusText = "Due Today";
                elseif ($evtDate > $targetDate) $statusText = "Upcoming" . ($event['alert_before_days'] > 0 ? " (Alert)" : "");
                else $statusText = "Pending";
            } else {
                $statusText = $event['status_name'];
            }
            
            // Add missed reason if applicable
            $isMissed = !empty($event['status_name']) && strtolower(trim($event['status_name'])) === 'missed';
            if ($isMissed && !empty($event['missed_reason_name'])) {
                $statusText .= " ({$event['missed_reason_name']})";
            }
            
            $plain .= "  - " . $event['item_name'] . " | " . $event['event_date'] . " | Status: $statusText\n";
        }
        $plain .= "\n";
    }
} else {
    $plain .= "No events found for $currentMonth.\n";
}

// ================== SEND EMAIL ==================
foreach ($recipientEmails as $to) {
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) continue;

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
        $mail->Encoding   = 'base64';

        $mail->setFrom('triserv360businesssolutions@gmail.com', 'Workorio Alerts');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = '📋 Pending Events + Monthly Summary - ' . $endDate;
        $mail->Body    = $html;
        $mail->AltBody = $plain;

        $mail->send();
        echo "✅ Sent to $to<br>";
    } catch (Exception $e) {
        echo "❌ Failed to send to $to: {$mail->ErrorInfo}<br>";
    }
}

$stTomorrow->close();
$stMonthly->close();
$conn->close();

echo "<br>✅ Done for range: " . h($startDate) . " → " . h($endDate) . " | Monthly summary for: " . h($currentMonth);
