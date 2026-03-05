<?php
require_once '../config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Homepage Settings';

// Ensure tables exist (Silent fail/Lazy creation)
try {
    $pdo->query("SELECT 1 FROM homepage_sections LIMIT 1");
} catch (Exception $e) {
    // Redirect to install script or run it silently? 
    // Let's just run the SQL here to be safe and seamless.
    $pdo->exec("CREATE TABLE IF NOT EXISTS homepage_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(50) UNIQUE NOT NULL,
        section_name VARCHAR(100) NOT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS curated_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        video_path VARCHAR(255) NOT NULL,
        product_id INT NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $sections = [
        ['hero', 'Hero Slider'],
        ['categories', 'Categories Horizontal Grid'],
        ['marquee', 'Announcement Marquee'],
        ['curated', 'Curated for You (Videos)'],
        ['featured', 'Featured Products'],
        ['new_arrivals', 'New Arrivals'],
        ['best_sellers', 'Best Sellers'],
        ['features', 'Service Features (Shipping/Support)']
    ];

    $check = $pdo->query("SELECT COUNT(*) FROM homepage_sections")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO homepage_sections (section_key, section_name, display_order) VALUES (?, ?, ?)");
        foreach ($sections as $index => $section) {
            $stmt->execute([$section[0], $section[1], $index + 1]);
        }
    }
}

// Handle Section Order Update
if (isset($_POST['update_order'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        foreach ($_POST['order'] as $id => $order) {
            $is_active = isset($_POST['active'][$id]) ? 1 : 0;
            $stmt = $pdo->prepare("UPDATE homepage_sections SET display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$order, $is_active, $id]);
        }
        set_flash_message('success', 'Homepage layout updated successfully.');
        redirect('homepage-settings.php');
    }
}

// Handle Curated Item Addition
if (isset($_POST['add_curated'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $product_id = intval($_POST['product_id']);

        if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
            $allowed = ['mp4', 'webm', 'ogg'];
            $filename = $_FILES['video']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $new_name = 'video_' . time() . '.' . $ext;
                $upload_dir = '../uploads/videos/';
                if (!file_exists($upload_dir))
                    mkdir($upload_dir, 0755, true);

                if (move_uploaded_file($_FILES['video']['tmp_name'], $upload_dir . $new_name)) {
                    $stmt = $pdo->prepare("INSERT INTO curated_items (video_path, product_id, display_order) VALUES (?, ?, ?)");
                    $stmt->execute([$new_name, $product_id, 0]);
                    set_flash_message('success', 'Video item added successfully.');
                } else {
                    set_flash_message('danger', 'Failed to upload video.');
                }
            } else {
                set_flash_message('danger', 'Invalid video format. Only MP4 allowed.');
            }
        } else {
            set_flash_message('danger', 'Please select a video file.');
        }
        redirect('homepage-settings.php');
    }
}

// Handle Curated Item Deletion
if (isset($_GET['delete_curated'])) {
    $id = intval($_GET['delete_curated']);
    $stmt = $pdo->prepare("SELECT video_path FROM curated_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if ($item) {
        @unlink('../uploads/videos/' . $item['video_path']);
        $pdo->prepare("DELETE FROM curated_items WHERE id = ?")->execute([$id]);
        set_flash_message('success', 'Item deleted.');
    }
    redirect('homepage-settings.php');
}

// Fetch Data
$sections = $pdo->query("SELECT * FROM homepage_sections ORDER BY display_order ASC")->fetchAll();
$curated_items = $pdo->query("SELECT c.*, p.name as product_name FROM curated_items c JOIN products p ON c.product_id = p.id ORDER BY c.display_order ASC")->fetchAll();
$all_products = $pdo->query("SELECT id, name FROM products WHERE status = 'active' ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <div class="page-header mb-4">
                <h1 class="h3 fw-bold">Homepage Settings</h1>
                <p class="text-muted">Manage section order and curated video content.</p>
            </div>

            <?php
            $flash = get_flash_message();
            if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show shadow-sm border-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Section Ordering -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3 mt-1">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-sort-amount-down text-primary me-2"></i> Section
                                Display Order</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">Order</th>
                                                <th>Section Name</th>
                                                <th width="100" class="text-center">Visible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sections as $section): ?>
                                                <tr>
                                                    <td>
                                                        <input type="number" name="order[<?php echo $section['id']; ?>]"
                                                            value="<?php echo $section['display_order']; ?>"
                                                            class="form-control form-control-sm text-center"
                                                            style="width: 50px;">
                                                    </td>
                                                    <td>
                                                        <span class="fw-medium">
                                                            <?php echo $section['section_name']; ?>
                                                        </span>
                                                        <br><small class="text-muted">
                                                            <?php echo $section['section_key']; ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="active[<?php echo $section['id']; ?>]" <?php echo $section['is_active'] ? 'checked' : ''; ?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" name="update_order" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Save Section Layout
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Curated Videos -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3 mt-1">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-video text-success me-2"></i> Curated Video
                                Gallery</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Add Form -->
                            <form action="" method="POST" enctype="multipart/form-data"
                                class="mb-4 p-3 bg-light rounded-3 border">
                                <p class="fw-bold mb-3 small text-uppercase">Add New Video Card</p>
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Select Video (MP4 recommended)</label>
                                        <input type="file" name="video" class="form-control" accept="video/*" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Linked Product</label>
                                        <select name="product_id" class="form-select select2" required>
                                            <option value="">Choose a product...</option>
                                            <?php foreach ($all_products as $p): ?>
                                                <option value="<?php echo $p['id']; ?>">
                                                    <?php echo htmlspecialchars($p['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" name="add_curated" class="btn btn-success w-100">
                                            <i class="fas fa-upload me-2"></i> Upload & Add
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Items List -->
                            <div class="row g-3">
                                <?php foreach ($curated_items as $item): ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border rounded-3 overflow-hidden position-relative">
                                            <video style="height: 200px; object-fit: cover;" muted>
                                                <source src="../uploads/videos/<?php echo $item['video_path']; ?>"
                                                    type="video/mp4">
                                            </video>
                                            <div class="card-body p-2">
                                                <p class="small fw-bold mb-0 text-truncate">
                                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                                </p>
                                                <a href="?delete_curated=<?php echo $item['id']; ?>"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                    onclick="return confirm('Delete this video?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($curated_items)): ?>
                                    <div class="col-12 text-center py-4">
                                        <i class="fas fa-video-slash fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No curated videos added yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>