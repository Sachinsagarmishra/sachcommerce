<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Edit Banner';

if (!isset($_GET['id'])) {
    header('Location: manage-banners.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
$stmt->execute([$id]);
$banner = $stmt->fetch();

if (!$banner) {
    header('Location: manage-banners.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = sanitize_input($_POST['link']);
    $sort_order = (int) $_POST['sort_order'];
    $status = isset($_POST['status']) ? 1 : 0;

    $image_desktop = $banner['image_desktop'];
    $image_mobile = $banner['image_mobile'];
    $upload_dir = '../uploads/banners/';

    // Handle Desktop Image
    if (isset($_FILES['image_desktop']) && $_FILES['image_desktop']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image_desktop']['name'], PATHINFO_EXTENSION));
        $filename = 'desktop_' . time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['image_desktop']['tmp_name'], $upload_dir . $filename)) {
            // Delete old image
            if (file_exists($upload_dir . $banner['image_desktop'])) {
                unlink($upload_dir . $banner['image_desktop']);
            }
            $image_desktop = $filename;
        }
    }

    // Handle Mobile Image
    if (isset($_FILES['image_mobile']) && $_FILES['image_mobile']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image_mobile']['name'], PATHINFO_EXTENSION));
        $filename = 'mobile_' . time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['image_mobile']['tmp_name'], $upload_dir . $filename)) {
            // Delete old image
            if (file_exists($upload_dir . $banner['image_mobile'])) {
                unlink($upload_dir . $banner['image_mobile']);
            }
            $image_mobile = $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE banners SET image_desktop = ?, image_mobile = ?, link = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$image_desktop, $image_mobile, $link, $sort_order, $status, $id]);

        $_SESSION['success'] = 'Banner updated successfully';
        header('Location: manage-banners.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Edit Banner</h1>
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
                            <label class="form-label">Desktop Image</label>
                            <div class="mb-2">
                                <img src="../uploads/banners/<?php echo $banner['image_desktop']; ?>" height="100"
                                    class="border rounded">
                            </div>
                            <input type="file" class="form-control" name="image_desktop" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image. Recommended: 1920x600px</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Image</label>
                            <div class="mb-2">
                                <img src="../uploads/banners/<?php echo $banner['image_mobile']; ?>" height="100"
                                    class="border rounded">
                            </div>
                            <input type="file" class="form-control" name="image_mobile" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image. Recommended: 600x800px</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Link URL</label>
                            <input type="text" class="form-control" name="link"
                                value="<?php echo htmlspecialchars($banner['link']); ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order"
                                value="<?php echo $banner['sort_order']; ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" <?php echo $banner['status'] ? 'checked' : ''; ?>>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i>Update Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>