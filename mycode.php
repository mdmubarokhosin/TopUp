<?php 
include 'common/header.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$uid = (int)$_SESSION['user_id'];

// ==========================================
// ১. অর্ডার রিসিভ এবং ডাটাবেজে পাঠানোর সিস্টেম
// ==========================================
if(isset($_POST['action']) && $_POST['action'] == 'create_order') {
    $gid = (int)$_POST['game_id'];
    $pid = (int)$_POST['product_id'];
    $amt = (float)$_POST['amount'];
    $ply = isset($_POST['player_id']) ? $conn->real_escape_string($_POST['player_id']) : '';
    $met = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';
    $trx = isset($_POST['trx_id']) ? $conn->real_escape_string($_POST['trx_id']) : '';
    $wal = isset($_POST['wallet_number']) ? $conn->real_escape_string($_POST['wallet_number']) : '';

    // গেম টাইপ চেক (ভাউচার নাকি অন্য কিছু)
    $gameType = 'uid'; 
    if($gid > 0) {
        $gRes = $conn->query("SELECT type FROM games WHERE id=$gid");
        if($gRes && $gRes->num_rows > 0) {
            $gameType = $gRes->fetch_assoc()['type'];
        }
    }

    // ভাউচারের ক্ষেত্রে স্টক চেক
    if($gameType == 'voucher') {
        $checkStock = $conn->query("SELECT COUNT(id) as total FROM redeem_codes WHERE product_id=$pid AND (order_id IS NULL OR order_id = 0)");
        $stockRow = $checkStock->fetch_assoc();
        if($stockRow['total'] < 1) {
            echo "<script>alert('Sorry, Out of Stock!'); window.location.href='mycode.php';</script>";
            exit;
        }
    }

    // ওয়ালেট পেমেন্ট প্রসেস
    if($met === 'Wallet') {
        $uRes = $conn->query("SELECT balance FROM users WHERE id=$uid");
        $current_balance = ($uRes->num_rows > 0) ? (float)$uRes->fetch_assoc()['balance'] : 0;

        if($current_balance < $amt) {
            echo "<script>alert('Insufficient Wallet Balance!'); window.location.href='mycode.php';</script>";
            exit;
        }

        // ব্যালেন্স কাটা
        $conn->query("UPDATE users SET balance = balance - $amt WHERE id=$uid");
        $trx = "WAL-" . strtoupper(uniqid());
        $status = ($gameType == 'voucher') ? 'completed' : 'pending';

        // অর্ডার ইনসার্ট
        $stmt = $conn->prepare("INSERT INTO orders (user_id, game_id, product_id, amount, player_id, transaction_id, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'Wallet', ?)");
        $stmt->bind_param("iiidsss", $uid, $gid, $pid, $amt, $ply, $trx, $status);
        
        if($stmt->execute()){
            $inserted_id = $conn->insert_id;
            if($gameType == 'voucher') {
                // স্টক থেকে কোড লিংক করা
                $vRes = $conn->query("SELECT id FROM redeem_codes WHERE product_id=$pid AND (order_id IS NULL OR order_id = 0) LIMIT 1");
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
        // ম্যানুয়াল পেমেন্ট (bKash/Nagad/Rocket)
        $stmt = $conn->prepare("INSERT INTO orders (user_id, game_id, product_id, amount, player_id, transaction_id, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiidsss", $uid, $gid, $pid, $amt, $ply, $trx, $met);
        if($stmt->execute()){
            echo "<script>alert('Manual Order Received! Code will release after admin approval.'); window.location.href='mycode.php';</script>";
            exit;
        }
    }
}
?>

<!-- ==========================================
২. ভিউ সেকশন: যেখানে ইউজার তার কেনা কোডগুলো দেখবে
========================================== -->
<div class="container mx-auto px-4 py-6 mb-24">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold border-l-4 border-blue-600 pl-3">My Vouchers / UniPin Codes</h2>
        <a href="https://shop.garena.my/app" target="_blank" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm shadow hover:bg-red-600 transition flex items-center gap-2">
            <i class="fa-solid fa-up-right-from-square"></i> Redeem Site
        </a>
    </div>

    <div class="space-y-4">
        <?php 
        // ইউজারের সফল হওয়া সকল ভাউচার কোড দেখানোর কুয়েরি
        $sql = "SELECT rc.code, p.name as pname, g.name as gname, o.id as order_id, o.amount, o.transaction_id, o.created_at 
                FROM redeem_codes rc 
                LEFT JOIN orders o ON rc.order_id = o.id 
                LEFT JOIN products p ON o.product_id = p.id
                LEFT JOIN games g ON o.game_id = g.id
                WHERE o.user_id = $uid 
                AND rc.order_id IS NOT NULL 
                AND rc.order_id > 0
                ORDER BY rc.id DESC";
        
        $res = $conn->query($sql);
        if($res && $res->num_rows > 0):
        while($row = $res->fetch_assoc()): 
            $redeemCode = htmlspecialchars($row['code']); 
            $gameName = !empty($row['gname']) ? $row['gname'] : 'UniPin / Voucher';
            $productName = !empty($row['pname']) ? $row['pname'] : 'Premium Package';
        ?>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="flex gap-4 items-center mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 text-sm"><?php echo $gameName; ?></h3>
                        <p class="text-xs text-gray-500"><?php echo $productName; ?></p>
                        <p class="text-[10px] text-gray-400 mt-1">Order #<?php echo $row['order_id']; ?> • <?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : date('d M Y'); ?></p>
                    </div>
                    <div class="text-right">
                         <span class="font-bold text-blue-600"><?php echo getSetting($conn, 'currency').$row['amount']; ?></span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 p-3 rounded-lg flex justify-between items-center gap-3">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Redeem Code</p>
                        <code class="text-blue-700 font-mono font-bold text-sm select-all break-all"><?php echo $redeemCode; ?></code>
                    </div>
                    <button type="button" onclick="copyToClipboard('<?php echo addslashes($redeemCode); ?>')" class="bg-white text-gray-600 w-10 h-10 rounded-full shadow hover:text-blue-600 hover:shadow-md transition flex items-center justify-center active:scale-95">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>

                <div class="mt-3 text-[10px] text-gray-400 text-center">
                    TrxID: <span class="font-mono"><?php echo !empty($row['transaction_id']) ? $row['transaction_id'] : 'N/A'; ?></span>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="text-center py-10">
                <i class="fa-solid fa-ticket text-gray-300 text-5xl mb-3"></i>
                <p class="text-gray-500">No UniPin vouchers purchased yet.</p>
                <a href="index.php" class="text-blue-500 text-sm font-bold mt-2 inline-block">Buy Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Code copied to clipboard.');
            }).catch(err => { fallbackCopy(text); });
        } else { fallbackCopy(text); }
    }
    function fallbackCopy(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed"; textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus(); textArea.select();
        try { document.execCommand('copy'); alert('Code copied to clipboard.'); } catch (err) { alert('Failed to copy code.'); }
        document.body.removeChild(textArea);
    }
</script>

<?php 
include 'common/bottom.php'; 
include 'common/footer.php'; 
?>
