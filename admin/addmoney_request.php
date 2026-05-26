<?php include 'common/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* Custom Premium Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }
</style>

<?php 
// --- Handle Actions (Approve/Reject) ---
if(isset($_POST['action'])) {
    $req_id = (int)$_POST['req_id'];
    $act = $_POST['action'];

    if($act == 'approve') {
        $req = $conn->query("SELECT * FROM deposits WHERE id=$req_id AND status='pending'")->fetch_assoc();
        if($req) {
            $amount = $req['amount'];
            $uid = $req['user_id'];
            $conn->query("UPDATE deposits SET status='approved' WHERE id=$req_id");
            $conn->query("UPDATE users SET balance = balance + $amount WHERE id=$uid");
            
            // SweetAlert Success Message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Balance has been added successfully.',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl shadow-2xl' }
                    }).then(() => { window.location.href='addmoney_request.php?tab=approved'; });
                });
            </script>";
        }
    } elseif($act == 'reject') {
        $conn->query("UPDATE deposits SET status='rejected' WHERE id=$req_id");
        
        // SweetAlert Reject Message
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Rejected!',
                    text: 'Request has been rejected.',
                    confirmButtonColor: '#EF4444',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl shadow-2xl' }
                }).then(() => { window.location.href='addmoney_request.php?tab=rejected'; });
            });
        </script>";
    }
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
?>

<div class="max-w-7xl mx-auto px-4 py-8 animate-fade-in-up">
    
    <!-- Header & Navigation -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Add Money Requests
            </h2>
            <p class="text-gray-500 text-sm mt-1 md:ml-11">Review and manage user deposit requests securely.</p>
        </div>
        
        <!-- Premium Tabs -->
        <div class="bg-gray-50/80 p-1.5 rounded-2xl border border-gray-200/60 flex shadow-sm">
            <a href="?tab=pending" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center <?php echo $tab=='pending'?'bg-white text-blue-600 shadow-[0_4px_12px_rgb(0,0,0,0.05)] border border-gray-100':'text-gray-500 hover:text-gray-800 hover:bg-gray-100/50'; ?>">
                <i class="fa-regular fa-clock mr-2 <?php echo $tab=='pending'?'text-blue-500':''; ?>"></i> Pending
            </a>
            <a href="?tab=approved" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center <?php echo $tab=='approved'?'bg-white text-green-600 shadow-[0_4px_12px_rgb(0,0,0,0.05)] border border-gray-100':'text-gray-500 hover:text-gray-800 hover:bg-gray-100/50'; ?>">
                <i class="fa-regular fa-circle-check mr-2 <?php echo $tab=='approved'?'text-green-500':''; ?>"></i> Approved
            </a>
            <a href="?tab=rejected" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center <?php echo $tab=='rejected'?'bg-white text-red-600 shadow-[0_4px_12px_rgb(0,0,0,0.05)] border border-gray-100':'text-gray-500 hover:text-gray-800 hover:bg-gray-100/50'; ?>">
                <i class="fa-regular fa-circle-xmark mr-2 <?php echo $tab=='rejected'?'text-red-500':''; ?>"></i> Rejected
            </a>
        </div>
    </div>

    <!-- Cards Grid System -->
    <?php 
    $sql = "SELECT d.*, u.name, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status='$tab' ORDER BY d.id DESC";
    $res = $conn->query($sql);
    
    if($res->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $delay = 0.1; // Stagger animation delay counter
            while($row = $res->fetch_assoc()): ?>
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.03)] border border-gray-100 p-6 flex flex-col justify-between hover:shadow-[0_12px_40px_rgb(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group animate-fade-in-up" style="animation-delay: <?php echo $delay; ?>s;">
                    
                    <!-- Card Header: User Info & Amount -->
                    <div>
                        <div class="flex items-start justify-between gap-4 border-b border-gray-50 pb-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 flex items-center justify-center font-extrabold text-lg shadow-inner border border-blue-100/70">
                                    <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-base leading-tight"><?php echo $row['name']; ?></h4>
                                    <span class="text-xs text-gray-400 font-mono flex items-center mt-1">
                                        <i class="fa-solid fa-phone text-[9px] mr-1 opacity-60"></i> <?php echo $row['phone']; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Amount Badge -->
                            <div class="text-right">
                                <span class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight block">
                                    <?php echo getSetting($conn, 'currency') . number_format($row['amount'], 2); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body: Payment Information -->
                        <div class="space-y-2.5 bg-gray-50/60 p-3.5 rounded-xl border border-gray-100 mb-5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Method</span>
                                <span class="font-bold text-gray-700 px-2.5 py-0.5 bg-white rounded-md shadow-sm border border-gray-100/50 text-xs"><?php echo $row['method']; ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Wallet</span>
                                <span class="font-mono font-bold text-gray-700"><?php echo $row['wallet_number']; ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Trx ID</span>
                                <span class="font-mono text-indigo-600 font-bold bg-indigo-50/50 px-2 py-0.5 rounded border border-indigo-100/60 cursor-pointer hover:bg-indigo-100/50 transition-colors flex items-center text-xs" onclick="copyToClipboard('<?php echo $row['trx_id']; ?>')" title="Click to copy">
                                    <?php echo $row['trx_id']; ?> <i class="fa-regular fa-copy ml-1.5 opacity-60"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Time & Actions -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                        <div class="text-[11px] text-gray-400 font-medium">
                            <div class="flex items-center gap-1"><i class="fa-regular fa-calendar-days opacity-60"></i> <?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
                            <div class="flex items-center gap-1 mt-0.5"><i class="fa-regular fa-clock opacity-60"></i> <?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                        </div>

                        <!-- Action Buttons -->
                        <div>
                            <?php if($tab == 'pending'): ?>
                                <div class="flex items-center gap-2">
                                    <button onclick="showDetails('<?php echo $row['trx_id']; ?>', '<?php echo $row['wallet_number']; ?>', '<?php echo $row['method']; ?>', '<?php echo $row['amount']; ?>')" 
                                            class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-sm transition-all transform hover:-translate-y-0.5 flex items-center justify-center" title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    
                                    <form id="approve-form-<?php echo $row['id']; ?>" method="POST" class="m-0">
                                        <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="button" onclick="confirmAction('approve', <?php echo $row['id']; ?>)" 
                                                class="w-9 h-9 rounded-xl bg-green-50/80 border border-green-100 text-green-600 hover:bg-green-500 hover:text-white hover:border-green-500 shadow-sm transition-all transform hover:-translate-y-0.5 hover:shadow-md hover:shadow-green-500/20 flex items-center justify-center" title="Approve">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </form>

                                    <form id="reject-form-<?php echo $row['id']; ?>" method="POST" class="m-0">
                                        <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button" onclick="confirmAction('reject', <?php echo $row['id']; ?>)" 
                                                class="w-9 h-9 rounded-xl bg-red-50/80 border border-red-100 text-red-600 hover:bg-red-500 hover:text-white hover:border-red-500 shadow-sm transition-all transform hover:-translate-y-0.5 hover:shadow-md hover:shadow-red-500/20 flex items-center justify-center" title="Reject">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider <?php echo $tab=='approved'?'bg-green-50 text-green-700 border border-green-100':'bg-red-50 text-red-700 border border-red-100'; ?> shadow-sm">
                                    <i class="fa-solid <?php echo $tab=='approved'?'fa-check':'fa-xmark'; ?>"></i>
                                    <?php echo $tab; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php 
            $delay += 0.05; // Smooth incremental delay
            endwhile; ?>
        </div>
    <?php else: ?>
        <!-- Empty State Card -->
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.03)] border border-gray-100 p-16 text-center animate-fade-in-up">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 mx-auto border border-gray-100">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-base font-semibold text-gray-500">No <?php echo $tab; ?> requests found.</p>
            <p class="text-sm text-gray-400 mt-1">There are currently no records to display here.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    // 1. Details Popup with Animation & Premium UI
    function showDetails(trx, wallet, method, amount) {
        Swal.fire({
            title: '<span class="text-gray-800 font-extrabold text-xl tracking-tight">Transaction Details</span>',
            html: `
                <div class="text-left mt-4">
                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 mb-4 flex justify-between items-center">
                        <span class="text-blue-600/80 text-sm font-semibold uppercase tracking-wider">Amount</span>
                        <span class="font-black text-blue-600 text-2xl">${amount}</span>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                        <div class="flex justify-between items-center border-b border-gray-200/60 pb-3">
                            <span class="text-gray-500 text-sm font-medium">Method</span>
                            <span class="font-bold text-gray-800 px-3 py-1 bg-white rounded-lg shadow-sm border border-gray-100">${method}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-200/60 pb-3">
                            <span class="text-gray-500 text-sm font-medium">Sender Wallet</span>
                            <span class="font-mono text-gray-800 font-bold">${wallet}</span>
                        </div>
                        <div class="flex flex-col pt-1">
                            <span class="text-gray-500 text-sm font-medium mb-2">Transaction ID</span>
                            <div class="font-mono text-indigo-600 font-bold bg-indigo-50/50 p-3 rounded-lg border border-indigo-100 select-all text-center tracking-wider text-lg cursor-copy" title="Select to copy">
                                ${trx}
                            </div>
                        </div>
                    </div>
                </div>
            `,
            showCloseButton: true,
            showConfirmButton: false,
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-gray-50',
                closeButton: 'focus:outline-none text-gray-400 hover:text-red-500 transition-colors'
            },
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            }
        });
    }

    // 2. Confirmation Popup for Approve/Reject
    function confirmAction(type, id) {
        const isApprove = type === 'approve';
        const config = isApprove ? {
            title: 'Approve Request?',
            text: "Amount will be added to user's wallet.",
            iconColor: '#10B981',
            confirmColor: 'bg-green-600 hover:bg-green-700',
            confirmText: 'Yes, Approve!'
        } : {
            title: 'Reject Request?',
            text: "This action cannot be undone.",
            iconColor: '#EF4444',
            confirmColor: 'bg-red-600 hover:bg-red-700',
            confirmText: 'Yes, Reject!'
        };
        
        Swal.fire({
            title: `<span class="font-extrabold text-gray-800">${config.title}</span>`,
            text: config.text,
            icon: isApprove ? 'question' : 'warning',
            iconColor: config.iconColor,
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: config.confirmText,
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-gray-50',
                confirmButton: `px-6 py-2.5 rounded-xl text-white font-bold mx-2 shadow-lg transition-all ${config.confirmColor}`,
                cancelButton: 'px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-all mx-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: '<span class="text-gray-800 font-bold">Processing...</span>',
                    text: 'Please wait a moment',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading() },
                    customClass: { popup: 'rounded-2xl shadow-2xl py-8' }
                });
                // Submit form
                document.getElementById(type + '-form-' + id).submit();
            }
        });
    }

    // 3. Copy to Clipboard Helper
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-xl shadow-lg border border-gray-100 mb-4 mr-4'
            }
        });
        Toast.fire({
            icon: 'success',
            title: '<span class="font-bold text-gray-800">TrxID Copied!</span>',
            iconColor: '#10B981'
        });
    }
</script>

</body>
</html>
