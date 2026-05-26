<?php
include 'common/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = (int)$_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $player_id = $_POST['player_id'] ?? '';

    // Validate player_id
    if (empty($player_id)) {
        $_SESSION['error'] = "Player ID is required!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Get product price safely
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        $_SESSION['error'] = "Product not found!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    $price = (float)$product['price'];

    // Check user balance safely
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $current_balance = (float)$user['balance'];

    if ($current_balance >= $price) {
        // Deduct from wallet
        $new_balance = $current_balance - $price;
        $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_balance, $uid);
        $stmt->execute();

        // Insert order
        $trx = "WAL-" . strtoupper(uniqid());
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, player_id, amount, status, transaction_id, payment_method) VALUES (?, ?, ?, ?, 'completed', ?, 'Wallet')");
        $stmt->bind_param("iisdss", $uid, $product_id, $player_id, $price, $trx);
        $stmt->execute();

        $_SESSION['msg'] = "Order placed successfully!";
        header("Location: order.php");
    } else {
        $_SESSION['error'] = "Insufficient balance! Please add money.";
        header("Location: addmoney.php");
    }
    exit;
}
?>
