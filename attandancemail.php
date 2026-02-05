<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// DB Connection
$host = "localhost";
$user = "u976774818_triserv360";
$pass = "g/OdVW0e8R";
$dbname = "u976774818_triserv360";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date("Y-m-d");
$startOfMonth = date("Y-m-01"); // First day of current month

// Fetch users except admin role for attendance data
// Fetch users linked to ACTIVE employees only (excluding admins)
$sql_users = "SELECT u.id, u.name, u.email 
              FROM users u
              JOIN employees e ON u.employee_id = e.id
              WHERE u.role_id != 1 AND e.status = 'active'";
$res_users = $conn->query($sql_users);

if (!$res_users) {
    die("Error fetching users: " . $conn->error);
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

// Function to get late reason from first movement of the day
function get_late_reason_for_day(mysqli $conn, int $attendanceId): string {
    $sql = "SELECT description FROM movements 
            WHERE attendance_id = ? AND movement_type IN ('office', 'field') 
            AND movement_action = 'in' 
            ORDER BY time ASC 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return '-';
    
    $stmt->bind_param("i", $attendanceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row && !empty($row['description'])) {
        return extract_late_reason($row['description']);
    }
    
    return '-';
}

// Prepare HTML content
$table = "
<h2 style='font-family: Arial, sans-serif; color: #2c3e50;'>📊 Attendance Summary</h2>
<p style='font-family: Arial, sans-serif; font-size:14px; color:#555;'>
Today's attendance summary and complete monthly breakdown for " . date("F Y") . ":
</p>
";

// First, get today's attendance for all users
$table .= "<h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📅 Today's Attendance Summary - " . date("F j, Y") . "</h3>";
$table .= "<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:14px; border:1px solid #ddd; margin-bottom: 30px;'>";
$table .= "<thead>
<tr style='background-color:#34495e; color:#fff;'>
<th style='padding:6px; border:1px solid #ddd;'>Employee</th>
<th style='padding:6px; border:1px solid #ddd;'>Punch-In</th>
<th style='padding:6px; border:1px solid #ddd;'>Punch-Out</th>
<th style='padding:6px; border:1px solid #ddd;'>Mode</th>
<th style='padding:6px; border:1px solid #ddd;'>Place</th>
<th style='padding:6px; border:1px solid #ddd;'>Total Hours</th>
<th style='padding:6px; border:1px solid #ddd;'>Late Reason</th>
</tr>
</thead>
<tbody>";

while ($user = $res_users->fetch_assoc()) {
    $userId = $user['id'];
    
    // Get today's attendance
    $attRes = $conn->query("SELECT id FROM attendance WHERE user_id=$userId AND date='$today'");
    
    if ($attRes->num_rows == 0) {
        // Check if user is on leave
        $leaveRes = $conn->query("SELECT id FROM leaves WHERE user_id=$userId AND date='$today' AND status='approved'");
        
        if ($leaveRes->num_rows > 0) {
            $table .= "<tr>
                <td style='padding:5px; border:1px solid #ddd;'>{$user['name']}</td>
                <td colspan='6' style='padding:5px; border:1px solid #ddd; text-align:center; color:#3498db; font-weight:bold;'>Leave</td>
            </tr>";
        } else {
            $table .= "<tr>
                <td style='padding:5px; border:1px solid #ddd;'>{$user['name']}</td>
                <td colspan='6' style='padding:5px; border:1px solid #ddd; text-align:center; color:#e74c3c; font-weight:bold;'>Absent</td>
            </tr>";
        }
        continue;
    }
    
    $attId = $attRes->fetch_assoc()['id'];
    
    // Get late reason for today
    $lateReason = get_late_reason_for_day($conn, $attId);
    $lateReasonHtml = htmlspecialchars($lateReason, ENT_QUOTES, 'UTF-8');
    $lateReasonStyle = ($lateReason !== '-') ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
    
    // Get today's movements
    $sqlMovements = "SELECT movement_type, movement_action, time, mode, place FROM movements 
                     WHERE attendance_id=$attId ORDER BY time";
    $resMovements = $conn->query($sqlMovements);
    
    if (!$resMovements) {
        $table .= "<tr>
            <td style='padding:5px; border:1px solid #ddd;'>{$user['name']}</td>
            <td colspan='6' style='padding:5px; border:1px solid #ddd; text-align:center;'>Error loading data</td>
        </tr>";
        continue;
    }
    
    $movements = [];
    while ($row = $resMovements->fetch_assoc()) {
        $movements[] = $row;
    }
    
    // Calculate today's hours
    $officeHours = calculateOfficeHours($movements);
    
    // Get punch in/out times for display
    $punchIn = getPunchInTime($movements);
    $punchOut = getPunchOutTime($movements);
    
    $punchInIST = $punchIn ? $punchIn->setTimezone(new DateTimeZone("Asia/Kolkata"))->format("h:i A") : "Not Marked";
    $punchOutIST = $punchOut ? $punchOut->setTimezone(new DateTimeZone("Asia/Kolkata"))->format("h:i A") : "Not Marked";
    
    // Calculate total hours (only office hours now)
    $totalHoursFormatted = $officeHours['formatted'];
    

    // Extract mode and place from the first movement or default to '-'
    $mode = htmlspecialchars((string)($movements[0]['mode'] ?? '-'), ENT_QUOTES, 'UTF-8');
    $place = htmlspecialchars((string)($movements[0]['place'] ?? '-'), ENT_QUOTES, 'UTF-8');

    $table .= "<tr style='background-color:#f9f9f9;'>
        <td style='padding:5px; border:1px solid #ddd;'>{$user['name']}</td>
        <td style='padding:5px; border:1px solid #ddd;'>{$punchInIST}</td>
        <td style='padding:5px; border:1px solid #ddd;'>{$punchOutIST}</td>
        <td style='padding:5px; border:1px solid #ddd;'>{$mode}</td>
        <td style='padding:5px; border:1px solid #ddd;'>{$place}</td>
        <td style='padding:5px; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{$totalHoursFormatted}</td>
        <td style='padding:5px; border:1px solid #ddd; $lateReasonStyle'>{$lateReasonHtml}</td>
    </tr>";
}

$table .= "</tbody></table>";

// Reset the result pointer to process users again for monthly data
$res_users->data_seek(0);

// Now show monthly breakdown for each user
$table .= "<h3 style='font-family: Arial, sans-serif; color: #34495e; margin-top: 30px;'>📊 Monthly Attendance Breakdown</h3>";
while ($user = $res_users->fetch_assoc()) {
    $userId = $user['id'];
    
    // Get all attendance records for this month
    $attRes = $conn->query("SELECT id, date FROM attendance WHERE user_id=$userId AND date BETWEEN '$startOfMonth' AND '$today' ORDER BY date");
    
    // Get all leaves for this month
    $leaveRes = $conn->query("SELECT date FROM leaves WHERE user_id=$userId AND date BETWEEN '$startOfMonth' AND '$today' AND status='approved' ORDER BY date");
    
    $attendanceRecords = [];
    while ($att = $attRes->fetch_assoc()) {
        $attendanceRecords[] = $att;
    }
    
    // Get leave dates
    $leaveDates = [];
    while ($leave = $leaveRes->fetch_assoc()) {
        $leaveDates[] = $leave['date'];
    }
    
    // Skip users with no attendance and no leaves
    if (count($attendanceRecords) == 0 && count($leaveDates) == 0) {
        continue;
    }
    
    // User header
    $table .= "<h4 style='font-family: Arial, sans-serif; color: #2c3e50; margin-top: 25px; margin-bottom: 10px;'>👤 {$user['name']}</h4>";
    
    // Monthly details table for this user
    $table .= "<table style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size:13px; border:1px solid #ddd; margin-bottom: 20px;'>";
    $table .= "<thead>
    <tr style='background-color:#ecf0f1;'>
    <th style='padding:4px; border:1px solid #ddd;'>Date</th>
    <th style='padding:4px; border:1px solid #ddd;'>Punch-In</th>
    <th style='padding:4px; border:1px solid #ddd;'>Punch-Out</th>
    <th style='padding:4px; border:1px solid #ddd;'>Mode</th>
    <th style='padding:4px; border:1px solid #ddd;'>Place</th>
    <th style='padding:4px; border:1px solid #ddd;'>Total Hours</th>
    <th style='padding:4px; border:1px solid #ddd;'>Late Reason</th>
    </tr>
    </thead>
    <tbody>";
    
    // Initialize monthly totals
    $monthlyOfficeTotal = 0;
    
    // Create a map of dates to attendance records for quick lookup
    $attendanceByDate = [];
    foreach ($attendanceRecords as $attendance) {
        $attendanceByDate[$attendance['date']] = $attendance;
    }
    
    // Get all unique dates (attendance + leaves) and sort them
    $allDates = array_unique(array_merge(array_keys($attendanceByDate), $leaveDates));
    sort($allDates);
    
    // Process each day
    foreach ($allDates as $date) {
        // Check if it's a leave day (and not an attendance day)
        if (in_array($date, $leaveDates) && !isset($attendanceByDate[$date])) {
            // Show leave row
            $table .= "<tr>
                <td style='padding:4px; border:1px solid #ddd;'>" . date("M j, Y", strtotime($date)) . "</td>
                <td colspan='6' style='padding:4px; border:1px solid #ddd; text-align:center; color:#3498db; font-weight:bold;'>Leave</td>
            </tr>";
            continue;
        }
        
        // Process attendance day
        if (isset($attendanceByDate[$date])) {
            $attendance = $attendanceByDate[$date];
            $attId = $attendance['id'];
            
            // Get late reason for this day
            $lateReason = get_late_reason_for_day($conn, $attId);
            $lateReasonHtml = htmlspecialchars($lateReason, ENT_QUOTES, 'UTF-8');
            $lateReasonStyle = ($lateReason !== '-') ? 'color:#e67e22; font-style:italic;' : 'color:#95a5a6;';
            
            // Get movements for this day
            $sqlMovements = "SELECT movement_type, movement_action, time, mode, place FROM movements 
                             WHERE attendance_id=$attId ORDER BY time";
            $resMovements = $conn->query($sqlMovements);
            
            if (!$resMovements) {
                continue;
            }
            
            $movements = [];
            while ($row = $resMovements->fetch_assoc()) {
                $movements[] = $row;
            }
            
            // Calculate hours for this day
            $officeHours = calculateOfficeHours($movements);
            
            // Add to monthly totals
            $monthlyOfficeTotal += $officeHours['total'];
            
            // Get punch in/out times for this day
            $punchIn = getPunchInTime($movements);
            $punchOut = getPunchOutTime($movements);
            
            $punchInIST = $punchIn ? $punchIn->setTimezone(new DateTimeZone("Asia/Kolkata"))->format("h:i A") : "Not Marked";
            $punchOutIST = $punchOut ? $punchOut->setTimezone(new DateTimeZone("Asia/Kolkata"))->format("h:i A") : "Not Marked";
            
            // Calculate total hours for this day (only office hours now)
            $dayTotalFormatted = $officeHours['formatted'];
            
            // Extract mode and place from the first movement or default to '-'
            $mode = htmlspecialchars((string)($movements[0]['mode'] ?? '-'), ENT_QUOTES, 'UTF-8');
            $place = htmlspecialchars((string)($movements[0]['place'] ?? '-'), ENT_QUOTES, 'UTF-8');

            $table .= "<tr>
                <td style='padding:4px; border:1px solid #ddd;'>" . date("M j, Y", strtotime($date)) . "</td>
                <td style='padding:4px; border:1px solid #ddd;'>{$punchInIST}</td>
                <td style='padding:4px; border:1px solid #ddd;'>{$punchOutIST}</td>
                <td style='padding:4px; border:1px solid #ddd;'>{$mode}</td>
                <td style='padding:4px; border:1px solid #ddd;'>{$place}</td>
                <td style='padding:4px; border:1px solid #ddd; color:#8e44ad; font-weight:bold;'>{$dayTotalFormatted}</td>
                <td style='padding:4px; border:1px solid #ddd; $lateReasonStyle'>{$lateReasonHtml}</td>
            </tr>";
        }
    }
    
    // Add monthly total row
    $table .= "<tr style='background-color:#f8f9fa; font-weight:bold;'>
        <td style='padding:4px; border:1px solid #ddd;'><strong>Monthly Total</strong></td>
        <td style='padding:4px; border:1px solid #ddd;'>-</td>
        <td style='padding:4px; border:1px solid #ddd;'>-</td>
        <td style='padding:4px; border:1px solid #ddd;'>-</td>
        <td style='padding:4px; border:1px solid #ddd;'>-</td>
        <td style='padding:4px; border:1px solid #ddd; color:#8e44ad;'>" . formatHoursMinutes($monthlyOfficeTotal) . "</td>
        <td style='padding:4px; border:1px solid #ddd; color:#95a5a6;'>-</td>
    </tr>";
    
    $table .= "</tbody></table>";
}

// Function to calculate office hours (first punch-in to last punch-out)
function calculateOfficeHours($movements) {
    $officeMovements = array_filter($movements, function($m) {
        return $m['movement_type'] === 'office';
    });
    
    if (empty($officeMovements)) {
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    $firstPunchIn = null;
    $lastPunchOut = null;
    
    foreach ($officeMovements as $movement) {
        if ($movement['movement_action'] === 'in' && !$firstPunchIn) {
            $firstPunchIn = new DateTime($movement['time'], new DateTimeZone("UTC"));
        }
        if ($movement['movement_action'] === 'out') {
            $lastPunchOut = new DateTime($movement['time'], new DateTimeZone("UTC"));
        }
    }
    
    if (!$firstPunchIn) {
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    if (!$lastPunchOut) {
        // If no punch-out, don't calculate office hours
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    $interval = $firstPunchIn->diff($lastPunchOut);
    $totalMinutes = ($interval->h * 60) + $interval->i;
    
    return [
        'total' => $totalMinutes / 60,
        'formatted' => formatHoursMinutes($totalMinutes / 60)
    ];
}

// Function to calculate field hours (first punch-in to last punch-out)
function calculateFieldHours($movements) {
    $fieldMovements = array_filter($movements, function($m) {
        return $m['movement_type'] === 'field';
    });
    
    if (empty($fieldMovements)) {
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    $firstPunchIn = null;
    $lastPunchOut = null;
    
    foreach ($fieldMovements as $movement) {
        if ($movement['movement_action'] === 'in' && !$firstPunchIn) {
            $firstPunchIn = new DateTime($movement['time'], new DateTimeZone("UTC"));
        }
        if ($movement['movement_action'] === 'out') {
            $lastPunchOut = new DateTime($movement['time'], new DateTimeZone("UTC"));
        }
    }
    
    if (!$firstPunchIn) {
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    if (!$lastPunchOut) {
        // If no field-out, don't calculate field hours
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    $interval = $firstPunchIn->diff($lastPunchOut);
    $totalMinutes = ($interval->h * 60) + $interval->i;
    
    return [
        'total' => $totalMinutes / 60,
        'formatted' => formatHoursMinutes($totalMinutes / 60)
    ];
}

// Function to calculate break hours (sum of all break periods)
function calculateBreakHours($movements) {
    $breakMovements = array_filter($movements, function($m) {
        return $m['movement_type'] === 'break';
    });
    
    if (empty($breakMovements)) {
        return ['total' => 0, 'formatted' => '0:00 hrs'];
    }
    
    $totalMinutes = 0;
    $start = null;
    
    foreach ($breakMovements as $movement) {
        if ($movement['movement_action'] === 'start') {
            $start = new DateTime($movement['time'], new DateTimeZone("UTC"));
        } elseif ($movement['movement_action'] === 'end' && $start) {
            $end = new DateTime($movement['time'], new DateTimeZone("UTC"));
            $interval = $start->diff($end);
            $totalMinutes += ($interval->h * 60) + $interval->i;
            $start = null;
        }
    }
    
    // If break is still active, calculate until now
    if ($start) {
        $end = new DateTime("now", new DateTimeZone("UTC"));
        $interval = $start->diff($end);
        $totalMinutes += ($interval->h * 60) + $interval->i;
    }
    
    return [
        'total' => $totalMinutes / 60,
        'formatted' => formatHoursMinutes($totalMinutes / 60)
    ];
}

// Function to format hours and minutes
function formatHoursMinutes($decimalHours) {
    if ($decimalHours === 0) {
        return '0:00 hrs';
    }
    
    $hours = floor($decimalHours);
    $minutes = round(($decimalHours - $hours) * 60);
    
    // Handle case where minutes round to 60
    if ($minutes === 60) {
        $hours += 1;
        $minutes = 0;
    }
    
    return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' hrs';
}

// Function to get first punch-in time
function getPunchInTime($movements) {
    $officeMovements = array_filter($movements, function($m) {
        return $m['movement_type'] === 'office' && $m['movement_action'] === 'in';
    });
    
    if (empty($officeMovements)) {
        return null;
    }
    
    $firstPunchIn = null;
    foreach ($officeMovements as $movement) {
        $punchTime = new DateTime($movement['time'], new DateTimeZone("UTC"));
        if (!$firstPunchIn || $punchTime < $firstPunchIn) {
            $firstPunchIn = $punchTime;
        }
    }
    
    return $firstPunchIn;
}

// Function to get last punch-out time
function getPunchOutTime($movements) {
    $officeMovements = array_filter($movements, function($m) {
        return $m['movement_type'] === 'office' && $m['movement_action'] === 'out';
    });
    
    if (empty($officeMovements)) {
        return null;
    }
    
    $lastPunchOut = null;
    foreach ($officeMovements as $movement) {
        $punchTime = new DateTime($movement['time'], new DateTimeZone("UTC"));
        if (!$lastPunchOut || $punchTime > $lastPunchOut) {
            $lastPunchOut = $punchTime;
        }
    }
    
    return $lastPunchOut;
}

// Send Mail
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'triserv360businesssolutions@gmail.com';
    $mail->Password   = 'mqyjzlbdxekoupqy';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet = "UTF-8";  
    $mail->Encoding = "base64"; 
    
    $mail->setFrom('triserv360businesssolutions@gmail.com', 'Workorio Alert');
    
    // Send to all users including admin
    // Send to all ACTIVE users (who have an active employee record)
    $sql_recipients = "SELECT u.email, u.name 
                       FROM users u
                       JOIN employees e ON u.employee_id = e.id
                       WHERE e.status = 'active'";
    $res_users = $conn->query($sql_recipients);
    if ($res_users && $res_users->num_rows > 0) {
        $sent_to = [];
        while ($u = $res_users->fetch_assoc()) {
            if (!empty($u['email'])) {
                $mail->addAddress($u['email'], $u['name']);
                $sent_to[] = $u['name'] . " (" . $u['email'] . ")";
            }
        }
    }
    
    $mail->isHTML(true);
    $mail->Subject = "Monthly Attendance Summary - " . date("F Y");
    $mail->Body    = $table . "<br><p style='font-family: Arial, sans-serif; font-size:13px; color:#555;'>Regards,<br><b>HR Team</b></p>";
    
    $mail->send();
    echo "✅ Attendance summary email sent successfully!<br><br>";
    echo "<strong>Mail sent to the following recipients:</strong><br>";
    echo implode("<br>", $sent_to);
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();

?>
