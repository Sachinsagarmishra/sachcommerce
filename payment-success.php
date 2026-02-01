<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Order Confirmed';

// Get order number
$order_number = $_GET['order'] ?? '';

if (!$order_number) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.order_number = ?");
$stmt->execute([$order_number]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . SITE_URL);
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.slug 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-checkmark-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h1 class="mb-3">Order Confirmed!</h1>
                <p class="lead text-muted">Thank you for your order. We'll send you a confirmation email shortly.</p>
            </div>
            
            <!-- Order Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white text-success">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Date</h6>
                            <p><?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Method</h6>
                            <p><?php echo strtoupper($order['payment_method']); ?></p>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h6 class="mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $item['slug']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo format_price($item['price']); ?></td>
                                    <td><?php echo format_price($item['subtotal']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><strong><?php echo format_price($order['subtotal']); ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                    <td><strong><?php echo $order['shipping_charge'] > 0 ? format_price($order['shipping_charge']) : '<span class="text-success">FREE</span>'; ?></strong></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><h5 class="mb-0">Total:</h5></td>
                                    <td><h5 class="mb-0 text-primary"><?php echo format_price($order['total_amount']); ?></h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- What's Next -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">What's Next?</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <i class="fas fa-package fa-3x text-primary mb-3"></i>
                            <h6>Order Processing</h6>
                            <p class="text-muted small">We're preparing your order for shipment</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                            <h6>Shipping</h6>
                            <p class="text-muted small">Your order will be shipped within 2-3 business days</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-home fa-3x text-primary mb-3"></i>
                            <h6>Delivery</h6>
                            <p class="text-muted small">Expected delivery in 5-7 business days</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="text-center">
                <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-list me-2"></i>View All Orders
                </a>
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
                </a>
            </div>
            
            <!-- Support -->
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Need Help?</strong> Contact us at <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a> or call <?php echo SITE_PHONE; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
