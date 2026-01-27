<?php
require_once 'config/config.php';

// Destroy session
session_unset();
session_destroy();

// Remove remember cookie
if (isset($_COOKIE['user_remember'])) {
    setcookie('user_remember', '', time() - 3600, '/');
}

// Redirect to homepage
header('Location: ' . SITE_URL);
exit;
