<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
    ]);
    exit;
}

// Get product ID
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (!$product_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product'
    ]);
    exit;
}

// Remove from wishlist
try {
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Removed from wishlist'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove from wishlist'
    ]);
}
