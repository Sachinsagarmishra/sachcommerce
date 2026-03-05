<?php
require_once '../config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Homepage Settings';

// Function to update site setting (local copy if not defined global or as helper)
if (!function_exists('update_site_setting')) {
    function update_site_setting($key, $value)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT id FROM site_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            if ($stmt->fetch()) {
                $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")->execute([$value, $key]);
            } else {
                $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)")->execute([$key, $value]);
            }
        } catch (Exception $e) {
        }
    }
}

// Ensure tables exist
$db_tables = [
    'homepage_sections' => "CREATE TABLE IF NOT EXISTS homepage_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(50) UNIQUE NOT NULL,
        section_name VARCHAR(100) NOT NULL,
        display_title VARCHAR(255) DEFAULT NULL,
        display_description TEXT DEFAULT NULL,
        cta_link VARCHAR(255) DEFAULT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        is_custom TINYINT(1) DEFAULT 0
    )",
    'curated_items' => "CREATE TABLE IF NOT EXISTS curated_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        video_path VARCHAR(255) NOT NULL,
        product_id INT NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    'homepage_section_items' => "CREATE TABLE IF NOT EXISTS homepage_section_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(50) NOT NULL,
        item_type ENUM('product', 'category') NOT NULL,
        item_id INT NOT NULL,
        display_order INT DEFAULT 0,
        INDEX(section_key)
    )"
];

foreach ($db_tables as $t_name => $t_sql) {
    try {
        @$pdo->query("SELECT 1 FROM $t_name LIMIT 1");
        // Migration: check if columns exist in homepage_sections
        if ($t_name == 'homepage_sections') {
            $cols = $pdo->query("DESCRIBE homepage_sections")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('display_title', $cols)) {
                $pdo->exec("ALTER TABLE homepage_sections ADD COLUMN display_title VARCHAR(255) DEFAULT NULL AFTER section_name");
            }
            if (!in_array('display_description', $cols)) {
                $pdo->exec("ALTER TABLE homepage_sections ADD COLUMN display_description TEXT DEFAULT NULL AFTER display_title");
            }
            if (!in_array('cta_link', $cols)) {
                $pdo->exec("ALTER TABLE homepage_sections ADD COLUMN cta_link VARCHAR(255) DEFAULT NULL AFTER display_description");
            }
            if (!in_array('is_custom', $cols)) {
                $pdo->exec("ALTER TABLE homepage_sections ADD COLUMN is_custom TINYINT(1) DEFAULT 0 AFTER is_active");
            }
        }
    } catch (Exception $e) {
        $pdo->exec($t_sql);
        if ($t_name == 'homepage_sections') {
            $initial_sections = [
                ['hero', 'Hero Slider', null, null, null],
                ['categories', 'Categories Horizontal Grid', null, null, null],
                ['marquee', 'Announcement Marquee', null, null, null],
                ['tabbed_categories', 'Category Tabs (Dynamic)', 'Our Best Collections', 'Each collection is exclusively made in small batches to create as little as possible.', null],
                ['curated', 'Curated for You (Videos)', null, null, null],
                ['featured', 'Featured Products', 'Featured Products', null, SITE_URL . '/shop'],
                ['new_arrivals', 'New Arrivals', 'New Arrivals', null, SITE_URL . '/shop?filter=new'],
                ['best_sellers', 'Best Sellers', 'Best Sellers', null, SITE_URL . '/shop?filter=bestseller'],
                ['features', 'Service Features (Shipping/Support)', null, null, null]
            ];
            $stmt = $pdo->prepare("INSERT INTO homepage_sections (section_key, section_name, display_title, display_description, cta_link, display_order) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($initial_sections as $index => $sec) {
                $stmt->execute([$sec[0], $sec[1], $sec[2], $sec[3], $sec[4], $index + 1]);
            }
        }
    }
}

// Handle Section Order & Content Update
if (isset($_POST['update_order'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        foreach ($_POST['order'] as $id => $order) {
            $is_active = isset($_POST['active'][$id]) ? 1 : 0;
            $name = $_POST['name'][$id];
            $title = $_POST['title'][$id];
            $desc = $_POST['description'][$id];
            $cta = $_POST['cta'][$id];
            $stmt = $pdo->prepare("UPDATE homepage_sections SET display_order = ?, is_active = ?, section_name = ?, display_title = ?, display_description = ?, cta_link = ? WHERE id = ?");
            $stmt->execute([$order, $is_active, $name, $title, $desc, $cta, $id]);
        }
        set_flash_message('success', 'Homepage layout updated successfully.');
        redirect('homepage-settings.php');
    }
}

// Handle Add Custom Section
if (isset($_POST['add_custom_section'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $name = $_POST['section_name'];
        $title = $_POST['display_title'] ?: $name;
        $cta = $_POST['cta_link'];
        $key = 'custom_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)) . '_' . time();

        $stmt = $pdo->prepare("INSERT INTO homepage_sections (section_key, section_name, display_title, cta_link, is_custom, display_order) VALUES (?, ?, ?, ?, 1, 99)");
        $stmt->execute([$key, $name, $title, $cta]);

        set_flash_message('success', 'Custom section created.');
        redirect('homepage-settings.php');
    }
}

// Handle Delete Custom Section
if (isset($_GET['delete_section'])) {
    $id = intval($_GET['delete_section']);
    $stmt = $pdo->prepare("SELECT section_key, is_custom FROM homepage_sections WHERE id = ?");
    $stmt->execute([$id]);
    $sec = $stmt->fetch();
    if ($sec && $sec['is_custom']) {
        $pdo->prepare("DELETE FROM homepage_section_items WHERE section_key = ?")->execute([$sec['section_key']]);
        $pdo->prepare("DELETE FROM homepage_sections WHERE id = ?")->execute([$id]);
        set_flash_message('success', 'Custom section deleted.');
    }
    redirect('homepage-settings.php');
}

// Handle Marquee Text Update
if (isset($_POST['save_marquee'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        update_site_setting('marquee_text', $_POST['marquee_text']);
        set_flash_message('success', 'Marquee text updated.');
        redirect('homepage-settings.php');
    }
}

// Handle Section Item Addition
if (isset($_POST['add_section_item'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $section_key = $_POST['section_key'];
        $item_id = intval($_POST['item_id']);

        // Determine type based on section key list
        $item_type = 'product';
        if (in_array($section_key, ['categories', 'tabbed_categories'])) {
            $item_type = 'category';
        }

        $stmt = $pdo->prepare("INSERT INTO homepage_section_items (section_key, item_type, item_id) VALUES (?, ?, ?)");
        $stmt->execute([$section_key, $item_type, $item_id]);

        set_flash_message('success', 'Item added to section.');
        redirect('homepage-settings.php');
    }
}

// Handle Section Item Deletion
if (isset($_GET['delete_item'])) {
    $id = intval($_GET['delete_item']);
    $pdo->prepare("DELETE FROM homepage_section_items WHERE id = ?")->execute([$id]);
    set_flash_message('success', 'Item removed from section.');
    redirect('homepage-settings.php');
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
try {
    $sections = $pdo->query("SELECT * FROM homepage_sections ORDER BY display_order ASC")->fetchAll();
    $curated_items = $pdo->query("SELECT c.*, p.name as product_name FROM curated_items c JOIN products p ON c.product_id = p.id ORDER BY c.display_order ASC")->fetchAll();
    $all_products = $pdo->query("SELECT id, name FROM products WHERE status = 'active' ORDER BY name ASC")->fetchAll();
    $all_categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $sections = $curated_items = $all_products = $all_categories = [];
}

$marquee_text = get_site_setting('marquee_text', 'Order by Oct 5th For Guaranteed Diwali Delivery ✦ $20 Duty/Tariff Fee ✦ Free Shipping On All Orders Above $100 USD ✦ Upto 50% Items In Sale');

// Fetch manual section items
$manual_items = [];
try {
    $res = $pdo->query("SELECT hsi.*, 
        CASE WHEN hsi.item_type = 'product' THEN p.name ELSE c.name END as item_name
        FROM homepage_section_items hsi 
        LEFT JOIN products p ON hsi.item_id = p.id AND hsi.item_type = 'product'
        LEFT JOIN categories c ON hsi.item_id = c.id AND hsi.item_type = 'category'
        ORDER BY hsi.section_key, hsi.display_order ASC")->fetchAll();

    foreach ($res as $r) {
        $manual_items[$r['section_key']][] = $r;
    }
} catch (Exception $e) {
    // Table might be missing or query failed
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <div class="page-header mb-4">
                <h1 class="h3 fw-bold">Homepage Settings</h1>
                <p class="text-muted">Manage section order, marquee text, and manual products.</p>
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
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 mt-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-sort-amount-down text-primary me-2"></i>
                                Section Management</h5>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">Order</th>
                                            <th>Section / Display Title</th>
                                            <th>CTA Link (View All)</th>
                                            <th width="60" class="text-center">Active</th>
                                            <th width="40"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sections as $section): ?>
                                            <tr>
                                                <td>
                                                    <input type="number" name="order[<?php echo $section['id']; ?>]"
                                                        value="<?php echo $section['display_order']; ?>"
                                                        class="form-control form-control-sm text-center px-1">
                                                </td>
                                                <td>
                                                    <div class="mb-1">
                                                        <input type="text" name="name[<?php echo $section['id']; ?>]"
                                                            value="<?php echo htmlspecialchars($section['section_name']); ?>"
                                                            class="form-control form-control-sm fw-bold mb-1"
                                                            placeholder="Internal Name">
                                                    </div>
                                                    <input type="text" name="title[<?php echo $section['id']; ?>]"
                                                        value="<?php echo htmlspecialchars($section['display_title'] ?? ''); ?>"
                                                        class="form-control form-control-sm mb-1"
                                                        placeholder="Display Title">
                                                    <textarea name="description[<?php echo $section['id']; ?>]"
                                                        class="form-control form-control-sm" rows="2"
                                                        placeholder="Description/Subtitle"><?php echo htmlspecialchars($section['display_description'] ?? ''); ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="cta[<?php echo $section['id']; ?>]"
                                                        value="<?php echo htmlspecialchars($section['cta_link'] ?? ''); ?>"
                                                        class="form-control form-control-sm" placeholder="URL Link">
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="active[<?php echo $section['id']; ?>]" <?php echo $section['is_active'] ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($section['is_custom']): ?>
                                                        <a href="?delete_section=<?php echo $section['id']; ?>"
                                                            class="text-danger small"
                                                            onclick="return confirm('Delete section and its links?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="submit" name="update_order" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save me-1"></i> Save Layout & Labels
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Custom Section -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 mt-1">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-plus text-success me-2"></i> Create Custom Section
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Internal Name (e.g. Summer Special)</label>
                                <input type="text" name="section_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Display Title on Site</label>
                                <input type="text" name="display_title" class="form-control"
                                    placeholder="Our Summer Collection">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">CTA Link (URL)</label>
                                <input type="text" name="cta_link" class="form-control"
                                    placeholder="<?php echo SITE_URL; ?>/shop?collection=summer">
                            </div>
                            <button type="submit" name="add_custom_section" class="btn btn-success fw-bold w-100">
                                <i class="fas fa-magic me-2"></i> Create Section
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Marquee Setting -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 mt-1">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-bullhorn text-warning me-2"></i> Announcement
                            Marquee Text</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Marquee Content (Use ✦ to separate
                                    items)</label>
                                <textarea name="marquee_text" class="form-control" rows="4"
                                    required><?php echo htmlspecialchars($marquee_text); ?></textarea>
                            </div>
                            <button type="submit" name="save_marquee" class="btn btn-warning text-dark fw-bold">
                                <i class="fas fa-check me-2"></i> Update Text
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Manual Selection -->
            <div class="col-lg-6">
                <!-- Manual Item Management -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 mt-1">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-info me-2"></i> Manual Section
                            Items</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="section_key" class="form-select select-type" required>
                                        <option value="">Select Section...</option>
                                        <?php foreach ($sections as $sec):
                                            if (in_array($sec['section_key'], ['hero', 'marquee', 'curated', 'features']))
                                                continue;
                                            ?>
                                            <option value="<?php echo $sec['section_key']; ?>"
                                                data-type="<?php echo ($sec['section_key'] == 'categories' || $sec['section_key'] == 'tabbed_categories') ? 'category' : 'product'; ?>">
                                                <?php echo $sec['section_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5" id="item-selector">
                                    <select name="item_id" class="form-select select2" required>
                                        <option value="">Select Item...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" name="add_section_item"
                                        class="btn btn-info w-100">Add</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <div class="section-items-list mt-3">
                            <?php
                            foreach ($sections as $sec):
                                $k = $sec['section_key'];
                                if (in_array($k, ['hero', 'marquee', 'curated', 'features']))
                                    continue;
                                $label = $sec['section_name'];
                                ?>
                                <div class="mb-3 pb-2 border-bottom">
                                    <h6 class="fw-bold mb-2 small text-uppercase text-muted"><?php echo $label; ?></h6>
                                    <?php if (isset($manual_items[$k])): ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($manual_items[$k] as $item): ?>
                                                <div class="badge bg-light text-dark border p-2 d-flex align-items-center">
                                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                                    <a href="?delete_item=<?php echo $item['id']; ?>" class="ms-2 text-danger"
                                                        onclick="return confirm('Remove?')"><i class="fas fa-times"></i></a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Automatic (Default)</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Curated Videos -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 mt-1">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-video text-success me-2"></i> Curated Video
                            Gallery</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST" enctype="multipart/form-data"
                            class="mb-4 p-3 bg-light rounded-3 border">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Select Video (MP4)</label>
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
                                        <i class="fas fa-upload me-2"></i> Add Video
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="row g-2">
                            <?php foreach ($curated_items as $item): ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border rounded-3 overflow-hidden position-relative">
                                        <video style="height: 120px; width: 100%; object-fit: cover;" muted>
                                            <source src="../uploads/videos/<?php echo $item['video_path']; ?>"
                                                type="video/mp4">
                                        </video>
                                        <div class="card-body p-2">
                                            <p class="small fw-bold mb-0 text-truncate">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </p>
                                            <a href="?delete_curated=<?php echo $item['id']; ?>"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                onclick="return confirm('Delete?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

        const products = <?php echo json_encode($all_products); ?>;
        const categories = <?php echo json_encode($all_categories); ?>;

        $('.select-type').on('change', function () {
            const val = $(this).val();
            const type = $(this).find(':selected').data('type');
            let options = '<option value="">Select Item...</option>';
            const target = (type === 'category') ? categories : products;

            target.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('select[name="item_id"]').html(options).trigger('change');
        });
    });
</script>
?>