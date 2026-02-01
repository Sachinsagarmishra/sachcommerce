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
$reviews = get_product_reviews($product['id'], 5);
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
        border-radius: 8px;
        overflow: hidden;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-main-image img {
        max-width: 100%;
        max-height: 600px;
        transition: transform 0.5s ease;
    }

    .product-main-image:hover img {
        transform: scale(1.05);
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
                <div class="product-main-image">
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
                <div class="d-flex align-items-center">
                    <div class="stars me-2">
                        <?php
                        $rating = get_product_rating($product['id']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <span class="text-muted small">No reviews</span>
                </div>
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
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews
                        (<?php echo $rating_data['total_reviews'] ?? 0; ?>)</a>
                </li>
            </ul>

            <div class="tab-content p-4 border border-top-0">
                <!-- Description Tab -->
                <div id="description" class="tab-pane fade show active">
                    <?php echo nl2br(htmlspecialchars($product['long_description'])); ?>
                </div>

                <!-- Reviews Tab -->
                <div id="reviews" class="tab-pane fade">
                    <div class="row">
                        <!-- Left Summary (Amazon Style) -->
                        <div class="col-lg-4 mb-4">
                            <h4 class="fw-bold mb-3">Customer reviews</h4>
                            <div class="d-flex align-items-center mb-2">
                                <?php echo display_rating($rating_data['avg_rating'] ?? 0, false); ?>
                                <span
                                    class="ms-2 fw-bold h5 mb-0"><?php echo number_format($rating_data['avg_rating'] ?? 0, 1); ?>
                                    out of 5</span>
                            </div>
                            <p class="text-muted small"><?php echo $rating_data['total_reviews'] ?? 0; ?> global ratings
                            </p>

                            <hr>

                            <!-- Write a product review section -->
                            <div class="mt-4">
                                <h5 class="fw-bold">Review this product</h5>
                                <p class="text-muted small">Share your thoughts with other customers</p>
                                <?php if (is_logged_in()): ?>
                                    <button class="btn btn-outline-dark w-100 rounded-pill py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#writeReviewCollapse">
                                        Write a product review
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo SITE_URL; ?>/login"
                                        class="btn btn-outline-dark w-100 rounded-pill py-2">
                                        Login to write a review
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Content: Images Gallery & Reviews List -->
                        <div class="col-lg-8">
                            <?php
                            // Collect all approved images for gallery
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
                                <div class="reviews-gallery-container mb-4">
                                    <h5 class="fw-bold mb-3">Reviews with images</h5>
                                    <div class="gallery-grid">
                                        <?php foreach ($all_review_images as $img): ?>
                                            <div class="gallery-item" style="min-width: 100px; height: 100px;">
                                                <a href="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                    target="_blank">
                                                    <img src="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                        alt="Customer Photo">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Review Submission Form (Collapsed) -->
                            <?php if (is_logged_in()): ?>
                                <div class="collapse mb-4" id="writeReviewCollapse">
                                    <div class="card card-body border-0 bg-light rounded-4 shadow-sm">
                                        <h5 class="fw-bold mb-4">Create Review</h5>
                                        <form id="reviewForm" enctype="multipart/form-data">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-uppercase">Overall
                                                    Rating</label>
                                                <div class="rating-input">
                                                    <input type="radio" name="rating" value="5" id="star5" required>
                                                    <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="4" id="star4">
                                                    <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="3" id="star3">
                                                    <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="2" id="star2">
                                                    <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="1" id="star1">
                                                    <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-uppercase">Review
                                                    Comment</label>
                                                <textarea class="form-control border-0 bg-white" name="review_text" rows="4"
                                                    placeholder="Share your experience with this product..."
                                                    required></textarea>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold small text-uppercase">Upload Photos
                                                    (Optional)</label>
                                                <input type="file" class="form-control border-0" name="review_images[]"
                                                    multiple accept="image/png, image/jpeg, image/webp">
                                                <div class="form-text small">Select up to 5 images (PNG, JPG, WEBP). Max 5MB
                                                    each.</div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-danger px-4 rounded-pill"
                                                    id="submitReviewBtn">
                                                    Submit Review
                                                </button>
                                                <button class="btn btn-light px-4 rounded-pill" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#writeReviewCollapse">
                                                    Cancel
                                                </button>
                                            </div>

                                            <div id="reviewMessage" class="mt-3"></div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Reviews List -->
                            <div class="reviews-list-container">
                                <h5 class="fw-bold mb-4">Top reviews</h5>
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item mb-4 border-bottom pb-4">
                                            <div class="review-user-info d-flex align-items-center mb-2">
                                                <span
                                                    class="fw-bold"><?php echo htmlspecialchars($review['user_name']); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <?php echo display_rating($review['rating'], false); ?>
                                                <?php if (!empty($review['title'])): ?>
                                                    <span
                                                        class="ms-2 fw-bold"><?php echo htmlspecialchars($review['title']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="review-date mb-2">
                                                Reviewed on <?php echo date('d F Y', strtotime($review['created_at'])); ?>
                                            </div>
                                            <p class="review-text mb-2">
                                                <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                            </p>

                                            <?php if (!empty($review['images'])): ?>
                                                <div class="review-images-flex mb-3">
                                                    <?php foreach ($review['images'] as $img): ?>
                                                        <a href="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                            target="_blank" class="gallery-item-small">
                                                            <img src="<?php echo SITE_URL; ?>/uploads/reviews/<?php echo $img; ?>"
                                                                class="review-img-thumb" style="width: 60px; height: 60px;"
                                                                alt="Review Image">
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($review['admin_reply'])): ?>
                                                <div
                                                    class="admin-reply-box bg-light p-3 rounded-3 border-start border-4 border-primary">
                                                    <span class="admin-reply-label d-block fw-bold small text-primary mb-1">Response
                                                        from TrendsOne:</span>
                                                    <p class="mb-0 small">
                                                        <?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5 bg-light rounded-4">
                                        <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No reviews yet. Be the first to share your experience!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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

    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($product['name']); ?>',
                text: '<?php echo addslashes($product['short_description']); ?>',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href);
            if (typeof showToast === 'function') {
                showToast('Success', 'Link copied to clipboard!', 'success');
            } else {
                alert('Link copied to clipboard!');
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
            // Reset to 24h or stop
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

    // Quantity Picker Logic
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>

<?php include 'includes/footer.php'; ?>