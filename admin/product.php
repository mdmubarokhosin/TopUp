<?php 
include 'common/header.php'; 

// Add Product using Prepared Statement (SQL Injection Safe)
if (isset($_POST['add'])) {
    $gid = $_POST['game_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO products (game_id, name, price) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $gid, $name, $price);
    
    if ($stmt->execute()) {
        // Redirect to prevent form resubmission on refresh
        echo "<script>window.location='product.php';</script>";
        exit;
    }
}

// Delete Product
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>window.location='product.php';</script>";
        exit;
    }
}
?>

<!-- Premium UI Container with Fade-in Animation -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-4 md:p-6 animate-fade-in">
    
    <!-- Add Product Card -->
    <div class="md:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit transition-all duration-300 hover:shadow-md">
        <div class="flex items-center gap-3 mb-6 border-b pb-3">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fa-solid fa-plus-circle text-lg"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Add New Product</h3>
        </div>
        
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Game</label>
                <select name="game_id" class="w-full border border-gray-200 p-3 rounded-xl bg-gray-50 text-gray-700 focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all duration-200" required>
                    <option value="" disabled selected>Choose a game...</option>
                    <?php 
                    $games = $conn->query("SELECT * FROM games");
                    while($g = $games->fetch_assoc()): ?>
                        <option value="<?php echo $g['id']; ?>">
                            <?php echo htmlspecialchars($g['name']); ?> (<?php echo htmlspecialchars($g['type']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Package Name</label>
                <input type="text" name="name" placeholder="e.g. 100 Diamonds / Weekly" class="w-full border border-gray-200 p-3 rounded-xl text-gray-700 placeholder-gray-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all duration-200" required>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Selling Price</label>
                <div class="relative">
                    <span class="absolute left-3 top-3.5 text-gray-400 font-medium text-sm"><?php echo getSetting($conn, 'currency'); ?></span>
                    <input type="number" step="0.01" name="price" placeholder="0.00" class="w-full border border-gray-200 p-3 pl-8 rounded-xl text-gray-700 placeholder-gray-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all duration-200" required>
                </div>
            </div>
            
            <button type="submit" name="add" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                Add Product
            </button>
        </form>
    </div>

    <!-- Product List Card -->
    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center gap-3 mb-6 border-b pb-3">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <i class="fa-solid fa-list-ul text-lg"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Product List</h3>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <div class="overflow-y-auto max-h-[520px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="p-4 font-semibold">Game</th>
                            <th class="p-4 font-semibold">Package Name</th>
                            <th class="p-4 font-semibold">Price</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $prods = $conn->query("SELECT p.*, g.name as gname FROM products p JOIN games g ON p.game_id = g.id ORDER BY p.id DESC");
                        if($prods->num_rows > 0):
                            while($p = $prods->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50/80 transition-all duration-200 group transform hover:scale-[1.005]">
                                <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($p['gname']); ?></td>
                                <td class="p-4 text-gray-600 font-medium"><?php echo htmlspecialchars($p['name']); ?></td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-green-50 text-green-700">
                                        <?php echo getSetting($conn, 'currency').number_format($p['price'], 2); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="?del=<?php echo $p['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this package?')" 
                                       class="inline-flex items-center justify-center w-9 h-9 text-red-500 bg-red-50 rounded-xl hover:bg-red-500 hover:text-white transition-all duration-300 transform hover:rotate-12 active:scale-95"
                                       title="Delete Product">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; 
                        else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400 font-medium">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 block text-gray-300 animate-bounce"></i>
                                    No products found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Extra Smooth Animations -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
</style>

</body>
</html>
