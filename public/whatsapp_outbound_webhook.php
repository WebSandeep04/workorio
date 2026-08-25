<?php

// MSG91 WhatsApp Outbound Webhook (core PHP)
// Point MSG91 Outbound Webhook URL to this file (HTTPS recommended)
// Responds with HTTP 200 JSON

// ================== CONFIG: EDIT THESE ==================
$dbHost  = '127.0.0.1';
$dbUser  = 'u976774818_TENHJSVNC';
$dbPass  = 'I]8g&:YVp';
$dbName  = 'u976774818_TENHJSVNC';
$dbPort  = 3306; // change if needed
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

// Log payload for debugging
file_put_contents(__DIR__ . '/msg91_webhook_payload.log', date('Y-m-d H:i:s') . " - " . json_encode($payload) . "\n", FILE_APPEND);

// Map variables from payload (handles default MSG91 structure or custom variables)
$requestId = $payload['requestId'] ?? $payload['request_id'] ?? null;
$customerNumber = $payload['customerNumber'] ?? $payload['mobile'] ?? $payload['recipient'] ?? null;
$status = $payload['status'] ?? $payload['eventName'] ?? null;
$failureReason = $payload['failureReason'] ?? $payload['reason'] ?? null;

// Handle nested data format if present
if (!$requestId && isset($payload['data']) && is_array($payload['data'])) {
    $data = $payload['data'];
    $requestId = $data['requestId'] ?? $data['request_id'] ?? null;
    $customerNumber = $data['customerNumber'] ?? $data['mobile'] ?? null;
    $status = $data['status'] ?? $data['eventName'] ?? null;
    $failureReason = $data['failureReason'] ?? $data['reason'] ?? null;
}

// If no request ID or customer number, ignore
if (!$requestId || !$customerNumber) {
    echo json_encode(['status' => 'ignored', 'message' => 'missing_request_id_or_customer_number']);
    exit;
}

// Handle MSG91 test run payload
if ($requestId === '{{requestId}}' || $customerNumber === '{{customerNumber}}') {
    echo json_encode(['status' => 'success', 'message' => 'webhook_test_successful']);
    exit;
}

// Standardize status format (capitalize first letter)
if ($status) {
    $status = ucfirst(strtolower($status));
} else {
    $status = 'Unknown';
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

$updatedAt = gmdate('Y-m-d H:i:s');
$numWithout91 = preg_replace('/^91/', '', $customerNumber);

// Find campaign by request_id
$stmt = $mysqli->prepare("SELECT id FROM whatsapp_campaigns WHERE request_id = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'prepare_failed', 'error' => $mysqli->error]);
    exit;
}
$stmt->bind_param("s", $requestId);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $campaignId = $res->fetch_assoc()['id'];
    $stmt->close();

    // Update campaign member status
    $sql = "UPDATE whatsapp_campaign_members SET status = ?, error_message = ?, updated_at = ? WHERE whatsapp_campaign_id = ? AND (phone_number = ? OR phone_number LIKE ?)";
    $updateStmt = $mysqli->prepare($sql);
    
    if (!$updateStmt) {
        echo json_encode(['status' => 'error', 'message' => 'update_prepare_failed', 'error' => $mysqli->error]);
        exit;
    }

    $likeParam = "%{$numWithout91}";
    $updateStmt->bind_param("ssisss", $status, $failureReason, $updatedAt, $campaignId, $customerNumber, $likeParam);
    
    if ($updateStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'member_status_updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'update_failed', 'error' => $updateStmt->error]);
    }
    
    $updateStmt->close();
} else {
    // Campaign not found (happens during MSG91 test runs when they use dummy IDs)
    echo json_encode(['status' => 'success', 'message' => 'campaign_not_found_but_webhook_is_working']);
    $stmt->close();
}

$mysqli->close();
