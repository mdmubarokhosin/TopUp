<?php 
include 'common/header.php'; 

if(isset($_POST['update'])) {
    foreach($_POST as $key => $val) {
        if($key == 'update' || $key == 'new_pass') continue; 
        $val = $conn->real_escape_string($val);
        $check = $conn->query("SELECT id FROM settings WHERE name='$key'");
        if($check->num_rows > 0) {
            $conn->query("UPDATE settings SET value='$val' WHERE name='$key'");
        } else {
            $conn->query("INSERT INTO settings (name, value) VALUES ('$key', '$val')");
        }
    }
    
    // লোগো আপলোড সিস্টেম
    if(isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif");
        $filename = $_FILES['site_logo']['name'];
        $filesize = $_FILES['site_logo']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(array_key_exists(strtolower($ext), $allowed)) {
            if($filesize <= 5 * 1024 * 1024) {
                $new_filename = "logo_" . time() . "." . $ext;
                if (!file_exists('uploads')) mkdir('uploads', 0777, true);
                if(move_uploaded_file($_FILES['site_logo']['tmp_name'], "uploads/" . $new_filename)) {
                    $old_logo = getSetting($conn, 'logo_url');
                    if(!empty($old_logo) && file_exists("uploads/" . $old_logo)) unlink("uploads/" . $old_logo);
                    
                    $checkLogo = $conn->query("SELECT id FROM settings WHERE name='logo_url'");
                    if($checkLogo->num_rows > 0) {
                        $conn->query("UPDATE settings SET value='$new_filename' WHERE name='logo_url'");
                    } else {
                        $conn->query("INSERT INTO settings (name, value) VALUES ('logo_url', '$new_filename')");
                    }
                }
            } else {
                echo "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 shadow-sm'>Error: File size is too large (Max 5MB).</div>";
            }
        } else {
            echo "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 shadow-sm'>Error: Invalid file format. Only JPG, JPEG, PNG, GIF allowed.</div>";
        }
    }
    
    if(!empty($_POST['new_pass'])) {
        $np = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $aid = $_SESSION['admin_id'];
        $conn->query("UPDATE admins SET password='$np' WHERE id=$aid");
    }
    
    echo "<div class='bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-center'>Settings Updated Successfully!</div>";
}
?>

<style>
    .premium-input { transition: all 0.3s ease; }
    .premium-input:focus { box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); border-color: #3b82f6; }
</style>

<div class="max-w-6xl mx-auto py-8">
    <form method="POST" enctype="multipart/form-data" class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="md:col-span-2">
            <h2 class="text-xl font-extrabold border-b border-gray-100 pb-3 mb-2">General Settings</h2>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Site Name</label>
            <input type="text" name="site_name" value="<?php echo getSetting($conn, 'site_name'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Currency Symbol</label>
            <input type="text" name="currency" value="<?php echo getSetting($conn, 'currency'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>
        
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 items-center bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Site Logo</label>
                <input type="file" name="site_logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:bg-blue-600 file:text-white cursor-pointer border border-gray-200 bg-white rounded-full">
            </div>
            <div class="flex flex-col items-center">
                <span class="text-xs uppercase text-gray-500 font-bold mb-3">Current Logo</span>
                <?php $logo = getSetting($conn, 'logo_url'); if(!empty($logo) && file_exists("uploads/" . $logo)): ?>
                    <img src="uploads/<?php echo $logo; ?>" class="max-h-20 object-contain">
                <?php endif; ?>
            </div>
        </div>

        <div class="md:col-span-2 mt-2">
            <h2 class="text-xl font-extrabold text-blue-600 border-b border-gray-100 pb-3 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-bolt"></i> Auto Payment Gateway 
            </h2>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Bohudur API Key</label>
            <input type="text" name="bohudur_api_key" value="<?php echo getSetting($conn, 'bohudur_api_key'); ?>" placeholder="Enter your Bohudur API Key" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
            <p class="text-xs text-gray-400 mt-1">Get your API key from <a href="https://bohudur.one" target="_blank" class="text-blue-500 hover:underline">bohudur.one</a> dashboard</p>
        </div>
        
        <div class="md:col-span-2 mt-2">
            <h2 class="text-xl font-extrabold border-b border-gray-100 pb-3 mb-2">Name Checking API</h2>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">API Key</label>
            <input type="text" name="name_checking_api_key" value="<?php echo getSetting($conn, 'name_checking_api_key'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>

        <div class="md:col-span-2 mt-2">
            <h2 class="text-xl font-extrabold border-b border-gray-100 pb-3 mb-2">Social Media & Links</h2>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
            <input type="text" name="fb_url" value="<?php echo getSetting($conn, 'fb_url'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Messenger (TG) URL</label>
            <input type="text" name="messenger_url" value="<?php echo getSetting($conn, 'messenger_url'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube Channel URL</label>
            <input type="text" name="yt_url" value="<?php echo getSetting($conn, 'yt_url'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">FAB (Support) Link</label>
            <input type="text" name="fab_link" value="<?php echo getSetting($conn, 'fab_link'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>

        <div class="md:col-span-2 mt-2">
            <h2 class="text-xl font-extrabold border-b border-gray-100 pb-3 mb-2">Marquee Notification</h2>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Marquee Status</label>
            <select name="marquee_active" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
                <option value="1" <?php echo getSetting($conn, 'marquee_active')=='1'?'selected':''; ?>>🟢 Active</option>
                <option value="0" <?php echo getSetting($conn, 'marquee_active')=='0'?'selected':''; ?>>🔴 Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Marquee Text</label>
            <input type="text" name="marquee_text" value="<?php echo getSetting($conn, 'marquee_text'); ?>" class="premium-input w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl outline-none">
        </div>

        <div class="md:col-span-2 mt-2">
            <h2 class="text-xl font-extrabold text-red-600 border-b border-red-100 pb-3 mb-2">Admin Security</h2>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Change Admin Password</label>
            <input type="password" name="new_pass" placeholder="Leave empty to keep current" class="premium-input w-full bg-red-50/50 border border-red-200 text-gray-800 p-3 rounded-xl outline-none focus:border-red-400">
        </div>

        <div class="md:col-span-2 mt-6">
            <button type="submit" name="update" class="bg-blue-600 text-white px-10 py-3.5 rounded-xl shadow-md font-bold hover:bg-blue-700 transition-all">
                Save All Settings
            </button>
        </div>
    </form>
</div>
</body></html>
