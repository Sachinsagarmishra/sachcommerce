<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get product ID and quantity
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!$product_id || $quantity < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product or quantity'
    ]);
    exit;
}

// Check if product exists and has stock
$stmt = $pdo->prepare("SELECT id, stock_quantity FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}

if ($product['stock_quantity'] < $quantity) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock available'
    ]);
    exit;
}

// Determine user or session
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $session_id = null;
} else {
    $user_id = null;
    $session_id = session_id();
}

// Check if product already in cart
if ($user_id) {
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
} else {
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
}

$cart_item = $stmt->fetch();

try {
    if ($cart_item) {
        // Update quantity
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['stock_quantity']) {
            echo json_encode([
                'success' => false,
                'message' => 'Not enough stock available'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_item['id']]);
    } else {
        // Insert new cart item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, session_id, product_id, quantity, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $session_id, $product_id, $quantity]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add to cart'
    ]);
}
