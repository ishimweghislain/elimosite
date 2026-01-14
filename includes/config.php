<?php
/**
 * Elimo Real Estate - Configuration File
 * Database and application settings
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'elimo_real_estate');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('SITE_NAME', 'Elimo Real Estate');
define('SITE_URL', 'http://localhost/elimosite');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../images/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Session security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Kigali');

// Include functions
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/database.php';

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

// Redirect if not admin
function require_admin() {
    if (!is_admin()) {
        set_flash_message('Unauthorized access. Admin login required.', 'error');
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

// Get current user data
function get_logged_in_user() {
    if (is_logged_in()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

// Clean input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Pagination helper
function get_pagination($total_items, $items_per_page, $current_page) {
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;
    
    return [
        'total_items' => $total_items,
        'items_per_page' => $items_per_page,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

// Format price
function format_price($price) {
    return $price ? number_format($price, 0) . ' RWF' : 'Price on request';
}

// Truncate text
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Get site setting
function get_setting($key, $default = '') {
    global $pdo;
    static $settings = null;
    
    if ($settings === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Flash messaging system
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'text' => $message,
        'type' => $type
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Output session toast if exists
 */
function output_session_toast() {
    $flash = get_flash_message();
    if ($flash) {
        $text = addslashes($flash['text']);
        $type = $flash['type'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof ElimoToast !== 'undefined') {
                    ElimoToast.show('{$text}', '{$type}');
                } else {
                    // Fallback if script not loaded yet
                    window.addEventListener('load', () => ElimoToast.show('{$text}', '{$type}'));
                }
            });
        </script>";
    }
}
?>
