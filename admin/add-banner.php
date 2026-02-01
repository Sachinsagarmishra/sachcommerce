<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Add Banner';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = sanitize_input($_POST['link']);
    $sort_order = (int) $_POST['sort_order'];
    $status = isset($_POST['status']) ? 1 : 0;

    $image_desktop = '';
    $image_mobile = '';
    $upload_dir = '../uploads/banners/';

    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle Desktop Image
    if (isset($_FILES['image_desktop']) && $_FILES['image_desktop']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image_desktop']['name'], PATHINFO_EXTENSION));
        $filename = 'desktop_' . time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['image_desktop']['tmp_name'], $upload_dir . $filename)) {
            $image_desktop = $filename;
        }
    }

    // Handle Mobile Image
    if (isset($_FILES['image_mobile']) && $_FILES['image_mobile']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image_mobile']['name'], PATHINFO_EXTENSION));
        $filename = 'mobile_' . time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['image_mobile']['tmp_name'], $upload_dir . $filename)) {
            $image_mobile = $filename;
        }
    }

    if ($image_desktop && $image_mobile) {
        try {
            $stmt = $pdo->prepare("INSERT INTO banners (image_desktop, image_mobile, link, sort_order, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$image_desktop, $image_mobile, $link, $sort_order, $status]);

            $_SESSION['success'] = 'Banner added successfully';
            header('Location: manage-banners.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please upload both Desktop and Mobile images.';
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Add New Banner</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="manage-banners.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Banners
                </a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desktop Image *</label>
                            <input type="file" class="form-control" name="image_desktop" accept="image/*" required>
                            <small class="text-muted">Recommended: 1920x600px</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Image *</label>
                            <input type="file" class="form-control" name="image_mobile" accept="image/*" required>
                            <small class="text-muted">Recommended: 600x800px</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Link URL</label>
                            <input type="text" class="form-control" name="link" placeholder="https://example.com/shop">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="0">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i>Save Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>