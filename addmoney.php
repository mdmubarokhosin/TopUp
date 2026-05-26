<?php 
include 'common/header.php'; 

// Helper Function to convert ANY YouTube link to Embed link
function getYoutubeEmbedUrl($url) {
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[1];
    }
    return isset($youtube_id) ? 'https://www.youtube.com/embed/' . $youtube_id : $url;
}

$videoLink = getSetting($conn, 'add_money_video');
$embedLink = getYoutubeEmbedUrl($videoLink);
?>

<div class="bg-[#F0F4F9] min-h-screen pb-24 font-sans">
    <div class="container mx-auto px-4 py-8 max-w-xl">
        
        <div class="mb-6">
            <h2 class="font-extrabold text-gray-900 text-xl tracking-tight">Add Money</h2>
        </div>
        
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-100 shadow-sm mb-6">
            
            <form action="payment.php" method="GET" id="addMoneyForm" onsubmit="return validateAmount()">
                <input type="hidden" name="game_id" value="0">
                <input type="hidden" name="product_id" value="0">
                <input type="hidden" name="player_id" value="Wallet Deposit">

                <div class="text-center mb-6">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Enter Amount</label>
                    
                    <div class="relative max-w-xs mx-auto flex items-center">
                        <span class="absolute left-5 text-gray-400 font-bold text-2xl select-none">৳</span>
                        <input type="number" id="amountInput" class="w-full bg-gray-50 border border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 text-3xl font-bold text-center text-gray-900 py-3 pl-10 pr-5 rounded-xl transition-all outline-none" placeholder="500" name="amount" required min="10">
                    </div>
                </div>

                <div class="grid grid-cols-5 gap-2 mb-6 max-w-sm mx-auto">
                    <button type="button" onclick="setAmount(100)" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg transition-colors">100</button>
                    <button type="button" onclick="setAmount(200)" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg transition-colors">200</button>
                    <button type="button" onclick="setAmount(500)" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg transition-colors">500</button>
                    <button type="button" onclick="setAmount(1000)" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg transition-colors">1000</button>
                    <button type="button" onclick="setAmount(2000)" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg transition-colors">2000</button>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm transition-colors shadow-sm uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bolt text-xs"></i> Proceed to Auto Payment
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
                <h3 class="font-bold text-gray-900 text-sm">How to Add Money</h3>
            </div>
            
            <div class="p-5">
                <?php if($videoLink): ?>
                <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden relative border border-gray-100 shadow-sm">
                    <iframe class="w-full h-full" src="<?php echo $embedLink; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-gray-400 text-xs font-medium">No video tutorial available at the moment.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
function setAmount(amount) {
    const input = document.getElementById('amountInput');
    if(input) {
        input.value = amount;
        input.focus();
    }
}
function validateAmount() {
    const input = document.getElementById('amountInput');
    const val = parseFloat(input ? input.value : 0);
    if (val < 10) {
        alert('Minimum amount is 10');
        return false;
    }
    return true;
}
</script>

<?php include 'common/footer.php'; ?>
<?php include 'common/bottom.php'; ?>