<?php include 'common/header.php'; 

// Secure Delete Operation with Prepared Statement
if(isset($_GET['del'])) {
    $delete_id = (int)$_GET['del']; // Type casting to prevent SQL Injection
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if($stmt->execute()) {
        echo "<script>window.location='user.php';</script>";
        exit;
    }
}
$currency = getSetting($conn, 'currency');
?>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Tailwind Play CDN (Latest V4) -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">
    
    <!-- Table Header Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-1">
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-1.5 h-5 bg-blue-600 rounded-full"></span>
                User Management
            </h3>
            <p class="text-slate-400 text-xs mt-0.5">Manage, track, and review all registered users</p>
        </div>
    </div>

    <!-- Premium Table Wrapper (Horizontal scroll for mobile) -->
    <div class="animate-fade-in-up bg-white rounded-3xl border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-wider">
                        <th class="p-4 pl-6">ID</th>
                        <th class="p-4">User Details</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Wallet Balance</th>
                        <th class="p-4">Join Date</th>
                        <th class="p-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $users = $conn->query("SELECT * FROM users ORDER BY id DESC");
                    if($users->num_rows > 0):
                        while($u = $users->fetch_assoc()): 
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200 group">
                        <!-- ID -->
                        <td class="p-4 pl-6 font-semibold text-slate-500 text-xs">
                            #<?php echo $u['id']; ?>
                        </td>
                        
                        <!-- Name & Email -->
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm uppercase">
                                    <?php echo mb_substr($u['name'], 0, 1, 'utf-8'); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors duration-150"><?php echo htmlspecialchars($u['name']); ?></div>
                                    <div class="text-xs text-slate-400 font-medium mt-0.5"><?php echo htmlspecialchars($u['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Phone -->
                        <td class="p-4 font-medium text-slate-600">
                            <?php echo htmlspecialchars($u['phone']); ?>
                        </td>
                        
                        <!-- Balance -->
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-100/50">
                                <span class="text-[11px] font-normal text-emerald-600/80"><?php echo $currency; ?></span><?php echo number_format($u['balance'], 2); ?>
                            </span>
                        </td>
                        
                        <!-- Joined Date -->
                        <td class="p-4 text-slate-400 text-xs font-semibold">
                            <i class="far fa-calendar text-slate-300 mr-1"></i>
                            <?php echo date('d M, Y', strtotime($u['created_at'])); ?>
                        </td>
                        
                        <!-- Actions -->
                        <td class="p-4 pr-6 text-right">
                            <a href="?del=<?php echo $u['id']; ?>" 
                               onclick="return confirm('Are you sure you want to permanently delete/ban this user?')" 
                               class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-100 hover:border-red-500 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 active:scale-95 shadow-sm cursor-pointer">
                                <i class="fas fa-user-minus text-[10px]"></i>
                                <span>Delete</span>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 font-medium">
                            <i class="fas fa-users-slash text-2xl mb-2 block text-slate-300"></i>
                            No users found in the database.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
