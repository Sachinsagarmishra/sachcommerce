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
                    <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>">
                        <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250'; ?>" 
                             class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </a>
                </div>
                <div class="product-info">
                    <h6 class="product-title">
                        <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h6>
                    <div class="product-price">
                        <?php if ($product['sale_price']): ?>
                            <?php echo format_price($product['sale_price']); ?>
                            <span class="product-price-old"><?php echo format_price($product['price']); ?></span>
                        <?php else: ?>
                            <?php echo format_price($product['price']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-footer">
                    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart me-2"></i>Add to Cart
                    </button>
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
