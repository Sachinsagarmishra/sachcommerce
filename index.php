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

// Sample banners for hero slider
$hero_banners = [
    [
        'title' => 'Summer Collection 2024',
        'subtitle' => 'Get up to 50% off on selected items',
        'button_text' => 'Shop Now',
        'button_link' => SITE_URL . '/shop.php',
        'bg_color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
    ],
    [
        'title' => 'New Arrivals',
        'subtitle' => 'Discover the latest trends in fashion',
        'button_text' => 'Explore',
        'button_link' => SITE_URL . '/shop.php?filter=new',
        'bg_color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
    ],
    [
        'title' => 'Electronics Sale',
        'subtitle' => 'Premium gadgets at unbeatable prices',
        'button_text' => 'View Deals',
        'button_link' => SITE_URL . '/shop.php?category=electronics',
        'bg_color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ]
];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Custom Homepage Styles -->
<style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Hero Slider */
        .hero-slider {
            position: relative;
            height: 500px;
            overflow: hidden;
        }
        
        .hero-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .hero-slide.active {
            opacity: 1;
        }
        
        .hero-content {
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .hero-btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-transform: uppercase;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .slider-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            gap: 10px;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .slider-dot.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }
        
        .category-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .product-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }
        
        .product-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transform: translateY(-5px);
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f8f9fc;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .product-price-old {
            text-decoration: line-through;
            color: #999;
            font-size: 1rem;
        }
        
        .badge-discount {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 12px;
            z-index: 10;
        }
        
        .add-to-wishlist-btn {
            background: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
        
        .add-to-wishlist-btn.with-new-badge {
            top: 50px;
        }
        
        .add-to-wishlist-btn:hover {
            background: var(--danger-color);
            color: white;
            transform: scale(1.1);
        }
        
        .add-to-wishlist-btn.active {
            background: var(--danger-color);
            color: white;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 10px;
            font-weight: 600;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>

<!-- Hero Slider -->
    <section class="hero-slider">
        <?php foreach ($hero_banners as $index => $banner): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" style="background: <?php echo $banner['bg_color']; ?>;">
            <div class="hero-content">
                <h1><?php echo $banner['title']; ?></h1>
                <p><?php echo $banner['subtitle']; ?></p>
                <a href="<?php echo $banner['button_link']; ?>" class="btn btn-light btn-lg hero-btn">
                    <?php echo $banner['button_text']; ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Slider Controls -->
        <div class="slider-controls">
            <?php foreach ($hero_banners as $index => $banner): ?>
            <span class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="row g-4">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $category['slug']; ?>" class="text-decoration-none">
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
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php if (!empty($featured_products)): ?>
                    <?php foreach ($featured_products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <?php if ($product['discount_percentage'] > 0): ?>
                                <span class="badge-discount"><?php echo $product['discount_percentage']; ?>% OFF</span>
                                <?php endif; ?>
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>">
                                    <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                         class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <?php if (is_logged_in()): ?>
                                <button class="add-to-wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">
                                    <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small flex-grow-1"><?php echo truncate_text($product['short_description'], 60); ?></p>
                                <div class="mt-auto">
                                    <div class="mb-2">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="product-price"><?php echo format_price($product['sale_price']); ?></span>
                                            <span class="product-price-old"><?php echo format_price($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
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
                <a href="<?php echo SITE_URL; ?>/shop.php?filter=new" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($new_arrivals as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <span class="badge bg-success position-absolute top-0 start-0 m-2">NEW</span>
                            <?php if ($product['discount_percentage'] > 0): ?>
                            <span class="badge-discount"><?php echo $product['discount_percentage']; ?>% OFF</span>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                     class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            <?php if (is_logged_in()): ?>
                            <button class="add-to-wishlist-btn with-new-badge" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1"><?php echo truncate_text($product['short_description'], 60); ?></p>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="product-price"><?php echo format_price($product['sale_price']); ?></span>
                                        <span class="product-price-old"><?php echo format_price($product['price']); ?></span>
                                    <?php else: ?>
                                        <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-primary btn-sm w-100 add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
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
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }
        
        // Auto slide
        setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000);
        
        // Dot navigation
        document.addEventListener('DOMContentLoaded', function() {
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                });
            });
        });
    </script>

<?php include 'includes/footer.php'; ?>
