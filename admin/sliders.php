<?php include 'common/header.php'; 

if(isset($_POST['add'])) {
    $link = $conn->real_escape_string($_POST['link']);
    $imagePath = "";

    // Image Upload Logic
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $newFilename = "slider_" . time() . "." . $ext;
            $uploadDir = "../uploads/";
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                $imagePath = "uploads/" . $newFilename;
            }
        }
    }

    if(!empty($imagePath)) {
        $conn->query("INSERT INTO sliders (image, link) VALUES ('$imagePath', '$link')");
        echo "<script>window.location='sliders.php';</script>";
    } else {
        echo "<script>alert('Failed to upload image. Please check format.');</script>";
    }
}

if(isset($_GET['del'])) {
    // নিরাপত্তা জোরদার করতে intval ব্যবহার করা হয়েছে
    $del_id = intval($_GET['del']);
    $conn->query("DELETE FROM sliders WHERE id=".$del_id);
    echo "<script>window.location='sliders.php';</script>";
}
?>

<div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 transition-all duration-300 hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-gray-100">
        <div class="w-2 h-6 bg-blue-600 rounded-full"></div>
        <h3 class="font-bold text-xl text-gray-800 tracking-tight">Add New Slider</h3>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-6 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Slider Image</label>
            <div class="relative group">
                <input type="file" name="image" accept="image/*" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm text-gray-600 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all focus:outline-none focus:border-blue-500 cursor-pointer" required>
            </div>
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Direct Link (Optional)</label>
            <input type="text" name="link" placeholder="https://example.com" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder:text-gray-400 text-gray-700">
        </div>
        <button type="submit" name="add" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-medium shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 w-full md:w-auto text-sm tracking-wide">
            Add Slider
        </button>
    </form>
</div>

<!-- গ্রিড লেআউট এবং অ্যানিমেশন ইফেক্ট -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php 
    $sliders = $conn->query("SELECT * FROM sliders ORDER BY id DESC");
    while($s = $sliders->fetch_assoc()): 
    ?>
    <div class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl bg-white border border-gray-100 h-48 transition-all duration-300 hover:-translate-y-1">
        <!-- ইমেজ ট্যাগ -->
        <img src="../<?php echo htmlspecialchars($s['image']); ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" alt="Slider">
        
        <!-- প্রিমিয়াম ওভারলে এবং গ্লাস-মরফিজম ব্লার ইফেক্ট -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/10 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-4 backdrop-blur-[2px]">
            <?php if(!empty($s['link'])): ?>
                <a href="<?php echo htmlspecialchars($s['link']); ?>" target="_blank" class="bg-white/90 text-gray-800 w-11 h-11 rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:text-blue-600 hover:scale-110 active:scale-95 transition-all duration-200" title="Visit Link">
                    <i class="fa-solid fa-link text-sm"></i>
                </a>
            <?php endif; ?>
            <a href="?del=<?php echo $s['id']; ?>" onclick="return confirm('Are you sure you want to delete this slider?')" class="bg-white/90 text-red-500 w-11 h-11 rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 hover:text-red-600 hover:scale-110 active:scale-95 transition-all duration-200" title="Delete Slider">
                <i class="fa-solid fa-trash text-sm"></i>
            </a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
