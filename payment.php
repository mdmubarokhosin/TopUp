<?php
/**
 * Payment Page - Bohudur Payment Gateway Integration
 * Creates a payment session and redirects user to Bohudur checkout
 */
session_start();

// Load config
if (file_exists('common/config.php')) {
    include 'common/config.php';
} else if (file_exists('../common/config.php')) {
    include '../common/config.php';
} else {
    die("<b>Error:</b> config.php file not found.");
}

// Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ============================================================
// Load Bohudur API Key from Database
// ============================================================
$bohudur_api_key = "";
if (isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'bohudur_api_key');
}

// Fallback: Try old key name for backward compatibility
if (empty($bohudur_api_key) && isset($conn) && $conn) {
    $bohudur_api_key = getSetting($conn, 'shohoj_api_key');
}

if (empty($bohudur_api_key)) {
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif; max-width:500px; margin-left:auto; margin-right:auto; padding:20px; border:1px solid #ecc94b; background:#fefcbf; border-radius:8px;'>";
    echo "<h2 style='color:#b7791f;'>Payment Gateway Not Configured!</h2>";
    echo "<p style='color:#744210; font-size:13px;'>Please set your Bohudur API Key in Admin Panel > Settings > Payment Gateway.</p>";
    echo "<button onclick='history.back()' style='padding:10px 20px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; border:none; cursor:pointer; font-weight:bold; margin-top:10px;'>Go Back</button>";
    echo "</div>";
    exit();
}

// ============================================================
// Get Order Parameters
// ============================================================
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$game_id    = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
$player_id  = isset($_GET['player_id']) ? trim($_GET['player_id']) : '';
$sub_email  = isset($_GET['sub_email']) ? trim($_GET['sub_email']) : '';
$amount     = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;

if (!$product_id && $amount <= 0) { 
    header("Location: index.php"); 
    exit(); 
}

// Verify product price from database
if ($product_id > 0 && $conn) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (isset($product) && $product) { 
        $amount = (float)$product['price'];
    } else if ($amount <= 0) {
        die("Product not found or invalid amount."); 
    }
}

// ============================================================
// Build Callback URLs
// ============================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 
$base_url = rtrim($protocol . $domain . $script_path, '/');

$redirect_url = $base_url . "/success.php";
$cancel_url   = $base_url . "/cancel.php";
$webhook_url  = $base_url . "/webhook.php";

// ============================================================
// Get User Info
// ============================================================
$user_id = (int)$_SESSION['user_id'];
$user_name = "Customer";
$user_email = $sub_email ?: "customer@example.com";

if ($conn) {
    $ustmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $ustmt->bind_param("i", $user_id);
    $ustmt->execute();
    $ures = $ustmt->get_result();
    if ($ures && $ures->num_rows > 0) {
        $urow = $ures->fetch_assoc();
        $user_name = $urow['name'] ?: "Customer";
        if (!empty($urow['email'])) $user_email = $urow['email'];
    }
}

// ============================================================
// Call Bohudur Create Payment API
// ============================================================
$api_url = "https://request.bohudur.one/create/v2/";

$data = [
    'full_name'    => $user_name,
    'email'         => $user_email,
    'amount'        => $amount,
    'return_type'   => 'GET',
    'redirect_url'  => $redirect_url,
    'cancel_url'    => $cancel_url,
    'metadata'      => [
        'user_id'    => $user_id,
        'product_id' => $product_id,
        'game_id'    => $game_id,
        'player_id'  => $player_id
    ],
    'webhook'       => [
        'success'    => $webhook_url,
        'cancel'     => $base_url . "/cancel.php"
    ]
];

$headers = [
    "Content-Type: application/json",
    "AH-BOHUDUR-API-KEY: " . trim($bohudur_api_key)
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}
curl_close($ch);

$res = json_decode($response, true);

// ============================================================
// Handle Response
// ============================================================
if (isset($res['status']) && $res['status'] === 'success' && isset($res['payment_url']) && isset($res['paymentkey'])) {
    // Store order data in session
    $_SESSION['temp_order'] = [
        'paymentkey' => $res['paymentkey'],
        'product_id' => $product_id,
        'game_id'    => $game_id,
        'player_id'  => $player_id,
        'amount'     => $amount
    ];
    
    // Redirect to Bohudur payment page
    header("Location: " . $res['payment_url']);
    exit();
} else {
    $error_msg = $res['message'] ?? 'API Connection/Response Error';
    $error_code = $res['responseCode'] ?? 'N/A';
    
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif; max-width:500px; margin-left:auto; margin-right:auto; padding:20px; border:1px solid #ecc94b; background:#fefcbf; border-radius:8px;'>";
    echo "<h2 style='color:#b7791f;'>Payment Initialization Failed!</h2>";
    echo "<p>Gateway Response: <b>" . htmlspecialchars($error_msg) . "</b></p>";
    echo "<p style='color:#744210; font-size:13px;'>Error Code: " . htmlspecialchars($error_code) . "</p>";
    echo "<p style='color:#744210; font-size:13px;'>Please ensure your Bohudur API Key is valid and your domain is approved.</p>";
    echo "<button onclick='history.back()' style='padding:10px 20px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; border:none; cursor:pointer; font-weight:bold; margin-top:10px;'>Go Back</button>";
    echo "</div>";
}
?>
