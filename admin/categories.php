<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Categories';

// Handle delete
if (isset($_GET['delete'])) {
    $category_id = (int) $_GET['delete'];

    // Get image filename before deleting
    $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $cat = $stmt->fetch();

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt->execute([$category_id])) {
        // Delete image file if exists
        if (!empty($cat['image'])) {
            delete_file(CATEGORY_IMAGE_PATH . $cat['image']);
        }
        $_SESSION['success'] = 'Category deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete category';
    }
    header('Location: categories.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $slug = sanitize_input($_POST['slug']);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);
    $display_order = (int) $_POST['display_order'];
    $image = null;

    // Image Upload Logic
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_image($_FILES['image'], CATEGORY_IMAGE_PATH, 'cat_');
        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $_SESSION['error'] = $upload['message'];
        }
    }

    if (isset($_POST['category_id']) && $_POST['category_id']) {
        // Update
        $category_id = (int) $_POST['category_id'];

        if ($image) {
            // Delete old image if new one uploaded
            $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $old_image = $stmt->fetchColumn();
            if ($old_image) {
                delete_file(CATEGORY_IMAGE_PATH . $old_image);
            }

            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, image = ?, description = ?, status = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $image, $description, $status, $display_order, $category_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, status = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $status, $display_order, $category_id]);
        }
        $_SESSION['success'] = 'Category updated successfully';
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image, description, status, display_order, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $slug, $image, $description, $status, $display_order]);
        $_SESSION['success'] = 'Category added successfully';
    }
    header('Location: categories.php');
    exit;
}

// Get category for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $edit_category = $stmt->fetch();
}

// Get all categories
$stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.id = p.category_id 
                     GROUP BY c.id 
                     ORDER BY c.display_order ASC, c.name ASC");
$categories = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Categories</h1>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo $edit_category ? 'Edit' : 'Add'; ?> Category</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <?php if ($edit_category): ?>
                                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Category Image</label>
                                <?php if ($edit_category && $edit_category['image']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo CATEGORY_IMAGE_URL . $edit_category['image']; ?>" alt=""
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="text-muted">Recommended: Square image (500x500px)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" name="name"
                                    value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" class="form-control" name="slug"
                                    value="<?php echo $edit_category ? htmlspecialchars($edit_category['slug']) : ''; ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description"
                                    rows="3"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" class="form-control" name="display_order"
                                    value="<?php echo $edit_category ? $edit_category['display_order'] : 0; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($edit_category && $edit_category['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($edit_category && $edit_category['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i><?php echo $edit_category ? 'Update' : 'Add'; ?>
                                Category
                            </button>

                            <?php if ($edit_category): ?>
                                <a href="categories.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Categories List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['display_order']; ?></td>
                                            <td>
                                                <?php if ($category['image']): ?>
                                                    <img src="<?php echo CATEGORY_IMAGE_URL . $category['image']; ?>" alt=""
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                <?php else: ?>
                                                    <div
                                                        style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                            <td><?php echo $category['product_count']; ?></td>
                                            <td>
                                                <span
                                                    class="badge <?php echo $category['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($category['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="categories.php?edit=<?php echo $category['id']; ?>"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="categories.php?delete=<?php echo $category['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure? This will affect all products in this category.')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>