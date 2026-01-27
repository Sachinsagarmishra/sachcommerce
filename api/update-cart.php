<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if (!$cart_id || $quantity < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid cart item or quantity'
    ]);
    exit;
}

// Get cart item and check ownership
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT c.*, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.session_id = ?");
    $stmt->execute([$cart_id, session_id()]);
}

$cart_item = $stmt->fetch();

if (!$cart_item) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item not found'
    ]);
    exit;
}

// Check stock
if ($quantity > $cart_item['stock_quantity']) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock available'
    ]);
    exit;
}

// Update quantity
try {
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update cart'
    ]);
}
