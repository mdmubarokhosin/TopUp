<?php
/**
 * Success Page - Bohudur Payment Verification & Processing
 * Verifies payment via Bohudur Query API, then executes it
 * Handles: Wallet deposits and Game Top-up orders
 */
ini_set('display_errors', 0);
error_reporting(0);

// Load config
if (file_exists('common/config.php')) {
    include 'common/config.php';
} else if (file_exists('../common/config.php')) {
    include '../common/config.php';
} else {
    die("<b>Error:</b> config.php file not found.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Load Bohudur API Key
// ============================================================
$bohudur_api_key = "";
if (isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'bohudur_api_key');
}
if (empty($bohudur_api_key) && isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'shohoj_api_key');
}

// ============================================================
// Check Session Data
// ============================================================
if (!isset($_SESSION['temp_order'])) {
    header("Location: index.php");
    exit();
}

$temp = $_SESSION['temp_order'];
$paymentkey = $temp['paymentkey'] ?? '';
$user_id = (int)($_SESSION['user_id'] ?? 0);
$game_id = (int)($temp['game_id'] ?? 0);
$prod_id = (int)($temp['product_id'] ?? 0);
$amount = (float)($temp['amount'] ?? 0);
$player_id = !empty($temp['player_id']) ? $temp['player_id'] : 'Auto Payment';

if ($user_id <= 0 || empty($paymentkey)) {
    unset($_SESSION['temp_order']);
    header("Location: index.php");
    exit();
}

// ============================================================
// Step 1: Query Payment Status from Bohudur
// ============================================================
$payment_verified = false;
$payment_executed = false;

if (!empty($bohudur_api_key)) {
    // Query payment status
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
    
    if (isset($query_result['status'])) {
        $payment_status = $query_result['status'];
        
        // Payment is COMPLETED - need to execute
        if ($payment_status === 'COMPLETED') {
            $payment_verified = true;
            
            // Step 2: Execute Payment (one-time only)
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
            
            $exec_result = json_decode($exec_response, true);
            
            if (isset($exec_result['status']) && $exec_result['status'] === 'EXECUTED') {
                $payment_executed = true;
            }
            // If already executed (3108), that's also fine - idempotent
            if (isset($exec_result['responseCode']) && $exec_result['responseCode'] == 3108) {
                $payment_executed = true;
            }
        }
        // If already EXECUTED (from webhook or previous visit)
        elseif ($payment_status === 'EXECUTED') {
            $payment_verified = true;
            $payment_executed = true;
        }
    }
}

// ============================================================
// Step 3: Process Order in Database (only if payment verified)
// ============================================================
if ($payment_verified && $payment_executed && $conn) {
    
    // Check if already processed
    $check_stmt = $conn->prepare("SELECT id FROM orders WHERE transaction_id = ? LIMIT 1");
    $check_stmt->bind_param("s", $paymentkey);
    $check_stmt->execute();
    $check_res = $check_stmt->get_result();
    
    if ($check_res->num_rows == 0) {
        // Fresh payment - process it
        
        if ($prod_id == 0 && $game_id == 0) {
            // ==========================================
            // WALLET DEPOSIT
            // ==========================================
            $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $user_id);
            $stmt->execute();
            
            // Use prepared statement for insert
            $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount, method, trx_id, status, created_at) VALUES (?, ?, 'Bohudur Pay', ?, 'completed', NOW())");
            $stmt->bind_param("ids", $user_id, $amount, $paymentkey);
            $stmt->execute();
            
            unset($_SESSION['temp_order']);
            
            // Redirect to avoid double processing
            header("Location: addmoney.php?msg=success");
            exit();
        } else {
            // ==========================================
            // GAME TOP-UP ORDER
            // ==========================================
            $gameType = 'uid';
            $gstmt = $conn->prepare("SELECT type FROM games WHERE id = ?");
            $gstmt->bind_param("i", $game_id);
            $gstmt->execute();
            $gres = $gstmt->get_result();
            if ($gres && $gres->num_rows > 0) {
                $grow = $gres->fetch_assoc();
                $gameType = $grow['type'];
            }
            
            $status = ($gameType == 'voucher') ? 'completed' : 'processing';
            
            $stmt = $conn->prepare("INSERT INTO orders (user_id, game_id, product_id, amount, player_id, transaction_id, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Bohudur Pay', ?, NOW())");
            $stmt->bind_param("iiidssss", $user_id, $game_id, $prod_id, $amount, $player_id, $paymentkey, $status);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            if ($gameType == 'voucher' && $order_id > 0) {
                // Auto-assign voucher code
                $vstmt = $conn->prepare("SELECT id FROM redeem_codes WHERE product_id = ? AND (order_id IS NULL OR order_id = 0) LIMIT 1");
                $vstmt->bind_param("i", $prod_id);
                $vstmt->execute();
                $vres = $vstmt->get_result();
                if ($vres && $vres->num_rows > 0) {
                    $vrow = $vres->fetch_assoc();
                    $ustmt = $conn->prepare("UPDATE redeem_codes SET order_id = ? WHERE id = ?");
                    $ustmt->bind_param("ii", $order_id, $vrow['id']);
                    $ustmt->execute();
                }
                unset($_SESSION['temp_order']);
                header("Location: mycode.php?msg=success");
                exit();
            }
            
            unset($_SESSION['temp_order']);
            header("Location: order.php?msg=success");
            exit();
        }
    } else {
        // Already processed - just clean up
        unset($_SESSION['temp_order']);
        header("Location: order.php");
        exit();
    }
}

// ============================================================
// Step 4: Payment Not Completed Yet - Show Status Page
// ============================================================
$page_msg = "Payment is being verified...";
$page_icon = "fa-spinner fa-spin";
$page_color = "#3b82f6";

if (!$payment_verified) {
    $page_msg = "Payment is still pending or was not completed.";
    $page_icon = "fa-clock";
    $page_color = "#f59e0b";
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$base_url = rtrim($protocol . $domain . dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; text-align: center; max-width: 420px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: #fff; }
        h2 { color: #1e293b; margin-bottom: 8px; font-size: 1.3rem; }
        p { color: #64748b; margin-bottom: 20px; line-height: 1.5; font-size: 0.9rem; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #f1f5f9; color: #475569; margin-left: 8px; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon" style="background: <?php echo $page_color; ?>;">
            <i class="fas <?php echo $page_icon; ?>"></i>
        </div>
        <h2><?php echo htmlspecialchars($page_msg); ?></h2>
        <p>If you've already completed the payment, it may take a few moments to verify. You can check your order history.</p>
        <div>
            <a href="<?php echo $base_url; ?>/index.php" class="btn btn-primary">Go Home</a>
            <a href="<?php echo $base_url; ?>/order.php" class="btn btn-secondary">My Orders</a>
        </div>
    </div>
</body>
</html>
