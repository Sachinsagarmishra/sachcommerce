<?php
require_once '../config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Theme Settings';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_theme'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_flash_message('danger', 'CSRF token validation failed.');
    } else {
        // Color Settings
        update_site_setting('primary_color', $_POST['primary_color'], 'color', 'theme');
        update_site_setting('secondary_color', $_POST['secondary_color'], 'color', 'theme');
        update_site_setting('btn_primary_bg', $_POST['btn_primary_bg'], 'color', 'theme');
        update_site_setting('btn_primary_text', $_POST['btn_primary_text'], 'color', 'theme');
        update_site_setting('header_bg', $_POST['header_bg'], 'color', 'theme');
        update_site_setting('footer_bg', $_POST['footer_bg'], 'color', 'theme');

        // Font Settings
        update_site_setting('body_font_family', $_POST['body_font_family'], 'text', 'theme');
        update_site_setting('heading_font_family', $_POST['heading_font_family'], 'text', 'theme');
        update_site_setting('google_fonts_url', $_POST['google_fonts_url'], 'text', 'theme');

        log_activity($_SESSION['user_id'], 'update_theme_settings', 'Updated theme colors and fonts');
        set_flash_message('success', 'Theme settings updated successfully.');
        redirect('theme-settings.php');
    }
}

// Get current settings
$primary_color = get_site_setting('primary_color', '#83b735');
$secondary_color = get_site_setting('secondary_color', '#858796');
$btn_primary_bg = get_site_setting('btn_primary_bg', '#83b735');
$btn_primary_text = get_site_setting('btn_primary_text', '#ffffff');
$header_bg = get_site_setting('header_bg', '#ffffff');
$footer_bg = get_site_setting('footer_bg', '#2c3e50');

$body_font_family = get_site_setting('body_font_family', "'Inter', sans-serif");
$heading_font_family = get_site_setting('heading_font_family', "'Jost', sans-serif");
$google_fonts_url = get_site_setting('google_fonts_url', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@400;500;600;700&display=swap');

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <div class="page-header mb-4">
                <h1 class="h3 fw-bold">Theme Settings</h1>
                <p class="text-muted">Customize your store's appearance, colors, and typography.</p>
            </div>

            <?php
            $flash = get_flash_message();
            if ($flash):
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="card shadow-sm border-0">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="card-body p-lg-5">
                    <div class="row g-5">
                        <!-- Color Settings -->
                        <div class="col-lg-6">
                            <h5 class="mb-4 text-primary d-flex align-items-center">
                                <span class="bg-primary-subtle text-primary p-2 rounded me-3">
                                    <i class="fas fa-palette"></i>
                                </span>
                                Color Palette
                            </h5>

                            <div class="mb-4">
                                <label class="form-label">Primary Color</label>
                                <div class="input-group input-group-lg">
                                    <input type="color" class="form-control form-control-color border-end-0" name="primary_color" value="<?php echo $primary_color; ?>" style="width: 80px;">
                                    <input type="text" class="form-control" value="<?php echo $primary_color; ?>" onchange="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="form-text mt-2">Used for brand elements, links, and accents.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Secondary Color</label>
                                <div class="input-group input-group-lg">
                                    <input type="color" class="form-control form-control-color border-end-0" name="secondary_color" value="<?php echo $secondary_color; ?>" style="width: 80px;">
                                    <input type="text" class="form-control" value="<?php echo $secondary_color; ?>" onchange="this.previousElementSibling.value = this.value">
                                </div>
                                <div class="form-text mt-2">Used for auxiliary text and secondary backgrounds.</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Button Background</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" name="btn_primary_bg" value="<?php echo $btn_primary_bg; ?>">
                                        <input type="text" class="form-control" value="<?php echo $btn_primary_bg; ?>" onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Button Text</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" name="btn_primary_text" value="<?php echo $btn_primary_text; ?>">
                                        <input type="text" class="form-control" value="<?php echo $btn_primary_text; ?>" onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Header Background</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" name="header_bg" value="<?php echo $header_bg; ?>">
                                        <input type="text" class="form-control" value="<?php echo $header_bg; ?>" onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Footer Background</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" name="footer_bg" value="<?php echo $footer_bg; ?>">
                                        <input type="text" class="form-control" value="<?php echo $footer_bg; ?>" onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Font Settings -->
                        <div class="col-lg-6 ps-lg-5 border-start">
                            <h5 class="mb-4 text-primary d-flex align-items-center">
                                <span class="bg-primary-subtle text-primary p-2 rounded me-3">
                                    <i class="fas fa-font"></i>
                                </span>
                                Typography Settings
                            </h5>

                            <div class="mb-4">
                                <label class="form-label">Google Fonts URL</label>
                                <input type="text" class="form-control form-control-lg" name="google_fonts_url" value="<?php echo htmlspecialchars($google_fonts_url); ?>" placeholder="https://fonts.googleapis.com/...">
                                <div class="form-text mt-2">Paste the Google Fonts &lt;link&gt; href URL here.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Body Font Family</label>
                                <input type="text" class="form-control form-control-lg" name="body_font_family" value="<?php echo htmlspecialchars($body_font_family); ?>">
                                <div class="form-text mt-2">Default: 'Inter', sans-serif</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Heading Font Family</label>
                                <input type="text" class="form-control form-control-lg" name="heading_font_family" value="<?php echo htmlspecialchars($heading_font_family); ?>">
                                <div class="form-text mt-2">Default: 'Jost', sans-serif (Used for headings and titles)</div>
                            </div>

                            <div class="mt-5 p-4 bg-light rounded-4 shadow-sm border border-dashed text-center">
                                <h6 class="mb-4 text-muted text-uppercase fw-bold letter-spacing-1 small">Live Preview</h6>
                                <div class="preview-box p-3 bg-white rounded-3 mb-3 shadow-sm">
                                    <h3 class="mb-3" style="font-family: <?php echo $heading_font_family; ?>;">TrendsOne Branding</h3>
                                    <p class="mb-4 text-muted" style="font-family: <?php echo $body_font_family; ?>;">Explore the latest collections in fashion and lifestyle with premium materials and designs.</p>
                                    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" style="background-color: <?php echo $btn_primary_bg; ?>; border-color: <?php echo $btn_primary_bg; ?>; color: <?php echo $btn_primary_text; ?>;">
                                        Add to Cart
                                    </button>
                                </div>
                                <p class="small text-muted mb-0"><i class="fas fa-info-circle me-1"></i> Preview reflects current saved settings.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-4 text-end">
                    <button type="submit" name="update_theme" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i> Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<style>
.bg-primary-subtle { background-color: rgba(78, 115, 223, 0.1); }
.letter-spacing-1 { letter-spacing: 1px; }
.border-dashed { border-style: dashed !important; }
.rounded-4 { border-radius: 1rem !important; }
</style>