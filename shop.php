<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Shop';
$meta_description = 'Browse our collection of products';

// Get filters from URL
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['q']) ? $_GET['q'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build filters array for the database query
$filters = [
    'sort' => $sort,
    'search' => $search
];

if ($min_price) $filters['min_price'] = $min_price;
if ($max_price) $filters['max_price'] = $max_price;

// Get category if specified
$category = null;
if ($category_slug) {
    $category = get_category_by_slug($category_slug);
    if ($category) {
        $filters['category'] = $category['id'];
        $page_title = $category['name'] . ' - Shop';
    }
}

// Get products
$products = get_all_products($per_page, $offset, $filters);
$total_products = get_total_products($filters);
$total_pages = ceil($total_products / $per_page);

// Get all categories for filter sidebar
$categories = get_menu_categories();

// Helper to preserve query string parameters (for pagination and links)
function build_query_params($exclude = []) {
    $params = $_GET;
    foreach ($exclude as $key) {
        unset($params[$key]);
    }
    return http_build_query($params);
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <?php if ($category): ?>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['name']); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active">Shop</li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<!-- Shop Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Categories -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Categories</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/shop.php" class="text-decoration-none <?php echo !$category_slug ? 'text-primary fw-bold' : 'text-dark'; ?>">
                                    All Products
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $cat['slug']; ?>" 
                                   class="text-decoration-none <?php echo $category_slug === $cat['slug'] ? 'text-primary fw-bold' : 'text-dark'; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Price Range</h6>
                        <form method="GET" action="shop.php">
                            <!-- Preserve existing filters -->
                            <?php if ($category_slug): ?>
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
                            <?php endif; ?>
                            <?php if ($search): ?>
                                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                            <?php endif; ?>
                            <!-- Preserve Sort when filtering price -->
                            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">

                            <div class="mb-2">
                                <input type="number" class="form-control form-control-sm" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($min_price); ?>">
                            </div>
                            <div class="mb-2">
                                <input type="number" class="form-control form-control-sm" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($max_price); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                        </form>
                    </div>
                    
                    <!-- Clear Filters -->
                    <?php if ($category_slug || $min_price || $max_price || $search): ?>
                    <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Toolbar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">
                        <?php if ($category): ?>
                            <?php echo htmlspecialchars($category['name']); ?>
                        <?php elseif ($search): ?>
                            Search Results for "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            All Products
                        <?php endif; ?>
                    </h4>
                    <small class="text-muted"><?php echo $total_products; ?> products found</small>
                </div>
                
                <!-- Sort -->
                <div>
                    <form method="GET" action="shop.php" class="d-inline">
                        <!-- Preserve Category -->
                        <?php if ($category_slug): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
                        <?php endif; ?>
                        <!-- Preserve Search -->
                        <?php if ($search): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                        <!-- Preserve Price Filters -->
                        <?php if ($min_price): ?>
                            <input type="hidden" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                        <?php endif; ?>
                        <?php if ($max_price): ?>
                            <input type="hidden" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">
                        <?php endif; ?>

                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name: A-Z</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Products -->
            <?php if (!empty($products)): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card product-card">
                        <div class="product-image-wrapper">
                            <?php if ($product['discount_percentage'] > 0): ?>
                                <span class="product-badge badge-sale"><?php echo $product['discount_percentage']; ?>% OFF</span>
                            <?php endif; ?>
                            <?php if ($product['is_new_arrival']): ?>
                                <span class="product-badge badge-new" style="top: 10px; left: 10px; right: auto;">NEW</span>
                            <?php endif; ?>
                            
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['primary_image'] ? PRODUCT_IMAGE_URL . $product['primary_image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
                                     class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            
                            <div class="product-actions">
                                <?php if (is_logged_in()): ?>
                                <button class="product-action-btn add-to-wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                                <?php endif; ?>
                            </div>
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
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <?php 
                // Build base URL for pagination
                $query_params = build_query_params(['page']);
                $pagination_base = '?' . ($query_params ? $query_params . '&' : '');
            ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $pagination_base; ?>page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page || $i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $pagination_base; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $pagination_base; ?>page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>No products found</h4>
                <p class="text-muted">Try adjusting your filters or search terms</p>
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">View All Products</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>