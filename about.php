<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'About Us';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="mb-4">About <?php echo SITE_NAME; ?></h1>
            <p class="lead"><?php echo SITE_TAGLINE; ?></p>
            <p>Welcome to <?php echo SITE_NAME; ?>, your number one source for all things fashion, electronics, and lifestyle products. We're dedicated to giving you the very best shopping experience, with a focus on quality, customer service, and uniqueness.</p>
            <p>Founded in 2024, <?php echo SITE_NAME; ?> has come a long way from its beginnings. When we first started out, our passion for providing the best products drove us to start our own business.</p>
        </div>
        <div class="col-lg-6">
            <img src="https://via.placeholder.com/600x400?text=About+Us" class="img-fluid rounded" alt="About Us">
        </div>
    </div>
    
    <div class="row text-center mb-5">
        <div class="col-md-3 mb-4">
            <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
            <h4>Fast Delivery</h4>
            <p class="text-muted">Quick and reliable shipping</p>
        </div>
        <div class="col-md-3 mb-4">
            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
            <h4>Secure Payment</h4>
            <p class="text-muted">100% secure transactions</p>
        </div>
        <div class="col-md-3 mb-4">
            <i class="fas fa-undo fa-3x text-warning mb-3"></i>
            <h4>Easy Returns</h4>
            <p class="text-muted">7-day return policy</p>
        </div>
        <div class="col-md-3 mb-4">
            <i class="fas fa-headset fa-3x text-info mb-3"></i>
            <h4>24/7 Support</h4>
            <p class="text-muted">Always here to help</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Our Mission</h2>
            <p class="text-center lead mb-5">To provide high-quality products at affordable prices while delivering exceptional customer service.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
