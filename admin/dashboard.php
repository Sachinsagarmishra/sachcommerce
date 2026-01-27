<?php
/**
 * Admin Dashboard
 */
require_once '../config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Dashboard';

// Get dashboard statistics
$stats = get_dashboard_stats();
$recent_orders = get_recent_orders(10);
$low_stock_products = get_low_stock_products(10);

// Get sales data for chart (last 7 days)
$sales_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as sales 
                          FROM orders 
                          WHERE DATE(created_at) = ? AND payment_status = 'paid'");
    $stmt->execute([$date]);
    $result = $stmt->fetch();
    $sales_data[] = [
        'date' => date('M d', strtotime($date)),
        'sales' => $result['sales']
    ];
}

include 'includes/header.php';
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-title-box">
            <div>
                <h1 class="mb-1">Dashboard</h1>
                <p class="text-grey mb-0">Welcome back, <?php echo explode(' ', $user['name'])[0]; ?>!</p>
            </div>
            <a href="product-add.php" class="btn-primary-ui text-white decoration-none">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Today's Sales</span>
                        <div class="action-icon"><i class="fas fa-ellipsis-v"></i></div>
                    </div>
                    <div class="stat-value"><?php echo format_price($stats['today_sales']); ?></div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>14.4%</span>
                        <span class="text-grey fw-normal ms-1">Last 7 days</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Month Sales</span>
                        <div class="action-icon"><i class="fas fa-ellipsis-v"></i></div>
                    </div>
                    <div class="stat-value"><?php echo format_price($stats['month_sales']); ?></div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>20%</span>
                        <span class="text-grey fw-normal ms-1">This month</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Pending Orders</span>
                        <div class="action-icon"><i class="fas fa-ellipsis-v"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                    <div class="stat-trend" style="color: var(--grey);">
                        <span>85%</span>
                        <span class="text-grey fw-normal ms-1">Last 7 days</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Low Stock</span>
                        <div class="action-icon"><i class="fas fa-ellipsis-v"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $stats['low_stock']; ?></div>
                    <div class="stat-trend trend-down">
                        <i class="fas fa-arrow-down"></i>
                        <span>5%</span>
                        <span class="text-grey fw-normal ms-1">Last 7 days</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?php echo $stats['total_orders']; ?></h3>
                        <p class="mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success"><?php echo $stats['total_products']; ?></h3>
                        <p class="mb-0">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-info"><?php echo $stats['total_customers']; ?></h3>
                        <p class="mb-0">Total Customers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo $stats['total_categories']; ?></h3>
                        <p class="mb-0">Categories</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line"></i> Sales Overview (Last 7 Days)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="add-product.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Product
                            </a>
                            <a href="orders.php" class="btn btn-info">
                                <i class="fas fa-shopping-cart"></i> View Orders
                            </a>
                            <a href="add-coupon.php" class="btn btn-success">
                                <i class="fas fa-ticket-alt"></i> Create Coupon
                            </a>
                            <a href="add-blog-post.php" class="btn btn-warning">
                                <i class="fas fa-blog"></i> Write Blog Post
                            </a>
                            <a href="general-settings.php" class="btn btn-secondary">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders & Low Stock -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="orders.php" class="text-primary fw-bold text-decoration-none small">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_orders)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No orders yet</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo format_price($order['total_amount']); ?></td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?php echo get_order_status_badge($order['order_status']); ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <a href="view-order.php?id=<?php echo $order['id']; ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Low Stock Alert
                    </div>
                    <div class="card-body">
                        <?php if (empty($low_stock_products)): ?>
                            <p class="text-center text-muted">All products are in stock</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($low_stock_products as $product): ?>
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                <small class="text-muted">SKU:
                                                    <?php echo htmlspecialchars($product['sku']); ?></small>
                                            </div>
                                            <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?> left</span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>
</div>

<?php include 'includes/sidebar.php'; ?>

<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($sales_data, 'date')); ?>,
            datasets: [{
                label: 'Sales (₹)',
                data: <?php echo json_encode(array_column($sales_data, 'sales')); ?>,
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>