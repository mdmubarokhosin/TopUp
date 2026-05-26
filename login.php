<?php
include 'common/config.php';
if(isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$msg = "";
if(!$conn) {
    $msg = "System is not configured yet. Please complete installation.";
} elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    if($type == 'login') {
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        if (empty($email) || empty($pass)) {
            $msg = "Email/Phone and password are required.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
            $stmt->bind_param("ss", $email, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if(password_verify($pass, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: index.php"); exit;
                } else { $msg = "Invalid Password"; }
            } else { $msg = "User not found"; }
        }
    } elseif($type == 'signup') {
         $name = trim($_POST['name'] ?? '');
         $phone = trim($_POST['phone'] ?? '');
         $email = trim($_POST['email'] ?? '');
         $pass = $_POST['password'] ?? '';

         if (empty($name) || empty($phone) || empty($email) || empty($pass)) {
             $msg = "All fields are required.";
         } else {
             $hashed = password_hash($pass, PASSWORD_DEFAULT);

             $stmt = $conn->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
             $stmt->bind_param("ssss", $name, $phone, $email, $hashed);
             if($stmt->execute()) {
                 $new_user_id = $conn->insert_id;
                 $_SESSION['user_id'] = $new_user_id;
                 header("Location: index.php");
                 exit;
             } else {
                 $msg = "Error: Email or Phone already exists.";
             }
         }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Join the Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* Smooth Fluid Grid Animation for White BG */
        .animated-bg {
            background-color: #f8fafc;
            position: relative;
            overflow: hidden;
        }
        .bg-glow-1 {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, rgba(255,255,255,0) 70%);
            animation: floatAround 8s ease-in-out infinite alternate;
        }
        .bg-glow-2 {
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37,99,235,0.12) 0%, rgba(255,255,255,0) 70%);
            animation: floatAround 12s ease-in-out infinite alternate-reverse;
        }
        @keyframes floatAround {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 40px) scale(1.1); }
        }
    </style>
</head>
<body class="animated-bg flex flex-col items-center justify-between min-h-screen p-4">

<div class="bg-glow-1"></div>
<div class="bg-glow-2"></div>

<div class="my-auto w-full max-w-sm relative z-10">
    <div class="bg-white/75 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-slate-200/50">
        
        <div class="p-8 text-center pb-2">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-sky-500">Welcome Back</h1>
            <p class="text-xs text-slate-500 mt-1">Enter your credentials to access your account.</p>
        </div>

        <div class="px-6 pb-2">
            <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button onclick="switchTab('login')" id="tab-login" class="w-1/2 py-2.5 text-center text-sm font-bold rounded-lg shadow-sm bg-gradient-to-r from-blue-600 to-sky-500 text-white transition-all duration-300">Log In</button>
                <button onclick="switchTab('signup')" id="tab-signup" class="w-1/2 py-2.5 text-center text-sm font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all duration-300">Sign Up</button>
            </div>
        </div>

        <div class="p-6 pt-4">
            <?php if($msg): ?>
                <div class="bg-sky-50 border border-sky-200 text-sky-600 p-3 rounded-xl mb-4 text-center text-xs font-bold shadow-sm animate-pulse"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form id="form-login" method="POST" class="space-y-4">
                <input type="hidden" name="type" value="login">
                <div class="relative group">
                    <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="text" name="email" placeholder="Email or Phone" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 bg-slate-50/50 text-slate-800 placeholder-slate-400 transition-all text-sm font-medium">
                </div>
                <div class="relative group">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 bg-slate-50/50 text-slate-800 placeholder-slate-400 transition-all text-sm font-medium">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-sky-500 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-sky-500/20 hover:opacity-95 hover:scale-[1.01] active:scale-95 transition-all duration-200">
                    Log In Securely
                </button>
            </form>

            <form id="form-signup" method="POST" class="space-y-4 hidden">
                <input type="hidden" name="type" value="signup">
                <div class="relative group">
                    <i class="fa-solid fa-user absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="text" name="name" placeholder="Full Name" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-800 placeholder-slate-400 transition-all text-sm">
                </div>
                <div class="relative group">
                    <i class="fa-solid fa-phone absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="text" name="phone" placeholder="Phone Number" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-800 placeholder-slate-400 transition-all text-sm">
                </div>
                <div class="relative group">
                    <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="email" name="email" placeholder="Email Address" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-800 placeholder-slate-400 transition-all text-sm">
                </div>
                <div class="relative group">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-800 placeholder-slate-400 transition-all text-sm">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-blue-600 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:opacity-95 hover:scale-[1.01] active:scale-95 transition-all duration-200">
                    Create Account
                </button>
            </form>
        </div>
        
        <div class="bg-slate-50/80 py-3 text-center text-[10px] text-slate-400 border-t border-slate-100">
            Developed by <span class="font-bold text-sky-500 tracking-wider">MD MUBAROK</span>
        </div>
    </div>
</div>
<script>
    function switchTab(tab) {
        const loginBtn = document.getElementById('tab-login');
        const signupBtn = document.getElementById('tab-signup');
        
        if(tab === 'login') {
            document.getElementById('form-login').classList.remove('hidden');
            document.getElementById('form-signup').classList.add('hidden');
            
            loginBtn.className = "w-1/2 py-2.5 text-center text-sm font-bold rounded-lg shadow-sm bg-gradient-to-r from-blue-600 to-sky-500 text-white transition-all duration-300";
            signupBtn.className = "w-1/2 py-2.5 text-center text-sm font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all duration-300";
        } else {
            document.getElementById('form-login').classList.add('hidden');
            document.getElementById('form-signup').classList.remove('hidden');
            
            signupBtn.className = "w-1/2 py-2.5 text-center text-sm font-bold rounded-lg shadow-sm bg-gradient-to-r from-sky-500 to-blue-600 text-white transition-all duration-300";
            loginBtn.className = "w-1/2 py-2.5 text-center text-sm font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all duration-300";
        }
    }
</script>
</body>
</html>
