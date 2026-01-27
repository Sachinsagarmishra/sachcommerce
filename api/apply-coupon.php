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
$stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND start_date <= NOW() AND (end_date IS NULL OR end_date >= NOW())");
$stmt->execute([$coupon_code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired coupon code'
    ]);
    exit;
}

// Check usage limit
if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
    echo json_encode([
        'success' => false,
        'message' => 'Coupon usage limit reached'
    ]);
    exit;
}

// Check minimum order amount
$cart_total = get_cart_total();
if ($coupon['minimum_amount'] && $cart_total < $coupon['minimum_amount']) {
    echo json_encode([
        'success' => false,
        'message' => 'Minimum order amount not met. Required: ' . format_price($coupon['minimum_amount'])
    ]);
    exit;
}

// Calculate discount
if ($coupon['discount_type'] === 'percentage') {
    $discount = ($cart_total * $coupon['discount_value']) / 100;
    if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
        $discount = $coupon['max_discount'];
    }
} else {
    $discount = $coupon['discount_value'];
}

// Store coupon in session
$_SESSION['applied_coupon'] = [
    'code' => $coupon['code'],
    'discount' => $discount
];

echo json_encode([
    'success' => true,
    'message' => 'Coupon applied successfully',
    'discount' => $discount,
    'discount_formatted' => format_price($discount)
]);
