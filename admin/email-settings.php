<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Email Settings';
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // SMTP Settings
        update_site_setting('smtp_host', $_POST['smtp_host'] ?? '', 'text', 'email');
        update_site_setting('smtp_port', $_POST['smtp_port'] ?? '587', 'text', 'email');
        update_site_setting('smtp_username', $_POST['smtp_username'] ?? '', 'text', 'email');
        
        // Only update password if provided
        if (!empty($_POST['smtp_password'])) {
            update_site_setting('smtp_password', $_POST['smtp_password'], 'password', 'email');
        }
        
        update_site_setting('smtp_encryption', $_POST['smtp_encryption'] ?? 'tls', 'text', 'email');
        update_site_setting('smtp_from_email', $_POST['smtp_from_email'] ?? '', 'text', 'email');
        update_site_setting('smtp_from_name', $_POST['smtp_from_name'] ?? '', 'text', 'email');
        
        // Email preferences
        update_site_setting('email_order_confirmation', isset($_POST['email_order_confirmation']) ? '1' : '0', 'boolean', 'email');
        
        $success = 'Email settings saved successfully!';
        
        // Test email if requested
        if (isset($_POST['send_test'])) {
            $test_result = send_order_email_test($_POST['test_email'] ?? '');
            if ($test_result['success']) {
                $success .= ' Test email sent successfully!';
            } else {
                $error = 'Test email failed: ' . $test_result['message'];
            }
        }
    } catch (Exception $e) {
        $error = 'Error saving settings: ' . $e->getMessage();
    }
}

// Get current settings
$settings = [
    'smtp_host' => get_site_setting('smtp_host', 'smtp.gmail.com'),
    'smtp_port' => get_site_setting('smtp_port', '587'),
    'smtp_username' => get_site_setting('smtp_username', ''),
    'smtp_password' => get_site_setting('smtp_password', ''),
    'smtp_encryption' => get_site_setting('smtp_encryption', 'tls'),
    'smtp_from_email' => get_site_setting('smtp_from_email', ''),
    'smtp_from_name' => get_site_setting('smtp_from_name', get_site_setting('site_name', 'TrendsOne')),
    'email_order_confirmation' => get_site_setting('email_order_confirmation', '1'),
];

// Test email function
function send_order_email_test($to) {
    if (empty($to)) {
        return ['success' => false, 'message' => 'No test email provided'];
    }
    
    // Get SMTP settings from database
    $smtp_host = get_site_setting('smtp_host', 'smtp.gmail.com');
    $smtp_port = get_site_setting('smtp_port', '587');
    $smtp_username = get_site_setting('smtp_username', '');
    $smtp_password = get_site_setting('smtp_password', '');
    $smtp_encryption = get_site_setting('smtp_encryption', 'tls');
    $smtp_from_email = get_site_setting('smtp_from_email', $smtp_username);
    $smtp_from_name = get_site_setting('smtp_from_name', 'TrendsOne');
    
    if (empty($smtp_username) || empty($smtp_password)) {
        return ['success' => false, 'message' => 'SMTP credentials not configured'];
    }
    
    // Check if PHPMailer exists
    $phpmailer_path = ROOT_PATH . '/vendor/phpmailer/PHPMailer.php';
    if (!file_exists($phpmailer_path)) {
        return ['success' => false, 'message' => 'PHPMailer not installed. Please upload PHPMailer to /vendor/phpmailer/'];
    }
    
    require_once $phpmailer_path;
    require_once ROOT_PATH . '/vendor/phpmailer/SMTP.php';
    require_once ROOT_PATH . '/vendor/phpmailer/Exception.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_encryption;
        $mail->Port = (int)$smtp_port;
        
        $mail->setFrom($smtp_from_email, $smtp_from_name);
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from ' . $smtp_from_name;
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #333;">Email Configuration Test</h2>
                <p>This is a test email to verify your SMTP settings are working correctly.</p>
                <p><strong>SMTP Host:</strong> ' . htmlspecialchars($smtp_host) . '</p>
                <p><strong>SMTP Port:</strong> ' . htmlspecialchars($smtp_port) . '</p>
                <p><strong>From:</strong> ' . htmlspecialchars($smtp_from_name) . ' &lt;' . htmlspecialchars($smtp_from_email) . '&gt;</p>
                <hr>
                <p style="color: #28a745;"><strong>✓ Your email settings are configured correctly!</strong></p>
            </div>
        ';
        
        $mail->send();
        return ['success' => true, 'message' => 'Test email sent'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
.settings-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-radius: 12px;
}
.settings-card .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    border-radius: 12px 12px 0 0 !important;
}
.form-label {
    font-weight: 500;
}
.password-toggle {
    cursor: pointer;
}
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">Email Settings</h1>
                <p class="text-muted mb-0">Configure SMTP for sending order confirmation emails</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <!-- SMTP Configuration -->
                    <div class="card settings-card mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="fas fa-server me-2 text-primary"></i>SMTP Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="smtp_host" 
                                           value="<?php echo htmlspecialchars($settings['smtp_host']); ?>"
                                           placeholder="smtp.gmail.com" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="smtp_port" 
                                           value="<?php echo htmlspecialchars($settings['smtp_port']); ?>"
                                           placeholder="587" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="smtp_username" 
                                           value="<?php echo htmlspecialchars($settings['smtp_username']); ?>"
                                           placeholder="your-email@gmail.com" required>
                                    <small class="text-muted">Usually your email address</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SMTP Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="smtp_password" id="smtp_password"
                                               placeholder="<?php echo $settings['smtp_password'] ? '••••••••' : 'App password'; ?>">
                                        <button class="btn btn-outline-secondary password-toggle" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">For Gmail, use App Password (not your regular password)</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Encryption</label>
                                <select class="form-select" name="smtp_encryption">
                                    <option value="tls" <?php echo $settings['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS (Recommended)</option>
                                    <option value="ssl" <?php echo $settings['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                </select>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">From Email</label>
                                    <input type="email" class="form-control" name="smtp_from_email" 
                                           value="<?php echo htmlspecialchars($settings['smtp_from_email']); ?>"
                                           placeholder="noreply@yourstore.com">
                                    <small class="text-muted">Leave empty to use SMTP username</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">From Name</label>
                                    <input type="text" class="form-control" name="smtp_from_name" 
                                           value="<?php echo htmlspecialchars($settings['smtp_from_name']); ?>"
                                           placeholder="TrendsOne">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Preferences -->
                    <div class="card settings-card mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i>Email Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="email_order_confirmation" 
                                       id="email_order_confirmation" <?php echo $settings['email_order_confirmation'] === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_order_confirmation">
                                    <strong>Order Confirmation Email</strong><br>
                                    <small class="text-muted">Send email to customer when order is placed successfully</small>
                                </label>
                            </div>
                            
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Only order confirmation emails will be sent to customers. No other automated emails (registration, password reset, etc.) are enabled.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Save Button -->
                    <div class="card settings-card mb-4">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>

                    <!-- Test Email -->
                    <div class="card settings-card mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="fas fa-paper-plane me-2 text-success"></i>Test Email</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Send Test Email To</label>
                                <input type="email" class="form-control" name="test_email" 
                                       placeholder="your-email@gmail.com">
                            </div>
                            <button type="submit" name="send_test" value="1" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Test Email
                            </button>
                        </div>
                    </div>

                    <!-- Gmail Setup Guide -->
                    <div class="card settings-card">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="fab fa-google me-2 text-danger"></i>Gmail Setup</h5>
                        </div>
                        <div class="card-body">
                            <ol class="ps-3 mb-0">
                                <li class="mb-2">Enable 2-Factor Authentication on your Gmail account</li>
                                <li class="mb-2">Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">Google App Passwords</a></li>
                                <li class="mb-2">Generate a new App Password for "Mail"</li>
                                <li class="mb-2">Use that 16-character password here</li>
                            </ol>
                            <hr>
                            <p class="mb-1"><strong>SMTP Host:</strong> smtp.gmail.com</p>
                            <p class="mb-1"><strong>SMTP Port:</strong> 587</p>
                            <p class="mb-0"><strong>Encryption:</strong> TLS</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('smtp_password');
    const icon = document.getElementById('toggleIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
