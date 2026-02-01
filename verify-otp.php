<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Verify OTP';

if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = sanitize_input($_POST['otp']);

    if (empty($otp)) {
        $error = 'Please enter the 6-digit OTP';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND forgot_otp = ? AND forgot_otp_expiry > NOW()");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['otp_verified'] = true;
            $_SESSION['reset_user_id'] = $user['id'];
            header('Location: reset-password.php');
            exit;
        } else {
            $error = 'Invalid or expired OTP. Please try again.';
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h2 class="fw-bold">Verify OTP</h2>
                        <p class="text-muted">We've sent a 6-digit code to <br><strong>
                                <?php echo htmlspecialchars($email); ?>
                            </strong></p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Enter OTP</label>
                            <input type="text" class="form-control form-control-lg text-center letter-spacing-lg"
                                name="otp" maxlength="6" pattern="\d{6}" placeholder="······" required autofocus
                                style="font-size: 2rem; letter-spacing: 0.5rem; border-radius: 10px;">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" style="border-radius: 10px;">
                            Verify OTP
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-0">Didn't receive the code?</p>
                            <a href="forgot-password.php" class="text-decoration-none fw-bold text-primary">Resend
                                OTP</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-lg {
        letter-spacing: 0.5rem;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<?php include 'includes/footer.php'; ?>