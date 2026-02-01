<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Get product slug
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('Location: ' . SITE_URL . '/shop');
    exit;
}

// Get product details
$product = get_product_by_slug($slug);

if (!$product) {
    header('Location: ' . SITE_URL . '/404');
    exit;
}

// Get product images
$product_images = get_product_images($product['id']);

// If no images found in product_images table, use primary_image from products table as fallback
if (empty($product_images) && !empty($product['primary_image'])) {
    $product_images[] = [
        'image_path' => $product['primary_image'],
        'is_primary' => 1
    ];
}

// Get product reviews
$reviews = get_product_reviews($product['id'], 3);
$rating_data = get_product_rating($product['id']);

// Get related products
$related_products = get_related_products($product['id'], $product['category_id'], 4);

$page_title = $product['name'];
$meta_description = $product['meta_description'] ?? $product['short_description'];

// Extra CSS for this page
$extra_css = [SITE_URL . '/assets/css/product-detail.css'];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    .product-detail-gallery {
        display: flex;
        gap: 15px;
    }

    .product-thumbnails {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100px;
    }

    .product-thumb-item {
        width: 100px;
        height: 100px;
        border: 1px solid #eee;
        border-radius: 4px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .product-thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-thumb-item.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 5px rgba(var(--primary-color-rgb), 0.3);
    }

    .product-main-image {
        flex: 1;
        border: 1px solid #eee;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        aspect-ratio: 1 / 1 !important;
        width: 100%;
        height: auto;
    }

    .product-main-image img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }

    .product-main-image.zoomed img {
        transform: scale(2);
        cursor: zoom-out;
    }

    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: white;
        border: none;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
    }

    .gallery-nav-btn:hover {
        background: #f8f9fa;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        color: var(--primary-color);
    }

    .gallery-nav-prev {
        left: 15px;
    }

    .gallery-nav-next {
        right: 15px;
    }

    .zoom-toggle-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: white;
        border: none;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 11;
        cursor: pointer;
        color: #333;
    }

    .price-section h2,
    .selling-price {
        color: var(--primary-color);
        font-weight: 700;
    }

    .btn-buy-now {
        background-color: #000;
        color: #fff;
        border: none;
        padding: 12px 25px;
        font-weight: 600;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-buy-now:hover {
        background-color: #333;
        color: #fff;
    }

    .btn-add-cart {
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        padding: 12px 25px;
        font-weight: 600;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-add-cart:hover {
        opacity: 0.9;
        color: #fff;
    }

    .stock-info {
        color: var(--primary-color);
        font-weight: 500;
    }


    .qty-selector {
        max-width: 120px;
    }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-section py-3 bg-light">
    <div class="container text-muted small">
        <a href="<?php echo SITE_URL; ?>" class="text-decoration-none text-muted">Home</a>
        <span class="mx-2">/</span>
        <?php if ($product['category_slug']): ?>
            <a href="<?php echo SITE_URL; ?>/shop?category=<?php echo $product['category_slug']; ?>"
                class="text-decoration-none text-muted"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <span class="mx-2">/</span>
        <?php endif; ?>
        <span class="text-dark"><?php echo htmlspecialchars($product['name']); ?></span>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Gallery -->
        <div class="col-lg-7 mb-4">
            <div class="product-detail-gallery">
                <div class="product-thumbnails d-none d-md-flex">
                    <?php foreach ($product_images as $index => $img): ?>
                        <div class="product-thumb-item <?php echo $index === 0 ? 'active' : ''; ?>"
                            data-image="<?php echo PRODUCT_IMAGE_URL . $img['image_path']; ?>">
                            <img src="<?php echo PRODUCT_IMAGE_URL . $img['image_path']; ?>" alt="Thumb">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="product-main-image" id="mainImageWrapper">
                    <button class="gallery-nav-btn gallery-nav-prev" id="prevImage"><i
                            class="fas fa-chevron-left"></i></button>
                    <button class="gallery-nav-btn gallery-nav-next" id="nextImage"><i
                            class="fas fa-chevron-right"></i></button>
                    <button class="zoom-toggle-btn" id="zoomToggle"><i class="fas fa-expand-alt"></i></button>

                    <img id="mainProductImage"
                        src="<?php echo !empty($product_images) ? PRODUCT_IMAGE_URL . $product_images[0]['image_path'] : 'https://via.placeholder.com/600x600?text=No+Image'; ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>
            <!-- Mobile Thumbnails -->
            <div class="product-thumbnails d-flex d-md-none flex-row mt-3 overflow-auto w-100">
                <?php foreach ($product_images as $index => $img): ?>
                    <div class="product-thumb-item me-2 <?php echo $index === 0 ? 'active' : ''; ?>"
                        data-image="<?php echo PRODUCT_IMAGE_URL . $img['image_path']; ?>"
                        style="min-width: 80px; height: 80px;">
                        <img src="<?php echo PRODUCT_IMAGE_URL . $img['image_path']; ?>" alt="Thumb">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Info -->
        <div class="col-lg-5 product-info-sidebar">
            <h1 class="product-title-serif mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="price-section d-flex align-items-baseline gap-3 mb-3">
                <?php if ($product['sale_price']): ?>
                    <h3 class="selling-price mb-0"><?php echo format_price($product['sale_price']); ?></h3>
                    <span
                        class="regular-price text-muted text-decoration-line-through"><?php echo format_price($product['price']); ?></span>
                <?php else: ?>
                    <h3 class="selling-price mb-0"><?php echo format_price($product['price']); ?></h3>
                <?php endif; ?>
            </div>

            <div class="rating-section mb-3">
                <div class="rating-pill d-inline-flex align-items-center bg-light rounded-pill px-3 py-1 border"
                    style="background-color: #f9f4f1 !important; border-color: #f0e6e0 !important;">
                    <div class="stars me-2">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <span class="fw-bold" style="color: #1a2b4c; font-size: 0.95rem;">(5.0)</span>
                </div>
                <span class="text-muted small ms-2">5 star rating</span>
            </div>

            <div class="short-description mb-4">
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
            </div>

            <?php if ($product['stock_quantity'] > 0): ?>
                <div class="cart-controls-wrapper mb-4">
                    <div class="d-flex gap-2">
                        <div class="quantity-picker-custom d-flex align-items-center">
                            <button class="qty-btn qty-btn-minus">-</button>
                            <input type="number" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>"
                                id="productQuantity" class="qty-input">
                            <button class="qty-btn qty-btn-plus">+</button>
                        </div>
                        <button class="btn btn-orange-custom add-to-cart-btn flex-grow-1"
                            data-product-id="<?php echo $product['id']; ?>">
                            ADD TO CART
                        </button>
                    </div>
                    <button class="btn btn-black-custom buy-now-btn w-100 mt-2"
                        data-product-id="<?php echo $product['id']; ?>">
                        BUY IT NOW
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-danger mb-4">Out of Stock</div>
            <?php endif; ?>

            <?php
            $current_time = time();
            $purchased_date = date('M d');
            $proc_date = date('M d', strtotime('+0 days')) . ' - ' . date('d', strtotime('+1 days'));
            $del_date = date('M d', strtotime('+2 days')) . ' - ' . date('d', strtotime('+3 days'));
            $del_range_full = date('M d, Y', strtotime('+2 days')) . ' to ' . date('M d, Y', strtotime('+3 days'));

            // Random countdown for effect (e.g. ends at end of day or next hour)
            $seconds_left = (24 * 3600) - (time() % (24 * 3600));
            $hours = floor($seconds_left / 3600);
            $mins = floor(($seconds_left % 3600) / 60);
            $secs = $seconds_left % 60;
            $countdown_formatted = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            ?>

            <div class="delivery-message mb-3">
                <p class="mb-1"><i class="far fa-clock me-2"></i>Order today within <span
                        class="countdown-timer text-orange fw-bold"
                        id="productCountdown"><?php echo $countdown_formatted; ?></span>, you'll receive your package
                    between
                    <span class="delivery-dates text-orange fw-bold"><?php echo $del_range_full; ?></span>
                </p>
            </div>

            <!-- Timeline Box -->
            <div class="timeline-box p-3 mb-4">
                <div class="timeline-line">
                    <div class="timeline-point active">
                        <div class="icon-circle"><i class="fas fa-shopping-bag"></i></div>
                        <span class="label">Purchased</span>
                        <span class="date"><?php echo $purchased_date; ?></span>
                    </div>
                    <div class="timeline-point">
                        <div class="icon-circle"><i class="fas fa-truck"></i></div>
                        <span class="label">Processing</span>
                        <span class="date"><?php echo $proc_date; ?></span>
                    </div>
                    <div class="timeline-point">
                        <div class="icon-circle"><i class="fas fa-map-marker-alt"></i></div>
                        <span class="label">Delivered</span>
                        <span class="date"><?php echo $del_date; ?></span>
                    </div>
                    <div class="line-hr"></div>
                </div>
            </div>

            <!-- Viewing Count Section -->
            <div class="viewing-count mb-4 py-2">
                <p class="mb-0"><span class="me-2">👀</span><span class="count-num">
                        <?php echo rand(15, 45); ?>
                    </span> <span class="text-dark fw-bold">customers are viewing this product</span></p>
            </div>

            <!-- Vendor & SKU Grid -->
            <div class="vendor-sku-grid d-flex gap-5 mb-4 py-3 border-top border-bottom">
                <div class="meta-item">
                    <span class="text-muted">Vendor:</span>
                    <span class="fw-bold ms-2 text-dark"><?php echo SITE_NAME; ?></span>
                </div>
                <div class="meta-item">
                    <span class="text-muted">Sku:</span>
                    <span class="fw-bold ms-2 text-dark"><?php echo htmlspecialchars($product['sku']); ?></span>
                </div>
            </div>

            <!-- Safe Checkout Section -->
            <div class="safe-checkout-section mb-4">
                <div class="safe-checkout-heading text-center mb-3">
                    <span class="px-3 bg-white h5 fw-normal">Guarantee safe checkout</span>
                </div>
                <div class="safe-checkout-box p-3 border rounded text-center">
                    <img src="<?php echo SITE_URL; ?>/uploads/img/safecheckout.png" alt="Safe Checkout Badges"
                        class="img-fluid">
                </div>
            </div>

            <div class="product-footer-meta small py-3">
                <div class="share-links-custom d-flex align-items-center gap-3">
                    <span class="text-muted"><strong>Share:</strong></span>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-telegram-plane"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="product-description-section mb-5">
                <h4 class="fw-bold mb-4">Description</h4>
                <div class="description-content">
                    <?php echo nl2br(htmlspecialchars($product['long_description'])); ?>
                </div>
            </div>

            <hr class="my-5">

            <div id="reviews" class="product-reviews-section">
                <!-- 1. Review Summary & Action -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Customer Reviews</h4>
                        <div class="d-flex align-items-center">
                            <?php echo display_rating($rating_data['avg_rating'] ?? 0, false); ?>
                            <span class="ms-2 fw-bold">5.0 out of 5</span>
                            <span class="ms-2 text-muted small">(5 star rating)</span>
                        </div>
                    </div>
                    <?php if (is_logged_in()): ?>
                        <button class="btn btn-primary rounded-pill px-4" type="button" data-bs-toggle="collapse"
                            data-bs-target="#writeReviewCollapse">
                            Write a Review
                        </button>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login" class="btn btn-outline-primary rounded-pill px-4">
                            Login to Review
                        </a>
                    <?php endif; ?>
                </div>

                <!-- 2. Reviews with Images Gallery -->
                <?php
                $all_review_images = [];
                foreach ($reviews as $rev) {
                    if (!empty($rev['images'])) {
                        foreach ($rev['images'] as $img) {
                            $all_review_images[] = $img;
                        }
                    }
                }
                ?>
                <?php if (!empty($all_review_images)): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Customer Photos</h6>
                        <div class="d-flex gap-2 overflow-auto pb-2 gallery-scroll-container"
                            style="scrollbar-width: thin;">
                            <?php foreach ($all_review_images as $img): ?>
                                <div class="flex-shrink-0"
                                    style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #eee;">
                                    <a href="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>" target="_blank">
                                        <img src="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="Review Image">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. Review Submission Form (Collapsed) -->
                <?php if (is_logged_in()): ?>
                    <div class="collapse mb-5" id="writeReviewCollapse">
                        <div class="p-4 bg-light rounded-4 border">
                            <h5 class="fw-bold mb-4">Submit Your Review</h5>
                            <form id="reviewForm" enctype="multipart/form-data">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                <div class="mb-3">
                                    <label class="form-label d-block fw-bold small">RATING</label>
                                    <div class="rating-input d-inline-flex flex-row-reverse border p-2 rounded bg-white">
                                        <input type="radio" name="rating" value="5" id="star5" required>
                                        <label for="star5" class="px-1"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4" class="px-1"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3" class="px-1"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2" class="px-1"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1" class="px-1"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">YOUR COMMENT</label>
                                    <textarea class="form-control" name="review_text" rows="4"
                                        placeholder="How was your experience?" required></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small">ATTACH PHOTOS (MAX 5)</label>
                                    <input type="file" class="form-control" name="review_images[]" multiple
                                        accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary px-5 rounded-pill" id="submitReviewBtn">Submit
                                    Review</button>
                                <div id="reviewMessage" class="mt-3"></div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 4. Filter and List -->
                <div class="reviews-list-container">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0">Top reviews</h5>
                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold text-muted mb-0">SORT BY:</label>
                            <select class="form-select form-select-sm rounded-pill px-3" id="reviewFilter"
                                style="width: auto; min-width: 160px;">
                                <option value="recent">Most Recent</option>
                                <option value="highest">Highest Rating</option>
                                <option value="lowest">Lowest Rating</option>
                                <option value="helpful">Most Helpful</option>
                                <option value="pics">Only Pictures</option>
                                <option value="pics_first">Pictures First</option>
                            </select>
                        </div>
                    </div>

                    <div id="reviewsList">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <?php
                                $display_name = $review['user_name'];
                                if ($display_name === 'Admin User')
                                    $display_name = 'Customer';
                                ?>
                                <div class="mb-4 pb-4 border-bottom">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($display_name); ?></span>
                                        <small
                                            class="text-muted"><?php echo date('d M Y', strtotime($review['created_at'])); ?></small>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo display_rating($review['rating'], false); ?>
                                    </div>
                                    <p class="mb-3 text-secondary">
                                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                    </p>

                                    <?php if (!empty($review['images'])): ?>
                                        <div class="d-flex gap-2 flex-wrap mb-3">
                                            <?php foreach ($review['images'] as $img): ?>
                                                <div
                                                    style="width: 70px; height: 70px; border-radius: 6px; overflow: hidden; border: 1px solid #ddd;">
                                                    <a href="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                        target="_blank">
                                                        <img src="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                            style="width: 100%; height: 100%; object-fit: cover;" alt="Review Image">
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($review['admin_reply'])): ?>
                                        <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                            <span class="d-block fw-bold small text-primary mb-1">Response from
                                                TrendsOne:</span>
                                            <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p class="text-muted">No reviews yet for this product.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination Container -->
                    <div id="reviewPagination" class="d-flex justify-content-center mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-3">
                        <div class="card product-card">
                            <div class="product-image-wrapper">
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $related['slug']; ?>">
                                    <img src="<?php echo $related['primary_image'] ? PRODUCT_IMAGE_URL . $related['primary_image'] : 'https://via.placeholder.com/300x250'; ?>"
                                        class="product-image" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <h6 class="product-title">
                                    <a href="<?php echo SITE_URL; ?>/products/<?php echo $related['slug']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($related['name']); ?>
                                    </a>
                                </h6>
                                <div class="product-price">
                                    <?php if ($related['sale_price']): ?>
                                        <?php echo format_price($related['sale_price']); ?>
                                        <span class="product-price-old"><?php echo format_price($related['price']); ?></span>
                                    <?php else: ?>
                                        <?php echo format_price($related['price']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="product-rating mt-2">
                                    <i class="fas fa-star text-warning small"></i>
                                    <i class="fas fa-star text-warning small"></i>
                                    <i class="fas fa-star text-warning small"></i>
                                    <i class="fas fa-star text-warning small"></i>
                                    <i class="fas fa-star text-warning small"></i>
                                    <span class="text-muted small ms-1">(5.0)</span>
                                </div>
                            </div>
                            <div class="product-footer">
                                <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                    data-product-id="<?php echo $related['id']; ?>">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Image Gallery Logic
        const mainImage = document.getElementById('mainProductImage');
        const mainImageWrapper = document.getElementById('mainImageWrapper');
        const thumbnails = document.querySelectorAll('.product-thumb-item');
        const prevBtn = document.getElementById('prevImage');
        const nextBtn = document.getElementById('nextImage');
        const zoomBtn = document.getElementById('zoomToggle');

        let currentIndex = 0;
        const thumbnailArray = Array.from(thumbnails);
        const images = thumbnailArray.map(t => t.getAttribute('data-image'));

        if (images.length === 0 && mainImage) {
            const singleImg = mainImage.getAttribute('src');
            if (singleImg) images.push(singleImg);
        }

        function updateMainImage(index) {
            if (images.length === 0) return;
            currentIndex = index;
            const newSrc = images[currentIndex];
            mainImage.src = newSrc;

            // Sync zoom origin reset
            mainImage.style.transformOrigin = 'center center';

            // Update thumbnails active state
            thumbnailArray.forEach((t, i) => {
                if (i === index) t.classList.add('active');
                else t.classList.remove('active');
            });

            // Re-sync with mobile/desktop thumbs
            document.querySelectorAll(`.product-thumb-item[data-image="${newSrc}"]`).forEach(t => t.classList.add('active'));
        }

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => updateMainImage(index));
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                let nextIndex = currentIndex - 1;
                if (nextIndex < 0) nextIndex = images.length - 1;
                updateMainImage(nextIndex);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                let nextIndex = currentIndex + 1;
                if (nextIndex >= images.length) nextIndex = 0;
                updateMainImage(nextIndex);
            });
        }

        // Zoom Logic
        function toggleZoom(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (!mainImageWrapper) return;
            mainImageWrapper.classList.toggle('zoomed');
            if (zoomBtn) {
                const icon = zoomBtn.querySelector('i');
                if (mainImageWrapper.classList.contains('zoomed')) {
                    icon.className = 'fas fa-compress-alt';
                } else {
                    icon.className = 'fas fa-expand-alt';
                }
            }
        }

        if (zoomBtn) zoomBtn.addEventListener('click', toggleZoom);
        if (mainImage) mainImage.addEventListener('click', toggleZoom);

        // Zoom mouse follow
        if (mainImageWrapper) {
            mainImageWrapper.addEventListener('mousemove', function (e) {
                if (this.classList.contains('zoomed') && mainImage) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const xPerc = (x / rect.width) * 100;
                    const yPerc = (y / rect.height) * 100;

                    mainImage.style.transformOrigin = `${xPerc}% ${yPerc}%`;
                }
            });

            mainImageWrapper.addEventListener('mouseleave', function () {
                if (this.classList.contains('zoomed')) {
                    toggleZoom();
                }
            });
        }

        function shareProduct() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($product['name']); ?>',
                    text: '<?php echo addslashes($product['short_description']); ?>',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                if (typeof showToast === 'function') {
                    showToast('Success', 'Link copied to clipboard!', 'success');
                }
            }
        }

        // Real-time Countdown Timer
        function updateCountdown() {
            const countdownElement = document.getElementById('productCountdown');
            if (!countdownElement) return;

            let curContent = countdownElement.innerText.trim();
            let timeParts = curContent.split(':');
            if (timeParts.length !== 3) return;

            let hours = parseInt(timeParts[0]);
            let minutes = parseInt(timeParts[1]);
            let seconds = parseInt(timeParts[2]);

            let totalSeconds = hours * 3600 + minutes * 60 + seconds;

            if (totalSeconds <= 0) {
                countdownElement.innerText = "00:00:00";
                return;
            }

            totalSeconds--;

            let h = Math.floor(totalSeconds / 3600);
            let m = Math.floor((totalSeconds % 3600) / 60);
            let s = totalSeconds % 60;

            countdownElement.innerText =
                (h < 10 ? '0' + h : h) + ':' +
                (m < 10 ? '0' + m : m) + ':' +
                (s < 10 ? '0' + s : s);
        }
        setInterval(updateCountdown, 1000);

        // Sub-elements initialization
        const qtyInput = document.getElementById('productQuantity');
        const minusBtn = document.querySelector('.qty-btn-minus');
        const plusBtn = document.querySelector('.qty-btn-plus');

        if (qtyInput && minusBtn && plusBtn) {
            minusBtn.addEventListener('click', function () {
                let val = parseInt(qtyInput.value);
                if (val > 1) qtyInput.value = val - 1;
            });

            plusBtn.addEventListener('click', function () {
                let val = parseInt(qtyInput.value);
                let max = parseInt(qtyInput.getAttribute('max')) || 99;
                if (val < max) qtyInput.value = val + 1;
            });
        }

        // Review Form Submission
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitReviewBtn');
                const messageDiv = document.getElementById('reviewMessage');
                const formData = new FormData(this);

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
                messageDiv.innerHTML = '';

                fetch('<?php echo SITE_URL; ?>/api/submit-review.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            reviewForm.reset();
                        } else {
                            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageDiv.innerHTML = `<div class="alert alert-danger">Something went wrong. Please try again later.</div>`;
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Review';
                    });
            });
        }

        // Review Filtering and Pagination
        const reviewsList = document.getElementById('reviewsList');
        const reviewFilter = document.getElementById('reviewFilter');
        const reviewPagination = document.getElementById('reviewPagination');
        const productId = <?php echo $product['id']; ?>;
        const siteUrl = '<?php echo SITE_URL; ?>';

        function loadReviews(page = 1) {
            const filter = reviewFilter ? reviewFilter.value : 'recent';
            reviewsList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

            fetch(`${siteUrl}/api/get-reviews.php?product_id=${productId}&filter=${filter}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderReviews(data.reviews);
                        renderPagination(data.pagination);
                    } else {
                        reviewsList.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching reviews:', error);
                    reviewsList.innerHTML = '<div class="alert alert-danger">Failed to load reviews.</div>';
                });
        }

        function renderReviews(reviews) {
            if (reviews.length === 0) {
                reviewsList.innerHTML = '<div class="text-center py-5"><p class="text-muted">No reviews found matching your criteria.</p></div>';
                return;
            }

            let html = '';
            reviews.forEach(review => {
                let imagesHtml = '';
                if (review.images && review.images.length > 0) {
                    imagesHtml = '<div class="d-flex gap-2 flex-wrap mb-3">';
                    review.images.forEach(img => {
                        imagesHtml += `
                        <div style="width: 70px; height: 70px; border-radius: 6px; overflow: hidden; border: 1px solid #ddd;">
                            <a href="${siteUrl}/uploads/reviews/${img}" target="_blank">
                                <img src="${siteUrl}/uploads/reviews/${img}" style="width: 100%; height: 100%; object-fit: cover;" alt="Review Image">
                            </a>
                        </div>`;
                    });
                    imagesHtml += '</div>';
                }

                let replyHtml = '';
                if (review.admin_reply) {
                    replyHtml = `
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary mt-3">
                        <span class="d-block fw-bold small text-primary mb-1">Response from TrendsOne:</span>
                        <p class="mb-0 small">${review.admin_reply}</p>
                    </div>`;
                }

                html += `
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold text-dark">${review.user_name}</span>
                        <small class="text-muted">${review.formatted_date}</small>
                    </div>
                    <div class="mb-2">
                        ${review.rating_html}
                    </div>
                    <p class="mb-3 text-secondary">${review.review_text.replace(/\n/g, '<br>')}</p>
                    ${imagesHtml}
                    ${replyHtml}
                </div>`;
            });
            reviewsList.innerHTML = html;
        }

        function renderPagination(pagination) {
            const { current_page, total_pages } = pagination;
            if (total_pages < 1) {
                reviewPagination.innerHTML = '';
                return;
            }

            let html = '<nav><ul class="pagination pagination-sm">';
            html += `
            <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link rounded-circle mx-1" href="#" data-page="${current_page - 1}">&laquo;</a>
            </li>`;

            for (let i = 1; i <= total_pages; i++) {
                html += `
                <li class="page-item ${current_page === i ? 'active' : ''}">
                    <a class="page-link rounded-circle mx-1" href="#" data-page="${i}">${i}</a>
                </li>`;
            }

            html += `
            <li class="page-item ${current_page === total_pages ? 'disabled' : ''}">
                <a class="page-link rounded-circle mx-1" href="#" data-page="${current_page + 1}">&raquo;</a>
            </li>`;

            html += '</ul></nav>';
            reviewPagination.innerHTML = html;

            reviewPagination.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page && page !== current_page) {
                        loadReviews(page);
                        document.getElementById('reviewsTabContent')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        }

        if (reviewFilter) {
            reviewFilter.addEventListener('change', () => loadReviews(1));
        }

        loadReviews(1);
    });
</script>

<?php include 'includes/footer.php'; ?>