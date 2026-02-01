<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$coupon_code = isset($_POST['coupon_code']) ? sanitize_input($_POST['coupon_code']) : '';

if (empty($coupon_code)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a coupon code'
    ]);
    exit;
}

// Check if coupon exists and is valid
// Admin columns: code, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, usage_per_user, valid_from, valid_to, status, used_count
$stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND valid_from <= NOW() AND valid_to >= NOW()");
$stmt->execute([$coupon_code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired coupon code'
    ]);
    exit;
}

// Check total usage limit
if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
    echo json_encode([
        'success' => false,
        'message' => 'Coupon usage limit reached'
    ]);
    exit;
}

// Check usage per user
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND coupon_code = ?");
    $stmt->execute([$user_id, $coupon_code]);
    $user_usage = $stmt->fetchColumn();

    if ($coupon['usage_per_user'] && $user_usage >= $coupon['usage_per_user']) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already reached the usage limit for this coupon'
        ]);
        exit;
    }
}

// Check minimum order amount
$cart_total = get_cart_total();
if ($coupon['min_order_amount'] && $cart_total < $coupon['min_order_amount']) {
    echo json_encode([
        'success' => false,
        'message' => 'Minimum order amount not met. Required: ' . format_price($coupon['min_order_amount'])
    ]);
    exit;
}

// Calculate discount
$discount = 0;
if ($coupon['discount_type'] === 'percentage') {
    $discount = ($cart_total * $coupon['discount_value']) / 100;
    if ($coupon['max_discount_amount'] && $discount > $coupon['max_discount_amount']) {
        $discount = $coupon['max_discount_amount'];
    }
} else {
    $discount = $coupon['discount_value'];
}

// Ensure discount doesn't exceed cart total
if ($discount > $cart_total) {
    $discount = $cart_total;
}

// Store coupon in session
$_SESSION['applied_coupon'] = [
    'code' => $coupon['code'],
    'discount' => $discount,
    'type' => $coupon['discount_type'],
    'value' => $coupon['discount_value']
];

echo json_encode([
    'success' => true,
    'message' => 'Coupon applied successfully',
    'discount' => $discount,
    'discount_formatted' => format_price($discount),
    'total' => $cart_total - $discount,
    'total_formatted' => format_price($cart_total - $discount)
]);
