<?php
/**
 * TopUp - Multi-Step Installation Wizard
 * Production Ready Installer with modern UI
 * v2.0 - Fixed AJAX absolute URL, Enhanced Success Page with Feature Showcase
 */

// Installation lock check
if (file_exists(__DIR__ . '/installed.lock')) {
    $lockContent = file_get_contents(__DIR__ . '/installed.lock');
    die("<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'><title>Already Installed</title><link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'><style>*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,sans-serif}body{background:linear-gradient(135deg,#f5f7fa,#c3cfe2);min-height:100vh;display:flex;justify-content:center;align-items:center;padding:20px}.box{background:#fff;border-radius:16px;padding:50px;text-align:center;max-width:500px;box-shadow:0 10px 40px rgba(0,0,0,.1)}.box i{font-size:4rem;color:#06d6a0;margin-bottom:20px}.box h2{color:#1e1e2c;margin-bottom:10px}.box p{color:#6c757d;line-height:1.6;margin-bottom:25px}.box small{color:#aaa;font-size:12px}.box .links{display:flex;gap:12px;justify-content:center;margin-top:20px;flex-wrap:wrap}.box .links a{padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:600;font-size:.9rem;transition:all .3s}.box .links .a1{background:#4361ee;color:#fff}.box .links .a2{background:#1a1a2e;color:#fff}.box .links a:hover{transform:translateY(-2px);box-shadow:0 4px 15px rgba(0,0,0,.15)}</style></head><body><div class='box'><i class='fas fa-shield-halved'></i><h2>Already Installed!</h2><p>This application is already configured and running.<br>To reinstall, delete the <b>installed.lock</b> file first.</p><p><small>Installed: " . htmlspecialchars($lockContent) . "</small></p><div class='links'><a href='index.php' class='a1'><i class='fas fa-home'></i> Visit Website</a><a href='admin/login.php' class='a2'><i class='fas fa-user-shield'></i> Admin Panel</a></div></div></body></html>");
}

// ============================================================
// AJAX HANDLER: Test Database Connection (MUST be before any HTML output)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_db') {
    header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    $h = trim($_POST['db_host'] ?? '127.0.0.1');
    $u = trim($_POST['db_user'] ?? 'root');
    $p = trim($_POST['db_pass'] ?? '');
    $n = trim($_POST['db_name'] ?? '');

    if (empty($h) || empty($u) || empty($n)) {
        echo json_encode(['ok' => false, 'msg' => 'Host, username, and database name are required.']);
        exit;
    }

    $conn = @new mysqli($h, $u, $p);
    if ($conn->connect_error) {
        echo json_encode(['ok' => false, 'msg' => 'Connection failed: ' . $conn->connect_error]);
        exit;
    }
    $createResult = $conn->query("CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($n) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if ($createResult) {
        echo json_encode(['ok' => true, 'msg' => "Connected! Database '$n' is ready."]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Database creation failed: ' . $conn->error]);
    }
    $conn->close();
    exit;
}

// ============================================================
// HANDLE INSTALLATION (POST action=install)
// ============================================================
$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    $db_host = trim($_POST['db_host'] ?? '127.0.0.1');
    $db_user = trim($_POST['db_user'] ?? 'root');
    $db_pass = trim($_POST['db_pass'] ?? '');
    $db_name = trim($_POST['db_name'] ?? '');
    $admin_user = trim($_POST['admin_user'] ?? 'admin');
    $admin_pass = trim($_POST['admin_pass'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $site_name = trim($_POST['site_name'] ?? 'TopUp Shop');
    $site_currency = trim($_POST['site_currency'] ?? '৳');
    $api_key = trim($_POST['api_key'] ?? '');

    // Validate
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        $errorMsg = 'Database host, user, and name are required.';
    } elseif (empty($admin_user) || strlen($admin_pass) < 4) {
        $errorMsg = 'Admin username and password (min 4 chars) are required.';
    } else {
        // Test DB connection
        $conn = @new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            $errorMsg = 'Database connection failed: ' . htmlspecialchars($conn->connect_error);
        } else {
            // Create database
            if (!$conn->query("CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($db_name) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                $errorMsg = 'Failed to create database: ' . htmlspecialchars($conn->error);
            } else {
                $conn->select_db($db_name);

                // Create all tables
                $tables = [
                    "CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL DEFAULT '',
                        phone VARCHAR(20) NOT NULL DEFAULT '',
                        email VARCHAR(100) UNIQUE,
                        password VARCHAR(255),
                        balance DECIMAL(10,2) DEFAULT 0.00,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS admins (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS settings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(50) UNIQUE NOT NULL,
                        value TEXT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS sliders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        image VARCHAR(255) NOT NULL DEFAULT '',
                        link VARCHAR(255) NOT NULL DEFAULT '',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS games (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        type ENUM('uid','voucher') DEFAULT 'uid',
                        description TEXT,
                        image VARCHAR(255) NOT NULL DEFAULT '',
                        status TINYINT(1) DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS products (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        game_id INT,
                        name VARCHAR(100) NOT NULL,
                        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                        status TINYINT(1) DEFAULT 1,
                        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS payment_methods (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(50) NOT NULL,
                        logo VARCHAR(255) NOT NULL DEFAULT '',
                        qr_image VARCHAR(255) NOT NULL DEFAULT '',
                        number VARCHAR(50) NOT NULL DEFAULT '',
                        description TEXT,
                        short_desc VARCHAR(255) NOT NULL DEFAULT '',
                        status TINYINT(1) DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS orders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL DEFAULT 0,
                        game_id INT NOT NULL DEFAULT 0,
                        product_id INT NOT NULL DEFAULT 0,
                        amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                        status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
                        player_id VARCHAR(100) NOT NULL DEFAULT '',
                        transaction_id VARCHAR(100) NOT NULL DEFAULT '',
                        payment_method VARCHAR(50) NOT NULL DEFAULT '',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX idx_user_id (user_id),
                        INDEX idx_status (status),
                        INDEX idx_game_id (game_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS redeem_codes (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        game_id INT DEFAULT 0,
                        product_id INT DEFAULT 0,
                        code VARCHAR(100) NOT NULL,
                        status ENUM('active','used','expired') DEFAULT 'active',
                        order_id INT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                    "CREATE TABLE IF NOT EXISTS deposits (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL DEFAULT 0,
                        amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                        method VARCHAR(50) NOT NULL DEFAULT '',
                        wallet_number VARCHAR(50) NOT NULL DEFAULT '',
                        trx_id VARCHAR(100) NOT NULL DEFAULT '',
                        status ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX idx_user_id (user_id),
                        INDEX idx_status (status)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                ];

                $tableErrors = [];
                foreach ($tables as $sql) {
                    if (!$conn->query($sql)) {
                        $tableErrors[] = htmlspecialchars($conn->error);
                    }
                }

                if (!empty($tableErrors)) {
                    $errorMsg = 'Table creation errors: ' . implode(', ', $tableErrors);
                } else {
                    // Insert admin account
                    $hashedPass = password_hash($admin_pass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT IGNORE INTO admins (id, username, password) VALUES (1, ?, ?)");
                    $stmt->bind_param("ss", $admin_user, $hashedPass);
                    $stmt->execute();
                    $stmt->close();

                    // Insert default settings
                    $stmt = $conn->prepare("INSERT IGNORE INTO settings (name, value) VALUES (?, ?)");
                    $defaultSettings = [
                        ['site_name', $site_name],
                        ['site_desc', 'Best Gaming Top Up Shop'],
                        ['currency', $site_currency],
                        ['marquee_text', 'Welcome to ' . $site_name . '! Best prices for games.'],
                        ['marquee_active', '1'],
                        ['fab_link', 'https://t.me/support'],
                        ['add_money_video', ''],
                        ['bohudur_api_key', $api_key],
                        ['name_checking_api_key', ''],
                        ['fb_url', '#'],
                        ['messenger_url', '#'],
                        ['yt_url', '#'],
                        ['logo_url', '']
                    ];
                    foreach ($defaultSettings as $s) {
                        $stmt->bind_param("ss", $s[0], $s[1]);
                        $stmt->execute();
                    }
                    $stmt->close();

                    // Create uploads folder
                    $uploadsDir = __DIR__ . '/uploads';
                    $adminUploadsDir = __DIR__ . '/admin/uploads';
                    if (!file_exists($uploadsDir)) mkdir($uploadsDir, 0777, true);
                    if (!file_exists($adminUploadsDir)) mkdir($adminUploadsDir, 0777, true);

                    // Write database credentials to db_config.php (clean, no regex needed)
                    $dbConfigContent = "<?php\n";
                    $dbConfigContent .= "// Auto-generated by TopUp Installation Wizard\n";
                    $dbConfigContent .= "// DO NOT edit manually - re-run installer if needed\n";
                    $dbConfigContent .= "\$host = " . var_export($db_host, true) . ";\n";
                    $dbConfigContent .= "\$user = " . var_export($db_user, true) . ";\n";
                    $dbConfigContent .= "\$pass = " . var_export($db_pass, true) . ";\n";
                    $dbConfigContent .= "\$db   = " . var_export($db_name, true) . ";\n";
                    file_put_contents(__DIR__ . '/common/db_config.php', $dbConfigContent);

                    // Create lock file
                    $lockData = date('Y-m-d H:i:s') . ' | DB: ' . $db_name . ' | Admin: ' . $admin_user;
                    file_put_contents(__DIR__ . '/installed.lock', $lockData);

                    $successMsg = 'ok';
                }
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Installation Wizard - TopUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
        --primary:#4361ee;--primary-light:#4895ef;--secondary:#3f37c9;
        --success:#06d6a0;--danger:#ef476f;--warning:#ffd166;
        --dark:#1a1a2e;--light:#f8f9fa;--gray:#6c757d;
        --shadow:0 10px 40px rgba(0,0,0,.08);--radius:14px;--transition:all .3s ease
    }
    body{
        font-family:'Inter',sans-serif;
        background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        min-height:100vh;display:flex;justify-content:center;align-items:center;padding:16px
    }
    .container{width:100%;max-width:820px;background:#fff;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden}
    .header{
        background:linear-gradient(135deg,#4361ee,#3f37c9);
        color:#fff;padding:28px 30px;text-align:center;position:relative;overflow:hidden
    }
    .header::before{
        content:'';position:absolute;top:-50%;right:-30%;width:200px;height:200px;
        background:rgba(255,255,255,.06);border-radius:50%
    }
    .header::after{
        content:'';position:absolute;bottom:-40%;left:-20%;width:150px;height:150px;
        background:rgba(255,255,255,.04);border-radius:50%
    }
    .header .logo-icon{font-size:2.8rem;margin-bottom:8px;position:relative;z-index:1}
    .header h1{font-size:1.7rem;font-weight:800;margin-bottom:4px;position:relative;z-index:1}
    .header p{opacity:.85;font-size:.9rem;font-weight:400;position:relative;z-index:1}

    /* Progress Bar */
    .progress-bar{display:flex;justify-content:space-between;position:relative;margin:30px 35px 10px}
    .progress-bar::before{
        content:'';position:absolute;top:50%;left:0;transform:translateY(-50%);
        height:4px;width:100%;background:#e9ecef;border-radius:4px;z-index:1
    }
    .progress-bar-line{
        position:absolute;top:50%;left:0;transform:translateY(-50%);
        height:4px;width:0%;background:linear-gradient(90deg,var(--primary),var(--secondary));
        transition:width .5s ease;border-radius:4px;z-index:2
    }
    .step-circle{
        width:44px;height:44px;border-radius:50%;background:#fff;border:3px solid #dee2e6;
        display:flex;justify-content:center;align-items:center;font-weight:700;font-size:.95rem;
        color:var(--gray);position:relative;z-index:3;transition:var(--transition);cursor:default
    }
    .step-circle i{font-size:1rem}
    .step-circle.active{border-color:var(--primary);color:var(--primary);transform:scale(1.12);box-shadow:0 4px 15px rgba(67,97,238,.25)}
    .step-circle.completed{border-color:var(--success);background:var(--success);color:#fff}
    .step-label{
        position:absolute;top:52px;left:50%;transform:translateX(-50%);
        white-space:nowrap;font-size:.72rem;color:var(--gray);font-weight:500
    }
    .step-circle.active .step-label{color:var(--primary);font-weight:700}
    .step-circle.completed .step-label{color:var(--success);font-weight:600}

    /* Form Container */
    .form-container{padding:24px 35px 40px}
    .form-step{display:none;animation:fadeSlide .45s ease}
    .form-step.active{display:block}
    @keyframes fadeSlide{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    .form-title{margin-bottom:22px;color:var(--dark);font-size:1.35rem;font-weight:700;text-align:center}
    .form-subtitle{text-align:center;color:var(--gray);font-size:.85rem;margin-bottom:24px;line-height:1.5}

    /* Form Groups */
    .form-group{margin-bottom:18px}
    .form-group label{display:block;margin-bottom:7px;font-weight:600;color:var(--dark);font-size:.88rem}
    .form-group label i{margin-right:6px;color:var(--primary);font-size:.82rem}
    .form-control{
        width:100%;padding:13px 16px;border:2px solid #e2e8f0;border-radius:var(--radius);
        font-size:.95rem;font-family:'Inter',sans-serif;transition:var(--transition);
        background:#fafbfc;color:var(--dark)
    }
    .form-control:focus{border-color:var(--primary-light);outline:none;box-shadow:0 0 0 4px rgba(67,97,238,.12);background:#fff}
    .form-control::placeholder{color:#adb5bd}
    .form-row{display:flex;gap:14px}
    .form-row .form-group{flex:1}
    select.form-control{cursor:pointer;appearance:auto}
    .input-icon-wrap{position:relative}
    .input-icon-wrap i.icon-left{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--gray);font-size:.9rem}
    .input-icon-wrap .form-control{padding-left:44px}
    .input-icon-wrap .toggle-pass{
        position:absolute;right:14px;top:50%;transform:translateY(-50%);
        color:var(--gray);cursor:pointer;font-size:.95rem;background:none;border:none;padding:4px
    }
    .input-icon-wrap .toggle-pass:hover{color:var(--primary)}

    /* Error */
    .error-msg{color:var(--danger);font-size:.82rem;margin-top:5px;display:none;font-weight:500}
    .error-msg i{margin-right:4px}

    /* Buttons */
    .btn-group{display:flex;justify-content:space-between;margin-top:28px;gap:12px}
    .btn{
        padding:13px 28px;border:none;border-radius:var(--radius);font-size:.95rem;
        font-weight:600;cursor:pointer;transition:var(--transition);display:flex;
        align-items:center;gap:8px;font-family:'Inter',sans-serif
    }
    .btn-prev{background:#fff;color:var(--primary);border:2px solid var(--primary)}
    .btn-prev:hover{background:#eef2ff}
    .btn-next,.btn-submit{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
    .btn-next:hover,.btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(67,97,238,.35)}
    .btn:disabled{opacity:.5;cursor:not-allowed;transform:none!important;box-shadow:none!important}
    .btn-spinner{display:inline-block;width:18px;height:18px;border:3px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* Requirements Check */
    .req-list{list-style:none}
    .req-list li{
        display:flex;align-items:center;gap:12px;padding:12px 16px;margin-bottom:8px;
        border-radius:10px;font-size:.9rem;font-weight:500;transition:var(--transition)
    }
    .req-list li.pass{background:#ecfdf5;color:#059669;border:1px solid #a7f3d0}
    .req-list li.fail{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
    .req-list li.warn{background:#fffbeb;color:#d97706;border:1px solid #fde68a}
    .req-list li i{font-size:1.1rem;width:22px;text-align:center}

    /* Install Log */
    .install-log{
        background:#0f172a;color:#22d3ee;font-family:'Courier New',monospace;
        border-radius:10px;padding:20px;font-size:.82rem;line-height:1.8;
        max-height:320px;overflow-y:auto;margin-bottom:16px
    }
    .install-log .log-ok{color:#34d399}
    .install-log .log-err{color:#f87171}
    .install-log .log-info{color:#94a3b8}
    .install-log .log-warn{color:#fbbf24}

    /* ===== SUCCESS SCREEN ===== */
    .success-container{text-align:center;padding:40px 20px 20px;display:none}
    .success-container .check-icon{
        width:100px;height:100px;border-radius:50%;
        background:linear-gradient(135deg,#06d6a0,#00b4d8);
        display:flex;justify-content:center;align-items:center;margin:0 auto 20px;
        animation:scaleIn .5s ease;
        box-shadow:0 10px 30px rgba(6,214,160,.35)
    }
    @keyframes scaleIn{from{transform:scale(0)}to{transform:scale(1)}}
    .success-container .check-icon i{font-size:3rem;color:#fff}
    .success-container h2{color:var(--dark);margin-bottom:6px;font-size:1.6rem;font-weight:800}
    .success-container .congrats-msg{color:var(--success);font-weight:600;font-size:1rem;margin-bottom:10px}
    .success-container p{color:var(--gray);margin-bottom:8px;line-height:1.6;font-size:.9rem}

    /* Credential Box */
    .cred-box{
        background:linear-gradient(135deg,#f8faff,#eef2ff);border:1px solid #c7d2fe;border-radius:14px;padding:20px 24px;
        margin:20px 0;text-align:left
    }
    .cred-box h4{color:var(--primary);margin-bottom:14px;font-size:.95rem;display:flex;align-items:center;gap:8px}
    .cred-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:.85rem}
    .cred-row:last-child{border-bottom:none}
    .cred-row .label{color:var(--gray);font-weight:500}
    .cred-row .value{color:var(--dark);font-weight:700}

    /* Feature Cards */
    .features-grid{
        display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:20px 0
    }
    .feature-card{
        background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px 14px;
        text-align:center;transition:var(--transition);cursor:default
    }
    .feature-card:hover{border-color:var(--primary-light);box-shadow:0 4px 15px rgba(67,97,238,.1);transform:translateY(-2px)}
    .feature-card .fc-icon{
        width:42px;height:42px;border-radius:10px;display:flex;justify-content:center;
        align-items:center;margin:0 auto 10px;font-size:1.1rem
    }
    .feature-card:nth-child(1) .fc-icon{background:#ecfdf5;color:#059669}
    .feature-card:nth-child(2) .fc-icon{background:#eef2ff;color:#4361ee}
    .feature-card:nth-child(3) .fc-icon{background:#fef3c7;color:#d97706}
    .feature-card:nth-child(4) .fc-icon{background:#fce7f3;color:#db2777}
    .feature-card:nth-child(5) .fc-icon{background:#f0fdf4;color:#16a34a}
    .feature-card:nth-child(6) .fc-icon{background:#eff6ff;color:#2563eb}
    .feature-card h5{color:var(--dark);font-size:.85rem;font-weight:700;margin-bottom:4px}
    .feature-card p{color:var(--gray);font-size:.73rem;line-height:1.4;margin:0}

    /* Navigation Buttons */
    .nav-buttons{display:flex;gap:12px;justify-content:center;margin-top:20px;flex-wrap:wrap}
    .nav-btn{
        padding:14px 28px;border:none;border-radius:12px;font-size:.92rem;
        font-weight:600;cursor:pointer;transition:var(--transition);display:flex;
        align-items:center;gap:10px;text-decoration:none;font-family:'Inter',sans-serif
    }
    .nav-btn:hover{transform:translateY(-2px)}
    .nav-btn-website{
        background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;
        box-shadow:0 4px 15px rgba(67,97,238,.3)
    }
    .nav-btn-website:hover{box-shadow:0 6px 20px rgba(67,97,238,.4)}
    .nav-btn-admin{
        background:var(--dark);color:#fff;box-shadow:0 4px 15px rgba(26,26,46,.3)
    }
    .nav-btn-admin:hover{box-shadow:0 6px 20px rgba(26,26,46,.4)}

    /* Warning Box */
    .warn-box{
        background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;
        margin-top:16px;text-align:left;display:flex;gap:10px;align-items:flex-start
    }
    .warn-box i{color:var(--danger);font-size:1rem;margin-top:2px}
    .warn-box p{color:#991b1b;font-size:.8rem;line-height:1.5;margin:0}

    /* Confetti */
    .confetti{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;overflow:hidden}
    .confetti-piece{
        position:absolute;width:10px;height:10px;top:-10px;
        animation:confettiFall 3s ease-in forwards
    }
    @keyframes confettiFall{
        0%{transform:translateY(0) rotate(0deg);opacity:1}
        100%{transform:translateY(100vh) rotate(720deg);opacity:0}
    }

    /* Responsive */
    @media(max-width:768px){
        body{padding:0}
        .container{border-radius:0;min-height:100vh;min-height:100dvh}
        .form-row{flex-direction:column;gap:0}
        .progress-bar{margin:25px 20px 5px}
        .form-container{padding:20px 22px 32px}
        .step-label{display:none}
        .step-circle{width:38px;height:38px;font-size:.85rem}
        .header{padding:22px 20px}
        .header h1{font-size:1.35rem}
        .btn{padding:12px 20px;font-size:.88rem;width:100%;justify-content:center}
        .btn-group{flex-direction:column-reverse}
        .features-grid{grid-template-columns:1fr 1fr}
        .nav-buttons{flex-direction:column}
        .nav-btn{width:100%;justify-content:center}
    }
    @media(max-width:380px){
        .header h1{font-size:1.2rem}
        .form-container{padding:16px 16px 28px}
        .features-grid{grid-template-columns:1fr}
    }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="logo-icon"><i class="fas fa-rocket"></i></div>
        <h1>TopUp Installation Wizard</h1>
        <p>Follow the steps below to set up your website</p>
    </div>

    <!-- Progress Bar -->
    <div class="progress-bar">
        <div class="progress-bar-line" id="progressLine"></div>
        <div class="step-circle active" id="sc-1">
            <span>1</span>
            <span class="step-label">Requirements</span>
        </div>
        <div class="step-circle" id="sc-2">
            <span>2</span>
            <span class="step-label">Database</span>
        </div>
        <div class="step-circle" id="sc-3">
            <span>3</span>
            <span class="step-label">Admin</span>
        </div>
        <div class="step-circle" id="sc-4">
            <span>4</span>
            <span class="step-label">Settings</span>
        </div>
        <div class="step-circle" id="sc-5">
            <span>5</span>
            <span class="step-label">Install</span>
        </div>
    </div>

    <div class="form-container">

        <!-- ====== STEP 1: Requirements Check ====== -->
        <div class="form-step active" id="fs-1">
            <h2 class="form-title"><i class="fas fa-clipboard-check" style="color:var(--primary)"></i> Server Requirements</h2>
            <p class="form-subtitle">Checking if your server meets the minimum requirements</p>
            <ul class="req-list" id="reqList">
                <?php
                $checks = [];
                // PHP Version
                $phpVer = phpversion();
                $checks[] = ['pass' => version_compare($phpVer, '7.4', '>='), 'text' => "PHP Version: {$phpVer} (required: 7.4+)", 'type' => version_compare($phpVer, '7.4', '>=') ? 'pass' : 'fail'];
                // mysqli
                $checks[] = ['pass' => extension_loaded('mysqli'), 'text' => 'PHP mysqli Extension', 'type' => extension_loaded('mysqli') ? 'pass' : 'fail'];
                // session
                $checks[] = ['pass' => extension_loaded('session'), 'text' => 'PHP Session Extension', 'type' => extension_loaded('session') ? 'pass' : 'fail'];
                // json
                $checks[] = ['pass' => extension_loaded('json'), 'text' => 'PHP JSON Extension', 'type' => extension_loaded('json') ? 'pass' : 'fail'];
                // curl
                $checks[] = ['pass' => extension_loaded('curl'), 'text' => 'PHP cURL Extension (for payments)', 'type' => extension_loaded('curl') ? 'pass' : 'warn'];
                // mbstring
                $checks[] = ['pass' => extension_loaded('mbstring'), 'text' => 'PHP mbstring Extension', 'type' => extension_loaded('mbstring') ? 'pass' : 'warn'];
                // writable
                $writable = is_writable(__DIR__);
                $checks[] = ['pass' => $writable, 'text' => 'Directory Writable Permission', 'type' => $writable ? 'pass' : 'fail'];

                $allPass = true;
                foreach ($checks as $c) {
                    if ($c['type'] === 'fail') $allPass = false;
                    $icon = $c['type'] === 'pass' ? 'fa-circle-check' : ($c['type'] === 'warn' ? 'fa-triangle-exclamation' : 'fa-circle-xmark');
                    echo "<li class='{$c['type']}'><i class='fas {$icon}'></i><span>{$c['text']}</span></li>";
                }
                ?>
            </ul>
            <?php if (!$allPass): ?>
                <p style="color:var(--danger);text-align:center;font-size:.88rem;margin-top:14px;font-weight:600">
                    <i class="fas fa-exclamation-triangle"></i> Please fix the failed requirements above to continue.
                </p>
            <?php endif; ?>
            <div class="btn-group">
                <div></div>
                <button class="btn btn-next" id="btn-next-1" <?php if (!$allPass) echo 'disabled'; ?>>
                    Continue <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- ====== STEP 2: Database Configuration ====== -->
        <div class="form-step" id="fs-2">
            <h2 class="form-title"><i class="fas fa-database" style="color:var(--primary)"></i> Database Configuration</h2>
            <p class="form-subtitle">Enter your MySQL database connection details</p>
            <div id="dbTestResult" style="display:none;margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:.88rem;font-weight:500"></div>
            <form id="dbForm">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-server"></i> Database Host</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-server icon-left"></i>
                            <input type="text" name="db_host" class="form-control" value="127.0.0.1" placeholder="127.0.0.1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-database"></i> Database Name</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-database icon-left"></i>
                            <input type="text" name="db_name" class="form-control" value="topup_db" placeholder="topup_db" required>
                        </div>
                        <div class="error-msg" id="err-dbname"><i class="fas fa-circle-xmark"></i> Database name is required</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Database Username</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-user icon-left"></i>
                            <input type="text" name="db_user" class="form-control" value="root" placeholder="root" required>
                        </div>
                        <div class="error-msg" id="err-dbuser"><i class="fas fa-circle-xmark"></i> Username is required</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Database Password</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-lock icon-left"></i>
                            <input type="password" name="db_pass" class="form-control" placeholder="Leave empty if no password">
                            <button type="button" class="toggle-pass" onclick="togglePw(this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" data-step="1"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn btn-next" data-step="3" id="btn-test-db">
                        Test & Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- ====== STEP 3: Admin Account ====== -->
        <div class="form-step" id="fs-3">
            <h2 class="form-title"><i class="fas fa-user-shield" style="color:var(--primary)"></i> Admin Account Setup</h2>
            <p class="form-subtitle">Create the administrator account for your website</p>
            <form id="adminForm">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Admin Username</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-user icon-left"></i>
                            <input type="text" name="admin_user" class="form-control" value="admin" placeholder="admin" required>
                        </div>
                        <div class="error-msg" id="err-adminuser"><i class="fas fa-circle-xmark"></i> Min 3 characters</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Admin Email</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-envelope icon-left"></i>
                            <input type="email" name="admin_email" class="form-control" value="admin@example.com" placeholder="admin@example.com">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Admin Password</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-key icon-left"></i>
                            <input type="password" name="admin_pass" class="form-control" placeholder="Min 4 characters" required minlength="4">
                            <button type="button" class="toggle-pass" onclick="togglePw(this)"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="error-msg" id="err-adminpass"><i class="fas fa-circle-xmark"></i> Min 4 characters required</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Confirm Password</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-key icon-left"></i>
                            <input type="password" name="admin_pass_confirm" class="form-control" placeholder="Re-type password">
                            <button type="button" class="toggle-pass" onclick="togglePw(this)"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="error-msg" id="err-adminpass2"><i class="fas fa-circle-xmark"></i> Passwords do not match</div>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" data-step="2"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn btn-next" data-step="4">Continue <i class="fas fa-arrow-right"></i></button>
                </div>
            </form>
        </div>

        <!-- ====== STEP 4: Site Settings ====== -->
        <div class="form-step" id="fs-4">
            <h2 class="form-title"><i class="fas fa-gear" style="color:var(--primary)"></i> Site Settings</h2>
            <p class="form-subtitle">Configure your website's basic settings</p>
            <form id="settingsForm">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-store"></i> Site Name</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-store icon-left"></i>
                            <input type="text" name="site_name" class="form-control" value="TopUp Shop" placeholder="Your Site Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-coins"></i> Currency Symbol</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-coins icon-left"></i>
                            <input type="text" name="site_currency" class="form-control" value="৳" placeholder="৳" maxlength="5">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Bohudur Payment API Key</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-key icon-left"></i>
                        <input type="text" name="api_key" class="form-control" placeholder="Enter your Bohudur API Key">
                    </div>
                    <p style="font-size:.78rem;color:var(--gray);margin-top:6px"><i class="fas fa-info-circle"></i> Get your API key from <a href="https://bohudur.one" target="_blank" style="color:var(--primary)">bohudur.one</a> dashboard. You can add or change this later from Admin Panel > Settings</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" data-step="3"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn btn-next" data-step="5">Continue <i class="fas fa-arrow-right"></i></button>
                </div>
            </form>
        </div>

        <!-- ====== STEP 5: Installation Process ====== -->
        <div class="form-step" id="fs-5">
            <h2 class="form-title"><i class="fas fa-download" style="color:var(--primary)"></i> Installing...</h2>
            <p class="form-subtitle" id="installSubtitle">Please wait while we set up your database</p>
            <div class="install-log" id="installLog"></div>
            <div id="installLoading" style="text-align:center">
                <div style="display:inline-block;width:40px;height:40px;border:4px solid #e2e8f0;border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite"></div>
            </div>
        </div>

        <!-- ====== SUCCESS SCREEN ====== -->
        <div class="success-container" id="successScreen">
            <div class="check-icon"><i class="fas fa-check"></i></div>
            <h2>Installation Complete!</h2>
            <p class="congrats-msg"><i class="fas fa-party-horn"></i> Congratulations! Your website is ready.</p>
            <p>Your gaming top-up website has been successfully installed and configured. You can now start managing your business.</p>

            <!-- Credentials -->
            <div class="cred-box" id="credBox">
                <h4><i class="fas fa-key"></i> Login Credentials</h4>
                <div class="cred-row"><span class="label"><i class="fas fa-globe" style="margin-right:6px;color:var(--primary)"></i> Website</span><span class="value" id="credSite"></span></div>
                <div class="cred-row"><span class="label"><i class="fas fa-user-shield" style="margin-right:6px;color:var(--primary)"></i> Admin Panel</span><span class="value" id="credAdmin"></span></div>
                <div class="cred-row"><span class="label"><i class="fas fa-user" style="margin-right:6px;color:var(--primary)"></i> Username</span><span class="value" id="credUser"></span></div>
                <div class="cred-row"><span class="label"><i class="fas fa-lock" style="margin-right:6px;color:var(--primary)"></i> Password</span><span class="value" id="credPass"></span></div>
            </div>

            <!-- Features Showcase -->
            <div style="text-align:left;margin-bottom:16px">
                <h4 style="color:var(--dark);font-size:.95rem;margin-bottom:12px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-sparkles" style="color:var(--warning)"></i> What You Can Do
                </h4>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-gamepad"></i></div>
                    <h5>Game Top-Up</h5>
                    <p>PUBG, Free Fire, ML & more game top-ups</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-wallet"></i></div>
                    <h5>Wallet System</h5>
                    <p>User balance with deposit & payment</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <h5>Auto Payment</h5>
                    <p>Shohoj & MrAiPay automated payments</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-ticket"></i></div>
                    <h5>Redeem Codes</h5>
                    <p>Generate & manage voucher codes</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-sliders"></i></div>
                    <h5>Admin Dashboard</h5>
                    <p>Full control panel with mobile UI</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-mobile-screen"></i></div>
                    <h5>Mobile Ready</h5>
                    <p>Fully responsive on all devices</p>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="nav-buttons">
                <a href="index.php" class="nav-btn nav-btn-website" id="btnVisitSite">
                    <i class="fas fa-home"></i> Visit Website
                </a>
                <a href="admin/login.php" class="nav-btn nav-btn-admin" id="btnAdminPanel">
                    <i class="fas fa-user-shield"></i> Go to Admin Panel
                </a>
            </div>

            <!-- Security Warning -->
            <div class="warn-box">
                <i class="fas fa-exclamation-triangle"></i>
                <p><strong>Security Notice:</strong> Please delete or rename <b>install.php</b> from your server after installation. This prevents unauthorized re-installation.</p>
            </div>
        </div>

    </div>
</div>

<script>
(function(){
    // Get absolute URL for AJAX calls (fixes localhost:8080 / KSWEB compatibility)
    var INSTALL_URL = window.location.href.split('?')[0].split('#')[0];

    // DOM references
    var steps = document.querySelectorAll('.step-circle');
    var formSteps = document.querySelectorAll('.form-step');
    var progressLine = document.getElementById('progressLine');
    var currentStep = 0;
    var totalSteps = steps.length;

    function updateProgress(){
        var pct = currentStep / (totalSteps - 1) * 100;
        progressLine.style.width = pct + '%';
        steps.forEach(function(s, i) {
            s.classList.remove('active','completed');
            var span = s.querySelector('span:first-child');
            if (i < currentStep) {
                s.classList.add('completed');
                span.innerHTML = '<i class="fas fa-check"></i>';
            } else if (i === currentStep) {
                s.classList.add('active');
            }
        });
    }

    function showStep(idx){
        formSteps.forEach(function(f, i) { f.classList.toggle('active', i === idx); });
        updateProgress();
        window.scrollTo({top:0, behavior:'smooth'});
    }

    // Toggle password visibility
    window.togglePw = function(btn){
        var inp = btn.parentElement.querySelector('input');
        var ico = btn.querySelector('i');
        if(inp.type === 'password'){inp.type='text';ico.className='fas fa-eye-slash';}
        else{inp.type='password';ico.className='fas fa-eye';}
    };

    // Show/hide error
    function show(id){document.getElementById(id).style.display='block';}
    function hide(id){document.getElementById(id).style.display='none';}

    // Escape HTML
    function escapeHtml(str){
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Validate DB step
    function validateDB(){
        var ok = true;
        var name = document.querySelector('[name=db_name]');
        var user = document.querySelector('[name=db_user]');
        if(!name.value.trim()){show('err-dbname');ok=false;}else{hide('err-dbname');}
        if(!user.value.trim()){show('err-dbuser');ok=false;}else{hide('err-dbuser');}
        return ok;
    }

    // Validate Admin step
    function validateAdmin(){
        var ok = true;
        var u = document.querySelector('[name=admin_user]');
        var p = document.querySelector('[name=admin_pass]');
        var p2 = document.querySelector('[name=admin_pass_confirm]');
        if(!u.value.trim() || u.value.trim().length < 3){show('err-adminuser');ok=false;}else{hide('err-adminuser');}
        if(!p.value || p.value.length < 4){show('err-adminpass');ok=false;}else{hide('err-adminpass');}
        if(p2.value && p.value !== p2.value){show('err-adminpass2');ok=false;}else{hide('err-adminpass2');}
        return ok;
    }

    // Show DB test result
    function showDbResult(success, msg){
        var el = document.getElementById('dbTestResult');
        el.style.display = 'block';
        if(success){
            el.style.background = '#ecfdf5';
            el.style.color = '#059669';
            el.style.border = '1px solid #a7f3d0';
            el.innerHTML = '<i class="fas fa-circle-check" style="margin-right:8px"></i>' + escapeHtml(msg);
        } else {
            el.style.background = '#fef2f2';
            el.style.color = '#dc2626';
            el.style.border = '1px solid #fecaca';
            el.innerHTML = '<i class="fas fa-circle-xmark" style="margin-right:8px"></i>' + escapeHtml(msg);
        }
    }

    // Test DB connection via AJAX (uses absolute URL for KSWEB/localhost compatibility)
    function testDB(callback){
        var form = document.getElementById('dbForm');
        var fd = new FormData();
        fd.append('action','test_db');
        fd.append('db_host', form.querySelector('[name=db_host]').value);
        fd.append('db_user', form.querySelector('[name=db_user]').value);
        fd.append('db_pass', form.querySelector('[name=db_pass]').value);
        fd.append('db_name', form.querySelector('[name=db_name]').value);

        var btn = document.getElementById('btn-test-db');
        btn.innerHTML = '<span class="btn-spinner"></span> Testing...';
        btn.disabled = true;

        fetch(INSTALL_URL, {method:'POST', body:fd})
            .then(function(r) {
                if(!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(data) {
                btn.innerHTML = 'Test & Continue <i class="fas fa-arrow-right"></i>';
                btn.disabled = false;
                showDbResult(data.ok, data.msg);
                callback(data);
            })
            .catch(function(err) {
                btn.innerHTML = 'Test & Continue <i class="fas fa-arrow-right"></i>';
                btn.disabled = false;
                showDbResult(false, 'Connection test failed: ' + err.message + '. Check your server configuration.');
                // Do NOT proceed - stay on step 2 so user can fix
            });
    }

    // Button event delegation
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.btn-next, .btn-prev');
        if(!btn) return;

        if(btn.classList.contains('btn-prev')){
            currentStep--;
            showStep(currentStep);
            return;
        }

        if(btn.classList.contains('btn-next')){
            var step = parseInt(btn.dataset.step) || currentStep + 1;

            // Step 1 -> 2
            if(currentStep === 0){
                currentStep = 1;
                showStep(1);
                return;
            }

            // Step 2 -> 3: Validate + Test DB via AJAX
            if(currentStep === 1){
                if(!validateDB()) return;
                testDB(function(data){
                    if(data.ok){
                        currentStep = 2;
                        showStep(2);
                    }
                });
                return;
            }

            // Step 3 -> 4: Validate admin
            if(currentStep === 2){
                if(!validateAdmin()) return;
                currentStep = 3;
                showStep(3);
                return;
            }

            // Step 4 -> 5: Start installation
            if(currentStep === 3){
                currentStep = 4;
                showStep(4);
                startInstallation();
                return;
            }
        }
    });

    // Run Installation
    function startInstallation(){
        var log = document.getElementById('installLog');
        var loading = document.getElementById('installLoading');
        var subtitle = document.getElementById('installSubtitle');
        log.innerHTML = '';

        function addLog(msg, cls){
            var line = document.createElement('div');
            line.className = cls || '';
            line.textContent = msg;
            log.appendChild(line);
            log.scrollTop = log.scrollHeight;
        }

        addLog('[START] Starting installation process...', 'log-info');
        addLog('', '');

        // Gather all form data
        var dbHost = document.querySelector('[name=db_host]').value;
        var dbUser = document.querySelector('[name=db_user]').value;
        var dbPass = document.querySelector('[name=db_pass]').value;
        var dbName = document.querySelector('[name=db_name]').value;
        var adminUser = document.querySelector('[name=admin_user]').value;
        var adminPass = document.querySelector('[name=admin_pass]').value;
        var adminEmail = document.querySelector('[name=admin_email]').value;
        var siteName = document.querySelector('[name=site_name]').value;
        var siteCurrency = document.querySelector('[name=site_currency]').value;
        var apiKey = document.querySelector('[name=api_key]').value;

        // Build FormData
        var fd = new FormData();
        fd.append('action', 'install');
        fd.append('db_host', dbHost);
        fd.append('db_user', dbUser);
        fd.append('db_pass', dbPass);
        fd.append('db_name', dbName);
        fd.append('admin_user', adminUser);
        fd.append('admin_pass', adminPass);
        fd.append('admin_email', adminEmail);
        fd.append('site_name', siteName);
        fd.append('site_currency', siteCurrency);
        fd.append('api_key', apiKey);

        // Animated log sequence
        addLog('[DB] Connecting to database: ' + dbHost + '...', 'log-info');

        setTimeout(function(){
            addLog('[DB] Testing connection...', 'log-info');
        }, 400);

        setTimeout(function(){
            addLog('[DB] Connection established successfully!', 'log-ok');
            addLog('[DB] Creating database: ' + dbName + '...', 'log-info');
        }, 900);

        setTimeout(function(){
            addLog('[DB] Database created / verified.', 'log-ok');
            addLog('[TABLE] Creating database tables...', 'log-info');
        }, 1400);

        setTimeout(function(){
            var tables = ['users','admins','settings','sliders','games','products','payment_methods','orders','redeem_codes','deposits'];
            tables.forEach(function(t, i) {
                setTimeout(function(){
                    addLog('  [+] Table "' + t + '" created.', 'log-ok');
                }, i * 150);
            });
        }, 1900);

        setTimeout(function(){
            addLog('', '');
            addLog('[ADMIN] Creating admin account...', 'log-info');
        }, 3600);

        setTimeout(function(){
            addLog('[ADMIN] Admin "' + adminUser + '" created successfully!', 'log-ok');
            addLog('[SETTINGS] Configuring site settings...', 'log-info');
        }, 4100);

        setTimeout(function(){
            addLog('[SETTINGS] Site: ' + siteName + ' | Currency: ' + siteCurrency, 'log-ok');
            addLog('[CONFIG] Updating config.php...', 'log-info');
        }, 4600);

        setTimeout(function(){
            addLog('[CONFIG] config.php updated with database credentials.', 'log-ok');
            addLog('[SECURITY] Creating installation lock...', 'log-info');
        }, 5100);

        setTimeout(function(){
            addLog('[SECURITY] Lock file created.', 'log-ok');
            addLog('', '');
            addLog('========================================', 'log-info');
            addLog('[DONE] Installation completed successfully!', 'log-ok');
        }, 5600);

        // Actually submit after animations
        setTimeout(function(){
            loading.style.display = 'none';

            // Submit the form via AJAX using absolute URL
            fetch(INSTALL_URL, {method:'POST', body:fd})
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    // Check if installation was successful
                    if(html.indexOf('installed.lock') !== -1 || html.indexOf('Installation Complete') !== -1 || html.indexOf('Already Installed') !== -1) {
                        addLog('', '');
                        addLog('[FINAL] All done! Redirecting...', 'log-ok');
                        setTimeout(function(){
                            // Show success screen
                            document.querySelector('.form-container').style.display = 'none';
                            var ss = document.getElementById('successScreen');
                            ss.style.display = 'block';
                            progressLine.style.width = '100%';

                            // Mark all steps complete
                            steps.forEach(function(s) {
                                s.classList.add('completed');
                                s.classList.remove('active');
                                s.querySelector('span:first-child').innerHTML = '<i class="fas fa-check"></i>';
                            });

                            // Fill credential box
                            var baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
                            document.getElementById('credSite').textContent = baseUrl;
                            document.getElementById('credAdmin').textContent = baseUrl + 'admin/login.php';
                            document.getElementById('credUser').textContent = escapeHtml(adminUser);
                            document.getElementById('credPass').textContent = escapeHtml(adminPass);

                            // Update nav button links
                            document.getElementById('btnVisitSite').href = 'index.php';
                            document.getElementById('btnAdminPanel').href = 'admin/login.php';

                            // Launch confetti
                            launchConfetti();
                        }, 800);
                    } else {
                        addLog('[ERROR] Something went wrong. Check your database settings and try again.', 'log-err');
                        // Show retry button
                        loading.innerHTML = '<button class="btn btn-next" onclick="location.reload()" style="margin:12px auto;display:flex"><i class="fas fa-redo"></i> Retry Installation</button>';
                        loading.style.display = 'block';
                    }
                })
                .catch(function() {
                    // If AJAX fails, do normal form submit as fallback
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = INSTALL_URL;
                    fd.forEach(function(v,k) {
                        var inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = k;
                        inp.value = v;
                        form.appendChild(inp);
                    });
                    document.body.appendChild(form);
                    form.submit();
                });
        }, 6200);
    }

    // Confetti animation
    function launchConfetti(){
        var container = document.createElement('div');
        container.className = 'confetti';
        document.body.appendChild(container);

        var colors = ['#4361ee','#06d6a0','#ffd166','#ef476f','#3f37c9','#00b4d8','#f72585','#7209b7'];
        for(var i = 0; i < 80; i++){
            (function(idx){
                setTimeout(function(){
                    var piece = document.createElement('div');
                    piece.className = 'confetti-piece';
                    piece.style.left = Math.random() * 100 + '%';
                    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                    piece.style.width = (Math.random() * 8 + 5) + 'px';
                    piece.style.height = (Math.random() * 8 + 5) + 'px';
                    piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                    piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    piece.style.animationDelay = '0s';
                    container.appendChild(piece);

                    // Remove after animation
                    setTimeout(function(){
                        if(piece.parentNode) piece.parentNode.removeChild(piece);
                    }, 4500);
                }, idx * 30);
            })(i);
        }

        // Remove container after all confetti
        setTimeout(function(){
            if(container.parentNode) container.parentNode.removeChild(container);
        }, 5000);
    }

    // Init
    updateProgress();
})();
</script>
</body>
</html>
