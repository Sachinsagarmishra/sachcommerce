<?php
/**
 * Homepage - TrendsOne eCommerce (Dynamic Version)
 */
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Check database connection
$db_connected = true;
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    $db_connected = false;
}

// Get basic data
$featured_products = $db_connected ? get_featured_products(8) : [];
$new_arrivals = $db_connected ? get_new_arrivals(8) : [];
$best_sellers = $db_connected ? get_best_sellers(8) : [];
$categories = $db_connected ? get_menu_categories(10) : [];

// Get section order from DB
$active_sections = [];
try {
    $stmt = $pdo->query("SELECT section_key FROM homepage_sections WHERE is_active = 1 ORDER BY display_order ASC");
    $active_sections = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    // Default order if table missing
    $active_sections = ['hero', 'categories', 'marquee', 'curated', 'featured', 'new_arrivals', 'best_sellers', 'features'];
}

// Get Curated Items
$curated_db_items = [];
try {
    $stmt = $pdo->query("SELECT c.*, p.name, p.slug, p.price, p.sale_price, p.primary_image 
                         FROM curated_items c 
                         JOIN products p ON c.product_id = p.id 
                         ORDER BY c.display_order ASC");
    $curated_db_items = $stmt->fetchAll();
} catch (Exception $e) {
}

// Get banners
$hero_banners = [];
if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, created_at DESC");
        $hero_banners = $stmt->fetchAll();
    } catch (Exception $e) {
    }
}
if (empty($hero_banners)) {
    $hero_banners = [['title' => 'Collection 2024', 'link' => SITE_URL . '/shop', 'image_desktop' => 'https://via.placeholder.com/1920x600?text=Banner', 'image_mobile' => 'https://via.placeholder.com/600x800', 'is_placeholder' => true]];
}

include 'includes/header.php';
include 'includes/navbar.php';

// Prepare Section Contents
$sections_html = [];

// 1. Hero
ob_start(); ?>
<section class="hero-slider">
    <?php foreach ($hero_banners as $index => $banner): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <?php if (!empty($banner['link'])): ?><a href="<?php echo $banner['link']; ?>"
                    style="display: block; width: 100%; height: 100%; z-index: 5; position: relative;"><?php endif; ?>
                <picture>
                    <?php if (isset($banner['is_placeholder'])): ?>
                        <source media="(max-width: 768px)" srcset="<?php echo $banner['image_mobile']; ?>">
                        <img src="<?php echo $banner['image_desktop']; ?>" alt="Banner">
                    <?php else: ?>
                        <source media="(max-width: 768px)" srcset="<?php echo BANNER_IMAGE_URL . $banner['image_mobile']; ?>">
                        <img src="<?php echo BANNER_IMAGE_URL . $banner['image_desktop']; ?>" alt="Banner">
                    <?php endif; ?>
                </picture>
                <?php if (!empty($banner['link'])): ?>
                </a><?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php if (count($hero_banners) > 1): ?>
        <div class="slider-controls">
            <?php foreach ($hero_banners as $index => $banner): ?>
                <span class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php $sections_html['hero'] = ob_get_clean();

// 2. Categories
ob_start(); ?>
<section class="py-4 bg-light">
    <div class="container">
        <div class="categories-horizontal-wrapper">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo SITE_URL; ?>/shop?category=<?php echo $category['slug']; ?>"
                        class="category-item-circular">
                        <div class="category-image-circle">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?php echo CATEGORY_IMAGE_URL . $category['image']; ?>"
                                    alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php else: ?><i class="fas fa-tag"></i><?php endif; ?>
                        </div>
                        <span class="category-name-circular"><?php echo htmlspecialchars($category['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $sections_html['categories'] = ob_get_clean();

// 3. Marquee
ob_start(); ?>
<div class="announcement-marquee">
    <div class="marquee-content">
        <span class="marquee-item">Order by Oct 5th For Guaranteed Diwali Delivery <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">$20 Duty/Tariff Fee - <a href="#" class="text-white text-decoration-underline">Learn
                More</a> <span class="marquee-separator">✦</span></span>
        <span class="marquee-item">Free Shipping On All Orders Above $100 USD <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">Upto 50% Items In Sale <span class="marquee-separator">✦</span></span>
        <span class="marquee-item">Order by Oct 5th For Guaranteed Diwali Delivery <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">$20 Duty/Tariff Fee - <a href="#" class="text-white text-decoration-underline">Learn
                More</a> <span class="marquee-separator">✦</span></span>
        <span class="marquee-item">Free Shipping On All Orders Above $100 USD <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">Upto 50% Items In Sale <span class="marquee-separator">✦</span></span>
    </div>
</div>
<?php $sections_html['marquee'] = ob_get_clean();

// 4. Curated
ob_start(); ?>
<section class="curated-section">
    <div class="container-fluid px-md-5">
        <h2 class="curated-title">Curated for You!</h2>
        <div class="curated-grid">
            <?php
            $curated_data = !empty($curated_db_items) ? $curated_db_items : [];

            // If empty, show dummy/fallback
            if (empty($curated_data)) {
                $fallback_products = array_slice($featured_products, 0, 6);
                $dummy_videos = [
                    'https://assets.mixkit.co/videos/preview/mixkit-fashion-model-posing-in-a-red-dress-12503-large.mp4',
                    'https://assets.mixkit.co/videos/preview/mixkit-young-woman-walking-on-the-beach-in-a-dress-42523-large.mp4',
                    'https://assets.mixkit.co/videos/preview/mixkit-woman-smiling-while-carrying-shopping-bags-41487-large.mp4',
                    'https://assets.mixkit.co/videos/preview/mixkit-close-up-of-a-woman-posing-in-a-pink-dress-12499-large.mp4',
                    'https://assets.mixkit.co/videos/preview/mixkit-woman-walking-with-a-red-dress-and-a-hat-12501-large.mp4',
                    'https://assets.mixkit.co/videos/preview/mixkit-girl-in-white-dress-posing-in-front-of-a-mirror-12505-large.mp4'
                ];
                foreach ($fallback_products as $idx => $p) {
                    $curated_data[] = array_merge($p, ['video_path' => $dummy_videos[$idx % 6], 'is_external' => true]);
                }
            }

            foreach ($curated_data as $item):
                $video_url = isset($item['is_external']) ? $item['video_path'] : SITE_URL . '/uploads/videos/' . $item['video_path'];
                ?>
                <div class="curated-card">
                    <div class="curated-video-wrapper">
                        <video class="curated-video" autoplay muted loop playsinline>
                            <source src="<?php echo $video_url; ?>" type="video/mp4">
                        </video>
                    </div>
                    <div class="curated-card-overlay"
                        onclick="window.location.href='<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>'">
                        <img src="<?php echo $item['primary_image'] ? PRODUCT_IMAGE_URL . $item['primary_image'] : 'https://via.placeholder.com/100'; ?>"
                            class="curated-thumb" alt="">
                        <div class="curated-info">
                            <p class="curated-name"><?php echo htmlspecialchars($item['name']); ?></p>
                            <span class="curated-price">
                                <?php if ($item['sale_price']): ?>
                                    <span class="text-white"><?php echo format_price($item['sale_price']); ?></span>
                                    <span
                                        class="text-white-50 text-decoration-line-through small ms-1"><?php echo format_price($item['price']); ?></span>
                                <?php else: ?>
                                    <span class="text-white"><?php echo format_price($item['price']); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php $sections_html['curated'] = ob_get_clean();

// 5. Featured
ob_start(); ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Products</h2><a href="<?php echo SITE_URL; ?>/shop"
                class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            <?php if (!empty($featured_products)):
                foreach ($featured_products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card">
                            <?php include 'includes/templates/product-card-body.php'; ?>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No Featured Products Yet</p>
                </div><?php endif; ?>
        </div>
    </div>
</section>
<?php $sections_html['featured'] = ob_get_clean();

// 6. New Arrivals
ob_start();
if (!empty($new_arrivals)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">New Arrivals</h2><a href="<?php echo SITE_URL; ?>/shop?filter=new"
                    class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($new_arrivals as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card">
                            <?php include 'includes/templates/product-card-body.php'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif;
$sections_html['new_arrivals'] = ob_get_clean();

// 7. Best Sellers
ob_start();
if (!empty($best_sellers)): ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Best Sellers</h2><a href="<?php echo SITE_URL; ?>/shop?filter=bestseller"
                    class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($best_sellers as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card">
                            <?php include 'includes/templates/product-card-body.php'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif;
$sections_html['best_sellers'] = ob_get_clean();

// 8. Features
ob_start(); ?>
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3"><i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>Free Shipping</h5>
                <p class="text-muted small">On orders above <?php echo format_price(FREE_SHIPPING_THRESHOLD); ?></p>
            </div>
            <div class="col-md-3"><i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                <h5>Secure Payment</h5>
                <p class="text-muted small">Razorpay integration with COD option</p>
            </div>
            <div class="col-md-3"><i class="fas fa-undo fa-3x text-warning mb-3"></i>
                <h5>Easy Returns</h5>
                <p class="text-muted small">7-day return policy</p>
            </div>
            <div class="col-md-3"><i class="fas fa-headset fa-3x text-info mb-3"></i>
                <h5>24/7 Support</h5>
                <p class="text-muted small">Customer support available</p>
            </div>
        </div>
    </div>
</section>
<?php $sections_html['features'] = ob_get_clean();

// Output Sections in Order
foreach ($active_sections as $key) {
    if (isset($sections_html[$key])) {
        echo $sections_html[$key];
    }
}

include 'includes/footer.php';
?>