<?php include 'common/header.php'; 

// Handle Game Add with Image Upload
if(isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $type = $conn->real_escape_string($_POST['type']); // Escaped for extra safety
    $desc = $conn->real_escape_string($_POST['description']);
    
    $imagePath = "";
    
    // Image Upload Logic
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Generate unique name
            $newFilename = "game_" . time() . "." . $ext;
            $uploadDir = "../uploads/";
            $dest = $uploadDir . $newFilename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = "uploads/" . $newFilename; // Path to store in DB
            } else {
                echo "<script>alert('Failed to move uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file format. Only JPG, PNG, WEBP allowed.');</script>";
        }
    }

    if(!empty($imagePath)) {
        $conn->query("INSERT INTO games (name, type, description, image) VALUES ('$name', '$type', '$desc', '$imagePath')");
        echo "<script>window.location='game.php';</script>";
    } else {
        echo "<script>alert('Please upload an image.');</script>";
    }
}

// Delete Logic
if(isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $conn->query("DELETE FROM games WHERE id=$id");
    echo "<script>window.location='game.php';</script>";
}
?>

<!-- মডার্ন ফর্ম সেকশন (Clean Dashboard Design) -->
<div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 transition-all duration-300 hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-gray-100">
        <div class="w-2 h-6 bg-blue-600 rounded-full"></div>
        <h3 class="font-bold text-xl text-gray-800 tracking-tight">Add New Game</h3>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Game Name</label>
            <input type="text" name="name" placeholder="e.g. Free Fire, PUBG Mobile" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700" required>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Type</label>
            <div class="relative">
                <select name="type" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700 appearance-none cursor-pointer">
                    <option value="uid">Player ID (UID) Top Up</option>
                    <option value="voucher">Voucher / Pin Code</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 text-xs">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Game Cover Image</label>
            <input type="file" name="image" accept="image/*" class="w-full border border-gray-200 p-2.5 rounded-xl bg-white text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition-all cursor-pointer" required>
            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1"><i class="fa-solid fa-circle-info text-blue-500"></i> Supported formats: JPG, PNG, WEBP. Max size: 2MB.</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Description / Rules</label>
            <textarea name="description" rows="3" placeholder="Enter special rules, conditions, or event info for this game..." class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700 resize-none"></textarea>
        </div>

        <button type="submit" name="add" class="bg-blue-600 text-white py-3.5 rounded-xl font-medium shadow-sm shadow-blue-100 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 md:col-span-2 text-sm tracking-wide">
            Add Game Dashboard
        </button>
    </form>
</div>

<!-- গেম লিস্ট সেকশন (Premium Cards Grid) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php 
    $games = $conn->query("SELECT * FROM games ORDER BY id DESC"); 
    while($g = $games->fetch_assoc()): 
    ?>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 flex items-center gap-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
        
        <!-- গেম থাম্বনেইল/কভার -->
        <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shrink-0 shadow-2xs">
            <img src="../<?php echo htmlspecialchars($g['image']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Game Logo">
        </div>
        
        <!-- গেমের তথ্য -->
        <div class="flex-1 space-y-1.5">
            <h4 class="font-bold text-gray-800 text-base tracking-tight group-hover:text-blue-600 transition-colors">
                <?php echo htmlspecialchars($g['name']); ?>
            </h4>
            
            <!-- কাস্টম ব্যাজ স্টাইল -->
            <?php if($g['type'] == 'uid'): ?>
                <span class="inline-flex items-center text-[10px] bg-sky-50 text-sky-700 px-2.5 py-0.5 rounded-md font-bold uppercase border border-sky-100 tracking-wider">
                    <span class="w-1 h-1 bg-sky-500 rounded-full mr-1 animate-pulse"></span> UID Topup
                </span>
            <?php else: ?>
                <span class="inline-flex items-center text-[10px] bg-purple-50 text-purple-700 px-2.5 py-0.5 rounded-md font-bold uppercase border border-purple-100 tracking-wider">
                    <span class="w-1 h-1 bg-purple-500 rounded-full mr-1 animate-pulse"></span> Voucher
                </span>
            <?php endif; ?>
        </div>
        
        <!-- ডিলিট বাটন -->
        <a href="?del=<?php echo $g['id']; ?>" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2.5 rounded-xl active:scale-95 transition-all duration-200" onclick="return confirm('Are you sure you want to delete this game?')" title="Delete Game">
            <i class="fa-solid fa-trash-can text-base"></i>
        </a>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
