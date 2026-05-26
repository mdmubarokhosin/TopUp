<?php include 'common/header.php'; ?>

<div class="bg-[#F0F4F9] min-h-screen pb-24 font-sans">

    <?php if(getSetting($conn, 'marquee_active') == '1'): ?>
    <div class="container mx-auto px-2 pt-3 max-w-6xl">
        <div class="bg-blue-600 rounded-xl p-4 relative shadow-sm border border-blue-400/20">
            <h3 class="text-white font-bold text-base flex items-center gap-2">
                <i class="fa-solid fa-bell animate-pulse"></i> Notice
            </h3>
            <p class="text-white/90 text-xs md:text-sm mt-1 pr-6 leading-relaxed font-medium">
                <?php echo getSetting($conn, 'marquee_text'); ?>
            </p>
            <button class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors" onclick="this.parentElement.style.display='none';">
                <i class="fa-solid fa-circle-xmark text-lg"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="container mx-auto px-2 mt-4 max-w-6xl">
        <div class="relative w-full overflow-hidden h-44 sm:h-64 md:h-[350px] rounded-2xl border border-gray-200 bg-white group">
            
            <div class="absolute inset-0 flex items-center justify-center z-20 pointer-events-none">
                <div class="relative flex items-center justify-center w-14 h-14 md:w-20 md:h-20 bg-orange-500 rounded-full text-white shadow-lg transition-transform duration-300 group-hover:scale-110">
                    <span class="absolute inset-0 rounded-full bg-orange-500 animate-ping opacity-75"></span>
                    <i class="fa-solid fa-play text-xl md:text-3xl ml-1 relative z-10"></i>
                </div>
            </div>

            <div id="slider" class="flex transition-transform duration-700 ease-in-out h-full">
                <?php 
                $sliders = $conn->query("SELECT * FROM sliders");
                $slide_count = 0;
                if($sliders->num_rows > 0):
                    while($slide = $sliders->fetch_assoc()): 
                        $slide_count++; ?>
                        <a href="<?php echo $slide['link'] ? $slide['link'] : '#'; ?>" class="min-w-full h-full block overflow-hidden">
                            <img src="<?php echo $slide['image']; ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700 ease-out" alt="Slider Image">
                        </a>
                    <?php endwhile; 
                endif; ?>
            </div>

            <?php if($slide_count > 1): ?>
            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                <?php for($i = 0; $i < $slide_count; $i++): ?>
                    <div class="dot w-1.5 h-1.5 rounded-full bg-white/40 transition-all duration-300 <?php echo $i === 0 ? 'bg-blue-600 w-4' : ''; ?>"></div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mx-auto px-2 mt-5 max-w-6xl">
        <?php include 'support-card.php'; ?>
    </div>

    <div class="container mx-auto px-2 py-6 max-w-6xl">
        
        <div class="text-center mb-6 mt-2">
            <h2 class="text-xl md:text-base font-black text-gray-800 tracking-tight uppercase inline-flex flex-col items-center gap-1">
                <span>FREE FIRE TOPUP</span>
                <span class="w-10 h-[3px] bg-blue-600 rounded-full"></span>
            </h2>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-x-2.5 gap-y-5 md:gap-x-4 md:gap-y-8">
            <?php 
            $games = $conn->query("SELECT * FROM games");
            if($games->num_rows > 0):
                while($game = $games->fetch_assoc()): ?>
                
                <a href="game_detail.php?id=<?php echo $game['id']; ?>" 
                   class="bg-white rounded-md border border-gray-200 shadow-[0_1px_0_0_#2563eb] flex flex-col group">
                    
                    <div class="relative aspect-square bg-white border-b border-gray-100 z-10 rounded-t-[6px]">
                        <img src="<?php echo $game['image']; ?>" class="w-full h-full object-cover relative transform transition-transform duration-300 rounded-t-[6px] group-hover:translate-y-[12px] group-active:translate-y-[12px]" alt="product">
                    </div>

                    <div class="py-3 px-1 md:py-5 flex-1 flex items-center justify-center bg-white rounded-b-md relative">
                        <h3 class="font-bold text-gray-800 text-[10px] md:text-[11px] text-center uppercase leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">
                            <?php echo $game['name']; ?>
                        </h3>
                    </div>
                </a>
                
            <?php endwhile; 
            else: ?>
                <div class="col-span-full text-center py-12 opacity-60 text-sm font-medium text-gray-500">No items found.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mx-auto px-2 py-4 max-w-6xl">
        <div class="text-center mb-6 mt-2">
            <h2 class="text-xl md:text-base font-black text-gray-800 tracking-tight uppercase inline-flex flex-col items-center gap-1">
                <span>LATEST RECENT ORDERS</span>
                <span class="w-10 h-[3px] bg-blue-600 rounded-full"></span>
            </h2>
        </div>

        <?php
        // --- Time Ago Function ---
        if(!function_exists('timeAgo')) {
            function timeAgo($time) {
                if (empty($time)) return '';
                date_default_timezone_set('Asia/Dhaka'); 
                
                $time_difference = time() - strtotime($time);
                
                if( $time_difference < 1 ) { return 'Just now'; }
                $condition = array( 
                    12 * 30 * 24 * 60 * 60 =>  'year',
                    30 * 24 * 60 * 60       =>  'month',
                    24 * 60 * 60            =>  'day',
                    60 * 60                 =>  'hour',
                    60                      =>  'min',
                    1                       =>  'sec'
                );
                foreach( $condition as $secs => $str ) {
                    $d = $time_difference / $secs;
                    if( $d >= 1 ) {
                        $t = round( $d );
                        return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
                    }
                }
                return 'Just now';
            }
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <?php 
            $sql = "SELECT o.*, g.name as gname, g.image as gimg, p.name as pname 
                    FROM orders o 
                    JOIN games g ON o.game_id = g.id 
                    JOIN products p ON o.product_id = p.id 
                    ORDER BY o.id DESC LIMIT 10";
            
            $latest_orders = $conn->query($sql);
            
            if($latest_orders && $latest_orders->num_rows > 0):
                while($order = $latest_orders->fetch_assoc()): 
                    $pid = $order['player_id'];
                    $masked_pid = (strlen($pid) > 5) ? substr($pid, 0, 3) . '***' . substr($pid, -2) : 'Hidden';
                    
                    // ডাইনামিক স্ট্যাটাস কালার ও আইকন লজিক
                    $status = strtolower($order['status']);
                    $statusBg = 'bg-gray-100'; $statusText = 'text-gray-700'; 
                    $iconBg = 'bg-gray-500'; $iconName = 'fa-clock';

                    if($status == 'completed' || $status == 'success') {
                        $statusBg = 'bg-[#ecfdf5]'; $statusText = 'text-[#16a34a]';
                        $iconBg = 'bg-[#22c55e]'; $iconName = 'fa-check';
                    } elseif($status == 'pending') {
                        $statusBg = 'bg-yellow-50'; $statusText = 'text-yellow-600';
                        $iconBg = 'bg-yellow-500'; $iconName = 'fa-hourglass-half';
                    } elseif($status == 'processing') {
                        $statusBg = 'bg-blue-50'; $statusText = 'text-blue-600';
                        $iconBg = 'bg-blue-500'; $iconName = 'fa-spinner fa-spin';
                    } elseif($status == 'cancelled') {
                        $statusBg = 'bg-red-50'; $statusText = 'text-red-600';
                        $iconBg = 'bg-red-500'; $iconName = 'fa-xmark';
                    }
            ?>
            
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3.5 hover:shadow-md hover:border-blue-200 transition-all duration-300 group">
                
                <div class="relative w-[72px] h-[72px] shrink-0">
                    <img src="<?php echo $order['gimg']; ?>" class="w-full h-full rounded-[12px] object-cover shadow-sm border border-gray-50">
                    <div class="absolute -bottom-1 -right-1 <?php echo $iconBg; ?> text-white w-4 h-4 rounded-full flex items-center justify-center text-[8px] border-2 border-white shadow-sm">
                        <i class="fa-solid <?php echo $iconName; ?>"></i>
                    </div>
                </div>

                <div class="flex-1 min-w-0 flex flex-col justify-between h-[72px] py-0.5">
                    
                    <div class="flex justify-between items-center mb-0.5">
                        <h4 class="font-extrabold text-[#1f2937] text-[13px] md:text-[14px] truncate pr-2 leading-none group-hover:text-blue-600 transition-colors"><?php echo $order['gname']; ?></h4>
                        <span class="text-[9px] text-[#6b7280] bg-[#f9fafb] border border-gray-100 px-1.5 py-0.5 rounded-md font-medium whitespace-nowrap flex items-center gap-1 shrink-0">
                            <i class="fa-regular fa-clock"></i> <?php echo timeAgo($order['created_at']); ?>
                        </span>
                    </div>
                    
                    <p class="text-[11px] font-bold text-blue-600 truncate leading-tight"><?php echo $order['pname']; ?></p>
                    
                    <p class="text-[10px] text-gray-500 font-medium leading-tight">ID: <span class="text-gray-700 font-bold"><?php echo $masked_pid; ?></span></p>
                    
                    <div class="flex justify-between items-end mt-auto">
                        <span class="text-[13px] font-extrabold text-[#ff6b00] leading-none">৳<?php echo (float)$order['amount']; ?></span>
                        <span class="text-[9px] <?php echo $statusBg . ' ' . $statusText; ?> px-2 py-0.5 rounded font-black uppercase tracking-wider leading-none"><?php echo $status; ?></span>
                    </div>
                    
                </div>
            </div>
            
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-span-full text-center py-8 opacity-60 text-sm font-medium text-gray-500 bg-white rounded-xl border border-gray-100">
                    No recent orders found yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

<script>
    let idx = 0;
    const slides = document.getElementById('slider');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides ? slides.children.length : 0;

    function updateSlider() {
        if(!slides) return;
        slides.style.transform = `translateX(-${idx * 100}%)`;
        
        dots.forEach((dot, i) => {
            if(i === idx) {
                dot.classList.add('bg-blue-600', 'w-4');
                dot.classList.remove('bg-white/40');
            } else {
                dot.classList.remove('bg-blue-600', 'w-4');
                dot.classList.add('bg-white/40');
            }
        });
    }

    if(totalSlides > 1) {
        setInterval(() => {
            idx = (idx + 1) % totalSlides;
            updateSlider();
        }, 3000); 
    }
</script>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>