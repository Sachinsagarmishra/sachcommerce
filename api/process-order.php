<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['error'] = 'Please login to place order';
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

// Get form data
$address_id = isset($_POST['address_id']) ? (int)$_POST['address_id'] : 0;
$payment_method = isset($_POST['payment_method']) ? sanitize_input($_POST['payment_method']) : '';

// Validation
if (!$address_id || !$payment_method) {
    $_SESSION['error'] = 'Please select address and payment method';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

if (!in_array($payment_method, ['razorpay', 'cod'])) {
    $_SESSION['error'] = 'Invalid payment method';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

// Get cart items
$cart_items = get_cart_items();
if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty';
    header('Location: ' . SITE_URL . '/cart.php');
    exit;
}

// Get address
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ? AND user_id = ?");
$stmt->execute([$address_id, $_SESSION['user_id']]);
$address = $stmt->fetch();

if (!$address) {
    $_SESSION['error'] = 'Invalid address selected';
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $item_price = $item['sale_price'] ?? $item['price'];
    $subtotal += $item_price * $item['quantity'];
}

$shipping_charge = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
$total_amount = $subtotal + $shipping_charge;

// Create order
try {
    $pdo->beginTransaction();
    
    // Generate order number
    $order_number = 'ORD' . date('Ymd') . rand(1000, 9999);
    
    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, order_number, order_status, payment_method, payment_status,
            subtotal, shipping_charge, total_amount,
            customer_name, customer_email, customer_phone,
            shipping_address, shipping_city, shipping_state, shipping_pincode,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $user = get_logged_user();
    
    $stmt->execute([
        $_SESSION['user_id'],
        $order_number,
        'pending',
        $payment_method,
        $payment_method === 'cod' ? 'pending' : 'pending',
        $subtotal,
        $shipping_charge,
        $total_amount,
        $address['full_name'],
        $user['email'],
        $address['phone'],
        $address['address_line1'] . ($address['address_line2'] ? ', ' . $address['address_line2'] : ''),
        $address['city'],
        $address['state'],
        $address['pincode']
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    // Insert order items
    foreach ($cart_items as $item) {
        $item_price = $item['sale_price'] ?? $item['price'];
        $item_subtotal = $item_price * $item['quantity'];
        
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item_price,
            $item_subtotal
        ]);
        
        // Update product stock
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $pdo->commit();
    
    // Redirect based on payment method
    if ($payment_method === 'razorpay') {
        // Redirect to Razorpay payment page
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_number'] = $order_number;
        header('Location: ' . SITE_URL . '/api/razorpay-payment.php?order_id=' . $order_id);
    } else {
        // COD - Direct success
        header('Location: ' . SITE_URL . '/payment-success.php?order=' . $order_number);
    }
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Failed to create order: ' . $e->getMessage();
    header('Location: ' . SITE_URL . '/checkout.php');
    exit;
}
