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
?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Theme Settings</h2>
        </div>

        <?php
        $flash = get_flash_message();
        if ($flash):
            ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="card shadow-sm">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="card-body">
                <div class="row">
                    <!-- Color Settings -->
                    <div class="col-md-6 border-end">
                        <h5 class="mb-4 text-primary"><i class="fas fa-palette me-2"></i> Color Palette</h5>

                        <div class="mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="primary_color"
                                    value="<?php echo $primary_color; ?>">
                                <input type="text" class="form-control" value="<?php echo $primary_color; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                            <div class="form-text">Used for links, icons, and primary elements.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="secondary_color"
                                    value="<?php echo $secondary_color; ?>">
                                <input type="text" class="form-control" value="<?php echo $secondary_color; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                            <div class="form-text">Used for secondary text and backgrounds.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Button Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="btn_primary_bg"
                                    value="<?php echo $btn_primary_bg; ?>">
                                <input type="text" class="form-control" value="<?php echo $btn_primary_bg; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Button Text Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="btn_primary_text"
                                    value="<?php echo $btn_primary_text; ?>">
                                <input type="text" class="form-control" value="<?php echo $btn_primary_text; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Header Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="header_bg"
                                    value="<?php echo $header_bg; ?>">
                                <input type="text" class="form-control" value="<?php echo $header_bg; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Footer Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="footer_bg"
                                    value="<?php echo $footer_bg; ?>">
                                <input type="text" class="form-control" value="<?php echo $footer_bg; ?>"
                                    onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>
                    </div>

                    <!-- Font Settings -->
                    <div class="col-md-6 ps-md-4">
                        <h5 class="mb-4 text-primary"><i class="fas fa-font me-2"></i> Typography Settings</h5>

                        <div class="mb-3">
                            <label class="form-label">Google Fonts URL</label>
                            <input type="text" class="form-control" name="google_fonts_url"
                                value="<?php echo htmlspecialchars($google_fonts_url); ?>">
                            <div class="form-text">Paste the Google Fonts
                                <link> href URL here.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Body Font Family</label>
                            <input type="text" class="form-control" name="body_font_family"
                                value="<?php echo htmlspecialchars($body_font_family); ?>">
                            <div class="form-text">Example: 'Inter', sans-serif</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Heading Font Family</label>
                            <input type="text" class="form-control" name="heading_font_family"
                                value="<?php echo htmlspecialchars($heading_font_family); ?>">
                            <div class="form-text">Used for H1-H6 and titles. Example: 'Jost', sans-serif</div>
                        </div>

                        <div class="mt-5 p-4 bg-light rounded shadow-sm border">
                            <h6><i class="fas fa-eye me-2"></i> Preview</h6>
                            <p style="font-family: <?php echo $body_font_family; ?>;">This is a preview of the body
                                text. Let's see how it looks.</p>
                            <h4 style="font-family: <?php echo $heading_font_family; ?>;">Sample Heading Content</h4>
                            <button type="button" class="btn btn-primary"
                                style="background-color: <?php echo $btn_primary_bg; ?>; border-color: <?php echo $btn_primary_bg; ?>; color: <?php echo $btn_primary_text; ?>;">Sample
                                Button</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end p-3">
                <button type="submit" name="update_theme" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/sidebar.php'; ?>