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
        $settings = [
            'primary_color',
            'secondary_color',
            'body_font_family',
            'heading_font_family',
            'google_fonts_url',
            'body_text_color',
            'heading_text_color',
            'navbar_bg',
            'nav_link_color',
            'nav_link_hover_color',
            'product_title_color',
            'product_price_color',
            'sale_badge_bg',
            'new_badge_bg',
            'btn_primary_bg',
            'btn_primary_text',
            'footer_bg',
            'footer_text_color',
            'footer_heading_color'
        ];

        foreach ($settings as $key) {
            if (isset($_POST[$key])) {
                update_site_setting($key, $_POST[$key], 'text', 'theme');
            }
        }

        log_activity($_SESSION['user_id'], 'update_theme_settings', 'Updated all theme settings');
        set_flash_message('success', 'Theme settings updated successfully.');
        redirect('theme-settings.php');
    }
}

// Get current settings with defaults
$primary_color = get_site_setting('primary_color', '#83b735');
$secondary_color = get_site_setting('secondary_color', '#858796');
$body_text_color = get_site_setting('body_text_color', '#333333');
$heading_text_color = get_site_setting('heading_text_color', '#2c3e50');

$body_font_family = get_site_setting('body_font_family', "'Inter', sans-serif");
$heading_font_family = get_site_setting('heading_font_family', "'Jost', sans-serif");
$google_fonts_url = get_site_setting('google_fonts_url', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@400;500;600;700&display=swap');

$navbar_bg = get_site_setting('navbar_bg', '#ffffff');
$nav_link_color = get_site_setting('nav_link_color', '#333333');
$nav_link_hover_color = get_site_setting('nav_link_hover_color', '#83b735');

$product_title_color = get_site_setting('product_title_color', '#2c3e50');
$product_price_color = get_site_setting('product_price_color', '#83b735');
$sale_badge_bg = get_site_setting('sale_badge_bg', '#e74a3b');
$new_badge_bg = get_site_setting('new_badge_bg', '#1cc88a');
$btn_primary_bg = get_site_setting('btn_primary_bg', '#83b735');
$btn_primary_text = get_site_setting('btn_primary_text', '#ffffff');

$footer_bg = get_site_setting('footer_bg', '#2c3e50');
$footer_text_color = get_site_setting('footer_text_color', '#ffffff');
$footer_heading_color = get_site_setting('footer_heading_color', '#ffffff');

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
                        <h1 class="h3 fw-bold mb-1">Theme Customization</h1>
                        <p class="text-muted mb-0">Control your store's aesthetics from one place.</p>
                    </div>
                    <button type="submit" form="themeForm" name="update_theme" class="btn btn-primary btn-lg shadow-sm">
                        <i class="lni lni-save me-2"></i> Save All Changes
                    </button>
                </div>
            </div>

            <?php
            $flash = get_flash_message();
            if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show shadow-sm border-0"
                    role="alert">
                    <i
                        class="lni <?php echo $flash['type'] === 'success' ? 'lni-checkmark-circle' : 'lni-warning'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="themeForm" class="pb-5">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="row g-4">
                    <!-- Left Column: Settings Blocks -->
                    <div class="col-lg-8">

                        <!-- Block 1: Global Branding & Fonts -->
                        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 mt-2">
                                <h5 class="mb-0 fw-bold text-dark"><i class="lni lni-world text-primary me-2"></i>
                                    Website Branding & Typography</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary Brand Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="p_color"
                                                value="<?php echo $primary_color; ?>"
                                                oninput="syncColor(this, 'primary_color')">
                                            <input type="text" class="form-control" name="primary_color"
                                                id="primary_color" value="<?php echo $primary_color; ?>"
                                                oninput="syncColor(this, 'p_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Secondary Brand Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="s_color"
                                                value="<?php echo $secondary_color; ?>"
                                                oninput="syncColor(this, 'secondary_color')">
                                            <input type="text" class="form-control" name="secondary_color"
                                                id="secondary_color" value="<?php echo $secondary_color; ?>"
                                                oninput="syncColor(this, 's_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Body Text Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="bt_color"
                                                value="<?php echo $body_text_color; ?>"
                                                oninput="syncColor(this, 'body_text_color')">
                                            <input type="text" class="form-control" name="body_text_color"
                                                id="body_text_color" value="<?php echo $body_text_color; ?>"
                                                oninput="syncColor(this, 'bt_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Heading Text Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="ht_color"
                                                value="<?php echo $heading_text_color; ?>"
                                                oninput="syncColor(this, 'heading_text_color')">
                                            <input type="text" class="form-control" name="heading_text_color"
                                                id="heading_text_color" value="<?php echo $heading_text_color; ?>"
                                                oninput="syncColor(this, 'ht_color')">
                                        </div>
                                    </div>
                                    <div class="col-12 border-top pt-4">
                                        <label class="form-label fw-semibold">Google Fonts Import URL</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0"
                                            name="google_fonts_url"
                                            value="<?php echo htmlspecialchars($google_fonts_url); ?>">
                                        <div class="form-text mt-2">Example:
                                            <code>https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap</code>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Body Font Family</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            name="body_font_family"
                                            value="<?php echo htmlspecialchars($body_font_family); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Heading Font Family</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            name="heading_font_family"
                                            value="<?php echo htmlspecialchars($heading_font_family); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Block 2: Navbar & Navigation -->
                        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 mt-2">
                                <h5 class="mb-0 fw-bold text-dark"><i class="lni lni-compass text-info me-2"></i> Navbar
                                    & Navigation</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Navbar Background</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="nav_bg"
                                                value="<?php echo $navbar_bg; ?>"
                                                oninput="syncColor(this, 'navbar_bg')">
                                            <input type="text" class="form-control" name="navbar_bg" id="navbar_bg"
                                                value="<?php echo $navbar_bg; ?>" oninput="syncColor(this, 'nav_bg')">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Nav Link Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="nl_color"
                                                value="<?php echo $nav_link_color; ?>"
                                                oninput="syncColor(this, 'nav_link_color')">
                                            <input type="text" class="form-control" name="nav_link_color"
                                                id="nav_link_color" value="<?php echo $nav_link_color; ?>"
                                                oninput="syncColor(this, 'nl_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Nav Link Hover</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="nlh_color"
                                                value="<?php echo $nav_link_hover_color; ?>"
                                                oninput="syncColor(this, 'nav_link_hover_color')">
                                            <input type="text" class="form-control" name="nav_link_hover_color"
                                                id="nav_link_hover_color" value="<?php echo $nav_link_hover_color; ?>"
                                                oninput="syncColor(this, 'nlh_color')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Block 3: Shop & Product Pages -->
                        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 mt-2">
                                <h5 class="mb-0 fw-bold text-dark"><i
                                        class="lni lni-shopping-basket text-success me-2"></i> Shop & Product Pages</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Product Title Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="pt_color"
                                                value="<?php echo $product_title_color; ?>"
                                                oninput="syncColor(this, 'product_title_color')">
                                            <input type="text" class="form-control" name="product_title_color"
                                                id="product_title_color" value="<?php echo $product_title_color; ?>"
                                                oninput="syncColor(this, 'pt_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Product Price Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="pp_color"
                                                value="<?php echo $product_price_color; ?>"
                                                oninput="syncColor(this, 'product_price_color')">
                                            <input type="text" class="form-control" name="product_price_color"
                                                id="product_price_color" value="<?php echo $product_price_color; ?>"
                                                oninput="syncColor(this, 'pp_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Sale Badge BG</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="sb_bg"
                                                value="<?php echo $sale_badge_bg; ?>"
                                                oninput="syncColor(this, 'sale_badge_bg')">
                                            <input type="text" class="form-control" name="sale_badge_bg"
                                                id="sale_badge_bg" value="<?php echo $sale_badge_bg; ?>"
                                                oninput="syncColor(this, 'sb_bg')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">New Badge BG</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="nb_bg"
                                                value="<?php echo $new_badge_bg; ?>"
                                                oninput="syncColor(this, 'new_badge_bg')">
                                            <input type="text" class="form-control" name="new_badge_bg"
                                                id="new_badge_bg" value="<?php echo $new_badge_bg; ?>"
                                                oninput="syncColor(this, 'nb_bg')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Button Background</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="bp_bg"
                                                value="<?php echo $btn_primary_bg; ?>"
                                                oninput="syncColor(this, 'btn_primary_bg')">
                                            <input type="text" class="form-control" name="btn_primary_bg"
                                                id="btn_primary_bg" value="<?php echo $btn_primary_bg; ?>"
                                                oninput="syncColor(this, 'bp_bg')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Button Text Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="bp_t"
                                                value="<?php echo $btn_primary_text; ?>"
                                                oninput="syncColor(this, 'btn_primary_text')">
                                            <input type="text" class="form-control" name="btn_primary_text"
                                                id="btn_primary_text" value="<?php echo $btn_primary_text; ?>"
                                                oninput="syncColor(this, 'bp_t')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Block 4: Footer Customization -->
                        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 mt-2">
                                <h5 class="mb-0 fw-bold text-dark"><i class="lni lni-layers text-warning me-2"></i>
                                    Footer Customization</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Footer Background</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="f_bg"
                                                value="<?php echo $footer_bg; ?>"
                                                oninput="syncColor(this, 'footer_bg')">
                                            <input type="text" class="form-control" name="footer_bg" id="footer_bg"
                                                value="<?php echo $footer_bg; ?>" oninput="syncColor(this, 'f_bg')">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Footer Heading</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="fh_color"
                                                value="<?php echo $footer_heading_color; ?>"
                                                oninput="syncColor(this, 'footer_heading_color')">
                                            <input type="text" class="form-control" name="footer_heading_color"
                                                id="footer_heading_color" value="<?php echo $footer_heading_color; ?>"
                                                oninput="syncColor(this, 'fh_color')">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Footer Text</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="ft_color"
                                                value="<?php echo $footer_text_color; ?>"
                                                oninput="syncColor(this, 'footer_text_color')">
                                            <input type="text" class="form-control" name="footer_text_color"
                                                id="footer_text_color" value="<?php echo $footer_text_color; ?>"
                                                oninput="syncColor(this, 'ft_color')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Interactive Preview -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 100px; z-index: 10;">
                            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                                <div class="card-header bg-dark text-white py-3">
                                    <h5 class="mb-0 fw-bold small text-uppercase"><i
                                            class="lni lni-magic-wand me-2"></i> Live
                                        Interface Preview</h5>
                                </div>
                                <div class="card-body p-0 bg-light">
                                    <!-- Mini Desktop Mockup -->
                                    <div
                                        class="mockup-browser shadow-sm bg-white mx-3 my-4 rounded-3 overflow-hidden border">
                                        <!-- Header -->
                                        <div class="p-2 border-bottom d-flex justify-content-between align-items-center"
                                            id="mock_navbar" style="background-color: <?php echo $navbar_bg; ?>;">
                                            <span class="fw-bold small" id="mock_nav_logo"
                                                style="color: <?php echo $nav_link_color; ?>; font-family: <?php echo $heading_font_family; ?>;">TrendsOne</span>
                                            <div class="d-flex gap-2">
                                                <div class="p-1 rounded-pill"
                                                    style="width: 20px; height: 4px; background-color: <?php echo $nav_link_color; ?>;">
                                                </div>
                                                <div class="p-1 rounded-pill"
                                                    style="width: 20px; height: 4px; background-color: <?php echo $nav_link_hover_color; ?>;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="p-3 bg-white">
                                            <div class="bg-light rounded p-2 mb-2 text-center position-relative">
                                                <span class="badge position-absolute top-0 end-0 m-1"
                                                    id="mock_sale_badge"
                                                    style="font-size: 8px; background-color: <?php echo $sale_badge_bg; ?>;">SALE</span>
                                                <i class="lni lni-image fa-2x text-secondary opacity-25"></i>
                                            </div>
                                            <div class="p-1 rounded-pill mb-1"
                                                style="width: 40px; height: 4px; background-color: <?php echo $secondary_color; ?>;">
                                            </div>
                                            <h6 class="mb-1" id="mock_product_title"
                                                style="font-size: 10px; color: <?php echo $product_title_color; ?>; font-family: <?php echo $heading_font_family; ?>;">
                                                Stylish Autumn Jacket</h6>
                                            <div class="fw-bold mb-2" id="mock_product_price"
                                                style="font-size: 12px; color: <?php echo $product_price_color; ?>;">
                                                $129.00</div>
                                            <div class="btn btn-sm w-100 py-1" id="mock_btn"
                                                style="background-color: <?php echo $btn_primary_bg; ?>; color: <?php echo $btn_primary_text; ?>; font-size: 9px;">
                                                ADD TO BAG</div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="p-2" id="mock_footer"
                                            style="background-color: <?php echo $footer_bg; ?>;">
                                            <div id="mock_footer_h"
                                                style="width: 30px; height: 3px; background-color: <?php echo $footer_heading_color; ?>; margin-bottom: 4px;">
                                            </div>
                                            <div id="mock_footer_t"
                                                style="width: 100%; height: 2px; background-color: <?php echo $footer_text_color; ?>; opacity: 0.5; margin-bottom: 2px;">
                                            </div>
                                            <div id="mock_footer_t2"
                                                style="width: 60%; height: 2px; background-color: <?php echo $footer_text_color; ?>; opacity: 0.5;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-4 pb-4">
                                        <div class="p-3 bg-white rounded-3 border border-dashed text-center">
                                            <p class="small text-muted mb-0">Changes will be visible to all users upon
                                                saving.</p>
                                        </div>
                                    </div>
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
    function syncColor(input, targetId) {
        const target = document.getElementById(targetId);
        if (target) {
            target.value = input.value;
            updateMockup(targetId, input.value);
        }
    }

    function updateMockup(id, color) {
        const mockMap = {
            'navbar_bg': 'mock_navbar',
            'nav_link_color': ['mock_nav_logo', 'mock_nav_logo_parent'], // Complex mapping handled below
            'nav_link_hover_color': null,
            'product_title_color': 'mock_product_title',
            'product_price_color': 'mock_product_price',
            'sale_badge_bg': 'mock_sale_badge',
            'btn_primary_bg': 'mock_btn',
            'btn_primary_text': 'mock_btn_text',
            'footer_bg': 'mock_footer',
            'footer_heading_color': 'mock_footer_h',
            'footer_text_color': ['mock_footer_t', 'mock_footer_t2']
        };

        // Generic mapping
        const mockElement = document.getElementById('mock_' + id);
        if (mockElement) {
            if (id.includes('bg')) {
                mockElement.style.backgroundColor = color;
            } else {
                mockElement.style.color = color;
            }
        }

        // Specific mapping for UI elements
        if (id === 'navbar_bg') document.getElementById('mock_navbar').style.backgroundColor = color;
        if (id === 'nav_link_color') document.getElementById('mock_nav_logo').style.color = color;
        if (id === 'product_title_color') document.getElementById('mock_product_title').style.color = color;
        if (id === 'product_price_color') document.getElementById('mock_product_price').style.color = color;
        if (id === 'sale_badge_bg') document.getElementById('mock_sale_badge').style.backgroundColor = color;
        if (id === 'btn_primary_bg') {
            document.getElementById('mock_btn').style.backgroundColor = color;
            document.getElementById('mock_btn').style.borderColor = color;
        }
        if (id === 'btn_primary_text') document.getElementById('mock_btn').style.color = color;
        if (id === 'footer_bg') document.getElementById('mock_footer').style.backgroundColor = color;
        if (id === 'footer_heading_color') document.getElementById('mock_footer_h').style.backgroundColor = color;
        if (id === 'footer_text_color') {
            document.getElementById('mock_footer_t').style.backgroundColor = color;
            document.getElementById('mock_footer_t2').style.backgroundColor = color;
        }
    }
</script>

<style>
    .form-control-color {
        width: 50px !important;
        padding: 3px !important;
        height: 44px !important;
        border-right: 0 !important;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .rounded-4 {
        border-radius: 1.25rem !important;
    }

    .mockup-browser {
        transition: all 0.3s ease;
    }
</style>