<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Payment Transactions';

// Filters
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize_input($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize_input($_GET['date_to']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;

// Check if table exists
$table_exists = false;
try {
    $table_check = $pdo->query("SHOW TABLES LIKE 'payment_transactions'");
    $table_exists = $table_check->rowCount() > 0;
} catch (PDOException $e) {
}

$transactions = [];
$total_records = 0;
$stats = ['total' => 0, 'paid' => 0, 'failed' => 0, 'refunded' => 0, 'total_amount' => 0];

if ($table_exists) {
    // Build query
    $where_conditions = [];
    $params = [];

    if ($status_filter) {
        $where_conditions[] = "pt.status = ?";
        $params[] = $status_filter;
    }

    if ($date_from) {
        $where_conditions[] = "DATE(pt.created_at) >= ?";
        $params[] = $date_from;
    }

    if ($date_to) {
        $where_conditions[] = "DATE(pt.created_at) <= ?";
        $params[] = $date_to;
    }

    if ($search) {
        $where_conditions[] = "(pt.transaction_id LIKE ? OR o.order_number LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $where_sql = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";

    // Get total count
    $count_sql = "SELECT COUNT(*) FROM payment_transactions pt 
                  LEFT JOIN orders o ON pt.order_id = o.id" . $where_sql;
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();

    // Get transactions
    $sql = "SELECT pt.*, o.order_number, o.customer_name, o.customer_email 
            FROM payment_transactions pt 
            LEFT JOIN orders o ON pt.order_id = o.id"
        . $where_sql .
        " ORDER BY pt.created_at DESC LIMIT $per_page OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();

    // Get stats
    $stats_sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'refunded' THEN 1 ELSE 0 END) as refunded,
                    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_amount
                  FROM payment_transactions";
    $stats = $pdo->query($stats_sql)->fetch();
}

$total_pages = ceil($total_records / $per_page);

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="fas fa-exchange-alt me-2"></i>Payment Transactions</h1>
            <a href="payment-settings.php" class="btn btn-outline-primary">
                <i class="fas fa-cog me-2"></i>Payment Settings
            </a>
        </div>

        <?php if (!$table_exists): ?>
            <div class="alert alert-warning">
                <h5 class="alert-heading"><i class="fas fa-database me-2"></i>Transaction Table Not Found</h5>
                <p class="mb-2">The payment_transactions table doesn't exist. Please run the following SQL to create it:</p>
                <pre class="bg-dark text-light p-3 rounded mb-0"><code>database/payment_transactions.sql</code></pre>
                <hr>
                <p class="mb-0">
                    <a href="<?php echo SITE_URL; ?>/database/payment_transactions.sql" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-1"></i>Download SQL File
                    </a>
                </p>
            </div>
        <?php else: ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Total Transactions</h6>
                                    <h3 class="mb-0">
                                        <?php echo number_format($stats['total'] ?? 0); ?>
                                    </h3>
                                </div>
                                <i class="fas fa-list fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Successful Payments</h6>
                                    <h3 class="mb-0">
                                        <?php echo number_format($stats['paid'] ?? 0); ?>
                                    </h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Failed Payments</h6>
                                    <h3 class="mb-0">
                                        <?php echo number_format($stats['failed'] ?? 0); ?>
                                    </h3>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Total Collected</h6>
                                    <h3 class="mb-0">
                                        <?php echo format_price($stats['total_amount'] ?? 0); ?>
                                    </h3>
                                </div>
                                <i class="fas fa-rupee-sign fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="paid" <?php echo $status_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending
                                </option>
                                <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed
                                </option>
                                <option value="refunded" <?php echo $status_filter === 'refunded' ? 'selected' : ''; ?>
                                    >Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Order ID or Transaction ID"
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="transactions.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>Transaction History</span>
                    <span class="badge bg-secondary">
                        <?php echo number_format($total_records); ?> records
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($transactions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No transactions found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <?php echo date('d M Y', strtotime($txn['created_at'])); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('H:i:s', strtotime($txn['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="order-detail.php?id=<?php echo $txn['order_id']; ?>" class="fw-bold">
                                                    #
                                                    <?php echo htmlspecialchars($txn['order_number'] ?? $txn['order_id']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php echo htmlspecialchars($txn['customer_name'] ?? '-'); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($txn['customer_email'] ?? ''); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="font-monospace">
                                                    <?php echo htmlspecialchars($txn['transaction_id']); ?>
                                                </small>
                                                <?php if ($txn['razorpay_order_id']): ?>
                                                    <br><small class="text-muted">Order:
                                                        <?php echo htmlspecialchars($txn['razorpay_order_id']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold">
                                                <?php echo format_price($txn['amount']); ?>
                                            </td>
                                            <td>
                                                <?php if ($txn['payment_method']): ?>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo ucfirst($txn['payment_method']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_badges = [
                                                    'paid' => 'bg-success',
                                                    'pending' => 'bg-warning text-dark',
                                                    'initiated' => 'bg-info',
                                                    'failed' => 'bg-danger',
                                                    'refunded' => 'bg-secondary'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $status_badges[$txn['status']] ?? 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($txn['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($txn['notes']): ?>
                                                    <small class="text-muted" title="<?php echo htmlspecialchars($txn['notes']); ?>">
                                                        <?php echo htmlspecialchars(truncate_text($txn['notes'], 40)); ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Transaction pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&search=<?php echo urlencode($search); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php
                                    $start = max(1, $page - 2);
                                    $end = min($total_pages, $page + 2);

                                    for ($i = $start; $i <= $end; $i++):
                                        ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&search=<?php echo urlencode($search); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&search=<?php echo urlencode($search); ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>