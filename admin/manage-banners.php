<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Manage Banners';

// Handle delete
if (isset($_GET['delete'])) {
    $banner_id = (int) $_GET['delete'];

    // Get banner images to delete from server
    $stmt = $pdo->prepare("SELECT image_desktop, image_mobile FROM banners WHERE id = ?");
    $stmt->execute([$banner_id]);
    $banner = $stmt->fetch();

    if ($banner) {
        $desktop_path = '../uploads/banners/' . $banner['image_desktop'];
        $mobile_path = '../uploads/banners/' . $banner['image_mobile'];

        if (file_exists($desktop_path))
            unlink($desktop_path);
        if (file_exists($mobile_path))
            unlink($mobile_path);

        $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
        if ($stmt->execute([$banner_id])) {
            $_SESSION['success'] = 'Banner deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete banner';
        }
    }
    header('Location: manage-banners.php');
    exit;
}

// Get all banners
$stmt = $pdo->query("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC");
$banners = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Homepage Banners</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="add-banner.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Banner
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sort</th>
                                <th>Desktop Image</th>
                                <th>Mobile Image</th>
                                <th>Link URL</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($banners as $banner): ?>
                                <tr>
                                    <td>
                                        <?php echo $banner['sort_order']; ?>
                                    </td>
                                    <td>
                                        <img src="../uploads/banners/<?php echo $banner['image_desktop']; ?>" alt="Desktop"
                                            style="height: 60px; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <img src="../uploads/banners/<?php echo $banner['image_mobile']; ?>" alt="Mobile"
                                            style="height: 60px; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($banner['link']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?php echo $banner['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $banner['status'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit-banner.php?id=<?php echo $banner['id']; ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage-banners.php?delete=<?php echo $banner['id']; ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirm('Delete this banner?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($banners)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No banners found. Add your first banner to see it on the homepage.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>