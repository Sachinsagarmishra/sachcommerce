<?php
/**
 * Admin Login Page
 */
require_once '../config/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id']) && is_admin()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Log activity
                log_activity($user['id'], 'admin_login', 'Admin logged in');
                
                // Remember me
                if ($remember) {
                    setcookie('admin_remember', $user['id'], time() + (86400 * 30), '/');
                }
                
                redirect(SITE_URL . '/admin/dashboard.php');
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again.';
        }
    }
}

// Check for error messages
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'login_required') {
        $error = 'Please login to access admin panel';
    } elseif ($_GET['error'] === 'unauthorized') {
        $error = 'Unauthorized access. Admin privileges required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/admin/assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card login-card shadow-lg">
                        <div class="card-body p-5">
                            <div class="login-header">
                                <h2><i class="fas fa-lock"></i> Admin Login</h2>
                                <p class="text-muted">Enter your credentials to access admin panel</p>
                            </div>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="admin@example.com" required autofocus
                                               value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Enter your password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-enter"></i> Login
                                </button>
                            </form>
                            
                            <div class="text-center mt-4">
                                <a href="<?php echo SITE_URL; ?>" class="text-muted">
                                    <i class="fas fa-arrow-left"></i> Back to Website
                                </a>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <small class="text-muted">
                                    <strong>Default Credentials:</strong><br>
                                    Email: admin@trendsone.com<br>
                                    Password: admin123
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
