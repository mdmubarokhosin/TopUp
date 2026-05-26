<?php include 'common/header.php'; 
$stats = [
    'Users' => ['count' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0], 'icon' => 'fas fa-users', 'color' => 'from-blue-500 to-indigo-600', 'bg' => 'bg-blue-50'],
    'Orders' => ['count' => $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0], 'icon' => 'fas fa-shopping-basket', 'color' => 'from-emerald-500 to-teal-600', 'bg' => 'bg-emerald-50'],
    'Revenue' => ['count' => $conn->query("SELECT SUM(amount) FROM orders WHERE status='completed'")->fetch_row()[0], 'icon' => 'fas fa-wallet', 'color' => 'from-amber-500 to-orange-600', 'bg' => 'bg-amber-50'],
    'Pending' => ['count' => $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetch_row()[0], 'icon' => 'fas fa-clock', 'color' => 'from-rose-500 to-red-600', 'bg' => 'bg-rose-50'],
];
?>

<!-- Font Awesome Components (If not added in header) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-8">

    <!-- Stats Section: Mobile friendly 2-column or 1-column layout -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <?php 
        $delay = 0;
        foreach($stats as $key => $data): 
            $delay += 75;
        ?>
        <div class="animate-fade-in-up opacity-0 bg-white p-4 sm:p-6 rounded-2xl border border-gray-100 shadow-sm active:scale-95 sm:hover:scale-[1.02] transition-all duration-200 group" style="animation-delay: <?php echo $delay; ?>ms;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-gray-400 text-[10px] sm:text-xs font-semibold uppercase tracking-wider mb-0.5 sm:mb-1"><?php echo $key; ?></p>
                    <h3 class="text-xl sm:text-3xl font-black text-gray-800 tracking-tight whitespace-nowrap">
                        <?php echo ($key == 'Revenue' ? '<span class="text-sm sm:text-xl font-medium text-gray-500 mr-0.5">৳</span>' : '').number_format((int)$data['count']); ?>
                    </h3>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center <?php echo $data['bg']; ?> text-sm sm:text-xl self-start sm:self-center">
                    <i class="<?php echo $data['icon']; ?> bg-gradient-to-br <?php echo $data['color']; ?> bg-clip-text text-transparent"></i>
                </div>
            </div>
            <!-- Progress Line Indicator -->
            <div class="w-full bg-gray-100 h-1 rounded-full mt-3 overflow-hidden hidden sm:block">
                <div class="bg-gradient-to-r <?php echo $data['color']; ?> h-full w-3/4 rounded-full"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Links Section: Smooth Grid for Mobile -->
    <div>
        <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 px-1">
            <span class="w-1.5 h-4 sm:h-5 bg-indigo-600 rounded-full"></span>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            
            <a href="game.php" class="group relative overflow-hidden bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-100/80 p-4 sm:p-5 rounded-2xl flex flex-col items-center justify-center gap-2 font-bold text-purple-700 active:scale-95 sm:hover:text-white transition-all duration-200 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-indigo-600 opacity-0 sm:group-hover:opacity-100 transition-opacity duration-300 -z-0"></div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-200/50 sm:group-hover:bg-white/20 flex items-center justify-center text-lg transition-all duration-300 z-10">
                    <i class="fas fa-gamepad"></i>
                </div>
                <span class="z-10 text-xs sm:text-sm tracking-wide">Add Game</span>
            </a>

            <a href="product.php" class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100/80 p-4 sm:p-5 rounded-2xl flex flex-col items-center justify-center gap-2 font-bold text-emerald-700 active:scale-95 sm:hover:text-white transition-all duration-200 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-600 opacity-0 sm:group-hover:opacity-100 transition-opacity duration-300 -z-0"></div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-200/50 sm:group-hover:bg-white/20 flex items-center justify-center text-lg transition-all duration-300 z-10">
                    <i class="fas fa-box-open"></i>
                </div>
                <span class="z-10 text-xs sm:text-sm tracking-wide">Add Product</span>
            </a>

            <a href="paymentmethod.php" class="group relative overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100/80 p-4 sm:p-5 rounded-2xl flex flex-col items-center justify-center gap-2 font-bold text-orange-700 active:scale-95 sm:hover:text-white transition-all duration-200 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-600 to-amber-600 opacity-0 sm:group-hover:opacity-100 transition-opacity duration-300 -z-0"></div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-200/50 sm:group-hover:bg-white/20 flex items-center justify-center text-lg transition-all duration-300 z-10">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="z-10 text-xs sm:text-sm tracking-wide whitespace-nowrap">Payment Methods</span>
            </a>

            <a href="setting.php" class="group relative overflow-hidden bg-gradient-to-br from-gray-50 to-slate-100 border border-gray-200/80 p-4 sm:p-5 rounded-2xl flex flex-col items-center justify-center gap-2 font-bold text-gray-700 active:scale-95 sm:hover:text-white transition-all duration-200 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-slate-800 opacity-0 sm:group-hover:opacity-100 transition-opacity duration-300 -z-0"></div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gray-200 sm:group-hover:bg-white/20 flex items-center justify-center text-lg transition-all duration-300 z-10">
                    <i class="fas fa-sliders"></i>
                </div>
                <span class="z-10 text-xs sm:text-sm tracking-wide">Settings</span>
            </a>

        </div>
    </div>

</div>

</body>
</html>
