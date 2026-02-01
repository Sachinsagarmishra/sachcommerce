<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

if (!$order_id) {
    $_SESSION['error'] = 'Invalid order';
    header('Location: ' . SITE_URL . '/checkout');
    exit;
}

// Get order details - Support both logged-in users and guests
if (is_logged_in()) {
    // Logged-in user - verify ownership
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
} else {
    // Guest user - verify via session
    $session_order_id = $_SESSION['last_order_id'] ?? 0;
    if ($session_order_id != $order_id) {
        $_SESSION['error'] = 'Order not found or session expired';
        header('Location: ' . SITE_URL . '/checkout');
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
}

$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: ' . SITE_URL . '/checkout');
    exit;
}

// ----------------------------------------------------------------------------
// DYNAMIC RAZORPAY ORDER CREATION
// ----------------------------------------------------------------------------
$razorpay_key = defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : '';
$razorpay_secret = defined('RAZORPAY_KEY_SECRET') ? RAZORPAY_KEY_SECRET : '';
$is_razorpay_configured = false;
$api_error = '';

// Check if keys are configured (and not default placeholders)
if (!empty($razorpay_key) && !empty($razorpay_secret) && strpos($razorpay_key, 'YOUR_') === false) {
    $is_razorpay_configured = true;

    // If razorpay_order_id is missing in DB, create it via API
    if (empty($order['razorpay_order_id'])) {
        try {
            $api_url = "https://api.razorpay.com/v1/orders";
            $data = [
                'amount' => $order['total_amount'] * 100, // Amount in paise
                'currency' => RAZORPAY_CURRENCY,
                'receipt' => $order['order_number'],
                'payment_capture' => 1
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_USERPWD, $razorpay_key . ':' . $razorpay_secret);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $result = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception('Curl Error: ' . curl_error($ch));
            }
            curl_close($ch);

            $response = json_decode($result, true);

            if ($http_status === 200 && isset($response['id'])) {
                // Save razorpay_order_id to database
                $rzp_order_id = $response['id'];
                $update_stmt = $pdo->prepare("UPDATE orders SET razorpay_order_id = ? WHERE id = ?");
                $update_stmt->execute([$rzp_order_id, $order_id]);

                // Refresh order data
                $order['razorpay_order_id'] = $rzp_order_id;
            } else {
                $err_msg = isset($response['error']['description']) ? $response['error']['description'] : 'Unknown API Error';
                $api_error = "Razorpay API Error: " . $err_msg;
                $is_razorpay_configured = false; // Disable button on error
            }
        } catch (Exception $e) {
            $api_error = "Connection Error: " . $e->getMessage();
            $is_razorpay_configured = false;
        }
    }
}

$page_title = 'Payment - ' . $order['order_number'];

include '../includes/header.php';
// Check if navbar exists in path, adjusted include path just in case
if (file_exists('../includes/navbar.php')) {
    include '../includes/navbar.php';
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Complete Payment</h5>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="lni lni-credit-cards fa-4x text-primary"></i>
                    </div>

                    <h5 class="text-muted">Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
                    <h2 class="text-primary mb-4"><?php echo format_price($order['total_amount']); ?></h2>

                    <!-- Configuration / API Status Messages -->
                    <?php if (!$is_razorpay_configured): ?>
                        <div class="alert alert-warning text-start">
                            <strong>Payment Configuration Issue</strong>
                            <?php if ($api_error): ?>
                                <p class="mb-0 mt-2 text-danger small"><?php echo htmlspecialchars($api_error); ?></p>
                            <?php else: ?>
                                <p class="mb-0 mt-2">Razorpay keys are not configured or are invalid.</p>
                                <p class="small text-muted mt-1">Please go to Admin Panel > Settings > Payment Settings to
                                    configure your API keys.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-3 mt-4">
                        <!-- Razorpay Button -->
                        <?php if ($is_razorpay_configured): ?>
                            <button id="rzp-button" class="btn btn-primary btn-lg">
                                Pay Now Securely
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg" disabled>
                                Payment Unavailable
                            </button>
                        <?php endif; ?>

                        <!-- Testing Bypass (Only visible in Development/Test Environment) -->
                        <?php if (defined('RAZORPAY_ENVIRONMENT') && RAZORPAY_ENVIRONMENT === 'test'): ?>
                            <div class="border-top pt-3 mt-2">
                                <p class="small text-muted mb-2">Development Mode Options</p>
                                <form action="<?php echo SITE_URL; ?>/api/mark-payment-success.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <button type="submit" class="btn btn-outline-success w-100">
                                        Bypass Payment (Test Success)
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <a href="<?php echo SITE_URL; ?>/payment-failed?order=<?php echo $order['order_number']; ?>"
                            class="btn btn-link text-danger text-decoration-none">
                            Cancel Payment
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dynamic Razorpay Script -->
            <?php if ($is_razorpay_configured): ?>
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    var options = {
                        "key": "<?php echo $razorpay_key; ?>",
                        "amount": "<?php echo $order['total_amount'] * 100; ?>",
                        "currency": "<?php echo RAZORPAY_CURRENCY; ?>",
                        "name": "<?php echo SITE_NAME; ?>",
                        "description": "Order #<?php echo $order['order_number']; ?>",
                        "image": "<?php echo SITE_URL; ?>/assets/images/logo.png", // Ensure you have a logo
                        "order_id": "<?php echo $order['razorpay_order_id']; ?>",
                        "handler": function (response) {
                            // Show processing state
                            $('#rzp-button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...');
                            $('#rzp-button').prop('disabled', true);

                            // Send payment details to server for verification
                            $.ajax({
                                url: '<?php echo SITE_URL; ?>/api/verify-razorpay-payment.php',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    order_id: <?php echo $order_id; ?>
                                },
                                success: function (result) {
                                    if (result.success) {
                                        window.location.href = '<?php echo SITE_URL; ?>/order-confirmed?order=<?php echo $order['order_number']; ?>';
                                    } else {
                                        alert(result.message || 'Payment verification failed');
                                        window.location.href = '<?php echo SITE_URL; ?>/payment-failed?order=<?php echo $order['order_number']; ?>';
                                    }
                                },
                                error: function () {
                                    alert('Network error verifying payment');
                                    $('#rzp-button').html('Pay Now Securely');
                                    $('#rzp-button').prop('disabled', false);
                                }
                            });
                        },
                        "prefill": {
                            "name": "<?php echo htmlspecialchars($order['customer_name']); ?>",
                            "email": "<?php echo htmlspecialchars($order['customer_email']); ?>",
                            "contact": "<?php echo htmlspecialchars($order['customer_phone']); ?>"
                        },
                        "theme": {
                            "color": "#4e73df"
                        },
                        "modal": {
                            "ondismiss": function () {
                                console.log('Checkout form closed');
                            }
                        }
                    };

                    var rzp = new Razorpay(options);

                    // Handle Payment Failure
                    rzp.on('payment.failed', function (response) {
                        console.error(response.error);
                        alert("Payment Failed: " + response.error.description);
                    });

                    document.getElementById('rzp-button').onclick = function (e) {
                        rzp.open();
                        e.preventDefault();
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>