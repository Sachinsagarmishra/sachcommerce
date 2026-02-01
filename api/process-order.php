<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/checkout');
    exit;
}

// Check if this is guest checkout
$is_guest_checkout = isset($_POST['guest_checkout']) && $_POST['guest_checkout'] == '1';

// Get form data
$payment_method = isset($_POST['payment_method']) ? sanitize_input($_POST['payment_method']) : '';
$order_notes = isset($_POST['order_notes']) ? sanitize_input($_POST['order_notes']) : '';

// Validate payment method
if (!$payment_method || !in_array($payment_method, ['razorpay', 'cod'])) {
    $_SESSION['error'] = 'Invalid payment method selected';
    header('Location: ' . SITE_URL . '/checkout');
    exit;
}

// Initialize variables
$user_id = null;
$customer_name = '';
$customer_email = '';
$customer_phone = '';
$shipping_address = '';
$shipping_city = '';
$shipping_state = '';
$shipping_pincode = '';
$temp_password = null;
$new_user_created = false;

if (is_logged_in() && !$is_guest_checkout) {
    // LOGGED-IN USER CHECKOUT
    $user_id = $_SESSION['user_id'];
    $user = get_logged_user();

    // Get selected address
    $address_id = isset($_POST['address_id']) ? (int) $_POST['address_id'] : 0;

    if (!$address_id) {
        $_SESSION['error'] = 'Please select a delivery address';
        header('Location: ' . SITE_URL . '/checkout');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$address_id, $user_id]);
    $address = $stmt->fetch();

    if (!$address) {
        $_SESSION['error'] = 'Invalid address selected';
        header('Location: ' . SITE_URL . '/checkout');
        exit;
    }

    $customer_name = $address['full_name'];
    $customer_email = $user['email'];
    $customer_phone = $address['phone'];
    $shipping_address = $address['address_line1'] . ($address['address_line2'] ? ', ' . $address['address_line2'] : '');
    $shipping_city = $address['city'];
    $shipping_state = $address['state'];
    $shipping_pincode = $address['pincode'];

} else {
    // GUEST CHECKOUT
    $full_name = isset($_POST['full_name']) ? sanitize_input($_POST['full_name']) : '';
    $customer_email = isset($_POST['customer_email']) ? sanitize_input($_POST['customer_email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $address_line1 = isset($_POST['address_line1']) ? sanitize_input($_POST['address_line1']) : '';
    $address_line2 = isset($_POST['address_line2']) ? sanitize_input($_POST['address_line2']) : '';
    $city = isset($_POST['city']) ? sanitize_input($_POST['city']) : '';
    $state = isset($_POST['state']) ? sanitize_input($_POST['state']) : '';
    $pincode = isset($_POST['pincode']) ? sanitize_input($_POST['pincode']) : '';

    // Validate guest checkout fields
    if (!$full_name || !$customer_email || !$phone || !$address_line1 || !$city || !$state || !$pincode) {
        $_SESSION['error'] = 'Please fill in all required fields';
        header('Location: ' . SITE_URL . '/checkout');
        exit;
    }

    // Validate email format
    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address';
        header('Location: ' . SITE_URL . '/checkout');
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$customer_email]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        // Email exists, use that user
        $user_id = $existing_user['id'];
    } else {
        // Create new guest user with temporary password
        $temp_password = generateTempPassword();
        $hashed_password = password_hash($temp_password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, status, role, email_verified, created_at) VALUES (?, ?, ?, ?, 'active', 'customer', 1, NOW())");
        $stmt->execute([$full_name, $customer_email, $hashed_password, $phone]);
        $user_id = $pdo->lastInsertId();
        $new_user_created = true;

        // Save address for new user
        $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, full_name, phone, address_line1, address_line2, city, state, pincode, is_default, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$user_id, $full_name, $phone, $address_line1, $address_line2, $city, $state, $pincode]);
    }

    $customer_name = $full_name;
    $customer_phone = $phone;
    $shipping_address = $address_line1 . ($address_line2 ? ', ' . $address_line2 : '');
    $shipping_city = $city;
    $shipping_state = $state;
    $shipping_pincode = $pincode;
}

// Get cart items
$cart_items = get_cart_items();
if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty';
    header('Location: ' . SITE_URL . '/cart');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $item_price = $item['sale_price'] ?? $item['price'];
    $subtotal += $item_price * $item['quantity'];
}

$shipping_charge = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
$total_amount = $subtotal + $shipping_charge;

// Create order
try {
    $pdo->beginTransaction();

    // Generate unique order number
    $order_number = 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, order_number, order_status, payment_method, payment_status,
            subtotal, shipping_charge, total_amount,
            customer_name, customer_email, customer_phone,
            shipping_address, shipping_city, shipping_state, shipping_pincode,
            order_notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $user_id,
        $order_number,
        'pending',
        $payment_method,
        $payment_method === 'cod' ? 'pending' : 'pending',
        $subtotal,
        $shipping_charge,
        $total_amount,
        $customer_name,
        $customer_email,
        $customer_phone,
        $shipping_address,
        $shipping_city,
        $shipping_state,
        $shipping_pincode,
        $order_notes
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

    // Clear cart - handle both session-based and user-based cart
    if (is_logged_in()) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([session_id()]);
    }

    $pdo->commit();

    // Store order info for thank you page
    $_SESSION['last_order_number'] = $order_number;
    $_SESSION['last_order_id'] = $order_id;

    // Send email for guest user with temporary password
    if ($new_user_created && $temp_password) {
        $_SESSION['guest_password'] = $temp_password;
        $_SESSION['guest_email'] = $customer_email;

        // Try to send email (non-blocking)
        try {
            sendGuestAccountEmail($customer_email, $customer_name, $temp_password, $order_number);
        } catch (Exception $e) {
            error_log("Failed to send guest account email: " . $e->getMessage());
        }
    }

    // Redirect based on payment method
    if ($payment_method === 'razorpay') {
        // Redirect to Razorpay payment page
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_number'] = $order_number;
        header('Location: ' . SITE_URL . '/api/razorpay-payment.php?order_id=' . $order_id);
    } else {
        // COD - Direct to thank you page
        header('Location: ' . SITE_URL . '/order-confirmed?order=' . $order_number);
    }
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Order creation failed: " . $e->getMessage());
    $_SESSION['error'] = 'Failed to create order. Please try again.';
    header('Location: ' . SITE_URL . '/checkout');
    exit;
}

/**
 * Generate a random temporary password
 */
function generateTempPassword($length = 10)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * Send email to guest user with account credentials
 */
function sendGuestAccountEmail($email, $name, $password, $order_number)
{
    $subject = "Your TrendsOne Account Created - Order #" . $order_number;

    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #83b735; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .credentials { background: #fff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #83b735; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .btn { display: inline-block; padding: 12px 30px; background: #83b735; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to TrendsOne!</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>{$name}</strong>,</p>
                <p>Thank you for your order! Your order <strong>#{$order_number}</strong> has been placed successfully.</p>
                <p>We've created an account for you so you can track your order and shop faster in the future.</p>
                
                <div class='credentials'>
                    <h3>Your Login Credentials</h3>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Temporary Password:</strong> {$password}</p>
                </div>
                
                <p><strong>Important:</strong> Please change your password after your first login for security.</p>
                
                <p style='text-align: center; margin-top: 30px;'>
                    <a href='" . SITE_URL . "/login' class='btn'>Login to Your Account</a>
                </p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " TrendsOne. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";

    mail($email, $subject, $message, $headers);
}
