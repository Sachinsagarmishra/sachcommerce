<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Orders';

// Get filters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
$params = [];

if ($status) {
    $sql .= " AND o.order_status = ?";
    $params[] = $status;
}

if ($search) {
    $sql .= " AND (o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Orders</h1>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search" placeholder="Search by order number, customer..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $status === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $status === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td><strong><?php echo format_price($order['total_amount']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span><br>
                                    <small class="text-muted"><?php echo strtoupper($order['payment_method']); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = 'bg-secondary';
                                    switch ($order['order_status']) {
                                        case 'pending': $badge_class = 'bg-warning'; break;
                                        case 'processing': $badge_class = 'bg-info'; break;
                                        case 'shipped': $badge_class = 'bg-primary'; break;
                                        case 'delivered': $badge_class = 'bg-success'; break;
                                        case 'cancelled': $badge_class = 'bg-danger'; break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No orders found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
