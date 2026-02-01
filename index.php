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
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="<?php echo SITE_URL; ?>/shop?category=<?php echo $category['slug']; ?>"
                            class="text-decoration-none">
                            <div class="card category-card text-center h-100">
                                <div class="card-body p-4">
                                    <i class="fas fa-tag fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($category['name']); ?></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
                        <div class="card product-card h-100">
                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>"
                                    class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo truncate_text($product['short_description'], 60); ?>
                                </p>
                                <div class="mt-auto">
                                    <div class="mb-2">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="product-price"><?php echo format_price($product['sale_price']); ?></span>
                                            <span class="product-price-old"><?php echo format_price($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <i class="lni lni-cart"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="lni lni-package-open fa-4x text-muted mb-3"></i>
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
                        <div class="card product-card h-100">
                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>"
                                    class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo truncate_text($product['short_description'], 60); ?>
                                </p>
                                <div class="mt-auto">
                                    <div class="mb-2">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="product-price"><?php echo format_price($product['sale_price']); ?></span>
                                            <span class="product-price-old"><?php echo format_price($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                        data-product-id="<?php echo $product['id']; ?>">
                                        <i class="lni lni-cart"></i> Add to Cart
                                    </button>
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