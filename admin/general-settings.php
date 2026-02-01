<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'General Settings';
$success = '';
$error = '';

// Create logo upload directory
$logo_dir = '../uploads/logos/';
if (!is_dir($logo_dir)) {
    mkdir($logo_dir, 0755, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $ext = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $logo_name = 'logo_' . time() . '.' . $ext;
                $target = $logo_dir . $logo_name;

                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target)) {
                    update_site_setting('site_logo', $logo_name, 'image', 'general');
                }
            }
        }

        // Handle favicon upload
        if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === 0) {
            $allowed = ['ico', 'png', 'svg'];
            $ext = strtolower(pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $favicon_name = 'favicon_' . time() . '.' . $ext;
                $target = $logo_dir . $favicon_name;

                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $target)) {
                    update_site_setting('site_favicon', $favicon_name, 'image', 'general');
                }
            }
        }

        // Basic Info
        update_site_setting('site_name', $_POST['site_name'] ?? '', 'text', 'general');
        update_site_setting('site_tagline', $_POST['site_tagline'] ?? '', 'text', 'general');
        update_site_setting('support_email', $_POST['support_email'] ?? '', 'text', 'general');
        update_site_setting('support_phone', $_POST['support_phone'] ?? '', 'text', 'general');
        update_site_setting('whatsapp_number', $_POST['whatsapp_number'] ?? '', 'text', 'general');
        update_site_setting('site_address', $_POST['site_address'] ?? '', 'textarea', 'general');

        // Social Media
        update_site_setting('facebook_url', $_POST['facebook_url'] ?? '', 'text', 'social');
        update_site_setting('instagram_url', $_POST['instagram_url'] ?? '', 'text', 'social');
        update_site_setting('twitter_url', $_POST['twitter_url'] ?? '', 'text', 'social');
        update_site_setting('youtube_url', $_POST['youtube_url'] ?? '', 'text', 'social');
        update_site_setting('linkedin_url', $_POST['linkedin_url'] ?? '', 'text', 'social');
        update_site_setting('pinterest_url', $_POST['pinterest_url'] ?? '', 'text', 'social');

        // SEO Settings
        update_site_setting('meta_title', $_POST['meta_title'] ?? '', 'text', 'seo');
        update_site_setting('meta_description', $_POST['meta_description'] ?? '', 'textarea', 'seo');
        update_site_setting('meta_keywords', $_POST['meta_keywords'] ?? '', 'textarea', 'seo');
        update_site_setting('og_image', $_POST['og_image'] ?? '', 'text', 'seo');

        // Analytics & Tracking
        update_site_setting('google_analytics_id', $_POST['google_analytics_id'] ?? '', 'text', 'analytics');
        update_site_setting('google_tag_manager_id', $_POST['google_tag_manager_id'] ?? '', 'text', 'analytics');
        update_site_setting('facebook_pixel_id', $_POST['facebook_pixel_id'] ?? '', 'text', 'analytics');
        update_site_setting('microsoft_clarity_id', $_POST['microsoft_clarity_id'] ?? '', 'text', 'analytics');
        update_site_setting('google_search_console', $_POST['google_search_console'] ?? '', 'textarea', 'analytics');
        update_site_setting('custom_header_scripts', $_POST['custom_header_scripts'] ?? '', 'textarea', 'analytics');
        update_site_setting('custom_footer_scripts', $_POST['custom_footer_scripts'] ?? '', 'textarea', 'analytics');

        $success = 'Settings saved successfully!';
    } catch (Exception $e) {
        $error = 'Error saving settings: ' . $e->getMessage();
    }
}

// Get current settings
$settings = [
    // Basic
    'site_name' => get_site_setting('site_name', SITE_NAME ?? 'TrendsOne'),
    'site_tagline' => get_site_setting('site_tagline', ''),
    'site_logo' => get_site_setting('site_logo', ''),
    'site_favicon' => get_site_setting('site_favicon', ''),
    'support_email' => get_site_setting('support_email', SITE_EMAIL ?? ''),
    'support_phone' => get_site_setting('support_phone', SITE_PHONE ?? ''),
    'whatsapp_number' => get_site_setting('whatsapp_number', ''),
    'site_address' => get_site_setting('site_address', ''),

    // Social
    'facebook_url' => get_site_setting('facebook_url', ''),
    'instagram_url' => get_site_setting('instagram_url', ''),
    'twitter_url' => get_site_setting('twitter_url', ''),
    'youtube_url' => get_site_setting('youtube_url', ''),
    'linkedin_url' => get_site_setting('linkedin_url', ''),
    'pinterest_url' => get_site_setting('pinterest_url', ''),

    // SEO
    'meta_title' => get_site_setting('meta_title', ''),
    'meta_description' => get_site_setting('meta_description', ''),
    'meta_keywords' => get_site_setting('meta_keywords', ''),
    'og_image' => get_site_setting('og_image', ''),

    // Analytics
    'google_analytics_id' => get_site_setting('google_analytics_id', ''),
    'google_tag_manager_id' => get_site_setting('google_tag_manager_id', ''),
    'facebook_pixel_id' => get_site_setting('facebook_pixel_id', ''),
    'microsoft_clarity_id' => get_site_setting('microsoft_clarity_id', ''),
    'google_search_console' => get_site_setting('google_search_console', ''),
    'custom_header_scripts' => get_site_setting('custom_header_scripts', ''),
    'custom_footer_scripts' => get_site_setting('custom_footer_scripts', ''),
];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
    .nav-pills .nav-link {
        color: #495057;
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 5px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .nav-pills .nav-link i {
        width: 24px;
    }

    .settings-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
    }

    .settings-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0 !important;
    }

    .logo-preview {
        max-height: 80px;
        max-width: 200px;
        border: 2px dashed #dee2e6;
        padding: 10px;
        border-radius: 8px;
        background: #f8f9fa;
    }

    .favicon-preview {
        height: 32px;
        width: 32px;
        border: 2px dashed #dee2e6;
        padding: 4px;
        border-radius: 4px;
        background: #f8f9fa;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .input-group-text {
        background: #f8f9fa;
    }

    .social-input .input-group-text {
        width: 45px;
        justify-content: center;
    }

    .social-input .input-group-text.facebook {
        color: #1877f2;
    }

    .social-input .input-group-text.instagram {
        color: #e4405f;
    }

    .social-input .input-group-text.twitter {
        color: #1da1f2;
    }

    .social-input .input-group-text.youtube {
        color: #ff0000;
    }

    .social-input .input-group-text.linkedin {
        color: #0077b5;
    }

    .social-input .input-group-text.pinterest {
        color: #bd081c;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">General Settings</h1>
                <p class="text-muted mb-0">Manage your website settings, branding, SEO, and analytics</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Sidebar Navigation -->
                <div class="col-lg-3 mb-4">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist">
                        <button class="nav-link active text-start" data-bs-toggle="pill" data-bs-target="#basic-info"
                            type="button">
                            <i class="fas fa-store me-2"></i>Basic Information
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#branding"
                            type="button">
                            <i class="fas fa-palette me-2"></i>Logo & Branding
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#social-media"
                            type="button">
                            <i class="fas fa-share-alt me-2"></i>Social Media
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#seo-settings"
                            type="button">
                            <i class="fas fa-search me-2"></i>SEO Settings
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#analytics"
                            type="button">
                            <i class="fas fa-chart-line me-2"></i>Analytics & Tracking
                        </button>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save All Settings
                        </button>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="col-lg-9">
                    <div class="tab-content">
                        <!-- Basic Information -->
                        <div class="tab-pane fade show active" id="basic-info">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-store me-2 text-primary"></i>Basic Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Store/Website Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="site_name"
                                                value="<?php echo htmlspecialchars($settings['site_name']); ?>"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tagline</label>
                                            <input type="text" class="form-control" name="site_tagline"
                                                value="<?php echo htmlspecialchars($settings['site_tagline']); ?>"
                                                placeholder="Your catchy slogan here">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Support Email <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" name="support_email"
                                                    value="<?php echo htmlspecialchars($settings['support_email']); ?>"
                                                    placeholder="support@example.com">
                                            </div>
                                            <small class="text-muted">Displayed on website header, footer, checkout,
                                                etc.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Support Phone <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" name="support_phone"
                                                    value="<?php echo htmlspecialchars($settings['support_phone']); ?>"
                                                    placeholder="+91 98765 43210">
                                            </div>
                                            <small class="text-muted">Displayed on website header, footer, checkout,
                                                etc.</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">WhatsApp Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fab fa-whatsapp text-success"></i></span>
                                                <input type="text" class="form-control" name="whatsapp_number"
                                                    value="<?php echo htmlspecialchars($settings['whatsapp_number']); ?>"
                                                    placeholder="919876543210 (with country code, no +)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Business Address</label>
                                        <textarea class="form-control" name="site_address" rows="3"
                                            placeholder="Full business address"><?php echo htmlspecialchars($settings['site_address']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branding -->
                        <div class="tab-pane fade" id="branding">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-palette me-2 text-primary"></i>Logo & Branding
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Website Logo</label>
                                            <div class="mb-2">
                                                <?php if ($settings['site_logo']): ?>
                                                    <img src="<?php echo SITE_URL; ?>/uploads/logos/<?php echo $settings['site_logo']; ?>"
                                                        class="logo-preview" alt="Current Logo">
                                                <?php else: ?>
                                                    <div
                                                        class="logo-preview d-flex align-items-center justify-content-center text-muted">
                                                        <span>No logo uploaded</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <input type="file" class="form-control" name="site_logo" accept="image/*">
                                            <small class="text-muted">Recommended: PNG with transparent background,
                                                200x60px</small>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Favicon</label>
                                            <div class="mb-2">
                                                <?php if ($settings['site_favicon']): ?>
                                                    <img src="<?php echo SITE_URL; ?>/uploads/logos/<?php echo $settings['site_favicon']; ?>"
                                                        class="favicon-preview" alt="Current Favicon">
                                                <?php else: ?>
                                                    <div
                                                        class="favicon-preview d-flex align-items-center justify-content-center text-muted">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <input type="file" class="form-control" name="site_favicon"
                                                accept=".ico,.png,.svg">
                                            <small class="text-muted">Recommended: 32x32px ICO or PNG</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="tab-pane fade" id="social-media">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-share-alt me-2 text-primary"></i>Social Media
                                        Links</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">Facebook</label>
                                            <div class="input-group">
                                                <span class="input-group-text facebook"><i
                                                        class="fab fa-facebook-f"></i></span>
                                                <input type="url" class="form-control" name="facebook_url"
                                                    value="<?php echo htmlspecialchars($settings['facebook_url']); ?>"
                                                    placeholder="https://facebook.com/yourpage">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">Instagram</label>
                                            <div class="input-group">
                                                <span class="input-group-text instagram"><i
                                                        class="fab fa-instagram"></i></span>
                                                <input type="url" class="form-control" name="instagram_url"
                                                    value="<?php echo htmlspecialchars($settings['instagram_url']); ?>"
                                                    placeholder="https://instagram.com/yourpage">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">Twitter / X</label>
                                            <div class="input-group">
                                                <span class="input-group-text twitter"><i
                                                        class="fab fa-twitter"></i></span>
                                                <input type="url" class="form-control" name="twitter_url"
                                                    value="<?php echo htmlspecialchars($settings['twitter_url']); ?>"
                                                    placeholder="https://twitter.com/yourpage">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">YouTube</label>
                                            <div class="input-group">
                                                <span class="input-group-text youtube"><i
                                                        class="fab fa-youtube"></i></span>
                                                <input type="url" class="form-control" name="youtube_url"
                                                    value="<?php echo htmlspecialchars($settings['youtube_url']); ?>"
                                                    placeholder="https://youtube.com/yourchannel">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">LinkedIn</label>
                                            <div class="input-group">
                                                <span class="input-group-text linkedin"><i
                                                        class="fab fa-linkedin-in"></i></span>
                                                <input type="url" class="form-control" name="linkedin_url"
                                                    value="<?php echo htmlspecialchars($settings['linkedin_url']); ?>"
                                                    placeholder="https://linkedin.com/company/yourcompany">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 social-input">
                                            <label class="form-label">Pinterest</label>
                                            <div class="input-group">
                                                <span class="input-group-text pinterest"><i
                                                        class="fab fa-pinterest-p"></i></span>
                                                <input type="url" class="form-control" name="pinterest_url"
                                                    value="<?php echo htmlspecialchars($settings['pinterest_url']); ?>"
                                                    placeholder="https://pinterest.com/yourpage">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="tab-pane fade" id="seo-settings">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>SEO Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title (Homepage)</label>
                                        <input type="text" class="form-control" name="meta_title"
                                            value="<?php echo htmlspecialchars($settings['meta_title']); ?>"
                                            placeholder="Your Website Name - Short Description" maxlength="60">
                                        <small class="text-muted">Recommended: 50-60 characters. Appears in browser tab
                                            and search results.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control" name="meta_description" rows="3" maxlength="160"
                                            placeholder="A brief description of your website (150-160 characters)"><?php echo htmlspecialchars($settings['meta_description']); ?></textarea>
                                        <small class="text-muted">Recommended: 150-160 characters. Shows in Google
                                            search results.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <textarea class="form-control" name="meta_keywords" rows="2"
                                            placeholder="keyword1, keyword2, keyword3"><?php echo htmlspecialchars($settings['meta_keywords']); ?></textarea>
                                        <small class="text-muted">Comma-separated keywords related to your
                                            business.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">OG Image URL (Social Share Image)</label>
                                        <input type="url" class="form-control" name="og_image"
                                            value="<?php echo htmlspecialchars($settings['og_image']); ?>"
                                            placeholder="https://yoursite.com/images/og-image.jpg">
                                        <small class="text-muted">Recommended: 1200x630px. Shows when shared on social
                                            media.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics & Tracking -->
                        <div class="tab-pane fade" id="analytics">
                            <div class="card settings-card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Analytics &
                                        Tracking</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Google Analytics ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fab fa-google text-warning"></i></span>
                                                <input type="text" class="form-control" name="google_analytics_id"
                                                    value="<?php echo htmlspecialchars($settings['google_analytics_id']); ?>"
                                                    placeholder="G-XXXXXXXXXX or UA-XXXXXXXX-X">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Google Tag Manager ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fas fa-tags text-info"></i></span>
                                                <input type="text" class="form-control" name="google_tag_manager_id"
                                                    value="<?php echo htmlspecialchars($settings['google_tag_manager_id']); ?>"
                                                    placeholder="GTM-XXXXXXX">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Facebook Pixel ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fab fa-facebook text-primary"></i></span>
                                                <input type="text" class="form-control" name="facebook_pixel_id"
                                                    value="<?php echo htmlspecialchars($settings['facebook_pixel_id']); ?>"
                                                    placeholder="XXXXXXXXXXXXXXX">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Microsoft Clarity ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fab fa-microsoft text-info"></i></span>
                                                <input type="text" class="form-control" name="microsoft_clarity_id"
                                                    value="<?php echo htmlspecialchars($settings['microsoft_clarity_id']); ?>"
                                                    placeholder="XXXXXXXXXX">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Google Search Console Verification</label>
                                        <input type="text" class="form-control" name="google_search_console"
                                            value="<?php echo htmlspecialchars($settings['google_search_console']); ?>"
                                            placeholder="<meta name='google-site-verification' content='XXXX' />">
                                        <small class="text-muted">Paste the full meta tag provided by Google Search
                                            Console.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card settings-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-code me-2 text-primary"></i>Custom Scripts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Custom Header Scripts</label>
                                        <textarea class="form-control font-monospace" name="custom_header_scripts"
                                            rows="4"
                                            placeholder="<!-- Add custom scripts here, they will be placed in <head> -->"><?php echo htmlspecialchars($settings['custom_header_scripts']); ?></textarea>
                                        <small class="text-muted">Scripts added here will be placed before &lt;/head&gt;
                                            tag on all pages.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Custom Footer Scripts</label>
                                        <textarea class="form-control font-monospace" name="custom_footer_scripts"
                                            rows="4"
                                            placeholder="<!-- Add custom scripts here, they will be placed before </body> -->"><?php echo htmlspecialchars($settings['custom_footer_scripts']); ?></textarea>
                                        <small class="text-muted">Scripts added here will be placed before &lt;/body&gt;
                                            tag on all pages.</small>
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