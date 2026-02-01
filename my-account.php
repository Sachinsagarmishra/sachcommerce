<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$page_title = 'My Account';

$user = get_logged_user();

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$order_stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_stats = $stmt->fetch();

// Get recent orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_orders = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if ($user['avatar']): ?>
                            <img src="<?php echo AVATAR_IMAGE_URL . $user['avatar']; ?>" class="rounded-circle" width="80"
                                height="80" alt="<?php echo htmlspecialchars($user['name']); ?>">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px; font-size: 32px;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <div class="list-group mt-3">
                <a href="<?php echo SITE_URL; ?>/my-account" class="list-group-item list-group-item-action active">
                    <i class="fas fa-dashboard me-2"></i>Dashboard
                </a>
                <a href="<?php echo SITE_URL; ?>/orders" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-basket me-2"></i>My Orders
                </a>
                <a href="<?php echo SITE_URL; ?>/wishlist" class="list-group-item list-group-item-action">
                    <i class="fas fa-heart me-2"></i>Wishlist
                </a>
                <a href="<?php echo SITE_URL; ?>/addresses" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker me-2"></i>Addresses
                </a>
                <a href="<?php echo SITE_URL; ?>/profile-edit" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </a>
                <a href="<?php echo SITE_URL; ?>/change-password" class="list-group-item list-group-item-action">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
                <a href="<?php echo SITE_URL; ?>/logout" class="list-group-item list-group-item-action text-danger">
                    <i class="fas fa-exit me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <h2 class="mb-4">Dashboard</h2>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-shopping-basket fa-3x text-primary mb-3"></i>
                            <h3 class="mb-0"><?php echo $order_stats['total_orders']; ?></h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                            <h3 class="mb-0"><?php echo $wishlist_stats['wishlist_count']; ?></h3>
                            <p class="text-muted mb-0">Wishlist Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                            <h3 class="mb-0">Active</h3>
                            <p class="text-muted mb-0">Account Status</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo format_price($order['total_amount']); ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo $order['order_status'] === 'delivered' ? 'success' : ($order['order_status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                                                    <?php echo ucfirst($order['order_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/order-detail?order=<?php echo $order['order_number']; ?>"
                                                    class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No orders yet</p>
                            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>