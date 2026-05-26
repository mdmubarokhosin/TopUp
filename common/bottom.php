<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<nav class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur-md border-t border-gray-100 flex justify-around items-center py-1.5 z-40 shadow-[0_-4px_20px_rgba(0,0,0,0.04)] select-none md:hidden">
    
    <a href="index.php" class="flex flex-col items-center justify-center w-full transition-all duration-200 <?php echo $activePage == 'index.php' ? 'text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-600'; ?>">
        <i class="fa-solid fa-house text-[19px]"></i>
        <span class="text-[10px] mt-1 tracking-wide">Home</span>
        <span class="w-4 h-[3px] bg-blue-600 rounded-full mt-0.5 transition-all duration-300 <?php echo $activePage == 'index.php' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'; ?>"></span>
    </a>
    
    <a href="addmoney.php" class="flex flex-col items-center justify-center w-full transition-all duration-200 <?php echo $activePage == 'addmoney.php' ? 'text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-600'; ?>">
        <i class="fa-solid fa-circle-plus text-[19px]"></i>
        <span class="text-[10px] mt-1 tracking-wide">Add Money</span>
        <span class="w-4 h-[3px] bg-blue-600 rounded-full mt-0.5 transition-all duration-300 <?php echo $activePage == 'addmoney.php' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'; ?>"></span>
    </a>
    
    <a href="order.php" class="flex flex-col items-center justify-center w-full transition-all duration-200 <?php echo $activePage == 'order.php' ? 'text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-600'; ?>">
        <i class="fa-solid fa-cart-shopping text-[19px]"></i>
        <span class="text-[10px] mt-1 tracking-wide">My Orders</span>
        <span class="w-4 h-[3px] bg-blue-600 rounded-full mt-0.5 transition-all duration-300 <?php echo $activePage == 'order.php' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'; ?>"></span>
    </a>
    
    <a href="mycode.php" class="flex flex-col items-center justify-center w-full transition-all duration-200 <?php echo $activePage == 'mycode.php' ? 'text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-600'; ?>">
        <i class="fa-solid fa-ticket text-[19px]"></i>
        <span class="text-[10px] mt-1 tracking-wide">Codes</span>
        <span class="w-4 h-[3px] bg-blue-600 rounded-full mt-0.5 transition-all duration-300 <?php echo $activePage == 'mycode.php' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'; ?>"></span>
    </a>
    
    <a href="profile.php" class="flex flex-col items-center justify-center w-full transition-all duration-200 <?php echo $activePage == 'profile.php' ? 'text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-600'; ?>">
        <i class="fa-solid fa-circle-user text-[19px]"></i>
        <span class="text-[10px] mt-1 tracking-wide">Profile</span>
        <span class="w-4 h-[3px] bg-blue-600 rounded-full mt-0.5 transition-all duration-300 <?php echo $activePage == 'profile.php' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'; ?>"></span>
    </a>
</nav>

<a href="<?php echo getSetting($conn, 'fab_link'); ?>" target="_blank" class="fixed bottom-20 right-4 w-12 h-12 md:w-14 md:h-14 bg-green-500 rounded-full shadow-lg flex items-center justify-center text-white text-2xl z-50 fab-bounce transition hover:scale-110 md:bottom-6">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<div id="loadingModal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/40 hidden backdrop-blur-sm">
    <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center justify-center w-32 h-32 animate-fade-in">
        <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-3"></div>
        <span class="text-xs font-bold text-gray-500 tracking-wider">LOADING</span>
    </div>
</div>

<div id="notifModal" class="fixed inset-0 z-[260] flex items-center justify-center bg-black/50 hidden backdrop-blur-[2px]">
    <div class="bg-white w-80 rounded-2xl shadow-2xl p-6 text-center transform scale-95 transition-all duration-300" id="notifContent">
        <div id="notifIcon" class="text-5xl mb-4"></div>
        <h3 id="notifTitle" class="text-lg font-bold text-gray-900 mb-2"></h3>
        <p id="notifMsg" class="text-sm text-gray-500 mb-6"></p>
        <button onclick="closeNotif()" class="bg-blue-600 text-white w-full py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-sm active:scale-98">Okay</button>
    </div>
</div>

<script>
    // UI Functional Controllers
    const loader = document.getElementById('loadingModal');
    const notif = document.getElementById('notifModal');
    
    function showLoader() { loader.classList.remove('hidden'); }
    function hideLoader() { loader.classList.add('hidden'); }
    
    function showNotif(type, title, msg) {
        const iconEl = document.getElementById('notifIcon');
        document.getElementById('notifTitle').innerText = title;
        document.getElementById('notifMsg').innerText = msg;
        
        if(type === 'success') {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-check text-green-500 animate-bounce"></i>';
        } else if(type === 'error') {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-xmark text-red-500 animate-pulse"></i>';
        } else {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-info text-blue-500"></i>';
        }
        
        notif.classList.remove('hidden');
        document.getElementById('notifContent').classList.remove('scale-95');
        document.getElementById('notifContent').classList.add('scale-100');
    }

    function closeNotif() {
        notif.classList.add('hidden');
    }

    // Generic AJAX Engine Interceptor Form Data
    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('form.ajax-form');
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                showLoader();
                
                const formData = new FormData(form);
                
                try {
                    const response = await fetch(form.action || window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const text = await response.text();
                    hideLoader();

                    try {
                        const data = JSON.parse(text);
                        if(data.status) {
                            showNotif(data.status, data.title, data.message);
                            if(data.redirect) {
                                setTimeout(() => window.location.href = data.redirect, 1500);
                            }
                        } else {
                            window.location.reload();
                        }
                    } catch(err) {
                         window.location.reload(); 
                    }

                } catch (error) {
                    hideLoader();
                    showNotif('error', 'Network Error', 'Something went wrong.');
                }
            });
        });
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.92); } to { opacity: 1; transform: scale(1); } }
    .fab-bounce { animation: bounce 2s infinite; }
    @keyframes bounce { 0%, 20%, 50%, 80%, 100% {transform: translateY(0);} 40% {transform: translateY(-8px);} 60% {transform: translateY(-4px);} }
</style>
</body>
</html>