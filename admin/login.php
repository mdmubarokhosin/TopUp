<?php
/**
 * Admin Login Page
 */
// Reliable config include - works from any admin subdirectory
$configPaths = [
    __DIR__ . '/../common/config.php',
    dirname(__DIR__) . '/common/config.php'
];
$configLoaded = false;
foreach ($configPaths as $cfg) {
    if (file_exists($cfg)) {
        include $cfg;
        $configLoaded = true;
        break;
    }
}
if (!$configLoaded) {
    die("System Error: Configuration file not found.");
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    header("Location: " . $protocol . "://" . $hostName . $scriptDir . "/index.php");
    exit;
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    if (empty($u) || empty($p)) {
        $err = "Username and password are required.";
    } elseif ($conn) {
        // SQL Injection Protection using Prepared Statements
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (password_verify($p, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Use absolute redirect URL
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                header("Location: " . $protocol . "://" . $hostName . $scriptDir . "/index.php");
                exit;
            }
        }
        $err = "Invalid username or password.";
    } else {
        $err = "Database connection failed. Please check your database settings or delete installed.lock to re-run the installer.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | TopUp</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex items-center justify-center p-4 relative font-sans">

    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] opacity-50 pointer-events-none"></div>
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="animate-fade-in w-full max-w-md bg-white border border-slate-200 p-8 sm:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative z-10">

        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 mb-4 transition-transform duration-300 hover:scale-105">
                <i class="fas fa-shield-halved text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900">CONTROL PANEL</h2>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Authorized Personnel Only</p>
        </div>

        <?php if (isset($err) && $err): ?>
            <div class="mb-6 bg-red-50 border border-red-100 text-red-600 p-3.5 rounded-xl text-sm flex items-center gap-3 animate-fade-in">
                <i class="fas fa-circle-exclamation text-base"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($err); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider px-1">Username</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200">
                        <i class="fas fa-user-shield text-sm"></i>
                    </span>
                    <input type="text" name="username" placeholder="Enter admin username" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl py-3.5 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider px-1">Password</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <input type="password" name="password" placeholder="Enter admin password" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl py-3.5 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                </div>
            </div>

            <button type="submit"
                class="w-full mt-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-blue-500/10 hover:shadow-lg hover:shadow-blue-500/20 active:scale-[0.99] transition-all duration-200 text-sm tracking-wide cursor-pointer flex items-center justify-center gap-2">
                <span>Sign In to Dashboard</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>

        </form>

        <div class="mt-8 text-center">
            <p class="text-slate-400 text-[11px] font-semibold tracking-wide">&copy; <?php echo date('Y'); ?> TopUp Admin. All rights reserved.</p>
        </div>

    </div>

</body>
</html>
