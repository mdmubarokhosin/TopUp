<?php
/**
 * Bohudur Webhook Handler
 * Receives POST notifications from Bohudur when a payment is completed or cancelled
 */
header('Content-Type: application/json');

// Load config
if (file_exists('common/config.php')) {
    include 'common/config.php';
} else if (file_exists('../common/config.php')) {
    include '../common/config.php';
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Config not found']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get webhook payload
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Verify required fields
$paymentkey = $payload['paymentkey'] ?? '';
$status = $payload['status'] ?? '';

if (empty($paymentkey) || empty($status)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing paymentkey or status']);
    exit;
}

// Load API key
$bohudur_api_key = "";
if (isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'bohudur_api_key');
}
if (empty($bohudur_api_key) && isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'shohoj_api_key');
}

// Only process COMPLETED payments
if ($status === 'COMPLETED' && !empty($bohudur_api_key)) {
    // Verify payment status with Bohudur Query API
    $query_data = json_encode(['paymentkey' => $paymentkey]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://request.bohudur.one/query/v2/");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "AH-BOHUDUR-API-KEY: " . trim($bohudur_api_key)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $query_response = curl_exec($ch);
    curl_close($ch);
    
    $query_result = json_decode($query_response, true);
    
    if (isset($query_result['status']) && ($query_result['status'] === 'COMPLETED' || $query_result['status'] === 'EXECUTED')) {
        
        // Check if already processed
        $check = $conn->prepare("SELECT id FROM orders WHERE transaction_id = ? LIMIT 1");
        $check->bind_param("s", $paymentkey);
        $check->execute();
        
        if ($check->get_result()->num_rows == 0) {
            // Need session data to know what was ordered
            // For webhook, we rely on the metadata from Bohudur
            // But since we store order data in session, webhook serves as backup
            
            // Try to execute the payment (idempotent)
            $exec_data = json_encode(['paymentkey' => $paymentkey]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://request.bohudur.one/execute/v2/");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $exec_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "AH-BOHUDUR-API-KEY: " . trim($bohudur_api_key)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $exec_response = curl_exec($ch);
            curl_close($ch);
            
            // Log webhook (optional: could be stored in DB)
            $log_file = __DIR__ . '/webhook_log.txt';
            $log_entry = date('Y-m-d H:i:s') . " | paymentkey: " . $paymentkey . " | status: " . $status . " | amount: " . ($payload['amount'] ?? 'N/A') . "\n";
            @file_put_contents($log_file, $log_entry, FILE_APPEND);
        }
    }
}

// Always return 200 OK to acknowledge receipt
http_response_code(200);
echo json_encode(['status' => 'ok']);
