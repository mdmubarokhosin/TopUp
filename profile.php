<?php 
include 'common/header.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$msg = "";
$msgType = "";

// Handle Profile Update logic 
if(isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    // ফোন নাম্বার আপডেটের লজিক যুক্ত করা হলো
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    
    $conn->query("UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$uid");
    $msg = "Profile Updated Successfully!";
    $msgType = "success";
}

// Fetch User Data
$u = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$orders = $conn->query("SELECT COUNT(*) as cnt, SUM(amount) as spent FROM orders WHERE user_id=$uid AND status='completed'")->fetch_assoc();

// নামের প্রথম অক্ষর বের করা
$initial = strtoupper(substr($u['name'], 0, 1));
?>

<style>
    /* Profile Premium Background - FIXED IMAGE PATH */
    .profile-bg {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.85) 0%, rgba(147, 51, 234, 0.85) 100%), url('uploads/profile.jpg') center/cover no-repeat;
    }
    
    .profile-img-container {
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .status-dot {
        height: 14px;
        width: 14px;
        background-color: #22c55e;
        border: 2px solid white;
        bottom: 2px;
        right: 2px;
    }

    /* Modal Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out forwards;
    }
</style>

<div class="bg-[#F0F4F9] min-h-screen pb-6">
    
    <div class="profile-bg pt-10 pb-16 px-4 text-center relative">
        <div class="relative inline-block mb-3">
            <div class="w-20 h-20 rounded-full profile-img-container mx-auto bg-blue-600 flex items-center justify-center text-3xl text-white font-bold backdrop-blur-sm">
                <?php echo $initial; ?>
            </div>
            <span class="absolute status-dot rounded-full shadow-sm"></span>
        </div>
        <h2 class="text-2xl font-bold text-white mb-1"><?php echo $u['name']; ?></h2>
        <p class="text-white/80 text-sm mb-4 font-medium"><?php echo $u['email']; ?></p>
        <button onclick="document.getElementById('editProfileModal').classList.remove('hidden')" class="bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white text-xs font-bold px-5 py-2 rounded-full transition-all shadow-sm">
            Edit Profile
        </button>
    </div>

    <div class="container mx-auto px-4 -mt-8 grid grid-cols-2 gap-3 relative z-10">
        
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:border-blue-200 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                <i class="fa-solid fa-id-badge text-lg"></i>
            </div>
            <h3 class="text-lg font-black text-gray-800 leading-none mb-1">#<?php echo $u['id']; ?></h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">User ID</p>
            <div class="absolute top-3 right-3 text-gray-300 hover:text-blue-500 cursor-pointer transition-colors" onclick="navigator.clipboard.writeText('<?php echo $u['id']; ?>'); alert('User ID Copied!');">
                <i class="fa-regular fa-copy"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden hover:border-green-200 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-500 flex items-center justify-center mb-3">
                <i class="fa-solid fa-money-bill-wave text-lg"></i>
            </div>
            <h3 class="text-lg font-black text-gray-800 leading-none mb-1">৳<?php echo (float)$u['balance']; ?></h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Wallet</p>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden hover:border-purple-200 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center mb-3">
                <i class="fa-solid fa-comment-dollar text-lg"></i>
            </div>
            <h3 class="text-lg font-black text-gray-800 leading-none mb-1">৳<?php echo (float)$orders['spent']; ?></h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Spent</p>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden hover:border-pink-200 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center mb-3">
                <i class="fa-solid fa-bag-shopping text-lg"></i>
            </div>
            <h3 class="text-lg font-black text-gray-800 leading-none mb-1"><?php echo (int)$orders['cnt']; ?></h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Orders</p>
        </div>

    </div>

    <div class="container mx-auto px-4 mt-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="p-3.5 bg-gray-50/50 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fa-regular fa-user text-xs"></i>
                </div>
                <h4 class="font-bold text-gray-800 text-sm">User Information</h4>
            </div>

            <div class="p-4 flex items-center gap-4 border-b border-gray-50">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Phone Number</p>
                    <p class="font-bold text-gray-700 text-sm"><?php echo !empty($u['phone']) ? $u['phone'] : 'Not provided'; ?></p>
                </div>
            </div>

            <div class="p-4 flex items-center gap-4 border-b border-gray-50">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                    <i class="fa-regular fa-envelope"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Email Address</p>
                    <p class="font-bold text-gray-700 text-sm truncate"><?php echo $u['email']; ?></p>
                </div>
            </div>

            <div class="bg-gray-50/30 p-4 flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Account Status</span>
                <span class="flex items-center gap-1.5 text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-md">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                </span>
            </div>

        </div>
        
        <a href="logout.php" class="mt-5 flex items-center justify-center gap-2 w-full p-3.5 bg-white text-red-500 font-bold text-sm rounded-xl shadow-sm border border-red-50 hover:bg-red-50 transition-all mb-4">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Logout
        </a>
    </div>
</div>

<div id="editProfileModal" class="fixed inset-0 bg-black/60 z-[200] hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl relative animate-fade-in-up">
        
        <button onclick="document.getElementById('editProfileModal').classList.add('hidden')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
        
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Edit Profile</h3>
        </div>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Full Name</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400"><i class="fa-regular fa-user"></i></span>
                    <input type="text" name="name" value="<?php echo $u['name']; ?>" class="w-full border border-gray-200 py-2.5 pl-9 pr-3 rounded-xl bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-sm font-medium" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" name="email" value="<?php echo $u['email']; ?>" class="w-full border border-gray-200 py-2.5 pl-9 pr-3 rounded-xl bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-sm font-medium" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 mb-1">Phone Number</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400"><i class="fa-solid fa-phone"></i></span>
                    <input type="text" name="phone" value="<?php echo isset($u['phone']) ? $u['phone'] : ''; ?>" placeholder="e.g. 017xxxxxxxx" class="w-full border border-gray-200 py-2.5 pl-9 pr-3 rounded-xl bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-sm font-medium">
                </div>
            </div>
            
            <button type="submit" name="update_profile" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/30 transition-all active:scale-95">
                Save Changes
            </button>
        </form>

    </div>
</div>

<?php 
// মেসেজ এলার্ট দেখানোর জন্য
if(!empty($msg)) {
    echo "<script>alert('$msg');</script>";
}
?>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>