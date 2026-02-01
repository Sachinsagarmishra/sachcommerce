<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo isset($meta_description) ? $meta_description : DEFAULT_META_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo isset($meta_keywords) ? $meta_keywords : DEFAULT_META_KEYWORDS; ?>">
    <meta name="author" content="<?php echo SITE_NAME; ?>">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description"
        content="<?php echo isset($meta_description) ? $meta_description : DEFAULT_META_DESCRIPTION; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="<?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta name="twitter:description"
        content="<?php echo isset($meta_description) ? $meta_description : DEFAULT_META_DESCRIPTION; ?>">

    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">

    <!-- Dynamic Theme Settings -->
    <?php
    $primary_color = get_site_setting('primary_color', '#83b735');
    $secondary_color = get_site_setting('secondary_color', '#858796');
    $btn_primary_bg = get_site_setting('btn_primary_bg', '#83b735');
    $btn_primary_text = get_site_setting('btn_primary_text', '#ffffff');
    $header_bg = get_site_setting('header_bg', '#ffffff');
    $footer_bg = get_site_setting('footer_bg', '#2c3e50');

    $body_font = get_site_setting('body_font_family', "'Inter', sans-serif");
    $heading_font = get_site_setting('heading_font_family', "'Jost', sans-serif");
    $fonts_url = get_site_setting('google_fonts_url', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap');
    ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo $fonts_url; ?>" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --primary-color:
                <?php echo $primary_color; ?>
            ;
            --secondary-color:
                <?php echo $secondary_color; ?>
            ;
            --btn-primary-bg:
                <?php echo $btn_primary_bg; ?>
            ;
            --btn-primary-text:
                <?php echo $btn_primary_text; ?>
            ;
            --header-bg:
                <?php echo $header_bg; ?>
            ;
            --footer-bg:
                <?php echo $footer_bg; ?>
            ;
        }

        body {
            font-family:
                <?php echo $body_font; ?>
                !important;
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
        }

        .btn-primary {
            background-color: var(--btn-primary-bg) !important;
            border-color: var(--btn-primary-bg) !important;
            color: var(--btn-primary-text) !important;
        }

        .btn-primary:hover {
            opacity: 0.9;
            background-color: var(--btn-primary-bg) !important;
            filter: brightness(90%);
        }

        .navbar {
            background-color: var(--header-bg) !important;
        }

        .footer {
            background-color: var(--footer-bg) !important;
        }

        .product-price,
        .selling-price {
            color: var(--primary-color) !important;
        }

        .btn-orange-custom,
        .btn-add-cart,
        .product-thumb-item.active {
            background-color: var(--btn-primary-bg) !important;
            border-color: var(--btn-primary-bg) !important;
            color: var(--btn-primary-text) !important;
        }

        .text-orange,
        .countdown-timer,
        .delivery-dates,
        .timeline-point .label,
        .timeline-point .date,
        .timeline-point .icon-circle {
            color: var(--primary-color) !important;
        }

        .timeline-box {
            border-color: var(--primary-color) !important;
            background-color: rgba(var(--primary-color-rgb, 131, 183, 53), 0.05) !important;
        }

        .timeline-point .icon-circle {
            border-color: var(--primary-color) !important;
        }

        .timeline-point.active .icon-circle {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }

        .line-hr {
            background-color: var(--primary-color) !important;
        }
    </style>

    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        window.baseSiteUrl = '<?php echo SITE_URL; ?>';
    </script>
</head>

<body>