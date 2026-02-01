<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Verify Email';

$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?");
        if ($stmt->execute([$user['id']])) {
            $success = true;
        }
    } else {
        $error = true;
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <?php if (isset($success)): ?>
            <i class="lni lni-checkmark-circle text-success fa-4x mb-4"></i>
            <h2>Email Verified!</h2>
            <p class="lead mb-4">Your email has been successfully verified.</p>
            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary">Login to Your Account</a>
            <?php elseif (isset($error)): ?>
            <i class="fas fa-times-circle text-danger fa-4x mb-4"></i>
            <h2>Verification Failed</h2>
            <p class="lead mb-4">Invalid or expired verification link.</p>
            <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary">Register Again</a>
            <?php else: ?>
            <i class="lni lni-envelope fa-4x text-muted mb-4"></i>
            <h2>Invalid Link</h2>
            <p class="lead mb-4">Please use the link sent to your email.</p>
            <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">Go to Homepage</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
