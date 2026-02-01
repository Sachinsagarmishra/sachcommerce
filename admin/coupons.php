<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Coupons Management';

// Initialize variables
$error = '';
$success = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$edit_coupon = null;

// --- HANDLE ACTIONS ---

// 1. DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Coupon deleted successfully';
        header('Location: coupons.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to delete coupon: ' . $e->getMessage();
    }
}

// 2. FORM SUBMISSION (ADD & EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and Validate Inputs
    $code = sanitize_input($_POST['code']);
    $description = sanitize_input($_POST['description']);
    $discount_type = sanitize_input($_POST['discount_type']);
    $discount_value = (float)$_POST['discount_value'];
    $min_order_amount = !empty($_POST['min_order_amount']) ? (float)$_POST['min_order_amount'] : 0;
    $max_discount_amount = !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
    $usage_per_user = !empty($_POST['usage_per_user']) ? (int)$_POST['usage_per_user'] : 1;
    $valid_from = $_POST['valid_from'];
    $valid_to = $_POST['valid_to'];
    $status = sanitize_input($_POST['status']);
    
    $coupon_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Basic Validation
    if (empty($code) || empty($valid_from) || empty($valid_to)) {
        $error = 'Please fill in all required fields (Code, Valid dates).';
    } else {
        try {
            if ($coupon_id > 0) {
                // UPDATE
                $sql = "UPDATE coupons SET 
                        code = ?, description = ?, discount_type = ?, discount_value = ?, 
                        min_order_amount = ?, max_discount_amount = ?, usage_limit = ?, 
                        usage_per_user = ?, valid_from = ?, valid_to = ?, status = ?, 
                        updated_at = NOW() 
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $code, $description, $discount_type, $discount_value,
                    $min_order_amount, $max_discount_amount, $usage_limit,
                    $usage_per_user, $valid_from, $valid_to, $status,
                    $coupon_id
                ]);
                $_SESSION['success'] = 'Coupon updated successfully';
            } else {
                // INSERT
                $sql = "INSERT INTO coupons (
                        code, description, discount_type, discount_value, 
                        min_order_amount, max_discount_amount, usage_limit, 
                        usage_per_user, valid_from, valid_to, status, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $code, $description, $discount_type, $discount_value,
                    $min_order_amount, $max_discount_amount, $usage_limit,
                    $usage_per_user, $valid_from, $valid_to, $status
                ]);
                $_SESSION['success'] = 'Coupon created successfully';
            }
            header('Location: coupons.php');
            exit;
        } catch (PDOException $e) {
            // Handle duplicate entry for code
            if ($e->getCode() == 23000) {
                $error = 'Coupon code already exists.';
            } else {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// 3. FETCH DATA FOR EDIT
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $edit_coupon = $stmt->fetch();
    
    if (!$edit_coupon) {
        $_SESSION['error'] = 'Coupon not found.';
        header('Location: coupons.php');
        exit;
    }
}

// 4. FETCH ALL FOR LIST
$stmt = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC");
$coupons = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">
                    <?php 
                    if ($action === 'add') echo 'Add New Coupon';
                    elseif ($action === 'edit') echo 'Edit Coupon';
                    else echo 'Coupons';
                    ?>
                </h1>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($action === 'list'): ?>
                    <a href="coupons.php?action=add" class="btn btn-primary">
                        <i class="lni lni-plus me-2"></i>Add New Coupon
                    </a>
                <?php else: ?>
                    <a href="coupons.php" class="btn btn-secondary">
                        <i class="lni lni-arrow-left me-2"></i>Back to List
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error']) || !empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo isset($_SESSION['error']) ? $_SESSION['error'] : $error; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- LOGIC VIEW SWITCHER -->
        <?php if ($action === 'add' || $action === 'edit'): ?>
            <!-- === FORM VIEW === -->
            <?php
                // Pre-fill data if editing, otherwise defaults
                $c_code = $edit_coupon ? $edit_coupon['code'] : '';
                $c_desc = $edit_coupon ? $edit_coupon['description'] : '';
                $c_type = $edit_coupon ? $edit_coupon['discount_type'] : 'percentage';
                $c_val  = $edit_coupon ? $edit_coupon['discount_value'] : '';
                $c_min  = $edit_coupon ? $edit_coupon['min_order_amount'] : '0.00';
                $c_max  = $edit_coupon ? $edit_coupon['max_discount_amount'] : '';
                $c_lim  = $edit_coupon ? $edit_coupon['usage_limit'] : '';
                $c_user = $edit_coupon ? $edit_coupon['usage_per_user'] : '1';
                $c_stat = $edit_coupon ? $edit_coupon['status'] : 'active';
                
                // Format dates for datetime-local input (YYYY-MM-DDTHH:MM)
                $c_from = ($edit_coupon && $edit_coupon['valid_from']) ? date('Y-m-d\TH:i', strtotime($edit_coupon['valid_from'])) : date('Y-m-d\TH:i');
                $c_to   = ($edit_coupon && $edit_coupon['valid_to'])   ? date('Y-m-d\TH:i', strtotime($edit_coupon['valid_to']))   : date('Y-m-d\TH:i', strtotime('+7 days'));
            ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_coupon['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <!-- Basic Info -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Coupon Code *</label>
                                <input type="text" class="form-control text-uppercase" name="code" value="<?php echo htmlspecialchars($c_code); ?>" required>
                                <small class="text-muted">Unique code users will enter.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo $c_stat === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $c_stat === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="expired" <?php echo $c_stat === 'expired' ? 'selected' : ''; ?>>Expired</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($c_desc); ?></textarea>
                            </div>

                            <!-- Discount Logic -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount Type *</label>
                                <select class="form-select" name="discount_type" id="discount_type">
                                    <option value="percentage" <?php echo $c_type === 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo $c_type === 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount Value *</label>
                                <input type="number" step="0.01" class="form-control" name="discount_value" value="<?php echo $c_val; ?>" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Discount Amount</label>
                                <input type="number" step="0.01" class="form-control" name="max_discount_amount" value="<?php echo $c_max; ?>">
                                <small class="text-muted">Useful for percentage discounts (e.g., Up to 500)</small>
                            </div>

                            <!-- Limits -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min Order Amount</label>
                                <input type="number" step="0.01" class="form-control" name="min_order_amount" value="<?php echo $c_min; ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Total Usage Limit</label>
                                <input type="number" class="form-control" name="usage_limit" value="<?php echo $c_lim; ?>">
                                <small class="text-muted">Empty for unlimited.</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Usage Per User</label>
                                <input type="number" class="form-control" name="usage_per_user" value="<?php echo $c_user; ?>">
                            </div>

                            <!-- Dates -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid From *</label>
                                <input type="datetime-local" class="form-control" name="valid_from" value="<?php echo $c_from; ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid To *</label>
                                <input type="datetime-local" class="form-control" name="valid_to" value="<?php echo $c_to; ?>" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="lni lni-save me-2"></i><?php echo $action === 'edit' ? 'Update Coupon' : 'Create Coupon'; ?>
                            </button>
                            <a href="coupons.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- === LIST VIEW === -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Discount</th>
                                    <th>Min Order</th>
                                    <th>Validity</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coupons as $coupon): ?>
                                    <?php 
                                    // Check validity status dynamically for display
                                    $now = new DateTime();
                                    $valid_to = new DateTime($coupon['valid_to']);
                                    $is_expired_date = $now > $valid_to;
                                    ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($coupon['code']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars(substr($coupon['description'], 0, 30)) . (strlen($coupon['description']) > 30 ? '...' : ''); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                            <span class="badge bg-info text-dark"><?php echo $coupon['discount_value']; ?>% OFF</span>
                                            <?php if($coupon['max_discount_amount']): ?>
                                                <br><small>Max: <?php echo format_price($coupon['max_discount_amount']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo format_price($coupon['discount_value']); ?> OFF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo format_price($coupon['min_order_amount']); ?></td>
                                    <td>
                                        <small>
                                            From: <?php echo date('d M Y', strtotime($coupon['valid_from'])); ?><br>
                                            To: <?php echo date('d M Y', strtotime($coupon['valid_to'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php echo $coupon['used_count']; ?> / <?php echo $coupon['usage_limit'] ? $coupon['usage_limit'] : '∞'; ?>
                                    </td>
                                    <td>
                                        <?php if ($is_expired_date): ?>
                                            <span class="badge bg-danger">Expired</span>
                                        <?php elseif ($coupon['status'] === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo ucfirst($coupon['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="coupons.php?action=edit&id=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="coupons.php?delete=<?php echo $coupon['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this coupon?')" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($coupons)): ?>
                    <div class="text-center py-5">
                        <i class="lni lni-ticket fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No coupons found.</p>
                        <a href="coupons.php?action=add" class="btn btn-outline-primary mt-2">Create your first coupon</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>