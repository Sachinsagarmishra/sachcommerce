<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// 1. Security Check: Ensure user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User session expired. Please login again.']);
    exit;
}

// 2. Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// 3. Get Payment Data from POST
$razorpay_payment_id = isset($_POST['razorpay_payment_id']) ? trim($_POST['razorpay_payment_id']) : '';
$razorpay_order_id   = isset($_POST['razorpay_order_id']) ? trim($_POST['razorpay_order_id']) : '';
$razorpay_signature  = isset($_POST['razorpay_signature']) ? trim($_POST['razorpay_signature']) : '';
$order_id            = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

// 4. Validate Inputs
if (empty($razorpay_payment_id) || empty($razorpay_order_id) || empty($razorpay_signature) || empty($order_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing payment details']);
    exit;
}

try {
    // 5. Verify Signature
    // Razorpay Logic: hmac_sha256(razorpay_order_id + "|" + razorpay_payment_id, secret)
    $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

    if (hash_equals($generated_signature, $razorpay_signature)) {
        // --- SIGNATURE MATCHES: PAYMENT SUCCESSFUL ---

        $pdo->beginTransaction();

        // Check if order exists and matches the Razorpay Order ID to prevent tampering
        $stmt = $pdo->prepare("SELECT id, order_number, total_amount FROM orders WHERE id = ? AND razorpay_order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $razorpay_order_id, $_SESSION['user_id']]);
        $order = $stmt->fetch();

        if ($order) {
            // Update Order Status to 'processing' (or 'paid' depending on your workflow)
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

            // Optional: Log Transaction
            if (function_exists('log_activity')) {
                log_activity(
                    $_SESSION['user_id'], 
                    'payment_verified', 
                    "Payment successful for Order #{$order['order_number']}. RZP ID: $razorpay_payment_id"
                );
            }

            $pdo->commit();
            
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
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid payment signature. Verification failed.'
        ]);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log the actual error on server side
    error_log("Razorpay Verification Error: " . $e->getMessage());
    
    // Return generic error to user
    echo json_encode([
        'success' => false, 
        'message' => 'Server error during payment verification.'
    ]);
}
?>