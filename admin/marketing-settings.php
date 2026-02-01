<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Marketing Settings';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_marketing'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_flash_message('danger', 'CSRF token validation failed.');
    } else {
        // Toggle feature
        $fake_notif_enabled = isset($_POST['fake_notif_enabled']) ? '1' : '0';
        update_site_setting('fake_notif_enabled', $fake_notif_enabled, 'text', 'marketing');

        // Notification Settings
        $fake_notif_interval = $_POST['fake_notif_interval'] ?? '10';
        update_site_setting('fake_notif_interval', $fake_notif_interval, 'text', 'marketing');

        $fake_notif_duration = $_POST['fake_notif_duration'] ?? '5';
        update_site_setting('fake_notif_duration', $fake_notif_duration, 'text', 'marketing');

        // Selected Products
        $selected_products = isset($_POST['fake_notif_products']) ? implode(',', $_POST['fake_notif_products']) : '';
        update_site_setting('fake_notif_products', $selected_products, 'text', 'marketing');

        // Log and redirect
        log_activity($_SESSION['user_id'], 'update_marketing_settings', 'Updated fake notification settings');
        set_flash_message('success', 'Marketing settings updated successfully.');
        redirect('marketing-settings.php');
    }
}

// Get current settings
$fake_notif_enabled = get_site_setting('fake_notif_enabled', '0');
$fake_notif_interval = get_site_setting('fake_notif_interval', '10'); // seconds
$fake_notif_duration = get_site_setting('fake_notif_duration', '5'); // seconds
$selected_product_ids = explode(',', get_site_setting('fake_notif_products', ''));

// Fetch all active products for selection
$stmt = $pdo->query("SELECT id, name, sku FROM products WHERE status = 'active' ORDER BY name ASC");
$all_products = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Marketing Settings</h1>
                        <p class="text-muted mb-0">Boost your sales with social proof and conversion tools.</p>
                    </div>
                </div>
            </div>

            <?php
            $flash = get_flash_message();
            if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show shadow-sm border-0"
                    role="alert">
                    <i
                        class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="marketingForm">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 mt-2">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-bell text-primary me-2"></i> Fake Purchase Notifications
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="fake_notif_enabled"
                                        id="fake_notif_enabled" <?php echo $fake_notif_enabled === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="fake_notif_enabled">Enable Fake
                                        Purchase
                                        Popups</label>
                                    <div class="form-text">Show a real-time notification popup to visitors when someone
                                        "buys" a product.</div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Interval (Seconds)</label>
                                        <input type="number" class="form-control" name="fake_notif_interval"
                                            value="<?php echo $fake_notif_interval; ?>" min="5" max="300" required>
                                        <div class="form-text">Wait time between two notifications.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Display Duration (Seconds)</label>
                                        <input type="number" class="form-control" name="fake_notif_duration"
                                            value="<?php echo $fake_notif_duration; ?>" min="2" max="20" required>
                                        <div class="form-text">How long the notification stays visible.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Select Products to Show</label>
                                    <div class="product-selector border rounded p-3 bg-light"
                                        style="max-height: 400px; overflow-y: auto;">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fas fa-search text-muted"></i></span>
                                            <input type="text" id="productSearch" class="form-control border-start-0"
                                                placeholder="Search products...">
                                        </div>
                                        <div class="product-list">
                                            <?php foreach ($all_products as $product): ?>
                                                <div class="form-check mb-2 product-item"
                                                    data-name="<?php echo strtolower($product['name']); ?>">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="fake_notif_products[]" value="<?php echo $product['id']; ?>"
                                                        id="prod_<?php echo $product['id']; ?>" <?php echo in_array($product['id'], $selected_product_ids) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label"
                                                        for="prod_<?php echo $product['id']; ?>">
                                                        <?php echo htmlspecialchars($product['name']); ?>
                                                        <small class="text-muted ms-2">(SKU:
                                                            <?php echo $product['sku']; ?>)
                                                        </small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">Select the products you want to appear in the fake
                                        notifications.</div>
                                </div>

                                <div class="border-top pt-4 mt-4 text-end">
                                    <button type="submit" name="update_marketing" class="btn btn-primary px-5 btn-lg">
                                        <i class="fas fa-save me-2"></i> Save Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 100px;">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0 fw-bold small text-uppercase">Notification Preview</h5>
                            </div>
                            <div class="card-body p-4 bg-light text-center">
                                <p class="text-muted small mb-4">This is how the notification will look on the live
                                    site.</p>

                                <div class="fake-notification-preview text-start bg-white shadow-lg p-3 rounded-4 border d-flex align-items-center gap-3"
                                    style="max-width: 320px; margin: 0 auto;">
                                    <div class="position-relative">
                                        <img src="https://via.placeholder.com/60" class="rounded-3" alt="Preview">
                                        <span
                                            class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success border border-light"
                                            style="font-size: 8px;">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="mb-0 small text-muted">Someone recently bought</p>
                                        <h6 class="mb-0 text-truncate fw-bold" style="font-size: 0.9rem;">Product Name
                                            Here
                                        </h6>
                                        <p class="mb-0 x-small text-muted" style="font-size: 0.75rem;">in
                                            <strong>Ludhiana,
                                                Punjab</strong></p>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="text-success x-small fw-bold"
                                                style="font-size: 0.7rem;">Verified
                                                Purchased</span>
                                            <span class="ms-2 text-muted x-small" style="font-size: 0.7rem;">just
                                                now</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close ms-auto small" style="font-size: 0.5rem;"
                                        disabled></button>
                                </div>

                                <div class="alert alert-info mt-4 mb-0 py-2">
                                    <i class="fas fa-info-circle me-2"></i> Locations are generated randomly (India
                                    only).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<script>
    // Simple product search
    document.getElementById('productSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<style>
    .x-small {
        font-size: 0.75rem;
    }

    .fake-notification-preview {
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .product-selector {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }

    .card {
        transition: all 0.3s ease;
    }

    .sticky-top {
        z-index: 10;
    }
</style>