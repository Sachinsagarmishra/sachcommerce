<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Payment Settings';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razorpay_key_id = isset($_POST['razorpay_key_id']) ? sanitize_input($_POST['razorpay_key_id']) : '';
    $razorpay_key_secret = isset($_POST['razorpay_key_secret']) ? sanitize_input($_POST['razorpay_key_secret']) : '';
    $razorpay_webhook_secret = isset($_POST['razorpay_webhook_secret']) ? sanitize_input($_POST['razorpay_webhook_secret']) : '';
    $razorpay_env = isset($_POST['razorpay_environment']) ? sanitize_input($_POST['razorpay_environment']) : 'test';

    // Update Settings in Database
    $update1 = update_site_setting('razorpay_key_id', $razorpay_key_id, 'text', 'payment');
    $update2 = update_site_setting('razorpay_key_secret', $razorpay_key_secret, 'password', 'payment');
    $update3 = update_site_setting('razorpay_webhook_secret', $razorpay_webhook_secret, 'password', 'payment');
    $update4 = update_site_setting('razorpay_environment', $razorpay_env, 'text', 'payment');

    if ($update1 && $update2 && $update3 && $update4) {
        $success = "Payment settings updated successfully!";
        if (function_exists('log_activity')) {
            log_activity($_SESSION['user_id'] ?? 0, 'update_payment_settings', 'Updated Razorpay settings');
        }
    } else {
        $error = "Failed to update some settings. Please try again.";
    }
}

// Fetch Current Values from DB
$current_key_id = get_site_setting('razorpay_key_id');
$current_key_secret = get_site_setting('razorpay_key_secret');
$current_webhook_secret = get_site_setting('razorpay_webhook_secret');
$current_env = get_site_setting('razorpay_environment', 'test');

// Fetch Recent Transactions
$transactions = [];
try {
    // Check if table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'payment_transactions'");
    if ($table_check->rowCount() > 0) {
        $stmt = $pdo->query("
            SELECT pt.*, o.order_number 
            FROM payment_transactions pt 
            LEFT JOIN orders o ON pt.order_id = o.id 
            ORDER BY pt.created_at DESC 
            LIMIT 50
        ");
        $transactions = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    // Table doesn't exist
}

// Get webhook URL for display
$webhook_url = SITE_URL . '/api/razorpay-webhook.php';

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4"><i class="fas fa-credit-card me-2"></i>Payment Settings</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Razorpay Configuration -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-cog me-2"></i>Razorpay Configuration</span>
                        <span
                            class="badge <?php echo $current_env === 'live' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo ucfirst($current_env); ?> Mode
                        </span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Environment Mode</label>
                                    <select class="form-select" name="razorpay_environment">
                                        <option value="test" <?php echo $current_env === 'test' ? 'selected' : ''; ?>>
                                            🧪 Test / Sandbox Mode
                                        </option>
                                        <option value="live" <?php echo $current_env === 'live' ? 'selected' : ''; ?>>
                                            🚀 Live / Production Mode
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Use Test mode while developing. Switch to Live only when ready for real
                                        payments.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-key me-1"></i>Razorpay Key ID
                                    </label>
                                    <input type="text" class="form-control" name="razorpay_key_id"
                                        value="<?php echo htmlspecialchars($current_key_id); ?>"
                                        placeholder="rzp_test_xxxxxxxxxxxx or rzp_live_xxxxxxxxxxxx" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Razorpay Key Secret
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="razorpay_key_secret"
                                            id="key_secret" value="<?php echo htmlspecialchars($current_key_secret); ?>"
                                            placeholder="Enter your key secret" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('key_secret')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-shield-alt me-1"></i>Webhook Secret (Optional but Recommended)
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="razorpay_webhook_secret"
                                            id="webhook_secret"
                                            value="<?php echo htmlspecialchars($current_webhook_secret); ?>"
                                            placeholder="Enter webhook secret from Razorpay dashboard">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('webhook_secret')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Get this from Razorpay Dashboard > Settings > Webhooks. Used to verify incoming
                                        webhooks.
                                    </small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Save Payment Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Webhook Configuration -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <i class="fas fa-satellite-dish me-2"></i>Webhook Configuration
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Important!</h6>
                            <p class="mb-2">Configure webhooks in Razorpay to ensure <strong>no payment is ever
                                    missed</strong>, even if the customer's browser closes after payment.</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Your Webhook URL:</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" value="<?php echo $webhook_url; ?>"
                                    id="webhookUrl" readonly>
                                <button class="btn btn-outline-primary" onclick="copyWebhookUrl()">
                                    <i class="fas fa-copy me-1"></i>Copy
                                </button>
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fas fa-list-ol me-2"></i>Setup Instructions:</h6>
                                <ol class="mb-0">
                                    <li class="mb-2">Login to <a href="https://dashboard.razorpay.com" target="_blank"
                                            class="text-primary">Razorpay Dashboard</a></li>
                                    <li class="mb-2">Go to <strong>Settings → Webhooks → Add New Webhook</strong></li>
                                    <li class="mb-2">Paste the webhook URL shown above</li>
                                    <li class="mb-2">Select these events:
                                        <ul>
                                            <li><code>payment.captured</code> - When payment is successful</li>
                                            <li><code>payment.failed</code> - When payment fails</li>
                                            <li><code>order.paid</code> - When order is paid</li>
                                            <li><code>refund.created</code> - When refund is initiated</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">Copy the <strong>Webhook Secret</strong> and paste it above</li>
                                    <li>Click <strong>Create Webhook</strong></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Currency Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <i class="fas fa-rupee-sign me-2"></i>Currency Settings
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="display-4 text-primary me-3">₹</div>
                            <div>
                                <h6 class="mb-0">Indian Rupee (INR)</h6>
                                <small class="text-muted">Default Currency</small>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            Currency is configured in the system settings. Contact developer to change currency.
                        </small>
                    </div>
                </div>

                <!-- Quick Help -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <i class="fas fa-question-circle me-2"></i>Quick Help
                    </div>
                    <div class="card-body">
                        <h6>How to get Razorpay Keys?</h6>
                        <ol class="small">
                            <li>Sign up at <a href="https://razorpay.com" target="_blank">razorpay.com</a></li>
                            <li>Complete KYC verification</li>
                            <li>Go to <strong>Settings → API Keys</strong></li>
                            <li>Generate Test/Live keys</li>
                        </ol>

                        <hr>

                        <h6>Test Card Details:</h6>
                        <table class="table table-sm small mb-0">
                            <tr>
                                <td>Card Number:</td>
                                <td><code>4111 1111 1111 1111</code></td>
                            </tr>
                            <tr>
                                <td>Expiry:</td>
                                <td>Any future date</td>
                            </tr>
                            <tr>
                                <td>CVV:</td>
                                <td>Any 3 digits</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Status -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <i class="fas fa-info-circle me-2"></i>Integration Status
                    </div>
                    <div class="card-body">
                        <?php
                        $is_configured = !empty($current_key_id) && strpos($current_key_id, 'YOUR_') === false;
                        $has_webhook = !empty($current_webhook_secret) && $current_webhook_secret !== 'YOUR_WEBHOOK_SECRET';
                        ?>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <?php if ($is_configured): ?>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                <?php endif; ?>
                                API Keys Configured
                            </li>
                            <li class="mb-2">
                                <?php if ($has_webhook): ?>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                <?php endif; ?>
                                Webhook Secret Set
                            </li>
                            <li>
                                <span
                                    class="badge <?php echo $current_env === 'live' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    <?php echo $current_env === 'live' ? 'Live Mode Active' : 'Test Mode Active'; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-2"></i>Recent Transactions</span>
                <?php if (!empty($transactions)): ?>
                    <span class="badge bg-primary"><?php echo count($transactions); ?> records</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No transactions recorded yet.</p>
                        <small class="text-muted d-block">
                            Transactions will appear here after the first payment is processed.<br>
                            Make sure to run the <code>database/payment_transactions.sql</code> script to create the
                            transactions table.
                        </small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $txn): ?>
                                    <tr>
                                        <td><?php echo date('d M Y H:i', strtotime($txn['created_at'])); ?></td>
                                        <td>
                                            <a href="order-detail.php?id=<?php echo $txn['order_id']; ?>">
                                                #<?php echo htmlspecialchars($txn['order_number'] ?? $txn['order_id']); ?>
                                            </a>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($txn['transaction_id']); ?></small></td>
                                        <td><?php echo format_price($txn['amount']); ?></td>
                                        <td><?php echo ucfirst($txn['payment_method'] ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $status_class = [
                                                'paid' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'failed' => 'bg-danger',
                                                'refunded' => 'bg-info'
                                            ];
                                            ?>
                                            <span class="badge <?php echo $status_class[$txn['status']] ?? 'bg-secondary'; ?>">
                                                <?php echo ucfirst($txn['status']); ?>
                                            </span>
                                        </td>
                                        <td><small
                                                class="text-muted"><?php echo htmlspecialchars(truncate_text($txn['notes'] ?? '', 30)); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(id) {
        var input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    function copyWebhookUrl() {
        var webhookUrl = document.getElementById('webhookUrl');
        webhookUrl.select();
        webhookUrl.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(webhookUrl.value);

        // Show feedback
        alert('Webhook URL copied to clipboard!');
    }
</script>

<?php include 'includes/footer.php'; ?>