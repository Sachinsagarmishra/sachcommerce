<?php
/**
 * Admin Authentication Check
 * Include this file at the top of every admin page
 */

if (!defined('IS_ADMIN_PANEL')) {
    die('Direct access not permitted');
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect(SITE_URL . '/admin/index.php?error=login_required');
}

// Check if user is admin
$user = get_logged_user();
if (!$user || $user['role'] !== 'admin') {
    session_destroy();
    redirect(SITE_URL . '/admin/index.php?error=unauthorized');
}

// Update last activity
$_SESSION['last_activity'] = time();
