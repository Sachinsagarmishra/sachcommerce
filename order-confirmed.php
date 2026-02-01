<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Order Confirmed';

// Get order number from URL or session
$order_number = $_GET['order'] ?? $_SESSION['last_order_number'] ?? '';

if (!$order_number) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get order details - Modified to work without requiring login
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                       FROM orders o 
                       LEFT JOIN users u ON o.user_id = u.id 
                       WHERE o.order_number = ?");
$stmt->execute([$order_number]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.slug, p.primary_image 
                       FROM order_items oi 
                       LEFT JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();

// Check if guest account was created
$guest_password = $_SESSION['guest_password'] ?? null;
$guest_email = $_SESSION['guest_email'] ?? null;

// Clear session data after displaying
unset($_SESSION['guest_password'], $_SESSION['guest_email'], $_SESSION['last_order_number'], $_SESSION['last_order_id']);

// Estimated delivery dates
$order_date = strtotime($order['created_at']);
$estimated_delivery_start = date('M d', $order_date + (3 * 24 * 60 * 60));
$estimated_delivery_end = date('M d, Y', $order_date + (7 * 24 * 60 * 60));

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.order-confirmed-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 40px 0 60px;
}

.success-animation {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.checkmark-circle {
    width: 120px;
    height: 120px;
    position: relative;
    display: inline-block;
    vertical-align: top;
    margin-left: auto;
    margin-right: auto;
}

.checkmark-circle .background {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #83b735 0%, #5a9c2e 100%);
    position: absolute;
    animation: scaleUp 0.5s ease-out forwards;
}

.checkmark-circle .checkmark {
    border-radius: 5px;
    position: absolute;
    width: 30px;
    height: 60px;
    border-right: 8px solid #fff;
    border-top: 8px solid #fff;
    transform: scaleX(-1) rotate(135deg);
    left: 35px;
    top: 28px;
    animation: drawCheckmark 0.5s ease-out 0.3s forwards;
    opacity: 0;
}

@keyframes scaleUp {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

@keyframes drawCheckmark {
    0% { opacity: 0; height: 0; width: 0; }
    100% { opacity: 1; height: 60px; width: 30px; }
}

.order-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 24px;
}

.order-header {
    background: linear-gradient(135deg, #83b735 0%, #5a9c2e 100%);
    color: white;
    padding: 24px;
    text-align: center;
}

.order-header h1 {
    font-size: 28px;
    margin: 0 0 10px;
    font-weight: 700;
}

.order-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.order-number-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 18px;
    font-weight: 600;
    margin-top: 15px;
}

.order-body {
    padding: 30px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
}

.info-item-label {
    font-size: 13px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.info-item-value {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.order-items-section {
    margin-top: 30px;
}

.order-items-section h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.order-item-row {
    display: flex;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #eee;
}

.order-item-row:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 16px;
    background: #f8f9fa;
}

.order-item-details {
    flex: 1;
}

.order-item-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.order-item-meta {
    font-size: 14px;
    color: #6c757d;
}

.order-item-price {
    text-align: right;
}

.order-item-price .price {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.order-item-price .qty {
    font-size: 13px;
    color: #6c757d;
}

.order-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
}

.order-summary-row.total {
    border-top: 2px solid #eee;
    margin-top: 15px;
    padding-top: 20px;
}

.order-summary-row.total span {
    font-size: 20px;
    font-weight: 700;
}

.order-summary-row.total .amount {
    color: var(--primary-color);
}

.guest-account-alert {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.guest-account-alert h4 {
    margin: 0 0 10px;
    font-weight: 600;
}

.guest-account-alert p {
    margin: 0 0 15px;
    opacity: 0.9;
}

.credentials-box {
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    padding: 15px;
    font-family: monospace;
    font-size: 14px;
}

.credentials-box .label {
    opacity: 0.8;
    margin-right: 10px;
}

.credentials-box .value {
    font-weight: 600;
}

.what-next-section {
    margin-top: 30px;
}

.timeline-step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 24px;
}

.timeline-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #83b735 0%, #5a9c2e 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    margin-right: 16px;
    flex-shrink: 0;
}

.timeline-icon.pending {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-content h5 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 5px;
    color: #2c3e50;
}

.timeline-content p {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.action-buttons .btn {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
}

.shipping-address-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.shipping-address-card h5 {
    font-size: 14px;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 12px;
}

.shipping-address-card p {
    margin: 0;
    line-height: 1.6;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .order-confirmed-section {
        padding: 20px 0 40px;
    }
    
    .order-header h1 {
        font-size: 22px;
    }
    
    .checkmark-circle, .checkmark-circle .background {
        width: 80px;
        height: 80px;
    }
    
    .checkmark-circle .checkmark {
        width: 20px;
        height: 40px;
        left: 25px;
        top: 18px;
        border-right-width: 5px;
        border-top-width: 5px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
}
</style>

<div class="order-confirmed-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <?php if ($guest_password && $guest_email): ?>
                <!-- Guest Account Created Alert -->
                <div class="guest-account-alert">
                    <h4><i class="fas fa-user-check me-2"></i>Account Created Successfully!</h4>
                    <p>We've created an account for you so you can track your order. Your login credentials have been sent to your email.</p>
                    <div class="credentials-box">
                        <div class="mb-2">
                            <span class="label">Email:</span>
                            <span class="value"><?php echo htmlspecialchars($guest_email); ?></span>
                        </div>
                        <div>
                            <span class="label">Temporary Password:</span>
                            <span class="value"><?php echo htmlspecialchars($guest_password); ?></span>
                        </div>
                    </div>
                    <p class="mt-3 mb-0"><small><i class="fas fa-info-circle me-1"></i>Please save these credentials and change your password after first login.</small></p>
                </div>
                <?php endif; ?>
                
                <!-- Main Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <div class="success-animation">
                            <div class="checkmark-circle">
                                <div class="background"></div>
                                <div class="checkmark"></div>
                            </div>
                        </div>
                        <h1>Thank You for Your Order!</h1>
                        <p>Your order has been placed successfully</p>
                        <div class="order-number-badge">
                            <i class="fas fa-receipt me-2"></i>Order #<?php echo htmlspecialchars($order['order_number']); ?>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <!-- Order Info Grid -->
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-item-label">Order Date</div>
                                <div class="info-item-value"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Payment Method</div>
                                <div class="info-item-value">
                                    <?php if ($order['payment_method'] === 'razorpay'): ?>
                                        <i class="fas fa-credit-card me-1"></i>Online Payment
                                    <?php else: ?>
                                        <i class="fas fa-money-bill-wave me-1"></i>Cash on Delivery
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Payment Status</div>
                                <div class="info-item-value">
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>Paid</span>
                                    <?php else: ?>
                                        <span class="text-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Estimated Delivery</div>
                                <div class="info-item-value"><?php echo $estimated_delivery_start; ?> - <?php echo $estimated_delivery_end; ?></div>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <div class="order-items-section">
                            <h3><i class="fas fa-box me-2"></i>Order Items</h3>
                            
                            <?php foreach ($order_items as $item): ?>
                            <div class="order-item-row">
                                <img src="<?php echo $item['primary_image'] ? PRODUCT_IMAGE_URL . $item['primary_image'] : 'https://via.placeholder.com/80'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">
                                        <?php if ($item['slug']): ?>
                                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-item-meta">Qty: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="order-item-price">
                                    <div class="price"><?php echo format_price($item['subtotal']); ?></div>
                                    <div class="qty"><?php echo format_price($item['price']); ?> each</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Order Summary -->
                            <div class="mt-4">
                                <div class="order-summary-row">
                                    <span>Subtotal</span>
                                    <span><?php echo format_price($order['subtotal']); ?></span>
                                </div>
                                <div class="order-summary-row">
                                    <span>Shipping</span>
                                    <span>
                                        <?php if ($order['shipping_charge'] > 0): ?>
                                            <?php echo format_price($order['shipping_charge']); ?>
                                        <?php else: ?>
                                            <span class="text-success">FREE</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if ($order['discount_amount'] > 0): ?>
                                <div class="order-summary-row">
                                    <span>Discount</span>
                                    <span class="text-success">-<?php echo format_price($order['discount_amount']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="order-summary-row total">
                                    <span>Total</span>
                                    <span class="amount"><?php echo format_price($order['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Address -->
                        <div class="shipping-address-card">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h5>
                            <p>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                                <?php echo htmlspecialchars($order['shipping_pincode']); ?><br>
                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($order['customer_email']); ?>
                            </p>
                        </div>
                        
                        <!-- What's Next Section -->
                        <div class="what-next-section">
                            <h3 class="mb-4"><i class="fas fa-road me-2"></i>What's Next?</h3>
                            
                            <div class="timeline-step">
                                <div class="timeline-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5>Order Confirmed</h5>
                                    <p>Your order has been received and is being processed</p>
                                </div>
                            </div>
                            
                            <div class="timeline-step">
                                <div class="timeline-icon pending">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5>Processing</h5>
                                    <p>We're preparing your order for shipment</p>
                                </div>
                            </div>
                            
                            <div class="timeline-step">
                                <div class="timeline-icon pending">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5>Shipped</h5>
                                    <p>Your order will be shipped within 2-3 business days</p>
                                </div>
                            </div>
                            
                            <div class="timeline-step">
                                <div class="timeline-icon pending">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5>Delivered</h5>
                                    <p>Expected delivery: <?php echo $estimated_delivery_start; ?> - <?php echo $estimated_delivery_end; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <?php if (is_logged_in() || $guest_password): ?>
                            <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>View My Orders
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Support Section -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Need help with your order?</p>
                    <p class="mb-0">
                        <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-decoration-none me-3">
                            <i class="fas fa-envelope me-1"></i><?php echo SITE_EMAIL; ?>
                        </a>
                        <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>" class="text-decoration-none">
                            <i class="fas fa-phone me-1"></i><?php echo SITE_PHONE; ?>
                        </a>
                    </p>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
