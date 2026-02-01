<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Reviews Management';

// Initialize variables
$error = '';
$success = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$edit_review = null;

// --- HANDLE ACTIONS ---

// 1. DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Review deleted successfully';
        header('Location: reviews.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to delete review: ' . $e->getMessage();
    }
}

// 2. UPDATE STATUS (Approve/Reject)
if (isset($_GET['status_id']) && isset($_GET['new_status'])) {
    $id = (int)$_GET['status_id'];
    $new_status = sanitize_input($_GET['new_status']);
    
    if (in_array($new_status, ['pending', 'approved', 'rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            $_SESSION['success'] = 'Review status updated to ' . ucfirst($new_status) . ' successfully';
            header('Location: reviews.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Failed to update status: ' . $e->getMessage();
        }
    }
}

// 3. EDIT (Admin Reply) FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = (int)$_POST['review_id'];
    $admin_reply = $_POST['admin_reply']; // Allow text/HTML
    $status = sanitize_input($_POST['status']);
    
    try {
        $stmt = $pdo->prepare("UPDATE reviews SET 
                admin_reply = ?, status = ?, updated_at = NOW() 
                WHERE id = ?");
        
        $stmt->execute([$admin_reply, $status, $review_id]);
        
        $_SESSION['success'] = 'Review and reply updated successfully';
        header('Location: reviews.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to update review: ' . $e->getMessage();
    }
}


// --- FETCH DATA FOR EDIT VIEW ---
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT r.*, p.name as product_name, p.slug as product_slug, u.name as user_name 
                          FROM reviews r
                          JOIN products p ON r.product_id = p.id
                          JOIN users u ON r.user_id = u.id
                          WHERE r.id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $edit_review = $stmt->fetch();
    
    if (!$edit_review) {
        $_SESSION['error'] = 'Review not found.';
        header('Location: reviews.php');
        exit;
    }
}


// --- FETCH DATA FOR LIST VIEW (Filtering) ---
$status_filter = $_GET['status'] ?? 'pending'; // Default to pending reviews
$product_filter = $_GET['product'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT r.*, p.name as product_name, p.slug as product_slug, u.name as user_name 
        FROM reviews r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.user_id = u.id
        WHERE 1=1";
$params = [];

if ($status_filter && $status_filter !== 'all') {
    $sql .= " AND r.status = ?";
    $params[] = $status_filter;
}

if ($product_filter) {
    $sql .= " AND r.product_id = ?";
    $params[] = $product_filter;
}

if ($search) {
    $sql .= " AND (r.title LIKE ? OR r.comment LIKE ? OR u.name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();

// Get products for the filter dropdown
$products_list = $pdo->query("SELECT id, name FROM products ORDER BY name ASC")->fetchAll();


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
                    if ($action === 'edit') echo 'View/Reply Review';
                    else echo 'Product Reviews';
                    ?>
                </h1>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($action === 'edit'): ?>
                    <a href="reviews.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Reviews List
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
        <?php if ($action === 'edit' && $edit_review): ?>
            <!-- === EDIT/REPLY VIEW === -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    Review for: <a href="../product-detail.php?slug=<?php echo htmlspecialchars($edit_review['product_slug']); ?>" target="_blank" class="fw-bold text-primary"><?php echo htmlspecialchars($edit_review['product_name']); ?></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($edit_review['title']); ?></h5>
                            <?php echo display_rating($edit_review['rating'], false); ?>
                            <p class="mt-2 text-muted fst-italic">by <?php echo htmlspecialchars($edit_review['user_name']); ?> on <?php echo date('M d, Y', strtotime($edit_review['created_at'])); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($edit_review['comment'])); ?></p>
                        </div>
                        <div class="col-md-6 border-start">
                            <form method="POST">
                                <input type="hidden" name="review_id" value="<?php echo $edit_review['id']; ?>">

                                <div class="mb-3">
                                    <label class="form-label">Review Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="pending" <?php echo $edit_review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $edit_review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $edit_review['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Admin Reply (Optional)</label>
                                    <textarea class="form-control" name="admin_reply" rows="4"><?php echo htmlspecialchars($edit_review['admin_reply']); ?></textarea>
                                    <small class="text-muted">This reply will be shown publicly under the review.</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Update Review & Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- === LIST VIEW === -->
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Search by title, comment, or user..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="all">All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending (<?php echo count(array_filter($reviews, fn($r) => $r['status'] == 'pending')); ?>)</option>
                                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="product" class="form-select">
                                <option value="">All Products</option>
                                <?php foreach ($products_list as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" <?php echo $product_filter == $product['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Rating & Title</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td>
                                        <a href="../product-detail.php?slug=<?php echo htmlspecialchars($review['product_slug']); ?>" target="_blank">
                                            <?php echo htmlspecialchars(substr($review['product_name'], 0, 40)) . (strlen($review['product_name']) > 40 ? '...' : ''); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo display_rating($review['rating'], false); ?><br>
                                        <small class="fw-bold"><?php echo htmlspecialchars(substr($review['title'], 0, 50)) . (strlen($review['title']) > 50 ? '...' : ''); ?></small>
                                        <p class="text-muted mb-0"><small><?php echo htmlspecialchars(substr($review['comment'], 0, 70)) . '...'; ?></small></p>
                                    </td>
                                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                    <td>
                                        <?php 
                                            $status_class = [
                                                'pending' => 'bg-warning text-dark',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger'
                                            ];
                                        ?>
                                        <span class="badge <?php echo $status_class[$review['status']]; ?>">
                                            <?php echo ucfirst($review['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="reviews.php?action=edit&id=<?php echo $review['id']; ?>" class="btn btn-sm btn-info text-white" title="View/Reply">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                        
                                        <?php if ($review['status'] !== 'approved'): ?>
                                        <a href="reviews.php?status_id=<?php echo $review['id']; ?>&new_status=approved" 
                                            class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-checkmark"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($review['status'] !== 'rejected'): ?>
                                        <a href="reviews.php?status_id=<?php echo $review['id']; ?>&new_status=rejected" 
                                            class="btn btn-sm btn-secondary" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="reviews.php?delete=<?php echo $review['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this review?')" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($reviews)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-star-filled fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No reviews found matching the criteria.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>