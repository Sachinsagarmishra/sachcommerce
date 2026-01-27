<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$order_number = $_GET['order'] ?? '';

if (!$order_number) {
    header('Location: ' . SITE_URL . '/orders.php');
    exit;
}

// Get order details
// FIX: Removed incorrect LEFT JOIN to user_addresses table. 
// Now selecting directly from 'orders' table.
$stmt = $pdo->prepare("SELECT o.* FROM orders o 
                       WHERE o.order_number = ? AND o.user_id = ?");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    // This could happen if the order number is invalid or doesn't belong to the logged-in user.
    $_SESSION['error'] = "Order not found or access denied.";
    header('Location: ' . SITE_URL . '/orders.php');
    exit;
}

// Get order items
// FIX: order_items table uses product_id, but the display data comes from the products table.
// Ensure we handle products that might have been deleted (using LEFT JOIN on products p).
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.slug, 
                       (SELECT primary_image FROM products WHERE id = oi.product_id) as image_from_products 
                       FROM order_items oi 
                       LEFT JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");

$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();

$page_title = 'Order #' . $order_number;

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/my-account.php">My Account</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/orders.php">Orders</a></li>
                <li class="breadcrumb-item active">Order #<?php echo htmlspecialchars($order_number); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Order #<?php echo htmlspecialchars($order_number); ?></h5>
                            <small class="text-muted">Placed on <?php echo date('F d, Y', strtotime($order['created_at'])); ?></small>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-<?php echo $order['order_status'] === 'delivered' ? 'success' : ($order['order_status'] === 'cancelled' ? 'danger' : 'warning'); ?> fs-6">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Items -->
                    <?php foreach ($order_items as $item): ?>
                    <div class="d-flex mb-3 pb-3 border-bottom">
                        <?php 
                        // Use product_image column from order_items if available (for historical accuracy), 
                        // otherwise fallback to primary_image from products table.
                        $image_path = $item['product_image'] ?: $item['image_from_products'];
                        ?>
                        <img src="<?php echo $image_path ? PRODUCT_IMAGE_URL . $image_path : 'https://via.placeholder.com/80?text=No+Img'; ?>" 
                             class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $item['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($item['product_name']); ?> <!-- Use product_name from order_items for accuracy -->
                                </a>
                            </h6>
                            <p class="text-muted mb-0">Quantity: <?php echo $item['quantity']; ?> × <?php echo format_price($item['price']); ?></p>
                        </div>
                        <div class="text-end">
                            <strong><?php echo format_price($item['subtotal']); ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Shipping Address (Using fields directly from orders table) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                    <p class="mb-1"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    <p class="mb-1">
                        <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                        <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                        <?php echo htmlspecialchars($order['shipping_pincode']); ?>
                    </p>
                    <p class="mb-0">Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <?php if (!empty($order['order_notes'])): ?>
                    <div class="mt-3">
                        <small class="text-muted d-block">Order Notes:</small>
                        <p class="mb-0 fst-italic"><?php echo nl2br(htmlspecialchars($order['order_notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong><?php echo format_price($order['subtotal']); ?></strong>
                    </div>
                    
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Discount (<?php echo htmlspecialchars($order['coupon_code']); ?>):</span>
                        <strong>- <?php echo format_price($order['discount_amount']); ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <strong><?php echo $order['shipping_charge'] > 0 ? format_price($order['shipping_charge']) : '<span class="text-success">FREE</span>'; ?></strong>
                    </div>
                    
                    <?php if ($order['tax_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <strong><?php echo format_price($order['tax_amount']); ?></strong>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5 class="text-primary"><?php echo format_price($order['total_amount']); ?></h5>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Payment Method</small>
                        <p class="mb-0"><strong><?php echo strtoupper($order['payment_method']); ?></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Payment Status</small>
                        <p class="mb-0">
                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="d-grid gap-2">
                <?php if ($order['order_status'] === 'pending'): ?>
                <!-- NOTE: Since we cannot implement the API here, I'll use a placeholder action -->
                <button class="btn btn-danger" onclick="alert('Order cancellation is typically handled via an API endpoint. This action is disabled.')">
                    Cancel Order
                </button>
                <?php endif; ?>
                <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-outline-secondary">
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>