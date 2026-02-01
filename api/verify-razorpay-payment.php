<?php
/**
 * Razorpay Payment Verification
 * Verifies payment signature and updates order status
 * Supports both logged-in users and guest checkout
 */

require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get Payment Data from POST
$razorpay_payment_id = isset($_POST['razorpay_payment_id']) ? trim($_POST['razorpay_payment_id']) : '';
$razorpay_order_id = isset($_POST['razorpay_order_id']) ? trim($_POST['razorpay_order_id']) : '';
$razorpay_signature = isset($_POST['razorpay_signature']) ? trim($_POST['razorpay_signature']) : '';
$order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;

// Validate Inputs
if (empty($razorpay_payment_id) || empty($razorpay_order_id) || empty($razorpay_signature) || empty($order_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing payment details']);
    exit;
}

try {
    // Verify Signature
    $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

    if (hash_equals($generated_signature, $razorpay_signature)) {
        // --- SIGNATURE MATCHES: PAYMENT SUCCESSFUL ---

        $pdo->beginTransaction();

        // Check if order exists and matches the Razorpay Order ID
        // Support both logged-in users and guests
        if (is_logged_in()) {
            $stmt = $pdo->prepare("SELECT id, order_number, total_amount, payment_status, user_id FROM orders WHERE id = ? AND razorpay_order_id = ? AND user_id = ?");
            $stmt->execute([$order_id, $razorpay_order_id, $_SESSION['user_id']]);
        } else {
            // For guests, verify via session
            $session_order_id = $_SESSION['last_order_id'] ?? 0;
            if ($session_order_id != $order_id) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Order session expired. Please try again.']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT id, order_number, total_amount, payment_status, user_id FROM orders WHERE id = ? AND razorpay_order_id = ?");
            $stmt->execute([$order_id, $razorpay_order_id]);
        }

        $order = $stmt->fetch();

        if ($order) {
            // Prevent duplicate processing
            if ($order['payment_status'] === 'paid') {
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => 'Payment already verified.'
                ]);
                exit;
            }

            // Update Order Status to 'processing'
            $update_stmt = $pdo->prepare("
                UPDATE orders SET 
                    payment_status = 'paid', 
                    order_status = 'processing', 
                    razorpay_payment_id = ?, 
                    razorpay_signature = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $update_stmt->execute([
                $razorpay_payment_id,
                $razorpay_signature,
                $order_id
            ]);

            // Log Transaction
            log_payment_transaction(
                $order_id,
                $razorpay_payment_id,
                $razorpay_order_id,
                $order['total_amount'],
                'paid',
                '',
                'Client-side verification successful'
            );

            // Log Activity for logged-in users
            if (is_logged_in() && function_exists('log_activity')) {
                log_activity(
                    $_SESSION['user_id'],
                    'payment_verified',
                    "Payment successful for Order #{$order['order_number']}. RZP ID: $razorpay_payment_id"
                );
            }

            // Clear Cart - for both logged-in and guest users
            if (is_logged_in()) {
                $clear_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $clear_cart->execute([$_SESSION['user_id']]);
            }
            // Clear session cart for guests
            if (isset($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }

            $pdo->commit();

            // Send Order Confirmation Email
            $email_enabled = get_site_setting('email_order_confirmation', '1');
            if ($email_enabled === '1' && !empty($order['customer_email'])) {
                try {
                    // Get order items
                    $items_stmt = $pdo->prepare("
                        SELECT oi.*, p.name 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?
                    ");
                    $items_stmt->execute([$order_id]);
                    $order_items = $items_stmt->fetchAll();

                    // Include process-order.php to get the email function
                    if (!function_exists('sendOrderConfirmationEmail')) {
                        require_once __DIR__ . '/process-order.php';
                    }

                    if (function_exists('sendOrderConfirmationEmail')) {
                        sendOrderConfirmationEmail(
                            $order_id,
                            $order['order_number'],
                            $order['customer_email'],
                            $order['customer_name'],
                            $order['total_amount'],
                            $order_items
                        );
                    }
                } catch (Exception $e) {
                    error_log("Failed to send order confirmation email: " . $e->getMessage());
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Payment verified and order updated successfully.'
            ]);
        } else {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Order mismatch. Verification failed.'
            ]);
        }

    } else {
        // --- SIGNATURE MISMATCH: POTENTIAL FRAUD ---

        // Log failed attempt
        log_payment_transaction(
            $order_id,
            $razorpay_payment_id,
            $razorpay_order_id,
            0,
            'failed',
            '',
            'Signature verification failed - possible fraud attempt'
        );

        echo json_encode([
            'success' => false,
            'message' => 'Invalid payment signature. Verification failed.'
        ]);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Razorpay Verification Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Server error during payment verification.'
    ]);
}

/**
 * Log payment transaction to database
 */
function log_payment_transaction($order_id, $transaction_id, $razorpay_order_id, $amount, $status, $method = '', $notes = '')
{
    global $pdo;

    try {
        // Check if table exists
        $table_check = $pdo->query("SHOW TABLES LIKE 'payment_transactions'");
        if ($table_check->rowCount() === 0) {
            return false;
        }

        $stmt = $pdo->prepare("
            INSERT INTO payment_transactions 
            (order_id, transaction_id, razorpay_order_id, amount, status, payment_method, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$order_id, $transaction_id, $razorpay_order_id, $amount, $status, $method, $notes]);
    } catch (PDOException $e) {
        error_log("Transaction log error: " . $e->getMessage());
        return false;
    }
}
?>