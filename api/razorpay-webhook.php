<?php
/**
 * Razorpay Webhook Handler
 * This endpoint receives payment events from Razorpay to ensure no payment is missed
 * 
 * IMPORTANT: Configure this URL in your Razorpay Dashboard:
 * Dashboard > Settings > Webhooks > Add New Webhook
 * URL: https://yourdomain.com/api/razorpay-webhook.php
 * Events: payment.captured, payment.failed, order.paid
 */

require_once '../config/config.php';
require_once '../includes/functions.php';

// Log all webhook requests for debugging
$log_file = ROOT_PATH . '/logs/razorpay_webhook.log';
$log_data = date('Y-m-d H:i:s') . " - Webhook received\n";

// Get webhook secret from database
$webhook_secret = get_site_setting('razorpay_webhook_secret', RAZORPAY_WEBHOOK_SECRET);

// Get raw POST data
$payload = file_get_contents('php://input');
$log_data .= "Payload: " . substr($payload, 0, 500) . "\n";

// Get signature from header
$razorpay_signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Verify webhook signature
if (!empty($webhook_secret) && $webhook_secret !== 'YOUR_WEBHOOK_SECRET') {
    $expected_signature = hash_hmac('sha256', $payload, $webhook_secret);

    if (!hash_equals($expected_signature, $razorpay_signature)) {
        $log_data .= "ERROR: Invalid signature\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
        exit;
    }
}

// Parse the webhook payload
$webhook_data = json_decode($payload, true);

if (!$webhook_data || !isset($webhook_data['event'])) {
    $log_data .= "ERROR: Invalid payload\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid payload']);
    exit;
}

$event = $webhook_data['event'];
$log_data .= "Event: $event\n";

try {
    $pdo->beginTransaction();

    switch ($event) {
        case 'payment.captured':
        case 'order.paid':
            // Payment was successful
            $payment_entity = $webhook_data['payload']['payment']['entity'] ?? null;

            if ($payment_entity) {
                $razorpay_payment_id = $payment_entity['id'];
                $razorpay_order_id = $payment_entity['order_id'];
                $amount = $payment_entity['amount'] / 100; // Convert from paise
                $status = $payment_entity['status'];
                $method = $payment_entity['method'] ?? 'unknown';

                $log_data .= "Payment ID: $razorpay_payment_id, Order ID: $razorpay_order_id, Amount: $amount, Status: $status\n";

                // Find order by razorpay_order_id
                $stmt = $pdo->prepare("SELECT id, order_number, payment_status FROM orders WHERE razorpay_order_id = ?");
                $stmt->execute([$razorpay_order_id]);
                $order = $stmt->fetch();

                if ($order) {
                    // Only update if not already paid (prevent duplicate updates)
                    if ($order['payment_status'] !== 'paid') {
                        // Update order status
                        $update_stmt = $pdo->prepare("
                            UPDATE orders SET 
                                payment_status = 'paid', 
                                order_status = 'processing', 
                                razorpay_payment_id = ?, 
                                updated_at = NOW()
                            WHERE id = ?
                        ");
                        $update_stmt->execute([$razorpay_payment_id, $order['id']]);

                        // Log transaction
                        log_transaction($order['id'], $razorpay_payment_id, $razorpay_order_id, $amount, 'paid', $method, 'Webhook: payment.captured');

                        $log_data .= "SUCCESS: Order #{$order['order_number']} updated to paid\n";
                    } else {
                        $log_data .= "INFO: Order #{$order['order_number']} already marked as paid\n";
                    }
                } else {
                    $log_data .= "WARNING: No order found for Razorpay Order ID: $razorpay_order_id\n";
                }
            }
            break;

        case 'payment.failed':
            // Payment failed
            $payment_entity = $webhook_data['payload']['payment']['entity'] ?? null;

            if ($payment_entity) {
                $razorpay_payment_id = $payment_entity['id'];
                $razorpay_order_id = $payment_entity['order_id'];
                $amount = $payment_entity['amount'] / 100;
                $error_code = $payment_entity['error_code'] ?? 'unknown';
                $error_description = $payment_entity['error_description'] ?? 'Unknown error';

                $log_data .= "FAILED - Payment ID: $razorpay_payment_id, Error: $error_code - $error_description\n";

                // Find order
                $stmt = $pdo->prepare("SELECT id, order_number FROM orders WHERE razorpay_order_id = ?");
                $stmt->execute([$razorpay_order_id]);
                $order = $stmt->fetch();

                if ($order) {
                    // Update order with failed payment
                    $update_stmt = $pdo->prepare("
                        UPDATE orders SET 
                            payment_status = 'failed',
                            razorpay_payment_id = ?,
                            admin_notes = CONCAT(IFNULL(admin_notes, ''), '\nPayment failed: ', ?),
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$razorpay_payment_id, $error_description, $order['id']]);

                    // Log transaction
                    log_transaction($order['id'], $razorpay_payment_id, $razorpay_order_id, $amount, 'failed', '', "Error: $error_code - $error_description");

                    $log_data .= "Order #{$order['order_number']} marked as failed\n";
                }
            }
            break;

        case 'refund.created':
            // Refund was initiated
            $refund_entity = $webhook_data['payload']['refund']['entity'] ?? null;

            if ($refund_entity) {
                $refund_id = $refund_entity['id'];
                $payment_id = $refund_entity['payment_id'];
                $refund_amount = $refund_entity['amount'] / 100;

                $log_data .= "REFUND - ID: $refund_id, Payment: $payment_id, Amount: $refund_amount\n";

                // Find order by payment ID
                $stmt = $pdo->prepare("SELECT id, order_number FROM orders WHERE razorpay_payment_id = ?");
                $stmt->execute([$payment_id]);
                $order = $stmt->fetch();

                if ($order) {
                    // Update order status
                    $update_stmt = $pdo->prepare("
                        UPDATE orders SET 
                            payment_status = 'refunded',
                            order_status = 'refunded',
                            admin_notes = CONCAT(IFNULL(admin_notes, ''), '\nRefund ID: ', ?),
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$refund_id, $order['id']]);

                    // Log transaction
                    log_transaction($order['id'], $refund_id, '', $refund_amount, 'refunded', '', "Refund processed");

                    $log_data .= "Order #{$order['order_number']} marked as refunded\n";
                }
            }
            break;

        default:
            $log_data .= "INFO: Unhandled event type: $event\n";
    }

    $pdo->commit();

    // Log success
    file_put_contents($log_file, $log_data . "---\n", FILE_APPEND);

    http_response_code(200);
    echo json_encode(['status' => 'ok']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $log_data .= "EXCEPTION: " . $e->getMessage() . "\n";
    file_put_contents($log_file, $log_data . "---\n", FILE_APPEND);

    error_log("Razorpay Webhook Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error']);
}

/**
 * Log transaction to database
 */
function log_transaction($order_id, $transaction_id, $razorpay_order_id, $amount, $status, $method, $notes = '')
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO payment_transactions 
            (order_id, transaction_id, razorpay_order_id, amount, status, payment_method, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$order_id, $transaction_id, $razorpay_order_id, $amount, $status, $method, $notes]);
    } catch (PDOException $e) {
        // Table might not exist, log error
        error_log("Transaction log error: " . $e->getMessage());
    }
}
?>