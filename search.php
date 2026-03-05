<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$search_query = $_GET['q'] ?? '';
$page_title = 'Search Results';

$products = [];
if ($search_query) {
    $products = get_all_products(20, 0, ['search' => $search_query]);
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Search Results</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
    <p class="text-muted mb-4">Found <?php echo count($products); ?> products</p>

    <?php if (!empty($products)): ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3">
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
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>" class="action-icon">
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
                                <button class="btn-hover-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <span>Add to cart</span>
                                    <div class="cart-icon-wrapper">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-brand">Brand: <?php echo SITE_NAME; ?></div>
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
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-search-alt fa-4x text-muted mb-4"></i>
            <h4>No products found</h4>
            <p class="text-muted mb-4">Try different keywords or browse our categories</p>
            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">Browse All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>