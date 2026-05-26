<?php 
include 'common/header.php'; 
?>

<div class="bg-[#F0F4F9] font-sans pb-4">
    <div class="container mx-auto px-2 py-6 max-w-6xl">
        
        <div class="flex items-center gap-2 mb-8 border-b border-gray-200 pb-3 mt-2 px-1">
            <i class="fa-solid fa-gamepad text-blue-600 text-xl"></i>
            <h2 class="text-lg md:text-xl font-black text-gray-800 tracking-tight uppercase">All Products</h2>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-x-2.5 gap-y-5 md:gap-x-4 md:gap-y-8">
            <?php 
            $games = $conn->query("SELECT * FROM games ORDER BY name ASC");
            if($games->num_rows > 0):
                while($game = $games->fetch_assoc()): ?>
                
                <a href="game_detail.php?id=<?php echo $game['id']; ?>" 
                   class="bg-white rounded-md border border-gray-200 shadow-[0_1px_0_0_#2563eb] flex flex-col group">
                    
                    <div class="relative aspect-square bg-white border-b border-gray-100 z-10 rounded-t-[6px]">
                        <img src="<?php echo $game['image']; ?>" class="w-full h-full object-cover relative transform transition-transform duration-300 rounded-t-[6px] group-hover:translate-y-[12px] group-active:translate-y-[12px]" alt="<?php echo $game['name']; ?>">
                    </div>

                    <div class="py-3 px-1 md:py-5 flex-1 flex items-center justify-center bg-white rounded-b-md relative">
                        <h3 class="font-bold text-gray-800 text-[10px] md:text-[11px] text-center uppercase leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">
                            <?php echo $game['name']; ?>
                        </h3>
                    </div>
                </a>
                
            <?php endwhile; 
            else: ?>
                <div class="col-span-full text-center py-12 opacity-60 text-sm font-medium text-gray-500">No games available.</div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>