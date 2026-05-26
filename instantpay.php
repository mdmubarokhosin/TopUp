<?php 
include 'common/header.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if($_SERVER['REQUEST_METHOD'] != 'POST') { header("Location: index.php"); exit; }

$uid = $_SESSION['user_id'];

// Get User Balance
$uRes = $conn->query("SELECT balance FROM users WHERE id=$uid");
$current_balance = ($uRes && $uRes->num_rows > 0) ? (float)$uRes->fetch_assoc()['balance'] : 0;

// Receive Data
$total = isset($_POST['total_amount']) ? $_POST['total_amount'] : 0;
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$player_id = isset($_POST['player_id']) ? $_POST['player_id'] : '';
$game_name = isset($_POST['game_name']) ? $_POST['game_name'] : '';
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

// Resolve Product Name & FIX Game ID issue
$product_name = "";
if($product_id != 0) {
    $prod = $conn->query("SELECT name, game_id FROM products WHERE id=$product_id")->fetch_assoc();
    $product_name = $prod ? $prod['name'] : 'Unknown Product';
    if(empty($game_id) || $game_id == 0) {
        $game_id = $prod['game_id']; 
    }
} else {
    $product_name = "Balance Add Request";
    if(empty($game_name)) $game_name = "Wallet";
}
?>

<div class="container mx-auto px-4 py-6 mb-20">
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6 text-center border-t-4 border-blue-600">
        <p class="text-gray-500 mb-1 text-sm uppercase font-bold">Total Payable Amount</p>
        <h1 class="text-4xl font-bold text-blue-600 my-2"><?php echo getSetting($conn, 'currency').$total; ?></h1>
        <div class="inline-block bg-gray-100 px-3 py-1 rounded-full text-xs text-gray-600 font-medium">
            <?php echo $game_name; ?> • <?php echo $product_name; ?>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-credit-card text-blue-500"></i> Select Payment Type
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div id="btnAutoPay" onclick="togglePaymentType('auto')" class="cursor-pointer border-2 border-blue-600 bg-blue-50 rounded-xl p-4 flex flex-col items-center justify-center gap-1 transition relative text-center">
                <span class="text-blue-600 text-2xl"><i class="fa-solid fa-bolt text-amber-500"></i></span>
                <span class="font-bold text-gray-800 text-sm">Auto Payment</span>
                <span class="text-[10px] text-gray-400">Instant Automated Delivery</span>
                <div id="checkAutoPay" class="absolute top-2 right-2 text-blue-600 block">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div id="btnManualPay" onclick="togglePaymentType('manual')" class="cursor-pointer border-2 border-gray-100 rounded-xl p-4 flex flex-col items-center justify-center gap-1 transition relative text-center">
                <span class="text-gray-600 text-2xl"><i class="fa-solid fa-hand"></i></span>
                <span class="font-bold text-gray-800 text-sm">Manual / Wallet</span>
                <span class="text-[10px] text-gray-400">Manual Trx or Wallet Balance</span>
                <div id="checkManualPay" class="absolute top-2 right-2 text-blue-600 hidden">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div id="autoPaySection" class="block">
        <form action="payment.php" method="GET">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $total; ?>">
            <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
            <input type="hidden" name="sub_email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
            
            <div class="bg-white p-6 rounded-xl shadow-lg border border-blue-100">
                <p class="text-sm text-gray-600 mb-4 text-center">You will be redirected to our secure automated gateway. Payment completes instantly.</p>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 rounded-xl font-bold shadow-lg hover:from-blue-700 hover:to-indigo-700 transition transform active:scale-95 flex justify-center items-center gap-2">
                    <i class="fa-solid fa-shield-halved"></i> Pay Now (Instant)
                </button>
            </div>
        </form>
    </div>

    <div id="manualPaySection" class="hidden">
        <form action="order.php" method="POST" id="manualPayForm">
            <input type="hidden" name="action" value="create_order">
            <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $total; ?>">
            <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">

            <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-wallet text-blue-500"></i> Select Manual Method
            </h3>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <?php if($product_id != 0): ?>
                <label class="cursor-pointer group">
                    <input type="radio" name="payment_method" value="Wallet" 
                           data-type="wallet" data-balance="<?php echo $current_balance; ?>"
                           class="peer sr-only" onchange="showPaymentDetails(this)">
                    <div class="border-2 border-gray-100 rounded-xl p-4 flex flex-col items-center justify-center gap-1 hover:bg-gray-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition relative overflow-hidden h-full">
                        <span class="text-blue-500 text-2xl mb-1"><i class="fa-solid fa-wallet"></i></span>
                        <span class="font-bold text-gray-700">My Wallet</span>
                        <span class="text-xs text-green-600 font-bold"><?php echo getSetting($conn, 'currency').$current_balance; ?></span>
                        <div class="absolute top-2 right-2 text-blue-600 opacity-0 peer-checked:opacity-100 transition">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </label>
                <?php endif; ?>

                <?php 
                $methods = $conn->query("SELECT * FROM payment_methods");
                while($m = $methods->fetch_assoc()): ?>
                <label class="cursor-pointer group">
                    <input type="radio" name="payment_method" value="<?php echo $m['name']; ?>" 
                           data-type="manual" data-number="<?php echo $m['number']; ?>" 
                           data-qr="<?php echo $m['qr_image']; ?>" data-desc="<?php echo $m['short_desc']; ?>"
                           class="peer sr-only" onchange="showPaymentDetails(this)">
                    <div class="border-2 border-gray-100 rounded-xl p-4 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition relative overflow-hidden h-full">
                        <?php if($m['logo']): ?>
                            <img src="<?php echo $m['logo']; ?>" class="h-10 object-contain">
                        <?php else: ?>
                            <span class="font-bold text-gray-700"><?php echo $m['name']; ?></span>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 text-blue-600 opacity-0 peer-checked:opacity-100 transition">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </label>
                <?php endwhile; ?>
            </div>

            <div id="paymentDetails" class="hidden bg-white p-6 rounded-xl shadow-lg border border-blue-100 animate-fade-in">
                <div id="payInfoBox" class="text-center mb-6">
                    <img id="qrImg" src="" class="w-32 h-32 mx-auto mb-3 hidden rounded-lg border p-1">
                    <p class="text-xs text-gray-500 mb-1 font-bold uppercase">Send Money To</p>
                    <div class="flex items-center justify-center gap-3 bg-gray-100 p-3 rounded-lg border border-gray-200">
                        <span id="payNumber" class="font-mono font-bold text-xl text-gray-800 tracking-wider"></span>
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('payNumber').innerText); alert('Number Copied!');" class="text-blue-600 hover:text-blue-800 transition">
                            <i class="fa-regular fa-copy text-lg"></i>
                        </button>
                    </div>
                    <p id="payDesc" class="text-xs text-orange-600 mt-2 font-medium bg-orange-50 inline-block px-2 py-1 rounded"></p>
                </div>

                <div id="walletWarning" class="hidden text-center mb-4 p-3 bg-red-100 text-red-700 rounded-lg border border-red-200 font-bold text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i> Insufficient Wallet Balance! Please Add Money first.
                </div>

                <div class="space-y-4">
                    <div id="manualInputs">
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Your Wallet Number</label>
                            <input type="text" name="wallet_number" id="wallet_num_input" placeholder="e.g. 017xxxxxxxx" class="w-full border p-3 rounded-lg focus:outline-none focus:border-blue-500 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Transaction ID (TrxID)</label>
                            <input type="text" name="trx_id" id="trx_id_input" placeholder="e.g. 8XHS..." class="w-full border p-3 rounded-lg focus:outline-none focus:border-blue-500 bg-gray-50">
                        </div>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold shadow-lg hover:bg-blue-700 hover:shadow-xl transition transform active:scale-95">
                        Confirm Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // ট্যাব সিলেকশন ও বর্ডার ডিজাইন কন্ট্রোল করার জাভাস্ক্রিপ্ট
    function togglePaymentType(type) {
        const autoSection = document.getElementById('autoPaySection');
        const manualSection = document.getElementById('manualPaySection');
        const btnAuto = document.getElementById('btnAutoPay');
        const btnManual = document.getElementById('btnManualPay');
        const checkAuto = document.getElementById('checkAutoPay');
        const checkManual = document.getElementById('checkManualPay');

        const walletInput = document.getElementById('wallet_num_input');
        const trxInput = document.getElementById('trx_id_input');

        if(type === 'auto') {
            autoSection.classList.remove('hidden');
            manualSection.classList.add('hidden');
            
            // বাটন হাইলাইট এক্টিভ
            btnAuto.classList.add('border-blue-600', 'bg-blue-50');
            btnManual.classList.remove('border-blue-600', 'bg-blue-50');
            btnManual.classList.add('border-gray-100');
            
            checkAuto.classList.remove('hidden');
            checkManual.classList.add('hidden');

            // রিকোয়ার্ড অপশন বন্ধ করা
            walletInput.removeAttribute('required');
            trxInput.removeAttribute('required');
        } else {
            autoSection.classList.add('hidden');
            manualSection.classList.remove('hidden');
            
            // বাটন হাইলাইট ম্যানুয়াল এক্টিভ
            btnManual.classList.add('border-blue-600', 'bg-blue-50');
            btnAuto.classList.remove('border-blue-600', 'bg-blue-50');
            btnAuto.classList.add('border-gray-100');
            
            checkManual.classList.remove('hidden');
            checkAuto.classList.add('hidden');
        }
    }

    function showPaymentDetails(input) {
        const details = document.getElementById('paymentDetails');
        const payInfoBox = document.getElementById('payInfoBox');
        const manualInputs = document.getElementById('manualInputs');
        const walletWarning = document.getElementById('walletWarning');
        const submitBtn = document.getElementById('submitBtn');
        const walletInput = document.getElementById('wallet_num_input');
        const trxInput = document.getElementById('trx_id_input');

        details.classList.remove('hidden');

        if(input.getAttribute('data-type') === 'wallet') {
            payInfoBox.classList.add('hidden');
            manualInputs.classList.add('hidden');
            walletInput.removeAttribute('required');
            trxInput.removeAttribute('required');

            let balance = parseFloat(input.getAttribute('data-balance'));
            let total = parseFloat(<?php echo $total; ?>);

            if(balance < total) {
                walletWarning.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hover:bg-blue-600');
                submitBtn.innerText = 'Insufficient Balance';
            } else {
                walletWarning.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hover:bg-blue-600');
                submitBtn.innerText = 'Pay Using Wallet Balance';
            }
        } else {
            payInfoBox.classList.remove('hidden');
            manualInputs.classList.remove('hidden');
            walletInput.setAttribute('required', 'required');
            trxInput.setAttribute('required', 'required');
            walletWarning.classList.add('hidden');
            
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hover:bg-blue-600');
            submitBtn.innerText = 'Confirm Payment';
            
            document.getElementById('payNumber').innerText = input.getAttribute('data-number');
            document.getElementById('payDesc').innerText = input.getAttribute('data-desc');
            
            const qr = input.getAttribute('data-qr');
            const qrImg = document.getElementById('qrImg');
            if(qr && qr !== "") {
                qrImg.src = qr;
                qrImg.classList.remove('hidden');
            } else {
                qrImg.classList.add('hidden');
            }
        }
        details.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>
