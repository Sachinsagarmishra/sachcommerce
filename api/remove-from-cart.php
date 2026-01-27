<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

if (!$cart_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid cart item'
    ]);
    exit;
}

// Delete cart item (check ownership)
try {
    if (is_logged_in()) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->execute([$cart_id, session_id()]);
    }
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cart item not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove item'
    ]);
}
