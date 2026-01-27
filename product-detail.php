<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Get product slug
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get product details
$product = get_product_by_slug($slug);

if (!$product) {
    header('Location: ' . SITE_URL . '/404.php');
    exit;
}

// Get product images - using primary_image from products table
// If you want multiple images, populate product_images table later
$product_images = [];
if (!empty($product['primary_image'])) {
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

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/shop.php">Shop</a></li>
                <?php if ($product['category_slug']): ?>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<div class="container my-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-detail-images">
                <!-- Main Image -->
                <div class="main-image-wrapper mb-3">
                    <img id="mainProductImage" 
                         src="<?php echo !empty($product_images) ? PRODUCT_IMAGE_URL . $product_images[0]['image_path'] : 'https://via.placeholder.com/600x600?text=No+Image'; ?>" 
                         class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <!-- Thumbnail Images -->
                <?php if (count($product_images) > 1): ?>
                <div class="row g-2">
                    <?php foreach ($product_images as $index => $image): ?>
                    <div class="col-3">
                        <img src="<?php echo PRODUCT_IMAGE_URL . $image['image_path']; ?>" 
                             class="img-fluid rounded product-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-image="<?php echo PRODUCT_IMAGE_URL . $image['image_path']; ?>"
                             style="cursor: pointer; border: 2px solid <?php echo $index === 0 ? 'var(--primary-color)' : 'transparent'; ?>;"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6">
            <h1 class="h2 mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <!-- Rating -->
            <div class="mb-3">
                <?php echo display_rating($rating_data['avg_rating'] ?? 0); ?>
                <span class="text-muted ms-2">(<?php echo $rating_data['total_reviews'] ?? 0; ?> reviews)</span>
            </div>
            
            <!-- Price -->
            <div class="mb-4">
                <?php if ($product['sale_price']): ?>
                    <h3 class="text-primary mb-0">
                        <?php echo format_price($product['sale_price']); ?>
                        <span class="h5 text-muted text-decoration-line-through ms-2"><?php echo format_price($product['price']); ?></span>
                        <span class="badge bg-danger ms-2"><?php echo $product['discount_percentage']; ?>% OFF</span>
                    </h3>
                <?php else: ?>
                    <h3 class="text-primary mb-0"><?php echo format_price($product['price']); ?></h3>
                <?php endif; ?>
                <small class="text-muted">Inclusive of all taxes</small>
            </div>
            
            <!-- Short Description -->
            <p class="lead"><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
            
            <!-- Stock Status -->
            <div class="mb-4">
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $product['stock_quantity']; ?> available)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Add to Cart Form -->
            <?php if ($product['stock_quantity'] > 0): ?>
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-auto">
                        <label class="form-label">Quantity:</label>
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary qty-btn-minus" type="button">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center qty-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" id="productQuantity">
                            <button class="btn btn-outline-secondary qty-btn-plus" type="button">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-primary btn-lg add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" onclick="addToCartWithQty(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Wishlist & Share -->
            <div class="mb-4 d-flex gap-2">
                <?php if (is_logged_in()): ?>
                <button class="btn btn-danger add-to-wishlist-btn-detail" data-product-id="<?php echo $product['id']; ?>" style="min-width: 180px;">
                    <i class="far fa-heart me-2"></i>Add to Wishlist
                </button>
                <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-danger" style="min-width: 180px;">
                    <i class="far fa-heart me-2"></i>Add to Wishlist
                </a>
                <?php endif; ?>
                <button class="btn btn-outline-secondary" onclick="shareProduct()">
                    <i class="fas fa-share-alt me-2"></i>Share
                </button>
            </div>
            
            <!-- Product Meta -->
            <div class="border-top pt-4">
                <p class="mb-2"><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                <p class="mb-2"><strong>Category:</strong> <a href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
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
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews (<?php echo $rating_data['total_reviews'] ?? 0; ?>)</a>
                </li>
            </ul>
            
            <div class="tab-content p-4 border border-top-0">
                <!-- Description Tab -->
                <div id="description" class="tab-pane fade show active">
                    <?php echo nl2br(htmlspecialchars($product['long_description'])); ?>
                </div>
                
                <!-- Reviews Tab -->
                <div id="reviews" class="tab-pane fade">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-item mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                    <?php echo display_rating($review['rating'], false); ?>
                                </div>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                    <?php endif; ?>
                    
                    <?php if (is_logged_in()): ?>
                    <div class="mt-4">
                        <h5>Write a Review</h5>
                        <form action="<?php echo SITE_URL; ?>/api/submit-review.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input">
                                    <input type="radio" name="rating" value="5" id="star5" required>
                                    <label for="star5"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="4" id="star4">
                                    <label for="star4"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="3" id="star3">
                                    <label for="star3"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="2" id="star2">
                                    <label for="star2"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="1" id="star1">
                                    <label for="star1"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Your Review</label>
                                <textarea class="form-control" name="review_text" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Please <a href="<?php echo SITE_URL; ?>/login.php">login</a> to write a review.</p>
                    <?php endif; ?>
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
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $related['slug']; ?>">
                                <img src="<?php echo $related['primary_image'] ? PRODUCT_IMAGE_URL . $related['primary_image'] : 'https://via.placeholder.com/300x250'; ?>" 
                                     class="product-image" alt="<?php echo htmlspecialchars($related['name']); ?>">
                            </a>
                        </div>
                        <div class="product-info">
                            <h6 class="product-title">
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $related['slug']; ?>" class="text-decoration-none text-dark">
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
                            <button class="btn btn-primary btn-sm w-100 add-to-cart-btn" data-product-id="<?php echo $related['id']; ?>">
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
function addToCartWithQty(productId) {
    const quantity = document.getElementById('productQuantity').value;
    $('.add-to-cart-btn').data('quantity', quantity);
}

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
        showToast('Success', 'Link copied to clipboard!', 'success');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
