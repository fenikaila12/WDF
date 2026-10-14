<?php
/* config.php - DB connection, session handling and security helpers */

// ---- Database settings ----
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "studenthub";

define('SESSION_TIMEOUT', 1800);        // 30 minutes of inactivity
define('REMEMBER_DAYS', 30);
define('UPLOAD_DIR', __DIR__ . '/assignment_uploads/');

// ---- Session ----
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
if (session_status() === PHP_SESSION_NONE) session_start();

// ---- Connection ----
mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) die("Database connection failed. Check config.php");
mysqli_set_charset($conn, "utf8mb4");

// ---- Helpers ----
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403);
        die("Invalid request (CSRF check failed).");
    }
}

function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert ' . e($t) . '">' . e($m) . '</div>';
    }
}

function start_user_session($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['last_activity'] = time();
}

// Remember-me: cookie "id:token", DB stores sha256(token)
function try_remember_login($conn) {
    if (!empty($_SESSION['user_id']) || empty($_COOKIE['remember'])) return;
    $parts = explode(':', $_COOKIE['remember'], 2);
    if (count($parts) !== 2) return;
    [$id, $token] = $parts;
    $stmt = mysqli_prepare($conn, "SELECT id,name,role,remember_token FROM users WHERE id=?");
    $id = (int)$id;
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user && $user['remember_token'] && hash_equals($user['remember_token'], hash('sha256', $token))) {
        start_user_session($user);
    }
}

function require_login() {
    global $conn;
    try_remember_login($conn);
    if (empty($_SESSION['user_id'])) { header("Location: login.php"); exit; }
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset(); session_destroy();
        session_start();
        flash("Session expired. Please log in again.", "error");
        header("Location: login.php"); exit;
    }
    $_SESSION['last_activity'] = time();
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        die("<h2 style='font-family:sans-serif;padding:40px'>403 - Access denied.</h2>");
    }
}
function is_admin() { return ($_SESSION['role'] ?? '') === 'admin'; }
