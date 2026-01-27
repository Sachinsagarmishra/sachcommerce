<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$page_title = 'Terms & Conditions';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Terms & Conditions</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Terms & Conditions</h1>
            <p class="text-muted">Last updated: <?php echo date('F d, Y'); ?></p>
            
            <h3 class="mt-4">1. Acceptance of Terms</h3>
            <p>By accessing and using <?php echo SITE_NAME; ?>, you accept and agree to be bound by these Terms and Conditions.</p>
            
            <h3 class="mt-4">2. Use of Website</h3>
            <p>You agree to use this website only for lawful purposes and in a way that does not infringe the rights of others.</p>
            
            <h3 class="mt-4">3. Product Information</h3>
            <p>We strive to provide accurate product information. However, we do not warrant that product descriptions or other content is accurate, complete, or error-free.</p>
            
            <h3 class="mt-4">4. Pricing</h3>
            <p>All prices are in Indian Rupees (₹) and are subject to change without notice. We reserve the right to modify or discontinue products at any time.</p>
            
            <h3 class="mt-4">5. Orders and Payment</h3>
            <p>By placing an order, you are making an offer to purchase products. We reserve the right to refuse or cancel any order for any reason.</p>
            
            <h3 class="mt-4">6. Shipping and Delivery</h3>
            <p>Delivery times are estimates and not guaranteed. We are not responsible for delays caused by shipping carriers or unforeseen circumstances.</p>
            
            <h3 class="mt-4">7. Returns and Refunds</h3>
            <p>Please refer to our Return Policy for information about returns and refunds.</p>
            
            <h3 class="mt-4">8. Intellectual Property</h3>
            <p>All content on this website, including text, graphics, logos, and images, is the property of <?php echo SITE_NAME; ?> and protected by copyright laws.</p>
            
            <h3 class="mt-4">9. Limitation of Liability</h3>
            <p>We shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our website or products.</p>
            
            <h3 class="mt-4">10. Contact Information</h3>
            <p>For questions about these Terms & Conditions, contact us at:</p>
            <p>Email: <?php echo SITE_EMAIL; ?><br>Phone: <?php echo SITE_PHONE; ?></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
