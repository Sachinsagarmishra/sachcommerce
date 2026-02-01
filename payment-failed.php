<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Payment Failed';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-4">
                <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
            </div>
            <h1 class="mb-3">Payment Failed</h1>
            <p class="lead text-muted mb-4">Unfortunately, your payment could not be processed. Please try again.</p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Common Reasons for Payment Failure:</h5>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Insufficient funds in account</li>
                        <li class="mb-2"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Incorrect card details</li>
                        <li class="mb-2"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Card expired or blocked</li>
                        <li class="mb-2"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Network connection issues</li>
                        <li class="mb-2"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Transaction limit exceeded</li>
                    </ul>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-redo me-2"></i>Try Again
                </a>
                <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-cart me-2"></i>Back to Cart
                </a>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Need Help?</strong> Contact our support team at <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
