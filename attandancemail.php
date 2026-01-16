 <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// ---------------- CONFIG ----------------
$host   = "localhost";
$user   = "u976774818_triserv360";
$pass   = "g/OdVW0e8R";
$dbname = "u976774818_triserv360";

// Today (server local). If you prefer a fixed zone, set default timezone or compute as needed.
$today = date("Y-m-d");

// ---------------- DB CONNECT ----------------
$conn = @new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// ---------------- HELPERS ----------------
function table_exists(mysqli $conn, string $name): bool {
    $name = $conn->real_escape_string($name);
    $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$name' LIMIT 1";
    $res = $conn->query($sql);
    return $res && $res->num_rows > 0;
}

function column_exists(mysqli $conn, string $table, string $column): bool {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $sql = "SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = '$table'
              AND column_name = '$column'
            LIMIT 1";
    $res = $conn->query($sql);
    return $res && $res->num_rows > 0;
}

// helper to pass bind_param by reference
function ref_values(array $arr) {
    if (strnatcmp(phpversion(),'5.3') >= 0) {
        $refs = [];
        foreach($arr as $k => $v) $refs[$k] = &$arr[$k];
        return $refs;
    }
    return $arr;
}

// Try to detect approved leave for a user on a given date
function user_is_on_leave_today(mysqli $conn, int $userId, string $today): bool {
    $tables = [
        'leaves','leave_requests','user_leaves','employee_leaves','hr_leaves','attendance_leaves'
    ];
    $userCols = ['user_id','employee_id'];
    $fromCols = ['start_date','from_date','leave_from','date_from','date_start'];
    $toCols   = ['end_date','to_date','leave_to','date_to','date_end'];
    $singleDateCols = ['leave_date','date'];
    $statusCols = ['status','approval_status','is_approved','approved','approved_status'];

    $approvedStringValues = ['approved','approve','approved_by_hr','accepted','granted'];
    $approvedNumericValues = ['1', 1, true];

    foreach ($tables as $table) {
        if (!table_exists($conn, $table)) continue;

        $haveUser = null;
        foreach ($userCols as $uc) {
            if (column_exists($conn, $table, $uc)) { $haveUser = $uc; break; }
        }
        if (!$haveUser) continue;

        $haveStatus = null;
        foreach ($statusCols as $sc) {
            if (column_exists($conn, $table, $sc)) { $haveStatus = $sc; break; }
        }

        $haveFrom = null; $haveTo = null;
        foreach ($fromCols as $fc) {
            if (column_exists($conn, $table, $fc)) { $haveFrom = $fc; break; }
        }
        foreach ($toCols as $tc) {
            if (column_exists($conn, $table, $tc)) { $haveTo = $tc; break; }
        }

        if ($haveFrom && $haveTo) {
            $sql = "SELECT 1 FROM `$table` WHERE `$haveUser` = ? AND ? BETWEEN `$haveFrom` AND `$haveTo`";
            $types = "is";
            $params = [$types, $userId, $today];

            if ($haveStatus) {
                $sql .= " AND (LOWER(COALESCE(`$haveStatus`,'')) IN (" . implode(',', array_fill(0, count($approvedStringValues), '?')) . ") OR COALESCE(`$haveStatus`,'') IN (" . implode(',', array_fill(0, count($approvedNumericValues), '?')) . "))";
                $types .= str_repeat("s", count($approvedStringValues)) . str_repeat("s", count($approvedNumericValues));
                $params = array_merge($params, $approvedStringValues, array_map('strval', $approvedNumericValues));
                $params[0] = $types;
            }

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param(...ref_values($params));
                $stmt->execute();
                $stmt->store_result();
                $found = $stmt->num_rows > 0;
                $stmt->close();
                if ($found) return true;
            }
        }

        foreach ($singleDateCols as $dc) {
            if (!column_exists($conn, $table, $dc)) continue;

            $sql = "SELECT 1 FROM `$table` WHERE `$haveUser` = ? AND `$dc` = ?";
            $types = "is";
            $params = [$types, $userId, $today];

            if ($haveStatus) {
                $sql .= " AND (LOWER(COALESCE(`$haveStatus`,'')) IN (" . implode(',', array_fill(0, count($approvedStringValues), '?')) . ") OR COALESCE(`$haveStatus`,'') IN (" . implode(',', array_fill(0, count($approvedNumericValues), '?')) . "))";
                $types .= str_repeat("s", count($approvedStringValues)) . str_repeat("s", count($approvedNumericValues));
                $params = array_merge($params, $approvedStringValues, array_map('strval', $approvedNumericValues));
                $params[0] = $types;
            }

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param(...ref_values($params));
                $stmt->execute();
                $stmt->store_result();
                $found = $stmt->num_rows > 0;
                $stmt->close();
                if ($found) return true;
            }
        }
    }

    return false;
}

// Get earliest movement (UTC → IST) - Updated to include description
function get_first_movement_today(mysqli $conn, int $userId, string $today): ?array {
    $sql = "SELECT m.movement_type, m.time, m.description
            FROM attendance a
            JOIN movements m ON m.attendance_id = a.id
            WHERE a.user_id = ? AND a.date = ?
            ORDER BY m.time ASC
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res && $res->num_rows > 0 ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row ?: null;
}

// Extract late reason from description (removes "Late punch-in: " prefix if present)
function extract_late_reason(?string $description): string {
    if (empty($description)) {
        return '-';
    }
    
    // Remove "Late punch-in: " prefix if it exists
    $prefix = "Late punch-in: ";
    if (stripos($description, $prefix) === 0) {
        return trim(substr($description, strlen($prefix)));
    }
    
    return trim($description);
}

// ---------------- FETCH USERS ----------------
$sqlUsers = "SELECT u.id, u.name, u.email 
             FROM users u
             JOIN employees e ON u.employee_id = e.id
             WHERE e.status = 'active'";
$resUsers = $conn->query($sqlUsers);

// ---------------- BUILD EMAIL TABLE ----------------
$table = "
<h2 style='font-family: Arial, sans-serif; color: #2c3e50; margin:0 0 8px;'>📌 Today's Attendance Report</h2>
<p style='font-family: Arial, sans-serif; font-size:13px; color:#555; margin:0 0 12px;'>
Here is the summary of the first punch-in/field-in for all employees today:
</p>
<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:13px; border:1px solid #ddd;'>
  <thead>
    <tr style='background-color:#34495e; color:#fff;'>
      <th style='padding:10px; border:1px solid #ddd; text-align:left;'>User</th>
      <th style='padding:10px; border:1px solid #ddd; text-align:left;'>Attendance Type</th>
      <th style='padding:10px; border:1px solid #ddd; text-align:left;'>Time (IST)</th>
      <th style='padding:10px; border:1px solid #ddd; text-align:left;'>Late Reason</th>
    </tr>
  </thead>
  <tbody>
";

if ($resUsers && $resUsers->num_rows > 0) {
    while ($userRow = $resUsers->fetch_assoc()) {
        $userId = (int)$userRow['id'];
        $userName = htmlspecialchars($userRow['name'] ?? 'User', ENT_QUOTES, 'UTF-8');

        $firstMovement = get_first_movement_today($conn, $userId, $today);
        if ($firstMovement) {
            $movementType = htmlspecialchars((string)$firstMovement['movement_type'], ENT_QUOTES, 'UTF-8');

            // Convert UTC -> IST
            $dt = new DateTime($firstMovement['time'], new DateTimeZone("UTC"));
            $dt->setTimezone(new DateTimeZone("Asia/Kolkata"));
            $timeIST = $dt->format("h:i A");

            // Extract late reason from description
            $lateReason = extract_late_reason($firstMovement['description'] ?? null);
            $lateReasonHtml = htmlspecialchars($lateReason, ENT_QUOTES, 'UTF-8');
            
            // Style late reason column - show in orange if there's a reason, gray if none
            $lateReasonStyle = ($lateReason !== '-') ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';

            $table .= "
            <tr style='background-color:#f9f9f9;'>
              <td style='padding:8px; border:1px solid #ddd;'>$userName</td>
              <td style='padding:8px; border:1px solid #ddd; text-transform:capitalize;'>$movementType</td>
              <td style='padding:8px; border:1px solid #ddd; color:#27ae60; font-weight:bold;'>$timeIST</td>
              <td style='padding:8px; border:1px solid #ddd; $lateReasonStyle'>$lateReasonHtml</td>
            </tr>";
        } else {
            // No attendance yet -> check if on approved leave
            if (user_is_on_leave_today($conn, $userId, $today)) {
                $table .= "
                <tr style='background-color:#fff9e6;'>
                  <td style='padding:8px; border:1px solid #ddd;'>$userName</td>
                  <td style='padding:8px; border:1px solid #ddd; color:#e67e22; font-weight:bold;'>Leave</td>
                  <td style='padding:8px; border:1px solid #ddd;'>-</td>
                  <td style='padding:8px; border:1px solid #ddd; color:#95a5a6;'>-</td>
                </tr>";
            } else {
                // Absent
                $table .= "
                <tr style='background-color:#ffe6e6;'>
                  <td style='padding:8px; border:1px solid #ddd;'>$userName</td>
                  <td style='padding:8px; border:1px solid #ddd; color:#e74c3c; font-weight:bold;'>Absent</td>
                  <td style='padding:8px; border:1px solid #ddd;'>-</td>
                  <td style='padding:8px; border:1px solid #ddd; color:#95a5a6;'>-</td>
                </tr>";
            }
        }
    }
} else {
    $table .= "<tr><td colspan='4' style='padding:10px; text-align:center; border:1px solid #ddd;'>No users found.</td></tr>";
}

$table .= "</tbody></table>";

// ---------------- SEND MAIL ----------------
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'triserv360businesssolutions@gmail.com';
    $mail->Password   = 'mqyjzlbdxekoupqy';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = "UTF-8";
    $mail->Encoding   = "base64";

    $mail->setFrom('triserv360businesssolutions@gmail.com', 'Workorio Alert');

    // ---------------- MANUAL RECIPIENTS ONLY ----------------
    $recipients = [
        "areesha@triserv360.com"      => "Admin Dept",
        "sandeep@triserv360.com"      => "Developer",
        "shamshad@triserv360.com"     => "Owner",
    ];

    foreach ($recipients as $email => $name) {
        $mail->addAddress($email, $name);
    }

    // ---------------- EMAIL CONTENT ----------------
    $mail->isHTML(true);
    $mail->Subject = "Today's Attendance Summary - " . date('d M Y');
    $mail->Body    = $table . "<br><p style='font-family: Arial, sans-serif; font-size:12px; color:#555;'>Regards,<br><b>HR Team</b></p>";
    $mail->AltBody = "Today's Attendance Summary\n\nPlease switch to HTML mode to view the table.";

    $mail->send();
    echo "✅ Attendance summary email sent to selected recipients only!";
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();
