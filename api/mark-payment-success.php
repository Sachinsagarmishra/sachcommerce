<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if (!$order_id) {
    $_SESSION['error'] = 'Invalid order';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

// Get order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

// Update payment status (for testing only)
try {
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'processing' WHERE id = ?");
    $stmt->execute([$order_id]);
    
    header('Location: ' . SITE_URL . '/payment-success.php?order=' . $order['order_number']);
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to update payment status';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}
