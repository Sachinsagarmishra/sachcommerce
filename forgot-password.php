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
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->execute([$reset_token, $reset_expiry, $user['id']]);
            
            // Send email (implement send_email function)
            // send_email($email, 'Password Reset', 'password-reset', ['name' => $user['name'], 'token' => $reset_token]);
            
            $success = 'Password reset link has been sent to your email';
        } else {
            $error = 'Email not found';
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
