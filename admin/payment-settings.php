<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Payment Settings';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Sanitization
    $razorpay_key_id = isset($_POST['razorpay_key_id']) ? sanitize_input($_POST['razorpay_key_id']) : '';
    $razorpay_key_secret = isset($_POST['razorpay_key_secret']) ? sanitize_input($_POST['razorpay_key_secret']) : '';
    $razorpay_env = isset($_POST['razorpay_environment']) ? sanitize_input($_POST['razorpay_environment']) : 'test';
    
    // Update Settings in Database
    $update1 = update_site_setting('razorpay_key_id', $razorpay_key_id, 'text', 'payment');
    $update2 = update_site_setting('razorpay_key_secret', $razorpay_key_secret, 'password', 'payment');
    $update3 = update_site_setting('razorpay_environment', $razorpay_env, 'text', 'payment');
    
    // Update Currency (Optional if you want to save this too, though config has it hardcoded currently)
    // $currency = isset($_POST['currency_symbol']) ? sanitize_input($_POST['currency_symbol']) : '';
    // update_site_setting('currency_symbol', $currency, 'text', 'general');

    if ($update1 && $update2 && $update3) {
        $success = "Payment settings updated successfully!";
        
        // Log the activity
        if (function_exists('log_activity')) {
            log_activity($_SESSION['user_id'] ?? 0, 'update_payment_settings', 'Updated Razorpay settings');
        }
    } else {
        $error = "Failed to update some settings. Please try again.";
    }
}

// Fetch Current Values from DB (or fallback to empty string if not set)
$current_key_id = get_site_setting('razorpay_key_id');
$current_key_secret = get_site_setting('razorpay_key_secret');
$current_env = get_site_setting('razorpay_environment', 'test'); // Default to test if not set

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Payment Settings</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-white font-weight-bold">Currency Settings</div>
            <div class="card-body">
                <div class="alert alert-info">
                    <small>Currency symbol is currently fixed in the configuration file to prevent encoding issues.</small>
                </div>
                <div class="mb-3">
                    <label>Currency Symbol</label>
                    <input type="text" class="form-control" value="&#8377;" disabled readonly>
                    <small class="text-muted">Currently set to Indian Rupee (INR)</small>
                </div>
            </div>
        </div>
        
        <!-- Updated from Stripe to Razorpay -->
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                <span>Razorpay Integration</span>
                <span class="badge <?php echo $current_env === 'live' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo ucfirst($current_env); ?> Mode
                </span>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Environment Mode</label>
                            <select class="form-control" name="razorpay_environment">
                                <option value="test" <?php echo $current_env === 'test' ? 'selected' : ''; ?>>Test / Sandbox</option>
                                <option value="live" <?php echo $current_env === 'live' ? 'selected' : ''; ?>>Live / Production</option>
                            </select>
                            <small class="text-muted">Select 'Live' only when you are ready to accept real payments.</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Razorpay Key ID</label>
                            <input type="text" class="form-control" name="razorpay_key_id" 
                                   value="<?php echo htmlspecialchars($current_key_id); ?>"
                                   placeholder="e.g. rzp_test_..." required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Razorpay Key Secret</label>
                            <input type="password" class="form-control" name="razorpay_key_secret" 
                                   value="<?php echo htmlspecialchars($current_key_secret); ?>"
                                   placeholder="Enter your key secret" required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary px-4">Save Payment Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>