<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$page_title = 'Shipping Policy';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Shipping Policy</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Shipping Policy</h1>
            <p class="lead">Fast and reliable shipping across India.</p>
            
            <h3 class="mt-4">Shipping Methods</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Delivery Time</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Standard Shipping</td>
                            <td>5-7 business days</td>
                            <td><?php echo format_price(SHIPPING_COST); ?></td>
                        </tr>
                        <tr>
                            <td>Express Shipping</td>
                            <td>2-3 business days</td>
                            <td><?php echo format_price(SHIPPING_COST * 2); ?></td>
                        </tr>
                        <tr>
                            <td>Free Shipping</td>
                            <td>5-7 business days</td>
                            <td>FREE on orders above <?php echo format_price(FREE_SHIPPING_THRESHOLD); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h3 class="mt-4">Processing Time</h3>
            <p>Orders are processed within 1-2 business days. Orders placed on weekends or holidays will be processed the next business day.</p>
            
            <h3 class="mt-4">Tracking</h3>
            <p>Once your order is shipped, you will receive a tracking number via email and SMS. You can track your order in the "My Orders" section of your account.</p>
            
            <h3 class="mt-4">Shipping Locations</h3>
            <p>We currently ship to all locations within India. International shipping is not available at this time.</p>
            
            <h3 class="mt-4">Delivery Issues</h3>
            <p>If you experience any issues with delivery:</p>
            <ul>
                <li>Check the tracking information for updates</li>
                <li>Contact the courier company directly</li>
                <li>Reach out to our customer support at <?php echo SITE_EMAIL; ?></li>
            </ul>
            
            <h3 class="mt-4">Failed Delivery</h3>
            <p>If delivery fails due to incorrect address or unavailability:</p>
            <ul>
                <li>The courier will attempt delivery 2-3 times</li>
                <li>You will be contacted for redelivery</li>
                <li>Additional charges may apply for redelivery</li>
            </ul>
            
            <h3 class="mt-4">Contact Us</h3>
            <p>For shipping-related queries:</p>
            <p>Email: <?php echo SITE_EMAIL; ?><br>Phone: <?php echo SITE_PHONE; ?></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
