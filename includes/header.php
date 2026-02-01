<?php
// Determine the current site URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$current_dir = dirname($script_name);
// SITE_URL is defined in config.php, but we might need to use BASE_PATH or similar

// Fetch General Site Settings
$site_settings = [
    'site_name' => get_site_setting('site_name', SITE_NAME ?? 'TrendsOne'),
    'site_tagline' => get_site_setting('site_tagline', ''),
    'site_logo' => get_site_setting('site_logo', ''),
    'site_favicon' => get_site_setting('site_favicon', ''),
    'support_email' => get_site_setting('support_email', SITE_EMAIL ?? ''),
    'support_phone' => get_site_setting('support_phone', SITE_PHONE ?? ''),
    'whatsapp_number' => get_site_setting('whatsapp_number', ''),
    'site_address' => get_site_setting('site_address', ''),
    // Social Media
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

// Build dynamic page title
$dynamic_title = isset($page_title) ? $page_title . ' - ' . $site_settings['site_name'] :
    ($site_settings['meta_title'] ? $site_settings['meta_title'] : $site_settings['site_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($dynamic_title); ?></title>

    <!-- SEO Meta Tags -->
    <?php if ($site_settings['meta_description']): ?>
        <meta name="description" content="<?php echo htmlspecialchars($site_settings['meta_description']); ?>">
    <?php endif; ?>
    <?php if ($site_settings['meta_keywords']): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($site_settings['meta_keywords']); ?>">
    <?php endif; ?>

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="<?php echo htmlspecialchars($dynamic_title); ?>">
    <?php if ($site_settings['meta_description']): ?>
        <meta property="og:description" content="<?php echo htmlspecialchars($site_settings['meta_description']); ?>">
    <?php endif; ?>
    <?php if ($site_settings['og_image']): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($site_settings['og_image']); ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($dynamic_title); ?>">
    <?php if ($site_settings['meta_description']): ?>
        <meta name="twitter:description" content="<?php echo htmlspecialchars($site_settings['meta_description']); ?>">
    <?php endif; ?>

    <!-- Google Search Console Verification -->
    <?php if ($site_settings['google_search_console']): ?>
        <?php echo $site_settings['google_search_console']; ?>
    <?php endif; ?>

    <!-- Google Tag Manager -->
    <?php if ($site_settings['google_tag_manager_id']): ?>
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || []; w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                }); var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '<?php echo $site_settings['google_tag_manager_id']; ?>');</script>
    <?php endif; ?>

    <!-- Google Analytics -->
    <?php if ($site_settings['google_analytics_id']): ?>
        <script async
            src="https://www.googletagmanager.com/gtag/js?id=<?php echo $site_settings['google_analytics_id']; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?php echo $site_settings['google_analytics_id']; ?>');
        </script>
    <?php endif; ?>

    <!-- Facebook Pixel -->
    <?php if ($site_settings['facebook_pixel_id']): ?>
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo $site_settings['facebook_pixel_id']; ?>');
            fbq('track', 'PageView');
        </script>
    <?php endif; ?>

    <!-- Microsoft Clarity -->
    <?php if ($site_settings['microsoft_clarity_id']): ?>
        <script type="text/javascript">
            (function (c, l, a, r, i, t, y) {
                c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
                t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "<?php echo $site_settings['microsoft_clarity_id']; ?>");
        </script>
    <?php endif; ?>

    <!-- Custom Header Scripts -->
    <?php if ($site_settings['custom_header_scripts']): ?>
        <?php echo $site_settings['custom_header_scripts']; ?>
    <?php endif; ?>

    <?php
    // Helper function for hex to rgb
    function hexToRgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "$r, $g, $b";
    }

    // Fetch Theme Settings
    $primary_color = get_site_setting('primary_color', '#83b735');
    $secondary_color = get_site_setting('secondary_color', '#858796');
    $body_text_color = get_site_setting('body_text_color', '#333333');
    $heading_text_color = get_site_setting('heading_text_color', '#2c3e50');

    $body_font = get_site_setting('body_font_family', "'Inter', sans-serif");
    $heading_font = get_site_setting('heading_font_family', "'Jost', sans-serif");
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

    $primary_rgb = hexToRgb($primary_color);
    ?>

    <!-- Dynamic Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo $google_fonts_url; ?>" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Custom Frontend CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <style>
        :root {
            --primary-color:
                <?php echo $primary_color; ?>
            ;
            --primary-rgb:
                <?php echo $primary_rgb; ?>
            ;
            --secondary-color:
                <?php echo $secondary_color; ?>
            ;
            --dark-color:
                <?php echo $heading_text_color; ?>
            ;
            /* Update dark color to match heading color */
        }

        body {
            font-family:
                <?php echo $body_font; ?>
                !important;
            color:
                <?php echo $body_text_color; ?>
            ;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .section-title,
        .navbar-brand {
            font-family:
                <?php echo $heading_font; ?>
                !important;
            color:
                <?php echo $heading_text_color; ?>
            ;
        }

        /* Navbar Customization */
        .navbar {
            background-color:
                <?php echo $navbar_bg; ?>
                !important;
        }

        .navbar-nav .nav-link {
            color:
                <?php echo $nav_link_color; ?>
                !important;
        }

        .navbar-nav .nav-link:hover {
            color:
                <?php echo $nav_link_hover_color; ?>
                !important;
        }

        .navbar-brand {
            color:
                <?php echo $nav_link_color; ?>
                !important;
        }

        /* Product Card Customization */
        .product-title,
        .product-title a {
            color:
                <?php echo $product_title_color; ?>
                !important;
        }

        .product-price {
            color:
                <?php echo $product_price_color; ?>
                !important;
        }

        .badge-sale,
        .badge-discount {
            background-color:
                <?php echo $sale_badge_bg; ?>
                !important;
        }

        .badge-new {
            background-color:
                <?php echo $new_badge_bg; ?>
                !important;
        }

        /* Button Customization */
        .btn-primary,
        .add-to-cart-btn {
            background-color:
                <?php echo $btn_primary_bg; ?>
                !important;
            border-color:
                <?php echo $btn_primary_bg; ?>
                !important;
            color:
                <?php echo $btn_primary_text; ?>
                !important;
        }

        .btn-primary:hover,
        .add-to-cart-btn:hover {
            opacity: 0.9;
            background-color:
                <?php echo $btn_primary_bg; ?>
                !important;
            border-color:
                <?php echo $btn_primary_bg; ?>
                !important;
        }

        .btn-outline-primary {
            color:
                <?php echo $btn_primary_bg; ?>
                !important;
            border-color:
                <?php echo $btn_primary_bg; ?>
                !important;
        }

        .btn-outline-primary:hover {
            background-color:
                <?php echo $btn_primary_bg; ?>
                !important;
            color:
                <?php echo $btn_primary_text; ?>
                !important;
        }

        /* Footer Customization */
        .footer {
            background-color:
                <?php echo $footer_bg; ?>
                !important;
        }

        .footer h4,
        .footer h5,
        .footer .footer-title,
        .footer .widget-title {
            color:
                <?php echo $footer_heading_color; ?>
                !important;
        }

        .footer p,
        .footer span,
        .footer div,
        .footer li,
        .footer small {
            color:
                <?php echo $footer_text_color; ?>
                !important;
        }

        .footer a {
            color:
                <?php echo $footer_text_color; ?>
                !important;
            opacity: 0.8;
            text-decoration: none;
        }

        .footer a:hover {
            color:
                <?php echo $primary_color; ?>
                !important;
            opacity: 1;
        }

        /* Timeline & Other details */
        .product-price-detail {
            color:
                <?php echo $product_price_color; ?>
            ;
        }
    </style>

    <!-- Favicon -->
    <?php if ($site_settings['site_favicon']): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/uploads/logos/<?php echo $site_settings['site_favicon']; ?>">
    <?php else: ?>
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    <?php endif; ?>

    <!-- Global JavaScript Variables -->
    <script>
        var baseSiteUrl = '<?php echo SITE_URL; ?>';
        var siteSettings = {
            phone: '<?php echo addslashes($site_settings['support_phone']); ?>',
            email: '<?php echo addslashes($site_settings['support_email']); ?>',
            whatsapp: '<?php echo addslashes($site_settings['whatsapp_number']); ?>'
        };
    </script>
</head>

<body>