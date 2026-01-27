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
    // Delete address (only if it belongs to the user)
    $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$address_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Address deleted successfully';
    } else {
        $_SESSION['error'] = 'Address not found';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to delete address';
}

header('Location: ' . SITE_URL . '/addresses.php');
exit;
