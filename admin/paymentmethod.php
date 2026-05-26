<?php include 'common/header.php'; 

if(isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $num = $conn->real_escape_string($_POST['number']);
    $desc = $conn->real_escape_string($_POST['description']);
    $short = $conn->real_escape_string($_POST['short_desc']);
    
    $logoPath = "";
    $qrPath = "";
    $uploadDir = "../uploads/";
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    // Handle Logo Upload
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $newName = "pay_logo_" . time() . "." . $ext;
            if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $newName)) {
                $logoPath = "uploads/" . $newName;
            }
        }
    }

    // Handle QR Upload
    if(isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['qr_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $newName = "pay_qr_" . time() . "." . $ext;
            if(move_uploaded_file($_FILES['qr_image']['tmp_name'], $uploadDir . $newName)) {
                $qrPath = "uploads/" . $newName;
            }
        }
    }
    
    $conn->query("INSERT INTO payment_methods (name, number, description, short_desc, logo, qr_image) VALUES ('$name', '$num', '$desc', '$short', '$logoPath', '$qrPath')");
    echo "<script>window.location='paymentmethod.php';</script>";
}

if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $conn->query("DELETE FROM payment_methods WHERE id=".$del_id);
    echo "<script>window.location='paymentmethod.php';</script>";
}
?>

<!-- ফর্ম সেকশন (Clean White Dashboard Style) -->
<div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 transition-all duration-300 hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-gray-100">
        <div class="w-2 h-6 bg-blue-600 rounded-full animate-pulse"></div>
        <h3 class="font-bold text-xl text-gray-800 tracking-tight">Add Payment Method</h3>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Method Name</label>
            <input type="text" name="name" placeholder="e.g. Bkash, Rocket" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700" required>
        </div>
        
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Wallet Number</label>
            <input type="text" name="number" placeholder="017xxxxxxxx" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700" required>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Method Logo</label>
            <input type="file" name="logo" accept="image/*" class="w-full border border-gray-200 p-2.5 rounded-xl bg-white text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition-all cursor-pointer">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">QR Code Image</label>
            <input type="file" name="qr_image" accept="image/*" class="w-full border border-gray-200 p-2.5 rounded-xl bg-white text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-600 hover:file:bg-green-100 transition-all cursor-pointer">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Short Description</label>
            <input type="text" name="short_desc" placeholder="e.g. Send Money Only (Personal)" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700" required>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Full Instructions</label>
            <textarea name="description" rows="3" placeholder="Step by step payment instructions..." class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-gray-700 resize-none"></textarea>
        </div>

        <button type="submit" name="add" class="bg-blue-600 text-white py-3.5 rounded-xl font-medium shadow-sm shadow-blue-100 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 md:col-span-2 text-sm tracking-wide">
            Save Payment Method
        </button>
    </form>
</div>

<!-- লিস্ট সেকশন (Premium Cards with Hover Animation) -->
<div class="grid grid-cols-1 gap-4">
    <?php 
    $methods = $conn->query("SELECT * FROM payment_methods ORDER BY id DESC");
    while($m = $methods->fetch_assoc()): 
    ?>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center gap-5 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group">
        
        <!-- লোগো কন্টেইনার -->
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center overflow-hidden border border-gray-100 p-2 group-hover:scale-105 transition-transform duration-300 shrink-0">
            <?php if(!empty($m['logo'])): ?>
                <img src="../<?php echo htmlspecialchars($m['logo']); ?>" class="w-full h-full object-contain">
            <?php else: ?>
                <span class="text-2xl font-black text-gray-300 uppercase"><?php echo substr(htmlspecialchars($m['name']), 0, 1); ?></span>
            <?php endif; ?>
        </div>

        <!-- ডিটেইলস সেকশন -->
        <div class="flex-1 space-y-1">
            <div class="flex flex-wrap items-center gap-2.5">
                <h4 class="font-bold text-lg text-gray-800 tracking-tight">
                    <?php echo htmlspecialchars($m['name']); ?>
                </h4>
                <span class="text-xs bg-gray-50 text-gray-700 px-3 py-1 rounded-full border border-gray-100 font-mono font-medium shadow-2xs">
                    <?php echo htmlspecialchars($m['number']); ?>
                </span>
            </div>
            <p class="text-sm text-gray-400 font-medium"><?php echo htmlspecialchars($m['short_desc']); ?></p>
            <?php if(!empty($m['description'])): ?>
                <p class="text-xs text-gray-400 line-clamp-1 bg-gray-50/50 p-1.5 rounded-lg border border-dashed border-gray-100 inline-block"><?php echo htmlspecialchars($m['description']); ?></p>
            <?php endif; ?>
        </div>

        <!-- অ্যাকশন এবং ব্যাজ বাটন -->
        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-50">
            <?php if(!empty($m['qr_image'])): ?> 
                <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full font-medium flex items-center gap-1.5 border border-emerald-100/60 shadow-2xs">
                    <i class="fa-solid fa-qrcode text-emerald-500 animate-pulse"></i> QR Active
                </span>
            <?php endif; ?>
            
            <a href="?del=<?php echo $m['id']; ?>" onclick="return confirm('Are you sure you want to delete this payment method?')" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2.5 rounded-xl active:scale-95 transition-all duration-200" title="Delete Method">
                <i class="fa-solid fa-trash-can text-base"></i>
            </a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
