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

// Handle nested data format if present
if (!$sender && isset($payload['data']) && is_array($payload['data'])) {
    $data = $payload['data'];
    $sender = $data['customerNumber'] ?? $data['sender'] ?? null;
    $receiver = $data['integratedNumber'] ?? $data['receiver'] ?? null;
    $messageText = $data['text'] ?? null;
    $messageArray = $data['message'] ?? null;
}

// If no sender or no message content, ignore
if (!$sender || (!$messageText && !$messageArray)) {
    echo json_encode(['status' => 'ignored', 'message' => 'missing_sender_or_message']);
    exit;
}

$messageType = $payload['contentType'] ?? $payload['messageType'] ?? 'text';
$mediaUrl = $payload['url'] ?? null;
$msg91MessageId = $payload['uuid'] ?? $payload['message_id'] ?? $payload['id'] ?? null;

// Parse older complex array format if needed
if (!$messageText && is_array($messageArray)) {
    $msgType = $messageArray['type'] ?? 'text';
    if ($msgType === 'text') {
        $messageText = $messageArray['text']['body'] ?? ($messageArray['text'] ?? '');
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

$sql = "INSERT INTO `$table` 
    (sender_number, receiver_number, message_text, media_url, message_type, msg91_message_id, is_read, received_at, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'prepare_failed', 'error' => $mysqli->error]);
    exit;
}

try {
    $stmt->bind_param(
        "ssssssisss",
        $sender,
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
