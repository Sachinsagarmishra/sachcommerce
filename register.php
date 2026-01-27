<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// If already logged in, redirect
if (is_logged_in()) {
    header('Location: ' . SITE_URL . '/my-account.php');
    exit;
}

$page_title = 'Register';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            // Create user
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            $verification_token = bin2hex(random_bytes(32));
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, email_verified, verification_token, status, created_at) VALUES (?, ?, ?, ?, 'customer', 0, ?, 'active', NOW())");
            
            if ($stmt->execute([$name, $email, $phone, $password_hash, $verification_token])) {
                $user_id = $pdo->lastInsertId();
                
                // Send verification email (optional)
                // send_email($email, 'Verify Email', 'registration-welcome', ['name' => $name, 'token' => $verification_token]);
                
                // Auto login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                
                $success = 'Registration successful! Welcome to ' . SITE_NAME;
                header('refresh:2;url=' . SITE_URL . '/my-account.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2>Create Account</h2>
                        <p class="text-muted">Join us today!</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $name ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $email ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo $phone ?? ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="text-muted">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="<?php echo SITE_URL; ?>/terms-conditions.php" target="_blank">Terms & Conditions</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="<?php echo SITE_URL; ?>/login.php" class="text-decoration-none fw-bold">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
