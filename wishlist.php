<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$page_title = 'My Wishlist';

// Get wishlist items
$wishlist_items = get_wishlist_items();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/my-account">My Account</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <h2 class="mb-4">My Wishlist (<?php echo count($wishlist_items); ?> items)</h2>

    <?php if (!empty($wishlist_items)): ?>
        <div class="row g-4">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="col-md-3">
                    <div class="card product-card">
                        <div class="product-image-wrapper">
                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>">
                                <img src="<?php echo $item['image'] ? PRODUCT_IMAGE_URL . $item['image'] : 'https://via.placeholder.com/300x250'; ?>"
                                    class="product-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </a>
                            <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <h6 class="product-title">
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>"
                                    class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </h6>
                            <div class="product-price">
                                <?php if ($item['sale_price']): ?>
                                    <?php echo format_price($item['sale_price']); ?>
                                    <span class="product-price-old"><?php echo format_price($item['price']); ?></span>
                                <?php else: ?>
                                    <?php echo format_price($item['price']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-footer">
                            <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                data-product-id="<?php echo $item['product_id']; ?>">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-heart fa-4x text-muted mb-4"></i>
            <h4>Your wishlist is empty</h4>
            <p class="text-muted mb-4">Save your favorite items here!</p>
            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    function removeFromWishlist(productId) {
        if (confirm('Remove from wishlist?')) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/api/remove-from-wishlist.php',
                method: 'POST',
                data: { product_id: productId },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                }
            });
        }
    }
</script>

<?php include 'includes/footer.php'; ?>