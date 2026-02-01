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
        <div class="page-header">
            <h1>Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-label text-success">Today's Sales</div>
                                <div class="stats-number"><?php echo format_price($stats['today_sales']); ?></div>
                            </div>
                            <div class="text-success">
                                <i class="lni lni-revenue fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-label text-info">This Month</div>
                                <div class="stats-number"><?php echo format_price($stats['month_sales']); ?></div>
                            </div>
                            <div class="text-info">
                                <i class="lni lni-calendar fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-label text-warning">Pending Orders</div>
                                <div class="stats-number"><?php echo $stats['pending_orders']; ?></div>
                            </div>
                            <div class="text-warning">
                                <i class="lni lni-cart fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-label text-danger">Low Stock</div>
                                <div class="stats-number"><?php echo $stats['low_stock']; ?></div>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-exclamation-triangle fa-3x"></i>
                            </div>
                        </div>
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
                                <i class="lni lni-plus"></i> Add New Product
                            </a>
                            <a href="orders.php" class="btn btn-info">
                                <i class="lni lni-cart"></i> View Orders
                            </a>
                            <a href="add-coupon.php" class="btn btn-success">
                                <i class="lni lni-ticket"></i> Create Coupon
                            </a>
                            <a href="add-blog-post.php" class="btn btn-warning">
                                <i class="fas fa-blog"></i> Write Blog Post
                            </a>
                            <a href="general-settings.php" class="btn btn-secondary">
                                <i class="lni lni-cog"></i> Settings
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="lni lni-cart"></i> Recent Orders</span>
                        <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo format_price($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo get_order_status_badge($order['order_status']); ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <a href="view-order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="lni lni-eye"></i>
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
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                <small class="text-muted">SKU: <?php echo htmlspecialchars($product['sku']); ?></small>
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
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
