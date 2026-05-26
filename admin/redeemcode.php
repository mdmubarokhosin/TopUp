<?php 
include 'common/header.php'; 

// Add Redeem Code (SQL Injection Protected)
if(isset($_POST['add_code'])) {
    $pid = $_POST['product_id'];
    $code = trim($_POST['code']);
    
    $stmt = $conn->prepare("INSERT INTO redeem_codes (product_id, code, status) VALUES (?, ?, 'active')");
    $stmt->bind_param("is", $pid, $code);
    
    if($stmt->execute()) {
        echo "<script>window.location=window.location.href;</script>";
        exit;
    }
}

// Assign Code to Order
if(isset($_POST['assign_order'])) {
    $cid = $_POST['code_id'];
    $oid = $_POST['order_id'];
    
    // Update code status
    $stmt1 = $conn->prepare("UPDATE redeem_codes SET order_id = ?, status = 'used' WHERE id = ?");
    $stmt1->bind_param("ii", $oid, $cid);
    
    // Auto complete order
    $stmt2 = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $stmt2->bind_param("i", $oid);
    
    if($stmt1->execute() && $stmt2->execute()) {
        echo "<script>window.location=window.location.href;</script>";
        exit;
    }
}
?>

<!-- Main Layout Container with Entry Animation -->
<div class="p-4 md:p-6 max-w-7xl mx-auto space-y-6 animate-fade-in">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Add Redeem Code Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
            <div class="flex items-center gap-3 mb-5 border-b pb-3">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fa-solid fa-ticket text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg">Add Redeem Code</h3>
            </div>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Product</label>
                    <select name="product_id" class="w-full border border-gray-200 p-3 rounded-xl bg-gray-50 text-gray-700 focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none transition-all duration-200" required>
                        <option value="" disabled selected>Choose a Voucher Product...</option>
                        <?php 
                        $prods = $conn->query("SELECT p.id, p.name, g.name as gname FROM products p JOIN games g ON p.game_id=g.id WHERE g.type='voucher'");
                        while($p = $prods->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['gname'] . " - " . $p['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Redeem Code</label>
                    <textarea name="code" placeholder="Enter code here (e.g., UP-12345)" rows="3" class="w-full border border-gray-200 p-3 rounded-xl text-gray-700 placeholder-gray-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none transition-all duration-200" required></textarea>
                </div>
                
                <button type="submit" name="add_code" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-emerald-100 hover:shadow-xl hover:shadow-emerald-200 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Add Code
                </button>
            </form>
        </div>

        <!-- Available Codes List Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
            <div class="flex items-center gap-3 mb-5 border-b pb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg">Available Active Codes</h3>
            </div>
            
            <div class="overflow-y-auto max-h-[310px] pr-2 custom-scrollbar">
                <ul class="space-y-2.5">
                    <?php 
                    $codes = $conn->query("SELECT r.*, p.name FROM redeem_codes r JOIN products p ON r.product_id=p.id WHERE r.status='active' ORDER BY r.id DESC");
                    if($codes->num_rows > 0):
                        while($c = $codes->fetch_assoc()): ?>
                        <li class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50/40 hover:border-blue-100 transition-all duration-150 group">
                            <span class="text-gray-700 font-medium text-sm"><?php echo htmlspecialchars($c['name']); ?></span>
                            <code class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-100 uppercase tracking-wide group-hover:scale-105 transition-all">
                                <?php echo htmlspecialchars($c['code']); ?>
                            </code>
                        </li>
                        <?php endwhile; 
                    else: ?>
                        <div class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-qrcode text-3xl block mb-2 text-gray-300 animate-pulse"></i>
                            No active codes available.
                        </div>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Assign Code Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center gap-3 mb-5 border-b pb-3">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fa-solid fa-truck-ramp-box text-lg"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Assign Code to Pending Order</h3>
        </div>
        
        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Order ID</label>
                <input type="text" name="order_id" placeholder="e.g. 10245" class="w-full border border-gray-200 p-3 rounded-xl text-gray-700 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all duration-200" required>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Available Code</label>
                <select name="code_id" class="w-full border border-gray-200 p-3 rounded-xl bg-gray-50 text-gray-700 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all duration-200" required>
                    <option value="" disabled selected>Choose a Code...</option>
                    <?php 
                    // Reset pointer or fetch again
                    $codes_assign = $conn->query("SELECT r.*, p.name FROM redeem_codes r JOIN products p ON r.product_id=p.id WHERE r.status='active'");
                    while($c = $codes_assign->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>">
                            <?php echo htmlspecialchars($c['name'] . " (" . $c['code'] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" name="assign_order" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-indigo-100 hover:shadow-xl hover:shadow-indigo-200 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 h-[48px]">
                Assign & Complete Order
            </button>
        </form>
    </div>
</div>

<!-- Extra CSS Effects -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

</body>
</html>
