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

// Fetch follow-ups
$sql = "
SELECT 
    sr.id,
    u.name AS user_name,
    sr.leads_name,
    sr.contact_person,
    sr.contact_number,
    sr.address,
    s.state_name,
    c.city_name,
    sr.email,
    bt.business_name AS business_type_name,
    ls.source_name AS lead_source_name,
    st.status_name,
    sr.next_follow_up_date,
    p.product_name,
    sr.status_id
FROM sales_records sr
LEFT JOIN users u ON u.id = sr.user_id
LEFT JOIN states s ON s.id = sr.state_id
LEFT JOIN cities c ON c.id = sr.city_id
LEFT JOIN sales_business_types bt ON bt.id = sr.business_type_id
LEFT JOIN sales_lead_sources ls ON ls.id = sr.lead_source_id
LEFT JOIN sales_status st ON st.id = sr.status_id
LEFT JOIN sales_products p ON p.id = sr.products_id
WHERE sr.next_follow_up_date <= ? AND sr.status_id NOT IN (1, 2, 15,20)
ORDER BY u.name ASC, sr.next_follow_up_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($rec = $result->fetch_assoc()) {
    $records[] = $rec;
}

// Fetch Leads Added Since Yesterday
$yesterday = date("Y-m-d", strtotime("-1 day"));
$sql_new_leads = "
SELECT 
    sr.leads_name,
    u.name AS user_name,
    sr.contact_person,
    sr.contact_number,
    c.city_name,
    st.status_name,
    p.product_name,
    sr.createdat
FROM sales_records sr
LEFT JOIN users u ON u.id = sr.user_id
LEFT JOIN cities c ON c.id = sr.city_id
LEFT JOIN sales_status st ON st.id = sr.status_id
LEFT JOIN sales_products p ON p.id = sr.products_id
WHERE sr.createdat >= ?
ORDER BY sr.createdat DESC
";

$stmt_new = $conn->prepare($sql_new_leads);
$stmt_new->bind_param("s", $yesterday);
$stmt_new->execute();
$result_new = $stmt_new->get_result();
$new_leads = [];
while ($row = $result_new->fetch_assoc()) {
    $new_leads[] = $row;
}

// --- Build Summary ---
$summary = [];
foreach ($records as $rec) {
    $user = $rec['user_name'] ?? 'Unknown';
    $summary[$user] = ($summary[$user] ?? 0) + 1;
}

// Build New Leads Section HTML
$table_new_leads = "
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
            <tbody>";

if (empty($new_leads)) {
    $table_new_leads .= "<tr><td colspan='6' style='text-align:center; padding:10px;'>No new leads added since yesterday.</td></tr>";
} else {
    foreach ($new_leads as $nl) {
        $nl_date = date("d M, Y", strtotime($nl['createdat']));
        $table_new_leads .= "
                <tr>
                    <td><strong>{$nl['leads_name']}</strong></td>
                    <td>{$nl['user_name']}</td>
                    <td>{$nl['contact_number']}</td>
                    <td>{$nl['city_name']}</td>
                    <td><span class='status-badge'>{$nl['status_name']}</span></td>
                    <td>$nl_date</td>
                </tr>";
    }
}
$table_new_leads .= "
            </tbody>
        </table>
    </div>
</div>";

// CSS Styles for mobile responsiveness
$styles = "
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
";

$table_summary = "
<div class='summary-section'>
    <h2 class='summary-title'>Follow-up Summary - $today</h2>
    <div class='table-container'>
        <table class='summary-table'>
            <thead>
                <tr>
                    <th>User</th>
                    <th style='text-align: center; width: 120px;'>Pending Follow-ups</th>
                </tr>
            </thead>
            <tbody>";

$row_index = 0;
foreach ($summary as $user => $count) {
    $table_summary .= "
                <tr>
                    <td><strong>$user</strong></td>
                    <td style='text-align: center;'><span class='count-badge'>$count</span></td>
                </tr>";
    $row_index++;
}
$table_summary .= "
            </tbody>
        </table>
    </div>
</div>";

$table_details = "
<div class='details-section'>
    <h2 class='details-title'>Detailed Follow-up Report</h2>";

$current_user = '';
$current_date = '';

foreach ($records as $rec) {
    if ($rec['user_name'] != $current_user) {
        if ($current_user !== '') {
            $table_details .= "</div></div>";
        }
        $current_user = $rec['user_name'];
        $table_details .= "
        <div class='user-section'>
            <h3 class='user-header'>USER: $current_user</h3>";
        $current_date = '';
    }

    if ($rec['next_follow_up_date'] != $current_date) {
        if ($current_date !== '') {
            $table_details .= "</div>";
        }
        $current_date = $rec['next_follow_up_date'];
        $table_details .= "
            <h4 class='date-header'>DATE: $current_date</h4>
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
                    <tbody>";
    }

    // Determine status styling
    $status_class = 'status-default';
    if (strtolower($rec['status_name']) == 'completed') $status_class = 'status-completed';
    if (strtolower($rec['status_name']) == 'rejected') $status_class = 'status-rejected';
    if (strtolower($rec['status_name']) == 'pending') $status_class = 'status-pending';

    $location = trim(($rec['city_name'] ?? '') . ', ' . ($rec['state_name'] ?? ''));
    $location = rtrim($location, ', ');

    $table_details .= "
                        <tr>
                            <td><strong>{$rec['leads_name']}</strong></td>
                            <td>{$rec['contact_person']}</td>
                            <td>{$rec['contact_number']}</td>
                            <td>$location</td>
                            <td>{$rec['email']}</td>
                            <td><span class='status-badge $status_class'>{$rec['status_name']}</span></td>
                            <td>{$rec['product_name']}</td>
                        </tr>";
}
if ($current_user !== '') {
    $table_details .= "
                    </tbody>
                </table>
            </div>
        </div>";
}

$table_details .= "</div>";

// --- Send Email ---
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
    $mail->addAddress("sandeep@triserv360.com");


    $mail->isHTML(true);
    $mail->Subject = "Follow-up Report ($today)";
    
    // Complete Email Body with mobile-responsive design
    $email_body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Follow-up Report - $today</title>
        $styles
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>Follow-up Report</h1>
                <p>Daily Sales Follow-up Summary - $today</p>
            </div>
            
            $table_summary
            
            $table_new_leads
            
            $table_details
            
            <div class='footer'>
                <p><strong>Best Regards,</strong><br>HR Team - Triserv360</p>
                <div class='contact-info'>
                    <strong>Contact:</strong> sandeep@triserv360.com<br>
                    <strong>System:</strong> Workorio Lead Management
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    $mail->Body = $email_body;

    $mail->send();
    echo "Follow-up report email sent successfully!";
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();
?>
