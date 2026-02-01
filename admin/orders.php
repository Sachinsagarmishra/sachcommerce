<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

// Set timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

$page_title = 'Orders';

// Handle resend confirmation email
if (isset($_POST['resend_email'])) {
    $order_id = (int) $_POST['order_id'];
    try {
        // Get order details
        $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email 
                               FROM orders o 
                               LEFT JOIN users u ON o.user_id = u.id 
                               WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        if ($order) {
            $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $items->execute([$order_id]);
            $order_items = $items->fetchAll();

            $success = sendOrderConfirmationEmail(
                $order['id'],
                $order['order_number'],
                $order['customer_email'],
                $order['customer_name'],
                $order['total_amount'],
                $order_items
            );

            if ($success) {
                $_SESSION['success'] = 'Confirmation email resent to ' . $order['customer_email'];
            } else {
                $_SESSION['error'] = 'Failed to send confirmation email. Check SMTP settings.';
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
    header('Location: orders.php');
    exit;
}

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    $order_ids = $_POST['order_ids'] ?? [];

    if (!empty($order_ids)) {
        try {
            $pdo->beginTransaction();

            // Delete order items first
            $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id IN ($placeholders)");
            $stmt->execute($order_ids);

            // Delete orders
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id IN ($placeholders)");
            $stmt->execute($order_ids);

            $pdo->commit();

            $_SESSION['success'] = count($order_ids) . ' order(s) deleted successfully';
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Failed to delete orders: ' . $e->getMessage();
        }

        header('Location: orders.php');
        exit;
    }
}

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

// Get messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
    .bulk-actions {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: none;
    }

    .bulk-actions.show {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .order-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .select-all-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Orders</h1>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search"
                            placeholder="Search by order number, customer..."
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending
                            </option>
                            <option value="processing" <?php echo $status === 'processing' ? 'selected' : ''; ?>>
                                Processing</option>
                            <option value="shipped" <?php echo $status === 'shipped' ? 'selected' : ''; ?>>Shipped
                            </option>
                            <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered
                            </option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled
                            </option>
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
                <!-- Bulk Actions Bar -->
                <form method="POST" id="bulkDeleteForm">
                    <div class="bulk-actions" id="bulkActionsBar">
                        <div>
                            <span id="selectedCount">0</span> order(s) selected
                        </div>
                        <div>
                            <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete the selected orders? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm ms-2" onclick="clearSelection()">
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="select-all-checkbox" id="selectAll"
                                            onchange="toggleSelectAll(this)">
                                    </th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="order-checkbox" name="order_ids[]"
                                                value="<?php echo $order['id']; ?>" onchange="updateBulkActions()">
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                            <small
                                                class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?><br>
                                            <small
                                                class="text-muted"><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                                        </td>
                                        <td><strong><?php echo format_price($order['total_amount']); ?></strong></td>
                                        <td>
                                            <span
                                                class="badge <?php echo $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span><br>
                                            <small
                                                class="text-muted"><?php echo strtoupper($order['payment_method']); ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = 'bg-secondary';
                                            switch ($order['order_status']) {
                                                case 'pending':
                                                    $badge_class = 'bg-warning';
                                                    break;
                                                case 'processing':
                                                    $badge_class = 'bg-info';
                                                    break;
                                                case 'shipped':
                                                    $badge_class = 'bg-primary';
                                                    break;
                                                case 'delivered':
                                                    $badge_class = 'bg-success';
                                                    break;
                                                case 'cancelled':
                                                    $badge_class = 'bg-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="order-detail.php?id=<?php echo $order['id']; ?>"
                                                class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye text-white"></i>
                                            </a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Resend confirmation email?')">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <button type="submit" name="resend_email" class="btn btn-sm btn-success" title="Resend Email">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>

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

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.order-checkbox:checked');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');

        selectedCount.textContent = checkboxes.length;

        if (checkboxes.length > 0) {
            bulkActionsBar.classList.add('show');
        } else {
            bulkActionsBar.classList.remove('show');
        }

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.order-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }
</script>

<?php include 'includes/footer.php'; ?>