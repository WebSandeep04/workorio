<?php

// IndiaMART Push API Webhook (core PHP)
// Ref: Push API guidelines https://help.indiamart.com/knowledge-base/integration-of-indiamarts-lead-manager-crm-push-api-with-third-party-crms-real-time-push-of-leads/#my-section
// - Point IndiaMART Push URL to this file (HTTPS recommended)
// - Configure DB credentials and optional shared token below
// - Responds with HTTP 200 JSON

// ================== CONFIG: EDIT THESE ==================
$sharedToken = 'REPLACE_WITH_SECURE_TOKEN';   // optional shared secret; IndiaMART can call: webhook.php?token=YOUR_TOKEN

$dbHost  = '127.0.0.1';
$dbUser  = 'u976774818_unique';
$dbPass  = '4l|lXOT6b:G';
$dbName  = 'u976774818_unique';
$dbPort  = 3306; // change if needed
$table   = 'indiamartleads';

// SMTP settings for PHPMailer
$smtpHost       = 'smtp.gmail.com';
$smtpPort       = 587;
$smtpUsername   = 'triserv360businesssolutions@gmail.com';
$smtpPassword   = 'mqyjzlbdxekoupqy';
$smtpEncryption = 'tls';
$mailFrom       = 'triserv360businesssolutions@gmail.com';
$mailFromName   = 'Workorio Alert';

// MSG91 WhatsApp API settings
// IMPORTANT: Replace 'YOUR_MSG91_AUTH_KEY' with your actual MSG91 auth key
// Template must have 2 body parameters: body_1 (sender name) and body_2 (company name)
$msg91AuthKey   = 'YOUR_MSG91_AUTH_KEY'; // Replace with your MSG91 auth key
$msg91IntegratedNumber = '919451820817';
$msg91TemplateNamespace = '78b03afb_64e5_429f_813d_68bad14fe1cd';
$msg91TemplateName = 'india_mart_leads_greeting';
// =======================================================

// Include Composer autoload for PHPMailer
$autoloadCandidates = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/leadmanagement/vendor/autoload.php',
    dirname(__DIR__, 2) . '/vendor/autoload.php',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/vendor/autoload.php',
];
foreach ($autoloadCandidates as $candidate) {
    if ($candidate && file_exists($candidate)) {
        require $candidate;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Catch all errors and return JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'internal_error',
            'error' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
});
set_exception_handler(function ($e) {
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => 'internal_error', 'error' => $e->getMessage()]);
    exit;
});

// Optional health check
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    echo json_encode(['success' => true, 'message' => 'OK']);
    exit;
}

// Enforce POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'method_not_allowed']);
    exit;
}

// Optional token check
if ($sharedToken !== 'REPLACE_WITH_SECURE_TOKEN') {
    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals($sharedToken, $token)) {
        echo json_encode(['success' => false, 'message' => 'unauthorized']);
        exit;
    }
}

$raw = file_get_contents('php://input');
if ($raw === false || $raw === '') {
    echo json_encode(['success' => false, 'message' => 'empty_body']);
    exit;
}

$payload = json_decode($raw, true);
if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'invalid_json', 'error' => json_last_error_msg()]);
    exit;
}
if (!is_array($payload)) {
    $payload = $_POST;
}
if (!is_array($payload)) {
    echo json_encode(['success' => false, 'message' => 'invalid_payload']);
    exit;
}

// Unwrap envelope if present
$envelope = $payload;
if (isset($payload['body']) && is_array($payload['body'])) {
    $envelope = $payload['body'];
}

// Extract leads
$leads = [];
if (isset($envelope['RESPONSE']) && is_array($envelope['RESPONSE'])) {
    $leads = isset($envelope['RESPONSE'][0]) && is_array($envelope['RESPONSE'][0]) ? $envelope['RESPONSE'] : [$envelope['RESPONSE']];
} elseif (isset($payload['RESPONSE']) && is_array($payload['RESPONSE'])) {
    $r = $payload['RESPONSE'];
    $leads = isset($r[0]) && is_array($r[0]) ? $r : [$r];
} else {
    $leads = [$envelope];
}

// Parse India time in multiple formats to UTC
function im_parse_query_time(?string $value): ?string {
    if (!$value) return null;
    $formats = [
        'd-M-Y H:i:s',     // e.g., 10-Apr-2024 11:17:14
        'Y-m-d H:i:s',     // e.g., 2024-04-10 11:17:14 (test payload)
        'd-M-YH:i:s',      // e.g., 10-Apr-202411:17:14
    ];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $value, new DateTimeZone('Asia/Kolkata'));
        if ($dt instanceof DateTime) {
            $dt->setTimezone(new DateTimeZone('UTC'));
            return $dt->format('Y-m-d H:i:s');
        }
    }
    return null;
}

function map_im_lead(array $lead): array {
    return [
        'unique_query_id'          => $lead['UNIQUE_QUERY_ID'] ?? null,
        'query_type'               => $lead['QUERY_TYPE'] ?? null,
        'query_time'               => im_parse_query_time($lead['QUERY_TIME'] ?? null),
        'query_product_name'       => $lead['QUERY_PRODUCT_NAME'] ?? ($lead['PRODUCT_NAME'] ?? null),
        'query_message'            => $lead['QUERY_MESSAGE'] ?? null,
        'query_mcat_name'          => $lead['QUERY_MCAT_NAME'] ?? null,
        'sender_name'              => $lead['SENDER_NAME'] ?? null,
        'sender_mobile'            => $lead['SENDER_MOBILE'] ?? null,
        'sender_mobile_alt'        => $lead['SENDER_MOBILE_ALT'] ?? null,
        'sender_email'             => $lead['SENDER_EMAIL'] ?? null,
        'sender_email_alt'         => $lead['SENDER_EMAIL_ALT'] ?? null,
        'sender_company'           => $lead['SENDER_COMPANY'] ?? null,
        'sender_address'           => $lead['SENDER_ADDRESS'] ?? null,
        'sender_city'              => $lead['SENDER_CITY'] ?? null,
        'sender_state'             => $lead['SENDER_STATE'] ?? null,
        'sender_pincode'           => $lead['SENDER_PINCODE'] ?? null,
        'sender_country_iso'       => $lead['SENDER_COUNTRY_ISO'] ?? null,
        'sender_other_mobile'      => $lead['SENDER_OTHER_MOBILE'] ?? null,
        'sender_phone'             => $lead['SENDER_PHONE'] ?? null,
        'sender_phone_alt'         => $lead['SENDER_PHONE_ALT'] ?? null,
        'receiver_mobile'          => $lead['RECEIVER_MOBILE'] ?? null,
        'call_duration'            => $lead['CALL_DURATION'] ?? null,
        'subject'                  => $lead['SUBJECT'] ?? null,
        'company_name'             => $lead['SENDER_COMPANY'] ?? ($lead['COMPANY_NAME'] ?? null),
        'product_name'             => $lead['QUERY_PRODUCT_NAME'] ?? ($lead['PRODUCT_NAME'] ?? null),
        'district'                 => $lead['DISTRICT'] ?? null,
        'org_sender_glusr_usr_id'  => $lead['ORG_SENDER_GLUSR_USR_ID'] ?? null,
        'enrichment_id'            => $lead['ENRICHMENT_ID'] ?? null,
        'im_member_since'          => $lead['IM_MEMBER_SINCE'] ?? null,
        'enq_id'                   => $lead['ENQ_ID'] ?? null,
        'enq_receiver_name'        => $lead['ENQ_RECEIVER_NAME'] ?? null,
        'enq_receiver_email'       => $lead['ENQ_RECEIVER_EMAIL'] ?? null,
        'enq_receiver_mobile'      => $lead['ENQ_RECEIVER_MOBILE'] ?? null,
        'status'                   => 'new',
        'is_processed'             => '0',
        'raw_data'                 => json_encode($lead, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'fetched_at'               => gmdate('Y-m-d H:i:s'),
        'notes'                    => null,
        'created_at'               => gmdate('Y-m-d H:i:s'),
        'updated_at'               => gmdate('Y-m-d H:i:s'),
    ];
}

// Function to send WhatsApp message via MSG91
function sendWhatsAppMessage($leadData) {
    // Get phone number - try sender_mobile first, then alternatives
    $phoneNumber = $leadData['sender_mobile'] ?? $leadData['sender_mobile_alt'] ?? $leadData['sender_other_mobile'] ?? null;
    
    if (empty($phoneNumber)) {
        error_log("IndiaMART Webhook: No phone number available for WhatsApp message");
        return false;
    }
    
    // Clean phone number (remove spaces, hyphens, etc.)
    $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
    
    // Ensure phone number starts with country code
    if (substr($phoneNumber, 0, 1) !== '+') {
        if (substr($phoneNumber, 0, 2) !== '91') {
            $phoneNumber = '91' . ltrim($phoneNumber, '0');
        }
    }
    
    // Remove + if present for API
    $phoneNumber = ltrim($phoneNumber, '+');
    
    // Get sender name and company for template variables
    $senderName = $leadData['sender_name'] ?? 'Customer';
    $companyName = $leadData['sender_company'] ?? $leadData['company_name'] ?? 'your company';
    
    // Prepare MSG91 WhatsApp API request
    $apiUrl = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';
    
    $payload = [
        'integrated_number' => $GLOBALS['msg91IntegratedNumber'],
        'content_type' => 'template',
        'payload' => [
            'messaging_product' => 'whatsapp',
            'type' => 'template',
            'template' => [
                'name' => $GLOBALS['msg91TemplateName'],
                'language' => [
                    'code' => 'en',
                    'policy' => 'deterministic'
                ],
                'namespace' => $GLOBALS['msg91TemplateNamespace'],
                'to_and_components' => [
                    [
                        'to' => [$phoneNumber],
                        'components' => [
                            'body_1' => [
                                'type' => 'text',
                                'value' => $senderName
                            ],
                            'body_2' => [
                                'type' => 'text',
                                'value' => $companyName
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
    
    try {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'authkey: ' . $GLOBALS['msg91AuthKey']
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            error_log("IndiaMART Webhook: WhatsApp message sent successfully to {$phoneNumber}");
            return true;
        } else {
            error_log("IndiaMART Webhook: WhatsApp message failed. HTTP Code: {$httpCode}, Response: {$response}");
            return false;
        }
    } catch (\Throwable $e) {
        error_log("IndiaMART Webhook: WhatsApp message exception: " . $e->getMessage());
        return false;
    }
}

// Function to send email notification
function notifySalesUsers($mysqli, $leadData) {
    // Use the same database connection for users (direct database)
    $usersResult = $mysqli->query("SELECT id, email, name FROM users WHERE is_sales = 1 AND is_indiaMart = 1 AND email IS NOT NULL AND email != ''");
    if (!$usersResult) return;

    $recipients = [];
    while ($row = $usersResult->fetch_assoc()) {
        $recipients[] = $row;
    }
    if (empty($recipients)) return;

    $subject = 'New IndiaMART Lead: ' . (($leadData['sender_company'] ?? '') ?: ($leadData['sender_name'] ?? 'Unknown'));
    $htmlBody = "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333}.container{max-width:600px;margin:0 auto;padding:20px;background:#fff;border:1px solid #eee}.header{background:#4CAF50;color:#fff;padding:15px;text-align:center}.field{margin:10px 0}.label{font-weight:bold;color:#555}.message{background:#f9f9f9;border-left:4px solid #4CAF50;padding:12px}</style></head><body><div class=container><div class=header><h2>New IndiaMART Lead Received</h2></div><div class=content>";
    $htmlBody .= '<div class=field><span class=label>Name:</span> ' . htmlspecialchars($leadData['sender_name'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Company:</span> ' . htmlspecialchars($leadData['sender_company'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Mobile:</span> ' . htmlspecialchars($leadData['sender_mobile'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Email:</span> ' . htmlspecialchars($leadData['sender_email'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Location:</span> ' . htmlspecialchars(($leadData['sender_city'] ?? 'N/A') . ', ' . ($leadData['sender_state'] ?? 'N/A')) . '</div>';
    $htmlBody .= '<div class=field><span class=label>Product Interest:</span> ' . htmlspecialchars($leadData['query_product_name'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Query Type:</span> ' . htmlspecialchars($leadData['query_type'] ?? 'N/A') . '</div>';
    if (!empty($leadData['query_message'])) {
        $htmlBody .= '<div class=message><strong>Message:</strong><br>' . nl2br(htmlspecialchars($leadData['query_message'])) . '</div>';
    }
    $htmlBody .= '<div class=field><span class=label>Query ID:</span> ' . htmlspecialchars($leadData['unique_query_id'] ?? 'N/A') . '</div>';
    $htmlBody .= '<div class=field><span class=label>Time:</span> ' . htmlspecialchars($leadData['query_time'] ?? gmdate('Y-m-d H:i:s')) . '</div>';
    $htmlBody .= '</div></div></body></html>';

    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host       = $GLOBALS['smtpHost'];
            $mailer->SMTPAuth   = true;
            $mailer->Username   = $GLOBALS['smtpUsername'];
            $mailer->Password   = $GLOBALS['smtpPassword'];
            $encryption = strtolower((string)$GLOBALS['smtpEncryption']);
            if ($encryption === 'ssl') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mailer->Port       = $GLOBALS['smtpPort'] ?: 465;
            } else {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mailer->Port       = $GLOBALS['smtpPort'] ?: 587;
            }
            $mailer->setFrom($GLOBALS['mailFrom'], $GLOBALS['mailFromName']);
            foreach ($recipients as $rec) {
                $mailer->addAddress($rec['email'], $rec['name'] ?? '');
            }
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body    = $htmlBody;
            $mailer->AltBody = strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $htmlBody));
            $mailer->send();
        } catch (\Throwable $e) {
            // Silent fail
        }
    }
    
    createInAppNotifications($mysqli, $recipients, $leadData);
}

function createInAppNotifications($mysqli, $recipients, $leadData) {
    if (empty($recipients)) return;
    
    $title = 'New IndiaMART Lead';
    $message = 'New lead from ' . ($leadData['sender_name'] ?? 'Unknown') . ' (' . ($leadData['sender_company'] ?? 'N/A') . ')';
    $notificationData = json_encode([
        'type' => 'indiamart_lead',
        'title' => $title,
        'message' => $message,
        'lead_id' => $leadData['unique_query_id'] ?? null,
        'sender_name' => $leadData['sender_name'] ?? 'N/A',
        'sender_company' => $leadData['sender_company'] ?? 'N/A',
        'sender_mobile' => $leadData['sender_mobile'] ?? 'N/A',
        'sender_city' => $leadData['sender_city'] ?? 'N/A',
        'sender_state' => $leadData['sender_state'] ?? 'N/A',
        'query_product_name' => $leadData['query_product_name'] ?? 'N/A',
        'query_type' => $leadData['query_type'] ?? 'N/A',
        'created_at' => gmdate('Y-m-d H:i:s'),
    ]);
    
    // Check if notifications table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'notifications'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        error_log("IndiaMART Webhook: Notifications table does not exist");
        return;
    }
    
    $sql = "INSERT INTO notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NULL, NOW(), NOW())";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("IndiaMART Webhook: Failed to prepare notification statement");
        return;
    }
    
    $notificationType = 'App\\Notifications\\IndiaMartLeadNotification';
    $notifiableType = 'App\\Models\\User';
    
    $insertCount = 0;
    foreach ($recipients as $recipient) {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        $notifiableId = (int)$recipient['id'];
        $stmt->bind_param('sssis', $uuid, $notificationType, $notifiableType, $notifiableId, $notificationData);
        if ($stmt->execute()) {
            $insertCount++;
        }
    }
    
    $stmt->close();
    // Log summary
    if ($insertCount > 0) {
        error_log("IndiaMART Webhook: Created {$insertCount} in-app notifications");
    }
}

// Use the direct database for all operations (leads, users, notifications)
try {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
    if ($mysqli->connect_errno) {
        echo json_encode(['success' => false, 'message' => 'db_connect_error', 'error' => $mysqli->connect_error]);
        exit;
    }
    $mysqli->set_charset('utf8mb4');
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'db_connect_exception', 'error' => $e->getMessage()]);
    exit;
}

$sql = "INSERT INTO `$table`
    (unique_query_id, query_type, query_time, query_product_name, query_message, query_mcat_name,
     sender_name, sender_mobile, sender_mobile_alt, sender_email, sender_email_alt, sender_company, sender_address,
     sender_city, sender_state, sender_pincode, sender_country_iso, sender_other_mobile, sender_phone, sender_phone_alt,
     receiver_mobile, call_duration, subject, company_name, product_name, district, org_sender_glusr_usr_id,
     enrichment_id, im_member_since, enq_id, enq_receiver_name, enq_receiver_email, enq_receiver_mobile,
     status, is_processed, raw_data, fetched_at, notes, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE unique_query_id = unique_query_id";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'prepare_failed']);
    exit;
}

try {
    $saved = 0; $errors = 0;
    $types = str_repeat('s', 40);

    foreach ($leads as $lead) {
        $m = map_im_lead($lead);
        if (empty($m['unique_query_id'])) { $errors++; continue; }

        // Assign to local variables (pass-by-reference safe)
        $p1 = (string)($m['unique_query_id'] ?? '');
        $p2 = (string)($m['query_type'] ?? '');
        $p3 = (string)($m['query_time'] ?? '');
        $p4 = (string)($m['query_product_name'] ?? '');
        $p5 = (string)($m['query_message'] ?? '');
        $p6 = (string)($m['query_mcat_name'] ?? '');
        $p7 = (string)($m['sender_name'] ?? '');
        $p8 = (string)($m['sender_mobile'] ?? '');
        $p9 = (string)($m['sender_mobile_alt'] ?? '');
        $p10 = (string)($m['sender_email'] ?? '');
        $p11 = (string)($m['sender_email_alt'] ?? '');
        $p12 = (string)($m['sender_company'] ?? '');
        $p13 = (string)($m['sender_address'] ?? '');
        $p14 = (string)($m['sender_city'] ?? '');
        $p15 = (string)($m['sender_state'] ?? '');
        $p16 = (string)($m['sender_pincode'] ?? '');
        $p17 = (string)($m['sender_country_iso'] ?? '');
        $p18 = (string)($m['sender_other_mobile'] ?? '');
        $p19 = (string)($m['sender_phone'] ?? '');
        $p20 = (string)($m['sender_phone_alt'] ?? '');
        $p21 = (string)($m['receiver_mobile'] ?? '');
        $p22 = (string)($m['call_duration'] ?? '');
        $p23 = (string)($m['subject'] ?? '');
        $p24 = (string)($m['company_name'] ?? '');
        $p25 = (string)($m['product_name'] ?? '');
        $p26 = (string)($m['district'] ?? '');
        $p27 = (string)($m['org_sender_glusr_usr_id'] ?? '');
        $p28 = (string)($m['enrichment_id'] ?? '');
        $p29 = (string)($m['im_member_since'] ?? '');
        $p30 = (string)($m['enq_id'] ?? '');
        $p31 = (string)($m['enq_receiver_name'] ?? '');
        $p32 = (string)($m['enq_receiver_email'] ?? '');
        $p33 = (string)($m['enq_receiver_mobile'] ?? '');
        $p34 = (string)($m['status'] ?? '');
        $p35 = (string)($m['is_processed'] ?? '0');
        $p36 = (string)($m['raw_data'] ?? '');
        $p37 = (string)($m['fetched_at'] ?? '');
        $p38 = (string)($m['notes'] ?? '');
        $p39 = (string)($m['created_at'] ?? '');
        $p40 = (string)($m['updated_at'] ?? '');

        if (!$stmt->bind_param(
            $types,
            $p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16,$p17,$p18,$p19,$p20,
            $p21,$p22,$p23,$p24,$p25,$p26,$p27,$p28,$p29,$p30,$p31,$p32,$p33,$p34,$p35,$p36,$p37,$p38,$p39,$p40
        )) { $errors++; continue; }

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) { 
                $saved++; 
                // Send email and notifications for new lead
                notifySalesUsers($mysqli, $m);
                // Send WhatsApp message to the lead
                sendWhatsAppMessage($m);
            }
        } else {
            $errors++;
        }
    }

    $stmt->close();
    $mysqli->close();

    echo json_encode(['success' => true, 'saved' => $saved, 'errors' => $errors]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'execution_error', 'error' => $e->getMessage(), 'line' => $e->getLine()]);
    exit;
}