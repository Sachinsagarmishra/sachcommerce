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
                            <div class="product-label-group">
                                <?php if ($item['sale_price']): ?>
                                    <span
                                        class="product-label label-sale">-<?php echo calculate_discount_percentage($item['price'], $item['sale_price']); ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-action-icons">
                                <a href="javascript:void(0)" class="action-icon text-danger"
                                    onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)">
                                    <i class="fas fa-trash-can"></i>
                                </a>
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>" class="action-icon">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>">
                                <img src="<?php echo $item['image'] ? PRODUCT_IMAGE_URL . $item['image'] : 'https://via.placeholder.com/300x250?text=No+Image'; ?>"
                                    class="product-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </a>
                            <div class="product-add-to-cart-hover">
                                <button class="btn-hover-cart add-to-cart-btn"
                                    data-product-id="<?php echo $item['product_id']; ?>">
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
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>"
                                    class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </h6>
                            <div class="product-price-wrapper">
                                <?php if ($item['sale_price']): ?>
                                    <span class="old-price"><?php echo format_price($item['price']); ?></span>
                                    <span class="current-price"><?php echo format_price($item['sale_price']); ?></span>
                                <?php else: ?>
                                    <span class="current-price"><?php echo format_price($item['price']); ?></span>
                                <?php endif; ?>
                            </div>
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
                <i class="fas fa-shopping-basket me-2"></i>Start Shopping
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