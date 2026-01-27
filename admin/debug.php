<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing config.php... <br>";
require_once '../config/config.php';
echo "config.php OK <br>";

echo "Testing includes/functions.php... <br>";
require_once '../includes/functions.php';
echo "includes/functions.php OK <br>";

echo "Testing admin/includes/functions.php... <br>";
require_once 'includes/functions.php';
echo "admin/includes/functions.php OK <br>";

echo "Testing admin/includes/auth-check.php... <br>";
// Define IS_ADMIN_PANEL if not already defined (usually in config.php)
if (!defined('IS_ADMIN_PANEL'))
    define('IS_ADMIN_PANEL', true);
require_once 'includes/auth-check.php';
echo "admin/includes/auth-check.php OK <br>";

echo "All files loaded successfully!";
?>