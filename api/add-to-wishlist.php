<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'login_required' => true,
        'message' => 'Please login to add items to wishlist'
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

// Check if product exists
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}

// Check if already in wishlist
$stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $product_id]);

if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Product already in wishlist'
    ]);
    exit;
}

// Add to wishlist
try {
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Added to wishlist successfully'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add to wishlist'
    ]);
}
