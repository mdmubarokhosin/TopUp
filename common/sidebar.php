<div id="userSidebar" class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-[200] flex flex-col select-none border-l border-gray-100">

    <?php if(isset($_SESSION['user_id']) && $conn):
        $uid = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT name, balance, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $u_res = $stmt->get_result();
        if ($u_res && $u_res->num_rows > 0) {
            $u_data = $u_res->fetch_assoc();
            $initial = strtoupper(substr($u_data['name'], 0, 1));
        } else {
            $initial = '?';
            $u_data = ['name' => 'User', 'balance' => 0, 'email' => ''];
        }
    ?>
        <div class="p-6 border-b border-gray-100 flex items-start gap-4 bg-white">
            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg uppercase shrink-0 shadow-sm">
                <?php echo $initial; ?>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-gray-900 font-bold text-base truncate leading-tight"><?php echo $u_data['name']; ?></h2>
                <p class="text-gray-400 text-xs truncate mt-1"><?php echo !empty($u_data['email']) ? $u_data['email'] : 'user@email.com'; ?></p>
                <a href="logout.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-1.5 rounded-md mt-3 transition active:scale-95 shadow-sm shadow-blue-200">
                    Logout
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="p-6 border-b border-gray-100 flex items-center gap-4 bg-white">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-lg shrink-0 border border-gray-200/50">
                G
            </div>
            <div>
                <h2 class="text-gray-900 font-bold text-base leading-tight">Guest User</h2>
                <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-1.5 rounded-md mt-2 transition active:scale-95 shadow-sm shadow-blue-200">
                    Login / Register
                </a>
            </div>
        </div>
    <?php endif; ?>

    <nav class="p-4 space-y-1 overflow-y-auto flex-1 custom-scrollbar">
        <?php
        $menuItems = [
            ['link'=>'index.php', 'icon'=>'fa-house', 'text'=>'Home'],
            ['link'=>'addmoney.php', 'icon'=>'fa-wallet', 'text'=>'Add Money'],
            ['link'=>'order.php', 'icon'=>'fa-box-open', 'text'=>'My Orders'],
            ['link'=>'mycode.php', 'icon'=>'fa-ticket', 'text'=>'My Codes'],
            ['link'=>'profile.php', 'icon'=>'fa-user-gear', 'text'=>'Profile Settings']
        ];

        foreach($menuItems as $item):
            $currentFile = basename($_SERVER['PHP_SELF']);
            $isActive = ($currentFile == $item['link']);

            if($isActive) {
                $statusClass = 'text-blue-600 font-bold bg-blue-50/40 rounded-lg';
                $iconClass = 'text-blue-600';
            } else {
                $statusClass = 'text-gray-700 hover:text-blue-600 hover:bg-gray-50/80 rounded-lg';
                $iconClass = 'text-gray-400 group-hover:text-blue-500';
            }
        ?>
        <a href="<?php echo $item['link']; ?>" class="group flex items-center gap-4 py-3 px-3 text-sm transition-all duration-200 <?php echo $statusClass; ?>">
            <i class="fa-solid <?php echo $item['icon']; ?> text-base w-5 text-center transition-colors <?php echo $iconClass; ?>"></i>
            <span class="tracking-wide font-medium text-[14px]"><?php echo $item['text']; ?></span>
        </a>
        <?php endforeach; ?>

        <div class="pt-4 mt-2 border-t border-gray-100">
            <?php
            $fab_link = '';
            if ($conn) {
                $fab_link = getSetting($conn, 'fab_link') ?: '#';
            }
            ?>
            <a href="<?php echo htmlspecialchars($fab_link); ?>" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2.5 px-4 flex items-center justify-center gap-2 font-bold transition shadow-sm shadow-blue-200 active:scale-95">
                <i class="fa-solid fa-headset text-sm"></i>
                <span>Support</span>
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col items-center justify-center gap-1">
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200/60 shadow-sm text-gray-500">
            <span class="text-[10px] font-medium tracking-wider uppercase text-gray-400">Dev:</span>
            <span class="text-xs font-bold tracking-wide">MD MUBAROK</span>
        </div>
        <p class="text-[9px] text-gray-400 font-mono tracking-widest mt-1">App Version 2.0.1</p>
    </div>
</div>

<div id="sidebarOverlay" onclick="toggleUserSidebar()" class="fixed inset-0 bg-black/30 z-[150] hidden backdrop-blur-[3px] transition-opacity duration-300"></div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d4d4d8; }
</style>

<script>
    function toggleUserSidebar() {
        const sb = document.getElementById('userSidebar');
        const ov = document.getElementById('sidebarOverlay');
        const isOpen = !sb.classList.contains('translate-x-full');

        if (isOpen) {
            sb.classList.add('translate-x-full');
            ov.classList.add('hidden');
            document.body.style.overflow = '';
        } else {
            sb.classList.remove('translate-x-full');
            ov.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
</script>
