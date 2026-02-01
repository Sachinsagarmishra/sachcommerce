<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$page_title = 'My Orders';

// Get all orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/my-account">My Account</a></li>
                <li class="breadcrumb-item active">My Orders</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>

    <?php if (!empty($orders)): ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <small class="text-muted">Order Number</small>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($order['order_number']); ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Date</small>
                                    <h6 class="mb-0"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Total</small>
                                    <h6 class="mb-0"><?php echo format_price($order['total_amount']); ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Status</small>
                                    <h6 class="mb-0">
                                        <span
                                            class="badge bg-<?php echo $order['order_status'] === 'delivered' ? 'success' : ($order['order_status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0">
                                        <strong>Payment:</strong> <?php echo strtoupper($order['payment_method']); ?>
                                        <span
                                            class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?> ms-2">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <a href="<?php echo SITE_URL; ?>/order-detail?order=<?php echo $order['order_number']; ?>"
                                        class="btn btn-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-basket fa-4x text-muted mb-4"></i>
            <h4>No orders yet</h4>
            <p class="text-muted mb-4">Start shopping to see your orders here!</p>
            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-basket me-2"></i>Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>