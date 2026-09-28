<?php

// MSG91 WhatsApp Inbound Webhook (core PHP)
// Point MSG91 Inbound Webhook URL to this file (HTTPS recommended)
// Responds with HTTP 200 JSON

// ================== CONFIG: EDIT THESE ==================
$dbHost  = '127.0.0.1';
$dbUser  = 'u976774818_TENHJSVNC';
$dbPass  = 'I]8g&:YVp';
$dbName  = 'u976774818_TENHJSVNC';
$dbPort  = 3306; // change if needed
$table   = 'whatsapp_inboxes';
// =======================================================

header('Content-Type: application/json');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Catch all errors and return JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(200);
        echo json_encode([
            'status' => 'error',
            'message' => 'internal_error',
            'error' => $error['message']
        ]);
    }
});
set_exception_handler(function ($e) {
    http_response_code(200);
    echo json_encode(['status' => 'error', 'message' => 'internal_error', 'error' => $e->getMessage()]);
    exit;
});

// Optional health check
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    echo json_encode(['status' => 'success', 'message' => 'OK']);
    exit;
}

// Enforce POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode(['status' => 'ignored', 'message' => 'method_not_allowed']);
    exit;
}

$raw = file_get_contents('php://input');
if ($raw === false || $raw === '') {
    echo json_encode(['status' => 'ignored', 'message' => 'empty_body']);
    exit;
}

// Log the incoming payload
file_put_contents(__DIR__ . '/webhook_log.txt', date('Y-m-d H:i:s') . "\n" . $raw . "\n\n", FILE_APPEND);

$payload = json_decode($raw, true);
if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'ignored', 'message' => 'invalid_json']);
    exit;
}
if (!is_array($payload)) {
    $payload = $_POST;
}
if (!is_array($payload)) {
    echo json_encode(['status' => 'ignored', 'message' => 'invalid_payload']);
    exit;
}

// Extract variables using MSG91 Inbound Request Received format
$sender = $payload['customerNumber'] ?? $payload['sender'] ?? $payload['from'] ?? null;
$receiver = $payload['integratedNumber'] ?? $payload['receiver'] ?? $payload['to'] ?? null;

$messageText = $payload['text'] ?? null;
$messageArray = $payload['message'] ?? null;

// Parse "messages" if it exists as a JSON string (MSG91 format)
$messagesJson = $payload['messages'] ?? null;
if ($messagesJson && is_string($messagesJson)) {
    $decodedMessages = json_decode($messagesJson, true);
    if (is_array($decodedMessages) && !empty($decodedMessages)) {
        $messageArray = $decodedMessages[0];
    }
}

// Handle nested data format if present
if (!$sender && isset($payload['data']) && is_array($payload['data'])) {
    $data = $payload['data'];
    $sender = $data['customerNumber'] ?? $data['sender'] ?? null;
    $receiver = $data['integratedNumber'] ?? $data['receiver'] ?? null;
    $messageText = $data['text'] ?? null;
    if (isset($data['messages']) && is_string($data['messages'])) {
         $dec = json_decode($data['messages'], true);
         if (is_array($dec) && !empty($dec)) {
             $messageArray = $dec[0];
         }
    } else {
         $messageArray = $data['message'] ?? null;
    }
}

// Handle Button/Interactive clicks
$messageType = $payload['contentType'] ?? $payload['messageType'] ?? 'text';
if ($messageType === 'button' || $messageType === 'interactive' || $messageType === 'listReply') {
    if (isset($payload['button']) && is_string($payload['button'])) {
        $btnData = json_decode($payload['button'], true);
        if ($btnData && isset($btnData['text'])) {
            $messageText = $btnData['text'];
        }
    } elseif (isset($payload['interactive']) && is_string($payload['interactive'])) {
        $intData = json_decode($payload['interactive'], true);
        if ($intData && isset($intData['list_reply']['title'])) {
            $messageText = $intData['list_reply']['title'];
        } elseif ($intData && isset($intData['button_reply']['title'])) {
            $messageText = $intData['button_reply']['title'];
        }
    }
}

// If no sender or no message content, ignore
if (!$sender || (empty($messageText) && empty($messageArray))) {
    echo json_encode(['status' => 'ignored', 'message' => 'missing_sender_or_message']);
    exit;
}

$mediaUrl = $payload['url'] ?? null;
$msg91MessageId = $payload['uuid'] ?? $payload['message_id'] ?? $payload['id'] ?? null;

// Parse older complex array format if needed
if (empty($messageText) && is_array($messageArray)) {
    $msgType = $messageArray['type'] ?? 'text';
    if ($msgType === 'text') {
        $messageText = $messageArray['text']['body'] ?? ($messageArray['text'] ?? '');
    } elseif ($msgType === 'button') {
        $messageText = $messageArray['button']['text'] ?? $messageArray['button']['payload'] ?? 'Button Clicked';
    } elseif (in_array($msgType, ['image', 'document', 'audio', 'video'])) {
        $mediaUrl = $messageArray['media_url'] ?? ($messageArray[$msgType]['link'] ?? null);
        $messageText = $messageArray['caption'] ?? null;
    }
    $messageType = $msgType;
}

// Convert message text array to string if necessary
if (is_array($messageText)) {
    $messageText = json_encode($messageText);
}

// Connect to database
try {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
    if ($mysqli->connect_errno) {
        echo json_encode(['status' => 'error', 'message' => 'db_connect_error', 'error' => $mysqli->connect_error]);
        exit;
    }
    $mysqli->set_charset('utf8mb4');
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'db_connect_exception', 'error' => $e->getMessage()]);
    exit;
}

$receivedAt = gmdate('Y-m-d H:i:s'); // or use timezone specific time
$createdAt = gmdate('Y-m-d H:i:s');
$updatedAt = gmdate('Y-m-d H:i:s');
$isRead = 0;

// Determine sender name
$senderName = null;
$numWithout91 = preg_replace('/^91/', '', $sender);

// 1. Check Customers
$res = $mysqli->query("SELECT name FROM customers WHERE phone = '$sender' OR phone = '$numWithout91' LIMIT 1");
if ($res && $res->num_rows > 0) {
    $senderName = $res->fetch_assoc()['name'];
}

// 2. Check Sales Records
if (!$senderName) {
    $res = $mysqli->query("SELECT leads_name, contact_person FROM sales_records WHERE contact_number = '$sender' OR contact_number = '$numWithout91' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $senderName = $row['leads_name'] ?: $row['contact_person'];
    }
}

// 3. Check Campaign Members
if (!$senderName) {
    $res = $mysqli->query("SELECT name FROM whatsapp_campaign_members WHERE phone_number = '$sender' OR phone_number = '$numWithout91' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $senderName = $res->fetch_assoc()['name'];
    }
}

$sql = "INSERT INTO `$table` 
    (sender_number, sender_name, receiver_number, message_text, media_url, message_type, msg91_message_id, is_read, received_at, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'prepare_failed', 'error' => $mysqli->error]);
    exit;
}

try {
    $stmt->bind_param(
        "sssssssisss",
        $sender,
        $senderName,
        $receiver,
        $messageText,
        $mediaUrl,
        $messageType,
        $msg91MessageId,
        $isRead,
        $receivedAt,
        $createdAt,
        $updatedAt
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'message_saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'insert_failed', 'error' => $stmt->error]);
    }
    
    $stmt->close();
    $mysqli->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'execution_error', 'error' => $e->getMessage()]);
}
