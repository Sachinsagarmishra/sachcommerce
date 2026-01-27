<?php
/**
 * TrendsOne eCommerce - Universal Configuration File
 * Automatically detects and configures for both LOCAL and LIVE environments
 * Fixed: Currency symbol encoding issue & Price Sanitization
 * Updated: Dynamic Payment Settings from Database
 */

// ============================================================================
// SET CHARACTER ENCODING (CRITICAL - Must be first)
// ============================================================================
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: text/html; charset=UTF-8');

// ============================================================================
// AUTO-DETECT ENVIRONMENT
// ============================================================================
$server_name = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
$is_local = in_array($server_name, ['localhost', '127.0.0.1', '::1']) ||
    strpos($server_name, 'localhost') !== false ||
    strpos($server_name, '.test') !== false ||
    strpos($server_name, '.local') !== false;

define('IS_LOCAL_ENV', $is_local);
define('IS_LIVE_ENV', !$is_local);

// ============================================================================
// ENVIRONMENT CONFIGURATION VARIABLES (Defaults)
// Note: Constants are now defined later, after DB connection
// ============================================================================
if (IS_LOCAL_ENV) {
    // LOCAL ENVIRONMENT
    define('ENVIRONMENT', 'development');
    define('SITE_URL', 'https://ivory-weasel-745273.hostingersite.com');

    // Database - Local
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u829703776_trendsone');
    define('DB_USER', 'u829703776_trendsone');
    define('DB_PASS', '2^cXhHgyu');

    // Razorpay - Local Defaults
    $default_razorpay_env = 'test';
    $default_razorpay_key = 'rzp_test_YOUR_KEY_ID';
    $default_razorpay_secret = 'YOUR_TEST_KEY_SECRET';

    // Email - Local Testing
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', 587);
    define('SMTP_USERNAME', 'your-local-email@gmail.com');
    define('SMTP_PASSWORD', 'your-app-password');

} else {
    // LIVE ENVIRONMENT
    define('ENVIRONMENT', 'production');
    define('SITE_URL', 'https://ivory-weasel-745273.hostingersite.com');

    // Database - Live
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u829703776_trendsone');
    define('DB_USER', 'u829703776_trendsone');
    define('DB_PASS', '2^cXhHgyu');

    // Razorpay - Live Defaults
    $default_razorpay_env = 'live';
    $default_razorpay_key = 'rzp_live_YOUR_LIVE_KEY';
    $default_razorpay_secret = 'YOUR_LIVE_KEY_SECRET';

    // Email - Live Production
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', 587);
    define('SMTP_USERNAME', 'your-production-email@gmail.com');
    define('SMTP_PASSWORD', 'your-production-app-password');
}

// ============================================================================
// SESSION CONFIGURATION (Must be set BEFORE session_start)
// ============================================================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// SameSite strict can sometimes break payment gateways redirection, use 'Lax' if issues arise
ini_set('session.cookie_samesite', 'Lax');

// Enable secure cookies only on HTTPS (live environment)
if (ENVIRONMENT === 'production' && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
    ini_set('session.cookie_secure', 1);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// ERROR REPORTING
// ============================================================================
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/error.log');
}

// ============================================================================
// DATABASE CONNECTION WITH UTF-8 SUPPORT
// ============================================================================
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Force UTF-8 encoding (Extra Security for Live Servers)
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET character_set_connection=utf8mb4");
    $pdo->exec("SET character_set_client=utf8mb4");
    $pdo->exec("SET character_set_results=utf8mb4");

} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Database Connection Failed: " . $e->getMessage());
    } else {
        error_log("Database Connection Failed: " . $e->getMessage());
        die("We're experiencing technical difficulties. Please try again later.");
    }
}

// ============================================================================
// DYNAMIC SETTINGS LOADING (Must be AFTER DB connection)
// ============================================================================
// Fetch Razorpay settings from DB, fallback to environment variables if not found

$db_razorpay_settings = [];
try {
    // We execute a raw query here because helper functions are defined lower in the file
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('razorpay_key_id', 'razorpay_key_secret', 'razorpay_environment')");
    while ($row = $stmt->fetch()) {
        $db_razorpay_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Table might not exist or connection issue - ignore and use defaults
}

// Define Constants - Prefer DB value, then Default
define('RAZORPAY_ENVIRONMENT', !empty($db_razorpay_settings['razorpay_environment']) ? $db_razorpay_settings['razorpay_environment'] : $default_razorpay_env);
define('RAZORPAY_KEY_ID', !empty($db_razorpay_settings['razorpay_key_id']) ? $db_razorpay_settings['razorpay_key_id'] : $default_razorpay_key);
define('RAZORPAY_KEY_SECRET', !empty($db_razorpay_settings['razorpay_key_secret']) ? $db_razorpay_settings['razorpay_key_secret'] : $default_razorpay_secret);

// ============================================================================
// SITE SETTINGS
// ============================================================================
define('SITE_NAME', 'TrendsOne');
define('SITE_TAGLINE', 'Your One-Stop Online Shopping Destination');
define('SITE_EMAIL', 'info@trendsone.com');
define('SITE_PHONE', '+91 9876543210');
define('SITE_ADDRESS', 'Mumbai, Maharashtra, India');
define('TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set(TIMEZONE);

// Social Media Links
define('FACEBOOK_URL', 'https://facebook.com/trendsone');
define('INSTAGRAM_URL', 'https://instagram.com/trendsone');
define('TWITTER_URL', 'https://twitter.com/trendsone');
define('YOUTUBE_URL', 'https://youtube.com/trendsone');
define('WHATSAPP_NUMBER', '919876543210');

// ============================================================================
// PAYMENT GATEWAY CONFIGURATION
// ============================================================================
define('RAZORPAY_CURRENCY', 'INR');
define('RAZORPAY_WEBHOOK_SECRET', 'YOUR_WEBHOOK_SECRET');

// ============================================================================
// EMAIL CONFIGURATION
// ============================================================================
define('SMTP_FROM_EMAIL', 'noreply@trendsone.com');
define('SMTP_FROM_NAME', 'TrendsOne');
define('SMTP_ENCRYPTION', 'tls'); // tls or ssl
define('EMAIL_TEMPLATE_PATH', __DIR__ . '/email-templates/');

// ============================================================================
// SECURITY CONFIGURATION
// ============================================================================
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('BCRYPT_COST', 12);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ============================================================================
// SEO CONFIGURATION
// ============================================================================
define('DEFAULT_META_TITLE', 'TrendsOne - Best Online Shopping Store in India');
define('DEFAULT_META_DESCRIPTION', 'Shop the latest trends in fashion, electronics, home & living at TrendsOne. Free shipping, COD available, easy returns. Best prices guaranteed!');
define('DEFAULT_META_KEYWORDS', 'online shopping, ecommerce, india, fashion, electronics, home decor, best prices, free shipping');
define('DEFAULT_OG_IMAGE', SITE_URL . '/assets/images/og-image.jpg');

// Analytics & Tracking (Add your IDs)
define('GOOGLE_ANALYTICS_ID', IS_LIVE_ENV ? 'G-XXXXXXXXXX' : '');
define('FACEBOOK_PIXEL_ID', IS_LIVE_ENV ? '' : '');
define('GOOGLE_TAG_MANAGER_ID', IS_LIVE_ENV ? '' : '');

// ============================================================================
// PAGINATION SETTINGS
// ============================================================================
define('PRODUCTS_PER_PAGE', 12);
define('BLOG_PER_PAGE', 9);
define('ADMIN_PER_PAGE', 20);
define('RELATED_PRODUCTS_COUNT', 4);
define('RECENT_PRODUCTS_COUNT', 8);

// ============================================================================
// INDIA-SPECIFIC SETTINGS - FIXED CURRENCY SYMBOL
// ============================================================================
// Use hex escape sequence for UTF-8 Rupee symbol to prevent file encoding corruption
// This is much safer than using the literal '₹' character which can be mangled by FTP/Editors
define('CURRENCY_SYMBOL', "\xE2\x82\xB9"); // UTF-8 Rupee symbol
define('CURRENCY_HTML', '&#8377;'); // HTML entity for rupee
define('CURRENCY_CODE', 'INR');
define('GST_PERCENTAGE', 18);
define('SHIPPING_CHARGE', 50);
define('FREE_SHIPPING_THRESHOLD', 500);
define('COD_ENABLED', true);
define('COD_CHARGE', 30);
define('COD_MAX_AMOUNT', 50000);

// ============================================================================
// PATH CONSTANTS
// ============================================================================
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('PRODUCT_IMAGE_PATH', UPLOAD_PATH . 'products/');
define('CATEGORY_IMAGE_PATH', UPLOAD_PATH . 'categories/');
define('BLOG_IMAGE_PATH', UPLOAD_PATH . 'blog/');
define('AVATAR_IMAGE_PATH', UPLOAD_PATH . 'avatars/');
define('TEMP_UPLOAD_PATH', UPLOAD_PATH . 'temp/');

// URL Constants
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('PRODUCT_IMAGE_URL', UPLOAD_URL . 'products/');
define('CATEGORY_IMAGE_URL', UPLOAD_URL . 'categories/');
define('BLOG_IMAGE_URL', UPLOAD_URL . 'blog/');
define('AVATAR_IMAGE_URL', UPLOAD_URL . 'avatars/');

// ============================================================================
// AUTO-DETECT ADMIN/FRONTEND
// ============================================================================
$current_script = $_SERVER['SCRIPT_NAME'] ?? '';
define('IS_ADMIN_PANEL', strpos($current_script, '/admin/') !== false);
define('IS_FRONTEND', !IS_ADMIN_PANEL);

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Sanitize input data
 */
function sanitize_input($data)
{
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF token
 */
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Format price with currency symbol - FIXED VERSION
 * Handles both standard and Indian numbering formats properly
 * Uses HTML entities for safe display
 */
function format_price($amount, $html = true)
{
    // 1. Sanitize: Remove any existing symbols or commas
    // Use a strict regex that only allows digits and a single decimal point
    // This fixes issues where previous formatting or garbage characters (like 262145...) caused errors
    $clean_amount = preg_replace('/[^0-9.]/', '', (string) $amount);

    // 2. Ensure amount is a valid float
    $amount = floatval($clean_amount);

    // 3. Indian Number Formatting (1,29,900.00)
    // Separate integer and decimal parts
    $decimal = number_format($amount - floor($amount), 2, '.', '');
    $decimal = substr($decimal, 1); // Remove the leading 0, keep .00

    $integer = floor($amount);
    $integer = (string) $integer;

    // Extract last 3 digits
    $last3 = substr($integer, -3);

    // Extract the rest
    $rest = substr($integer, 0, -3);

    if ($rest != '') {
        // Add commas every 2 digits for the rest part
        $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
        $formatted_number = $rest . ',' . $last3 . $decimal;
    } else {
        $formatted_number = $last3 . $decimal;
    }

    // Fallback for small numbers to ensure standard formatting (0.00, 50.00)
    if ($amount < 1000) {
        $formatted_number = number_format($amount, 2, '.', ',');
    }

    // 4. Return with appropriate currency symbol
    // Prefer HTML entity for web display to avoid encoding issues on live servers
    if ($html) {
        return CURRENCY_HTML . $formatted_number;
    }
    return CURRENCY_SYMBOL . $formatted_number;
}

/**
 * Format price for display (Indian style with lakhs notation for very large numbers)
 */
function format_price_indian($amount)
{
    // 1. Sanitize first to handle dirty database inputs
    $clean_amount = preg_replace('/[^0-9.]/', '', (string) $amount);
    $amount = floatval($clean_amount);

    // 2. Indian number format with suffixes
    if ($amount >= 10000000) { // 1 crore
        $crores = $amount / 10000000;
        return CURRENCY_HTML . number_format($crores, 2) . ' Cr';
    } elseif ($amount >= 100000) { // 1 lakh
        $lakhs = $amount / 100000;
        return CURRENCY_HTML . number_format($lakhs, 2) . ' L';
    } else {
        return format_price($amount, true);
    }
}

/**
 * Calculate discount percentage
 */
function calculate_discount_percentage($original_price, $sale_price)
{
    // Clean inputs first
    $original_price = floatval(preg_replace('/[^0-9.]/', '', (string) $original_price));
    $sale_price = floatval(preg_replace('/[^0-9.]/', '', (string) $sale_price));

    if ($original_price <= 0)
        return 0;
    return round((($original_price - $sale_price) / $original_price) * 100);
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    if (!headers_sent()) {
        header("Location: " . $url);
        exit();
    } else {
        echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
        exit();
    }
}

/**
 * Generate SEO-friendly slug
 */
function generate_slug($string)
{
    $string = mb_strtolower(trim($string), 'UTF-8');
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Upload image file
 */
function upload_image($file, $destination_folder, $prefix = '')
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum limit of 5MB'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed'];
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }

    if (!file_exists($destination_folder)) {
        mkdir($destination_folder, 0755, true);
    }

    $filename = $prefix . uniqid() . '_' . time() . '.' . $file_extension;
    $destination = $destination_folder . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => $destination];
    }

    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Delete file
 */
function delete_file($file_path)
{
    if (file_exists($file_path) && is_file($file_path)) {
        return unlink($file_path);
    }
    return false;
}

/**
 * Send email using PHPMailer
 */
function send_email($to, $subject, $template, $data = [])
{
    require_once ROOT_PATH . '/vendor/phpmailer/PHPMailer.php';
    require_once ROOT_PATH . '/vendor/phpmailer/SMTP.php';
    require_once ROOT_PATH . '/vendor/phpmailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;

        $template_file = EMAIL_TEMPLATE_PATH . $template . '.php';
        if (file_exists($template_file)) {
            ob_start();
            extract($data);
            include $template_file;
            $mail->Body = ob_get_clean();
        } else {
            $mail->Body = 'Template not found';
        }

        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return ['success' => false, 'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"];
    }
}

/**
 * Get current logged in user data
 */
function get_logged_user()
{
    global $pdo;

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get user error: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin()
{
    if (!is_logged_in()) {
        return false;
    }
    $user = get_logged_user();
    return $user && isset($user['role']) && $user['role'] === 'admin';
}

/**
 * Get cart count
 */
function get_cart_count()
{
    global $pdo;

    try {
        if (is_logged_in()) {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            $session_id = session_id();
            $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
            $stmt->execute([$session_id]);
        }

        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Cart count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get wishlist count
 */
function get_wishlist_count()
{
    global $pdo;

    if (!is_logged_in()) {
        return 0;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Wishlist count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Generate order number
 */
function generate_order_number()
{
    return 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

/**
 * Get site settings
 */
function get_site_setting($key, $default = '')
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Get setting error: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update site setting
 */
function update_site_setting($key, $value, $type = 'text', $group = 'general')
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, updated_at) 
                               VALUES (?, ?, ?, ?, NOW()) 
                               ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        return $stmt->execute([$key, $value, $type, $group, $value]);
    } catch (PDOException $e) {
        error_log("Update setting error: " . $e->getMessage());
        return false;
    }
}

/**
 * Flash message functions
 */
function set_flash_message($type, $message)
{
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function get_flash_message()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Time ago function
 */
function time_ago($datetime)
{
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) {
        return 'just now';
    } elseif ($difference < 3600) {
        return floor($difference / 60) . ' minutes ago';
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . ' hours ago';
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . ' days ago';
    }
    return date('M d, Y', $timestamp);
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 100, $suffix = '...')
{
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

/**
 * JSON response for API
 */
function json_response($success, $message = '', $data = [])
{
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * Log activity
 */
function log_activity($user_id, $action, $details = '')
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log("Log activity error: " . $e->getMessage());
    }
}

/**
 * Debug function (only works in development)
 */
function debug($data, $label = '')
{
    if (ENVIRONMENT === 'development') {
        echo '<pre style="background:#f4f4f4;border:1px solid #ddd;padding:10px;margin:10px 0;">';
        if ($label)
            echo "<strong>$label:</strong>\n";
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Get environment info
 */
function get_environment_info()
{
    return [
        'environment' => ENVIRONMENT,
        'is_local' => IS_LOCAL_ENV,
        'is_live' => IS_LIVE_ENV,
        'site_url' => SITE_URL,
        'db_name' => DB_NAME,
        'db_charset' => DB_CHARSET,
        'razorpay_mode' => RAZORPAY_ENVIRONMENT,
        'currency_symbol' => CURRENCY_SYMBOL,
        'php_version' => phpversion(),
        'encoding' => mb_internal_encoding()
    ];
}

// ============================================================================
// INITIALIZE
// ============================================================================

// Create upload directories if they don't exist
$upload_dirs = [
    UPLOAD_PATH,
    PRODUCT_IMAGE_PATH,
    CATEGORY_IMAGE_PATH,
    BLOG_IMAGE_PATH,
    AVATAR_IMAGE_PATH,
    TEMP_UPLOAD_PATH,
    ROOT_PATH . '/logs'
];

foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Generate CSRF token
generate_csrf_token();

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    session_start();
    generate_csrf_token();
}
$_SESSION['last_activity'] = time();

// Set environment identifier in session (useful for debugging)
if (!isset($_SESSION['env_detected'])) {
    $_SESSION['env_detected'] = IS_LOCAL_ENV ? 'local' : 'live';
}

// ============================================================================
// DISPLAY ENVIRONMENT INFO (Development Only)
// ============================================================================
if (ENVIRONMENT === 'development' && isset($_GET['show_config'])) {
    echo '<div style="background:#2c3e50;color:#ecf0f1;padding:20px;font-family:monospace;">';
    echo '<h2>🔧 Configuration Info</h2>';
    echo '<table style="width:100%;border-collapse:collapse;">';
    foreach (get_environment_info() as $key => $value) {
        echo '<tr style="border-bottom:1px solid #34495e;">';
        echo '<td style="padding:8px;font-weight:bold;">' . ucfirst(str_replace('_', ' ', $key)) . '</td>';
        echo '<td style="padding:8px;">' . (is_bool($value) ? ($value ? 'Yes' : 'No') : htmlspecialchars($value)) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<p style="margin-top:15px;"><strong>Test Price:</strong> ' . format_price(129900) . '</p>';
    echo '<p style="margin-top:15px;opacity:0.7;">Remove ?show_config from URL to hide this</p>';
    echo '</div>';
}
?>