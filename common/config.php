<?php
// common/config.php - Core Configuration
// ============================================================
// Step 1: Start Session (before anything else)
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Step 2: Database Connection
// Load credentials from db_config.php (written by installer)
// Falls back to empty defaults if not installed yet
// ============================================================
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "";

// Load actual credentials from installer-generated config
$dbConfigFile = __DIR__ . '/db_config.php';
if (file_exists($dbConfigFile)) {
    include $dbConfigFile;
}

// Attempt database connection
$conn = null;
if (!empty($db)) {
    $conn = @new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        $conn = null;
    }
}

// ============================================================
// Step 3: Installation Check
// Redirect to install.php if not installed (but NOT if already there)
// ============================================================
$isInstalled = file_exists(__DIR__ . '/../installed.lock');

if (!$isInstalled) {
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($currentScript !== 'install.php') {
        if (!headers_sent()) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
            $base = ($base === '/' || $base === '\\') ? '' : $base;
            header("Location: " . $protocol . "://" . $hostName . $base . "/install.php");
            exit;
        }
    }
}

// ============================================================
// Helper: Get Setting (SQL Injection Protected)
// ============================================================
function getSetting($conn, $key) {
    if (!$conn) return "";
    $stmt = $conn->prepare("SELECT value FROM settings WHERE name = ? LIMIT 1");
    if (!$stmt) return "";
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row['value'];
    }
    return "";
}

// ============================================================
// Helper: Escape HTML Output (XSS Prevention)
// ============================================================
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
