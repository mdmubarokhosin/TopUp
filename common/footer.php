<?php
// Load settings from database
$logo_url      = getSetting($conn, 'logo_url') ?: 'default.png';
$fb_url        = getSetting($conn, 'fb_url') ?: '#';
$yt_url        = getSetting($conn, 'yt_url') ?: '#';
$messenger_url = getSetting($conn, 'messenger_url') ?: '#';
$site_name     = getSetting($conn, 'site_name') ?: 'TopUp Shop';
$site_email    = getSetting($conn, 'site_email') ?: 'support@' . ($_SERVER['HTTP_HOST'] ?? 'domain.com');
?>

<footer class="bg-[#0f4b8f] px-5 py-10 md:py-14 font-sans pb-24 md:pb-10">

    <div class="container mx-auto max-w-6xl grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-8">

        <div>
            <h3 class="text-center md:text-left text-white font-extrabold text-[13px] tracking-wider mb-5 uppercase">Customer Support</h3>
            <a href="#" class="flex items-center gap-3 bg-[#185eaf] rounded-xl p-4 mb-3 shadow-sm border border-blue-400/20 hover:bg-[#1e6ac2] transition">
                <div class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-sm shrink-0">
                    <i class="fa-brands fa-whatsapp text-[22px] text-green-500"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm">WhatsApp Support</h4>
                    <p class="text-blue-100 text-xs mt-0.5">9AM - 12PM Daily</p>
                </div>
            </a>
            <a href="<?= htmlspecialchars($messenger_url); ?>" class="flex items-center gap-3 bg-[#185eaf] rounded-xl p-4 shadow-sm border border-blue-400/20 hover:bg-[#1e6ac2] transition">
                <div class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-sm shrink-0">
                    <i class="fa-brands fa-telegram text-[22px] text-blue-500 pr-0.5"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm">Telegram Support</h4>
                    <p class="text-blue-100 text-xs mt-0.5">9AM - 12PM Daily</p>
                </div>
            </a>
        </div>

        <div class="md:pl-10">
            <h3 class="text-center md:text-left text-white font-extrabold text-[13px] tracking-wider mb-5 uppercase">Information</h3>
            <div class="flex flex-col items-center md:items-start gap-4 text-sm font-medium text-blue-100">
                <a href="terms.php" class="hover:text-white transition">Terms & Conditions</a>
                <a href="privacy.php" class="hover:text-white transition">Privacy Policy</a>
                <a href="shipping.php" class="hover:text-white transition">Shipping Information</a>
                <a href="refund.php" class="hover:text-white transition">Refund & Returns</a>
            </div>
        </div>

        <div>
            <h3 class="text-center md:text-left text-white font-extrabold text-[13px] tracking-wider mb-5 uppercase">Stay Connected</h3>
            <div class="bg-[#185eaf] rounded-2xl p-6 text-center md:text-left border border-blue-400/20 shadow-sm">
                <h2 class="text-[17px] font-bold text-white mb-1"><?= htmlspecialchars($site_name); ?></h2>
                <p class="text-xs text-blue-100 mb-5 tracking-wide">Email: <?= htmlspecialchars($site_email); ?></p>
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <a href="<?= htmlspecialchars($fb_url); ?>" target="_blank" class="w-9 h-9 bg-[#1877F2] rounded flex items-center justify-center hover:opacity-90 transition shadow-sm">
                        <i class="fa-brands fa-facebook-f text-white text-lg"></i>
                    </a>
                    <a href="<?= htmlspecialchars($messenger_url); ?>" target="_blank" class="w-9 h-9 bg-[#00B2FF] rounded flex items-center justify-center hover:opacity-90 transition shadow-sm">
                        <i class="fa-brands fa-facebook-messenger text-white text-[19px]"></i>
                    </a>
                    <a href="<?= htmlspecialchars($yt_url); ?>" target="_blank" class="w-9 h-9 bg-[#FF0000] rounded flex items-center justify-center hover:opacity-90 transition shadow-sm">
                        <i class="fa-brands fa-youtube text-white text-[19px]"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center text-xs font-medium text-blue-100 pt-10 mt-10 border-t border-blue-400/30 leading-relaxed max-w-7xl mx-auto">
        &copy; <?= date('Y'); ?> <?= htmlspecialchars($site_name); ?> &mdash; All Rights Reserved.<br>
        Developed By <span class="font-bold text-white">MD MUBAROK</span>
    </div>
</footer>
