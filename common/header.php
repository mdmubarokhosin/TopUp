<?php include_once 'common/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo getSetting($conn, 'site_name'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #fafafa; 
            -webkit-tap-highlight-color: transparent; 
        }
        /* আপগ্রেড করা প্রিমিয়াম ও ট্রান্সপারেন্ট গ্লাস ইফেক্ট */
        .premium-glass {
            background: rgba(255, 255, 255, 0.65); /* ট্রান্সপারেন্ট করা হয়েছে */
            backdrop-filter: blur(20px) saturate(180%); /* ব্লার এবং স্যাচুরেশন বাড়ানো হয়েছে */
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4); /* স্মুথ ইনার বর্ডার */
        }
    </style>
</head>
<body class="bg-slate-50/50 text-slate-800 pb-24">

<?php 
// ডাটাবেস থেকে লোগো ফেচ করা হচ্ছে 
$logo_url = getSetting($conn, 'logo_url') ?: 'default.png'; 
?>

<header class="premium-glass sticky top-0 z-[100] transition-all duration-300 shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
    <div class="container mx-auto px-4 py-3.5 md:py-5 flex justify-between items-center max-w-7xl">
        
        <a href="index.php" class="flex-shrink-0 flex items-center transition-opacity hover:opacity-90">
            <img src="/admin/uploads/<?php echo htmlspecialchars($logo_url); ?>" alt="Logo" class="h-16 md:h-20 w-auto object-contain">
        </a>

        <div class="flex items-center gap-4 md:gap-8">
            
            <nav class="hidden md:flex items-center gap-7">
                <a href="/game.php" class="text-slate-900 font-bold text-[15px] hover:text-blue-600 transition-colors tracking-wide">Topup</a>
                <a href="contact.php" class="text-slate-900 font-bold text-[15px] hover:text-blue-600 transition-colors tracking-wide">Contact Us</a>
            </nav>

            <?php if(isset($_SESSION['user_id']) && $conn):
                $uid = (int)$_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT balance, name FROM users WHERE id = ?");
                $stmt->bind_param("i", $uid);
                $stmt->execute();
                $u_res = $stmt->get_result();
                if ($u_res && $u_res->num_rows > 0) {
                    $u_data = $u_res->fetch_assoc();
                    $initial = strtoupper(substr($u_data['name'], 0, 1));
                } else {
                    $initial = '?';
                    $u_data = ['balance' => 0, 'name' => 'User'];
                }
            ?>
                <div class="flex items-center gap-2.5 md:gap-3">
                    
                    <div class="bg-blue-600 text-white rounded-full px-4 py-1.5 md:px-5 md:py-2 flex items-center gap-1.5 md:gap-2 shadow-[0_4px_12px_rgba(37,99,235,0.2)] cursor-pointer hover:bg-blue-700 hover:shadow-[0_6px_20px_rgba(37,99,235,0.3)] transition-all duration-200">
                        <i class="fa-solid fa-wallet text-[12px] md:text-sm"></i>
                        <span class="font-bold text-[12px] md:text-[15px] tracking-tight pt-0.5">
                            <?php echo number_format($u_data['balance'], 2); ?> ৳
                        </span>
                    </div>

                    <button onclick="toggleUserSidebar()" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-[#f59e0b] text-slate-900 font-bold text-base md:text-lg flex items-center justify-center hover:opacity-90 transition shadow-sm active:scale-95 border border-[#d97706] shrink-0">
                        <?php echo $initial; ?>
                    </button>
                </div>
                
            <?php else: ?>
                <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 md:px-6 md:py-2.5 rounded-md text-[12px] md:text-[15px] font-bold shadow-[0_4px_12px_rgba(37,99,235,0.2)] transition-all active:scale-95">
                    Login
                </a>
            <?php endif; ?>
            
        </div>
    </div>
</header>

<?php include 'sidebar.php'; ?>