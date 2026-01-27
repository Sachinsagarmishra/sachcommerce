<?php
/**
 * Admin Logout
 */
require_once '../config/config.php';

// Log activity before destroying session
if (isset($_SESSION['user_id'])) {
    log_activity($_SESSION['user_id'], 'admin_logout', 'Admin logged out');
}

// Destroy session
session_unset();
session_destroy();

// Remove remember me cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Redirect to login page
redirect(SITE_URL . '/admin/index.php');
