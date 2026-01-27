<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$page_title = 'Privacy Policy';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Privacy Policy</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Privacy Policy</h1>
            <p class="text-muted">Last updated: <?php echo date('F d, Y'); ?></p>
            
            <h3 class="mt-4">1. Information We Collect</h3>
            <p>We collect information you provide directly to us, including name, email address, phone number, shipping address, and payment information.</p>
            
            <h3 class="mt-4">2. How We Use Your Information</h3>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Process and fulfill your orders</li>
                <li>Send you order confirmations and updates</li>
                <li>Respond to your comments and questions</li>
                <li>Send you marketing communications (with your consent)</li>
                <li>Improve our website and services</li>
            </ul>
            
            <h3 class="mt-4">3. Information Sharing</h3>
            <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with:</p>
            <ul>
                <li>Service providers who assist in our operations</li>
                <li>Payment processors for transaction processing</li>
                <li>Shipping companies for order delivery</li>
            </ul>
            
            <h3 class="mt-4">4. Data Security</h3>
            <p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.</p>
            
            <h3 class="mt-4">5. Cookies</h3>
            <p>We use cookies to enhance your browsing experience, analyze site traffic, and personalize content.</p>
            
            <h3 class="mt-4">6. Your Rights</h3>
            <p>You have the right to access, update, or delete your personal information. Contact us at <?php echo SITE_EMAIL; ?> for any privacy-related requests.</p>
            
            <h3 class="mt-4">7. Contact Us</h3>
            <p>If you have questions about this Privacy Policy, please contact us at:</p>
            <p>Email: <?php echo SITE_EMAIL; ?><br>Phone: <?php echo SITE_PHONE; ?></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
