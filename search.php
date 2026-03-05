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
                        <?php include 'includes/templates/product-card-body.php'; ?>
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