<?php 
include 'common/header.php'; 

if(!isset($_GET['id'])) header("Location: index.php");
$id = (int)$_GET['id'];
$game = $conn->query("SELECT * FROM games WHERE id=$id")->fetch_assoc();
if(!$game) header("Location: index.php");

$products = $conn->query("SELECT * FROM products WHERE game_id=$id ORDER BY price ASC");

// ইউজারের ডাইনামিক ব্যালেন্স চেক
$user_balance = 0;
if(isset($_SESSION['user_id'])) {
    $uid_session = (int)$_SESSION['user_id'];
    $user_q = $conn->query("SELECT balance FROM users WHERE id=$uid_session");
    if($user_q && $user_q->num_rows > 0) {
        $user_row = $user_q->fetch_assoc();
        $user_balance = (float)$user_row['balance'];
    }
}

// অটোমেটিক ক্রমানুসারে স্টেপ নাম্বার মেইনটেইন করার জন্য ভেরিয়েবল
$step = 1; 
?>

<style>
    body { background-color: #f8f9fa; }
    
    /* ভাসমান সব অনাকাঙ্ক্ষিত বাটন হাইড করার জন্য */
    .support-fab, .floating-contact, .whatsapp-float, #support-btn, .fab-wrapper { display: none !important; }

    /* গোল স্টেপ ব্যাজ ডিজাইন */
    .step-badge {
        width: 28px;
        height: 28px;
        background-color: #2563eb; 
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 14px;
    }

    /* সেকশন টাইটেল */
    .section-title {
        color: #2563eb;
        font-weight: 700;
        font-size: 16px;
    }

    /* প্লেয়ার আইডি চেক বাটন */
    #actionBtn {
        width: 100%;
        height: 42px;
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s;
        gap: 8px;
    }
    #actionBtn:hover { background-color: #1d4ed8; }
    #actionBtn.success { background-color: #10b981; }
    #actionBtn.error { background-color: #ef4444; }

    /* প্রোডাক্ট সিলেক্ট করার সিএসএস */
    .product-card input:checked + .card-content {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 1px #2563eb;
        background-color: #eff6ff !important;
    }

    /* পেমেন্ট কার্ড সিএসএস */
    .payment-card input:checked + .card-content {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 1.5px #2563eb;
    }
    .payment-card input:checked + .card-content .payment-footer {
        background-color: #2563eb !important;
        color: white !important;
    }
    .payment-card input:checked + .card-content .status-dot {
        background-color: white !important;
        border-color: white !important;
    }

    /* লোডিং স্পিনার */
    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top: 2px solid #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div class="container mx-auto px-4 py-6 max-w-5xl pb-16">

    <div class="relative w-full h-[140px] rounded-t-lg overflow-hidden mb-4 shadow-sm bg-gray-900 flex items-center">
        <img src="<?php echo $game['image']; ?>" class="absolute inset-0 w-full h-full object-cover opacity-50 blur-[2px]" alt="Banner">
        <div class="absolute inset-0 bg-black/30"></div>
        
        <div class="relative z-10 flex items-center gap-5 px-6 md:px-10">
            <img src="<?php echo $game['image']; ?>" class="w-20 h-20 md:w-24 md:h-24 rounded-lg border-2 border-[#2563eb] object-cover shadow-lg bg-black">
            <div>
                <h1 class="text-white text-lg md:text-xl font-bold uppercase tracking-wide flex items-center gap-2">
                    <?php echo $game['name']; ?>
                </h1>
                <div class="text-white text-[11px] md:text-xs mt-2 flex items-center gap-1.5 bg-black/60 border border-white/20 px-3 py-1.5 rounded-md w-max font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    মাত্র ২ সেকেন্ডে ডেলিভারি
                </div>
            </div>
        </div>
    </div>

    <form id="orderForm" action="order.php" method="POST" onsubmit="return validateForm()">
        
        <input type="hidden" name="action" value="create_order">
        <input type="hidden" name="game_id" value="<?php echo $id; ?>">
        <input type="hidden" name="sub_email" value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">

        <div class="bg-white border border-gray-200 rounded-lg mb-4 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-gray-100 bg-white">
                <div class="step-badge"><?php echo $step++; ?></div>
                <h2 class="section-title">Select Recharge</h2>
            </div>
            
            <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-3">
                <?php if($products->num_rows > 0): ?>
                    <?php while($prod = $products->fetch_assoc()): ?>
                    <label class="cursor-pointer product-card">
                        <input type="radio" name="product_id" value="<?php echo $prod['id']; ?>" data-price="<?php echo $prod['price']; ?>" class="sr-only" required onchange="updateTotal()">
                        <div class="card-content border border-gray-200 rounded p-3.5 flex justify-between items-center transition-all bg-white hover:border-blue-500">
                            <span class="text-[12px] font-bold text-gray-700 uppercase leading-snug flex items-center gap-1">
                                <?php echo $prod['name']; ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                </svg>
                            </span>
                            <span class="text-[13px] font-bold text-[#ff6b00]"><?php echo $prod['price']; ?>৳</span>
                        </div>
                    </label>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-sm p-2 col-span-full">কোন প্রোডাক্ট পাওয়া যায়নি!</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if($game['type'] == 'uid'): ?>
        <div class="bg-white border border-gray-200 rounded-lg mb-4 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-gray-100 bg-white">
                <div class="step-badge"><?php echo $step++; ?></div> 
                <h2 class="section-title">Account Info</h2>
            </div>
            
            <div class="p-5">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Enter Your Player ID</label>
                <input type="number" id="player_uid" name="player_id" placeholder="এখানে গেমের আইডি কোড লিখুন" class="w-full border border-gray-300 p-2.5 rounded focus:outline-none focus:border-blue-600 mb-3 text-sm transition-colors" required>
                
                <button type="button" id="actionBtn" onclick="getName()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="btnText">Check Player ID Name</span>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-lg mb-4 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-gray-100 bg-white">
                <div class="step-badge"><?php echo $step++; ?></div>
                <h2 class="section-title">Select Payment</h2>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 mb-5 max-w-xl">
                    
                    <label class="cursor-pointer block relative payment-card">
                        <input type="radio" name="payment_method" value="Wallet" class="sr-only" required checked onchange="togglePaymentAction()">
                        <div class="card-content border border-gray-300 rounded-lg overflow-hidden transition-all bg-white shadow-sm flex flex-col justify-between h-[100px] hover:border-blue-500">
                            <div class="p-1 flex justify-center items-center bg-white flex-1 overflow-hidden">
                                <img src="uploads/01KJJHSVY0CMRNDREF1NP78BHF.png" class="w-full h-full object-contain p-1" alt="Wallet Pay">
                            </div>
                            <div class="payment-footer bg-gray-100 text-gray-600 text-[10px] sm:text-xs text-center py-2.5 font-semibold flex justify-between px-2 sm:px-3 items-center transition-colors">
                                <span class="truncate">Wallet (৳:<?php echo number_format($user_balance, 0); ?>)</span>
                                <span class="status-dot border border-gray-400 rounded-full w-2 h-2 bg-transparent"></span>
                            </div>
                        </div>
                    </label>
                    
                    <label class="cursor-pointer block relative payment-card">
                        <input type="radio" name="payment_method" value="auto" class="sr-only" required onchange="togglePaymentAction()">
                        <div class="card-content border border-gray-300 rounded-lg overflow-hidden transition-all bg-white shadow-sm flex flex-col justify-between h-[100px] hover:border-blue-500">
                            <div class="p-1 flex justify-center items-center bg-white flex-1 overflow-hidden">
                                <img src="uploads/bd_payments.png" class="w-full h-full object-contain p-1" alt="Instant Auto Pay">
                            </div>
                            <div class="payment-footer bg-gray-100 text-gray-600 text-[10px] sm:text-xs text-center py-2.5 font-semibold flex justify-between px-2 sm:px-3 items-center transition-colors">
                                <span class="uppercase">Auto Pay</span>
                                <span class="status-dot border border-gray-400 rounded-full w-2 h-2 bg-transparent"></span>
                            </div>
                        </div>
                    </label>
                    
                </div>

                <div class="border border-gray-200 rounded mb-4 divide-y divide-gray-100">
                    <div class="p-3 flex justify-between items-center text-sm">
                        <span class="text-gray-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            আপনার একাউন্ট ব্যালেন্স <span class="font-bold text-blue-600">৳ <?php echo number_format($user_balance, 2); ?></span>
                        </span>
                    </div>
                    <div class="p-3 flex justify-between items-center text-sm bg-gray-50/50">
                        <span class="text-gray-700 flex items-center gap-2 font-medium">
                            <i class="fa-solid fa-cart-shopping text-blue-600"></i>
                            সর্বমোট পে করতে হবে:
                        </span>
                        <span id="totalDisplay" class="font-black text-blue-600 text-lg">৳ 0.00</span>
                    </div>
                </div>

                <div id="balanceWarning" class="hidden flex justify-between items-center bg-[#fff5f5] p-2.5 rounded border border-[#ffe5e5] mb-4">
                    <span class="text-[#e53e3e] text-sm flex items-center gap-2 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        পর্যাপ্ত ব্যালেন্স নেই
                    </span>
                    <a href="addmoney.php" class="bg-[#f56565] hover:bg-[#e53e3e] text-white text-xs px-4 py-2 rounded font-semibold flex items-center gap-1 transition-colors">
                        + ব্যালেন্স যোগ করুন
                    </a>
                </div>

                <input type="hidden" name="amount" id="totalInput" value="0">
                <input type="hidden" name="total_amount" id="totalInputLegacy" value="0">
                
                <button type="submit" class="w-full bg-gray-400 text-white font-bold py-3.5 rounded-lg transition-all text-sm shadow-md cursor-not-allowed uppercase tracking-wider flex justify-center items-center gap-2" id="buyNowBtn" disabled>
                    <i class="fa-solid fa-bolt"></i> <span id="btnTextLabel">Proceed to Pay</span>
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg mb-8 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 p-4 border-b border-gray-100 bg-white">
                <div class="step-badge"><?php echo $step++; ?></div>
                <h2 class="section-title">Description</h2>
            </div>
            <div class="p-5 text-sm text-black font-medium leading-[2.2]">
                <?php 
                if(!empty($game['description'])) {
                    echo nl2br($game['description']); 
                } else {
                    echo "🔔 Green Topup - গুরুত্বপূর্ণ নির্দেশনা 🔔<br>
                    1️⃣ ✅ শুধুমাত্র Bangladesh সার্ভারে Player ID কোড দিয়ে টপ-আপ করা যাবে।<br>
                    2️⃣ ⚠️ ভুল Player ID দিয়ে অর্ডার করলে ডায়মন্ড না পেলেও Green Topup কর্তৃপক্ষ দায়ী থাকবে না।<br>
                    3️⃣ ✅ অর্ডার সম্পন্ন হওয়ার পরও যদি ডায়মন্ড না আসে, তাহলে যাচাইয়ের জন্য Player ID ও পাসওয়ার্ড প্রদান করতে হবে।<br>
                    4️⃣ ❌ অর্ডার বাতিল হলে, বাতিলের কারণ Order History-তে উল্লেখ থাকবে। অনুগ্রহ করে দেখে নিন এবং সঠিক তথ্য দিয়ে পুনরায় অর্ডার করুন।<br>
                    5️⃣ 📌 অনুরোধ রইল, অর্ডার করার আগে উপরের নিয়মাবলী ভালোভাবে পড়ে নিশ্চিত হয়ে অর্ডার করুন।<br>
                    6️⃣ 📌 Green Topup — আপনার নির্ভরযোগ্য গেমিং টপ-আপ পার্টনার 🎮";
                }
                ?>
            </div>
        </div>

    </form>
</div>

<script>
const currentBalance = <?php echo $user_balance; ?>;
let selectedPrice = 0;

// প্রোডাক্ট সিলেক্ট ও প্রাইস আপডেট লজিক
function updateTotal() {
    const radios = document.getElementsByName('product_id');
    selectedPrice = 0;
    for (let r of radios) { 
        if (r.checked) { 
            selectedPrice = parseFloat(r.getAttribute('data-price')); 
            break; 
        } 
    }
    
    document.getElementById('totalDisplay').innerText = "৳ " + selectedPrice.toFixed(2);
    document.getElementById('totalInput').value = selectedPrice.toFixed(2);
    document.getElementById('totalInputLegacy').value = selectedPrice.toFixed(2);
    
    togglePaymentAction();
}

// পেমেন্ট অপশন পরিবর্তন অনুযায়ী ফর্ম অ্যাকশন ও বাটন চেঞ্জ করা
function togglePaymentAction() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const form = document.getElementById('orderForm');
    const btn = document.getElementById('buyNowBtn');
    const warning = document.getElementById('balanceWarning');
    const btnText = document.getElementById('btnTextLabel');

    if (selectedPrice <= 0) {
        btn.classList.add('bg-gray-400', 'cursor-not-allowed');
        btn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
        btn.disabled = true;
        warning.classList.add('hidden');
        return;
    }

    if (paymentMethod === 'Wallet') {
        form.action = 'order.php';
        form.method = 'POST'; // ওয়ালেট পেমেন্টের জন্য POST
        btnText.innerText = "Pay via Wallet";
        
        if (currentBalance < selectedPrice) {
            warning.classList.remove('hidden');
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            btn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
            btn.disabled = true;
        } else {
            warning.classList.add('hidden');
            btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
            btn.disabled = false;
        }
    } else {
        form.action = 'payment.php';
        form.method = 'GET'; // অটো পেমেন্টের জন্য GET
        btnText.innerText = "Pay via Auto Gateway";
        warning.classList.add('hidden');
        
        btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
        btn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
        btn.disabled = false;
    }
}

// ফর্ম সাবমিশন ভ্যালিডেশন সিকিউরিটি
function validateForm() {
    if (selectedPrice <= 0) {
        alert("অনুগ্রহ করে একটি প্রোডাক্ট সিলেক্ট করুন!");
        return false;
    }
    return true;
}

// প্লেয়ার আইডি থেকে নেম চেক করার AJAX লজিক
async function getName() {
    const uidInput = document.getElementById('player_uid');
    const btn = document.getElementById('actionBtn');
    const btnText = document.getElementById('btnText');
    const uid = uidInput.value.trim();

    if (!uid) {
        uidInput.style.borderColor = "#ef4444";
        setTimeout(() => uidInput.style.borderColor = "#ccc", 1000);
        return;
    }

    btnText.innerHTML = '<div class="spinner"></div>';
    btn.classList.remove('success', 'error');
    btn.style.pointerEvents = "none";

    try {
        const response = await fetch('check-name.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: uid })
        });

        const data = await response.json();

        if (!data.error) {
            btnText.innerText = data.nickname;
            btn.classList.add('success');
        } else {
            btnText.innerText = data.message;
            btn.classList.add('error');
        }
    } catch (err) {
        btnText.innerText = "সার্ভার এরর!";
        btn.classList.add('error');
    } finally {
        btn.style.pointerEvents = "auto";
    }
}
</script>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>