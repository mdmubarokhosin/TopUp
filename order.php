<?php 
include 'common/header.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'];

if(isset($_POST['action']) && $_POST['action'] == 'create_order') {
    $gid = (int)$_POST['game_id'];
    $pid = (int)$_POST['product_id'];
    $amt = (float)$_POST['amount'];
    $ply = isset($_POST['player_id']) ? $conn->real_escape_string($_POST['player_id']) : '';
    $met = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';
    $trx = isset($_POST['trx_id']) ? $conn->real_escape_string($_POST['trx_id']) : '';
    $wal = isset($_POST['wallet_number']) ? $conn->real_escape_string($_POST['wallet_number']) : '';

    // Check Game Type
    $gameType = 'uid'; 
    if($gid > 0) {
        $gRes = $conn->query("SELECT type FROM games WHERE id=$gid");
        if($gRes && $gRes->num_rows > 0) {
            $gameType = $gRes->fetch_assoc()['type'];
        }
    }

    if($met === 'Wallet') {
        // ওয়ালেট ব্যালেন্স চেক
        $uRes = $conn->query("SELECT balance FROM users WHERE id=$uid");
        $current_balance = ($uRes->num_rows > 0) ? (float)$uRes->fetch_assoc()['balance'] : 0;

        if($current_balance < $amt) {
            echo "<script>alert('Insufficient Wallet Balance!'); history.back();</script>";
            exit;
        }

        // ব্যালেন্স কেটে নেওয়া
        $conn->query("UPDATE users SET balance = balance - $amt WHERE id=$uid");
        $trx = "WAL-" . strtoupper(uniqid());
        $status = ($gameType == 'voucher') ? 'completed' : 'pending';

        // অর্ডার ইনসার্ট
        $stmt = $conn->prepare("INSERT INTO orders (user_id, game_id, product_id, amount, player_id, transaction_id, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'Wallet', ?)");
        $stmt->bind_param("iiidsss", $uid, $gid, $pid, $amt, $ply, $trx, $status);
        
        if($stmt->execute()){
            $inserted_id = $conn->insert_id;
            if($gameType == 'voucher') {
                // অটো কোড লিংক করা
                $vRes = $conn->query("SELECT id FROM redeem_codes WHERE product_id=$pid AND order_id IS NULL LIMIT 1");
                if($vRes && $vRes->num_rows > 0) {
                    $vRow = $vRes->fetch_assoc();
                    $conn->query("UPDATE redeem_codes SET order_id=$inserted_id WHERE id=".$vRow['id']);
                }
                echo "<script>alert('Purchased Successfully via Wallet!'); window.location.href='mycode.php';</script>";
            } else {
                echo "<script>alert('Order Placed via Wallet!'); window.location.href='order.php';</script>";
            }
            exit;
        }
    } else {
        // প্রথাগত ম্যানুয়াল পেমেন্ট
        if($gid == 0) {
            $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount, method, wallet_number, trx_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("idsss", $uid, $amt, $met, $wal, $trx);
            if($stmt->execute()){
                echo "<script>alert('Add Money Request Submitted!'); window.location.href='payment_history.php';</script>";
                exit;
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, game_id, product_id, amount, player_id, transaction_id, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iiidsss", $uid, $gid, $pid, $amt, $ply, $trx, $met);
            if($stmt->execute()){
                echo "<script>alert('Manual Order Request Received!'); window.location.href='order.php';</script>";
                exit;
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-6 mb-24"> <!-- নিচে সামান্য মার্জিন বাড়ানো হয়েছে ফুটারের জন্য -->
    <h2 class="text-xl font-bold mb-4 border-l-4 border-blue-600 pl-3">My Orders</h2>
    <div class="space-y-4">
        <?php 
        // শুধুমাত্র নরমাল অর্ডারগুলো (ভাউচার ছাড়া) দেখানোর জন্য কুয়েরি
        $sql = "SELECT o.*, g.name as gname, g.image as gimg, p.name as pname 
                FROM orders o 
                JOIN games g ON o.game_id = g.id 
                JOIN products p ON o.product_id = p.id 
                WHERE o.user_id=$uid AND g.type != 'voucher' 
                ORDER BY o.id DESC";
        $res = $conn->query($sql);
        
        if($res && $res->num_rows > 0):
        while($row = $res->fetch_assoc()): 
            $statusColor = 'yellow';
            if($row['status'] == 'completed') $statusColor = 'green';
            if($row['status'] == 'cancelled') $statusColor = 'red';
        ?>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex gap-4">
                <img src="<?php echo $row['gimg']; ?>" class="w-16 h-16 rounded-lg object-cover">
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800"><?php echo $row['gname']; ?></h3>
                    <p class="text-sm text-gray-500"><?php echo $row['pname']; ?></p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-bold text-blue-600"><?php echo getSetting($conn, 'currency').$row['amount']; ?></span>
                        <span class="text-xs px-2 py-1 rounded bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-700 font-bold uppercase"><?php echo $row['status']; ?></span>
                    </div>
                </div>
            </div>
            <details class="mt-2 text-xs text-gray-500 border-t pt-2">
                <summary class="cursor-pointer text-blue-500 mb-1">View Details</summary>
                <p>Order ID: #<?php echo $row['id']; ?></p>
                <p>Player ID: <?php echo $row['player_id']; ?></p>
                <p>TrxID: <?php echo $row['transaction_id']; ?></p>
                <p>Date: <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></p>
            </details>
        </div>
        <?php endwhile; 
        else: echo "<div class='text-center text-gray-500 mt-10'>No orders found.</div>";
        endif; ?>
    </div>
</div>

<?php 
// bottom এবং footer দুটি ফাইলই অন্তর্ভুক্ত করা হলো
include 'common/bottom.php'; 
include 'common/footer.php'; 
?>
