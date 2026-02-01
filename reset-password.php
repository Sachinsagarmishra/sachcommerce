<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Reset Password';

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['reset_user_id'];

// Get user info (optional, for personalization)
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password)) {
        $error = 'Please enter a password';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $stmt = $pdo->prepare("UPDATE users SET password = ?, forgot_otp = NULL, forgot_otp_expiry = NULL WHERE id = ?");

        if ($stmt->execute([$password_hash, $user_id])) {
            // Success - Clear session
            unset($_SESSION['otp_verified'], $_SESSION['reset_user_id'], $_SESSION['reset_email']);

            $success = 'Password reset successful! You can now login.';
            header('refresh:2;url=' . SITE_URL . '/login.php');
        } else {
            $error = 'Failed to reset password. Please try again.';
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
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h2>Reset Password</h2>
                        <p class="text-muted">Enter your new password</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" required autofocus>
                            <small class="text-muted">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>