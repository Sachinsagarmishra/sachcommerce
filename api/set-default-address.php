<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$address_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$address_id) {
    $_SESSION['error'] = 'Invalid address';
    header('Location: ' . SITE_URL . '/addresses.php');
    exit;
}

try {
    // Unset all defaults for this user
    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Set this address as default
    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$address_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Default address updated';
    } else {
        $_SESSION['error'] = 'Address not found';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to update address';
}

header('Location: ' . SITE_URL . '/addresses.php');
exit;
