<?php
/**
 * Homepage - TrendsOne eCommerce
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

// Get data only if database is connected
$featured_products = $db_connected ? get_featured_products(8) : [];
$new_arrivals = $db_connected ? get_new_arrivals(8) : [];
$best_sellers = $db_connected ? get_best_sellers(8) : [];
$categories = $db_connected ? get_menu_categories(6) : [];

// Get dynamic banners from database
if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, created_at DESC");
        $db_banners = $stmt->fetchAll();
    } catch (PDOException $e) {
        $db_banners = [];
    }
} else {
    $db_banners = [];
}

// Fallback to sample banners if database is empty or not connected
if (empty($db_banners)) {
    $hero_banners = [
        [
            'title' => 'Summer Collection 2024',
            'subtitle' => 'Get up to 50% off on selected items',
            'link' => SITE_URL . '/shop',
            'image_desktop' => 'https://via.placeholder.com/1920x600?text=Desktop+Banner',
            'image_mobile' => 'https://via.placeholder.com/600x800?text=Mobile+Banner',
            'is_placeholder' => true
        ]
    ];
} else {
    $hero_banners = $db_banners;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Custom Homepage Styles included via header.php -->


<!-- Hero Slider -->
<section class="hero-slider">
    <?php foreach ($hero_banners as $index => $banner): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <?php if (!empty($banner['link'])): ?>
                <a href="<?php echo $banner['link']; ?>"
                    style="display: block; width: 100%; height: 100%; z-index: 5; position: relative;">
                <?php endif; ?>
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
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- Slider Controls -->
    <?php if (count($hero_banners) > 1): ?>
        <div class="slider-controls">
            <?php foreach ($hero_banners as $index => $banner): ?>
                <span class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Categories Section -->
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
                            <?php else: ?>
                                <i class="fas fa-tag"></i>
                            <?php endif; ?>
                        </div>
                        <span class="category-name-circular"><?php echo htmlspecialchars($category['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Announcement Marquee -->
<div class="announcement-marquee">
    <div class="marquee-content">
        <span class="marquee-item">Order by Oct 5th For Guaranteed Diwali Delivery <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">$20 Duty/Tariff Fee - <a href="#" class="text-white text-decoration-underline">Learn
                More</a> <span class="marquee-separator">✦</span></span>
        <span class="marquee-item">Free Shipping On All Orders Above $100 USD <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">Upto 50% Items In Sale <span class="marquee-separator">✦</span></span>
        <!-- Duplicate for loop seamlessness -->
        <span class="marquee-item">Order by Oct 5th For Guaranteed Diwali Delivery <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">$20 Duty/Tariff Fee - <a href="#" class="text-white text-decoration-underline">Learn
                More</a> <span class="marquee-separator">✦</span></span>
        <span class="marquee-item">Free Shipping On All Orders Above $100 USD <span
                class="marquee-separator">✦</span></span>
        <span class="marquee-item">Upto 50% Items In Sale <span class="marquee-separator">✦</span></span>
    </div>
</div>

<!-- Curated for You / Video Section -->
<section class="curated-section">
    <div class="container-fluid px-md-5">
        <h2 class="curated-title">Curated for You!</h2>
        <div class="curated-grid">
            <?php
            // Use featured products for curated section with dummy vertical videos
            $curated_data = array_slice($featured_products, 0, 6);
            if (empty($curated_data))
                $curated_data = array_slice($new_arrivals, 0, 6);

            // Dummy vertical videos (using generic vertical stock footage links for demo)
            $dummy_videos = [
                'https://assets.mixkit.co/videos/preview/mixkit-fashion-model-posing-in-a-red-dress-12503-large.mp4',
                'https://assets.mixkit.co/videos/preview/mixkit-young-woman-walking-on-the-beach-in-a-dress-42523-large.mp4',
                'https://assets.mixkit.co/videos/preview/mixkit-woman-smiling-while-carrying-shopping-bags-41487-large.mp4',
                'https://assets.mixkit.co/videos/preview/mixkit-close-up-of-a-woman-posing-in-a-pink-dress-12499-large.mp4',
                'https://assets.mixkit.co/videos/preview/mixkit-woman-walking-with-a-red-dress-and-a-hat-12501-large.mp4',
                'https://assets.mixkit.co/videos/preview/mixkit-girl-in-white-dress-posing-in-front-of-a-mirror-12505-large.mp4'
            ];

            foreach ($curated_data as $index => $item):
                $video_url = $dummy_videos[$index % count($dummy_videos)];
                ?>
                <div class="curated-card">
                    <div class="curated-video-wrapper">
                        <video class="curated-video" autoplay muted loop playsinline>
                            <source src="<?php echo $video_url; ?>" type="video/mp4">
                        </video>
                    </div>

                    <a href="javascript:void(0)"
                        class="curated-wishlist add-to-wishlist-btn <?php echo is_in_wishlist($item['id']) ? 'active' : ''; ?>"
                        data-product-id="<?php echo $item['id']; ?>">
                        <i class="<?php echo is_in_wishlist($item['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>" class="curated-view">
                        <i class="far fa-eye"></i>
                    </a>

                    <div class="curated-card-overlay"
                        onclick="window.location.href='<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>'">
                        <img src="<?php echo $item['primary_image'] ? PRODUCT_IMAGE_URL . $item['primary_image'] : 'https://via.placeholder.com/100'; ?>"
                            class="curated-thumb" alt="">
                        <div class="curated-info">
                            <p class="curated-name">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </p>
                            <span class="curated-price">
                                <?php if ($item['sale_price']): ?>
                                    <span class="text-white">
                                        <?php echo format_price($item['sale_price']); ?>
                                    </span>
                                    <span class="text-white-50 text-decoration-line-through small ms-1">
                                        <?php echo format_price($item['price']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-white">
                                        <?php echo format_price($item['price']); ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Products</h2>
            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card">
                            <div class="product-image-wrapper">
                                <div class="product-label-group">
                                    <?php if ($product['is_new_arrival']): ?>
                                        <span class="product-label label-new">NEW</span>
                                    <?php endif; ?>
                                    <?php if ($product['sale_price']): ?>
                                        <span
                                            class="product-label label-sale">-<?php echo calculate_discount_percentage($product['price'], $product['sale_price']); ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-action-icons">
                                    <a href="javascript:void(0)"
                                        class="action-icon add-to-wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <i class="<?php echo is_in_wishlist($product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="action-icon">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-icon">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </a>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>">
                                    <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>"
                                        class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <div class="product-add-to-cart-hover">
                                    <button class="btn-hover-cart add-to-cart-btn"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <span>Add to cart</span>
                                        <div class="cart-icon-wrapper">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-brand">Brand: <?php echo SITE_NAME; ?></div>
                                <?php
                                $random_ratings = [4, 4.5, 5];
                                $random_rating = $random_ratings[array_rand($random_ratings)];
                                echo display_rating($random_rating, false);
                                ?>
                                <h6 class="product-name">
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <div class="product-price-wrapper">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="old-price"><?php echo format_price($product['price']); ?></span>
                                        <span class="current-price"><?php echo format_price($product['sale_price']); ?></span>
                                    <?php else: ?>
                                        <span class="current-price"><?php echo format_price($product['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No Featured Products Yet</h4>
                    <p class="text-muted">Import sample products or add products via admin panel</p>
                    <a href="<?php echo SITE_URL; ?>/admin/" class="btn btn-primary">Go to Admin Panel</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<?php if (!empty($new_arrivals)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">New Arrivals</h2>
                <a href="<?php echo SITE_URL; ?>/shop?filter=new" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($new_arrivals as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card">
                            <div class="product-image-wrapper">
                                <div class="product-label-group">
                                    <?php if ($product['is_new_arrival']): ?>
                                        <span class="product-label label-new">NEW</span>
                                    <?php endif; ?>
                                    <?php if ($product['sale_price']): ?>
                                        <span
                                            class="product-label label-sale">-<?php echo calculate_discount_percentage($product['price'], $product['sale_price']); ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-action-icons">
                                    <a href="javascript:void(0)"
                                        class="action-icon add-to-wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <i class="<?php echo is_in_wishlist($product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="action-icon">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-icon">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </a>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>">
                                    <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>"
                                        class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <div class="product-add-to-cart-hover">
                                    <button class="btn-hover-cart add-to-cart-btn"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <span>Add to cart</span>
                                        <div class="cart-icon-wrapper">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-brand">Brand: <?php echo SITE_NAME; ?></div>
                                <?php
                                $random_ratings = [4, 4.5, 5];
                                $random_rating = $random_ratings[array_rand($random_ratings)];
                                echo display_rating($random_rating, false);
                                ?>
                                <h6 class="product-name">
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <div class="product-price-wrapper">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="old-price"><?php echo format_price($product['price']); ?></span>
                                        <span class="current-price"><?php echo format_price($product['sale_price']); ?></span>
                                    <?php else: ?>
                                        <span class="current-price"><?php echo format_price($product['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Why Choose Us?</h2>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>Free Shipping</h5>
                <p class="text-muted">On orders above <?php echo format_price(FREE_SHIPPING_THRESHOLD); ?></p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                <h5>Secure Payment</h5>
                <p class="text-muted">Razorpay integration with COD option</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                <h5>Easy Returns</h5>
                <p class="text-muted">7-day return policy</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-headset fa-3x text-info mb-3"></i>
                <h5>24/7 Support</h5>
                <p class="text-muted">Customer support available</p>
            </div>
        </div>
    </div>
</section>

<!-- Hero Slider Script -->
<script>
    // Hero Slider Functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');

    function showSlide(n) {
        if (!slides || slides.length === 0) return;

        slides.forEach(slide => {
            if (slide && slide.classList) slide.classList.remove('active');
        });
        dots.forEach(dot => {
            if (dot && dot.classList) dot.classList.remove('active');
        });

        currentSlide = (n + slides.length) % slides.length;
        if (slides[currentSlide]) slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    // Auto slide
    if (slides.length > 0) {
        setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000);
    }

    // Dot navigation
    document.addEventListener('DOMContentLoaded', function () {
        if (dots.length > 0) {
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                });
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>