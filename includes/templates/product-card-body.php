<div class="product-card-img-wrapper">
    <div class="product-label-group">
        <?php if ($product['is_new_arrival']): ?>
            <span class="product-label label-new">NEW</span>
        <?php endif; ?>
        <?php if ($product['sale_price']): ?>
            <span class="product-label label-sale">-
                <?php echo calculate_discount_percentage($product['price'], $product['sale_price']); ?>%
            </span>
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
    <div class="product-brand"><?php echo htmlspecialchars(get_category_name($product['category_id'])); ?></div>
    <?php
    $random_ratings = [4, 4.5, 5];
    $random_rating = $random_ratings[array_rand($random_ratings)];
    echo display_rating($random_rating, false);
    ?>
    <h6 class="product-name">
        <a href="<?php echo SITE_URL; ?>/products/<?php echo $product['slug']; ?>"
            class="text-decoration-none text-dark">
            <?php echo htmlspecialchars($product['name']); ?>
        </a>
    </h6>
    <div class="product-price-wrapper">
        <?php if ($product['sale_price']): ?>
            <span class="old-price">
                <?php echo format_price($product['price']); ?>
            </span>
            <span class="current-price">
                <?php echo format_price($product['sale_price']); ?>
            </span>
        <?php else: ?>
            <span class="current-price">
                <?php echo format_price($product['price']); ?>
            </span>
        <?php endif; ?>
    </div>
</div>