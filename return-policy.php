<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$page_title = 'Return Policy';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Return Policy</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Return Policy</h1>
            <p class="lead">We want you to be completely satisfied with your purchase.</p>
            
            <h3 class="mt-4">7-Day Return Policy</h3>
            <p>You have 7 days from the date of delivery to return most items for a full refund or exchange.</p>
            
            <h3 class="mt-4">Eligibility</h3>
            <p>To be eligible for a return, items must be:</p>
            <ul>
                <li>Unused and in the same condition as received</li>
                <li>In original packaging with all tags attached</li>
                <li>Accompanied by proof of purchase</li>
            </ul>
            
            <h3 class="mt-4">Non-Returnable Items</h3>
            <p>The following items cannot be returned:</p>
            <ul>
                <li>Perishable goods</li>
                <li>Intimate or sanitary products</li>
                <li>Items marked as final sale</li>
                <li>Customized or personalized items</li>
            </ul>
            
            <h3 class="mt-4">How to Return</h3>
            <ol>
                <li>Login to your account and go to "My Orders"</li>
                <li>Select the order and click "Return Item"</li>
                <li>Choose return reason and submit request</li>
                <li>Pack the item securely in original packaging</li>
                <li>Our courier will pick up the item from your address</li>
            </ol>
            
            <h3 class="mt-4">Refund Process</h3>
            <p>Once we receive and inspect your return:</p>
            <ul>
                <li>Approved returns will be refunded within 5-7 business days</li>
                <li>Refunds will be issued to the original payment method</li>
                <li>Shipping charges are non-refundable</li>
            </ul>
            
            <h3 class="mt-4">Exchanges</h3>
            <p>We offer exchanges for defective or damaged items. Contact us at <?php echo SITE_EMAIL; ?> for exchange requests.</p>
            
            <h3 class="mt-4">Contact Us</h3>
            <p>For return-related queries:</p>
            <p>Email: <?php echo SITE_EMAIL; ?><br>Phone: <?php echo SITE_PHONE; ?></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
