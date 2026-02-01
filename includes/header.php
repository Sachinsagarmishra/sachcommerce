<?php
// Determine the current site URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$current_dir = dirname($script_name);
// SITE_URL is defined in config.php, but we might need to use BASE_PATH or similar
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>

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
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">

    <!-- Global JavaScript Variables -->
    <script>
        var baseSiteUrl = '<?php echo SITE_URL; ?>';
    </script>
</head>

<body>