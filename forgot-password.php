<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Forgot Password';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);

    if (empty($email)) {
        $error = 'Please enter your email address';
    } else {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ? AND role = 'customer'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Check if column exists (Safe approach in case script hasn't run)
            try {
                $stmt = $pdo->prepare("UPDATE users SET forgot_otp = ?, forgot_otp_expiry = ? WHERE id = ?");
                $stmt->execute([$otp, $expiry, $user['id']]);
            } catch (PDOException $e) {
                // If column missing, try adding it on the fly
                $pdo->exec("ALTER TABLE users ADD COLUMN forgot_otp VARCHAR(10) DEFAULT NULL, ADD COLUMN forgot_otp_expiry DATETIME DEFAULT NULL");
                $stmt = $pdo->prepare("UPDATE users SET forgot_otp = ?, forgot_otp_expiry = ? WHERE id = ?");
                $stmt->execute([$otp, $expiry, $user['id']]);
            }

            // Send OTP Email
            $site_name = get_site_setting('site_name', 'TrendsOne');
            $subject = "Password Reset OTP - " . $site_name;

            $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <h2 style='color: #333; text-align: center;'>Password Reset Request</h2>
                <p>Dear <strong>{$user['name']}</strong>,</p>
                <p>We received a request to reset your password. Use the following 6-digit OTP to proceed:</p>
                
                <div style='background: #f8f9fa; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                    <span style='font-size: 36px; font-weight: bold; letter-spacing: 5px; color: #83b735;'>{$otp}</span>
                </div>
                
                <p style='color: #666;'>This OTP is valid for **15 minutes**. If you did not request this reset, please ignore this email.</p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='text-align: center; color: #999; font-size: 12px;'>&copy; " . date('Y') . " {$site_name}. All rights reserved.</p>
            </div>
            ";

            $email_result = send_email($email, $subject, '', ['body' => $message]);

            if ($email_result['success']) {
                $_SESSION['reset_email'] = $email;
                header('Location: verify-otp.php');
                exit;
            } else {
                $error = 'Failed to send OTP email. Please check SMTP settings.';
            }
        } else {
            $error = 'Email address not found in our records.';
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h2>Forgot Password?</h2>
                        <p class="text-muted">Enter your email to reset your password</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>

                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/login.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>