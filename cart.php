<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Shopping Cart';

// Get cart items
$cart_items = get_cart_items();
$cart_total = get_cart_total();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Cart Content -->
<div class="container my-5">
    <?php if (!empty($cart_items)): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Shopping Cart (<?php echo count($cart_items); ?> items)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php foreach ($cart_items as $item): ?>
                                                <?php
                                                $item_price = $item['sale_price'] ?? $item['price'];
                                                $item_total = $item_price * $item['quantity'];
                                                ?>
          <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $item['image'] ? PRODUCT_IMAGE_URL . $item['image'] : 'https://via.placeholder.com/80x80'; ?>"
                                                        alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                        class="rounded me-3"
                                                        style="width: 80px; height: 80px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <a href="<?php echo SITE_URL; ?>/products/<?php echo $item['slug']; ?>"
                                                                class="text-decoration-none text-dark">
                                                                        <?php echo htmlspecialchars($item['name']); ?>
                                                            </a>
                                                        </h6>
                                                                <?php if ($item['stock_quantity'] < $item['quantity']): ?>
                                                            <small class="text-danger">Only <?php echo $item['stock_quantity']; ?>
                                                                left in stock</small>
                                                      <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                             <?php echo format_price($item_price); ?>
                                            </td>
                                            <td class="align-middle">
                                                <div class="input-group" style="width: 130px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control form-control-sm text-center"
                                                        value="<?php echo $item['quantity']; ?>" min="1"
                                                        max="<?php echo $item['stock_quantity']; ?>"
                                                        onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?php echo format_price($item_total); ?></strong>
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-link text-danger remove-from-cart-btn"
                                                    data-cart-id="<?php echo $item['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <strong><?php echo format_price($cart_total); ?></strong>
                        </div>

                            <?php
                            $shipping_cost = $cart_total >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
                            $total = $cart_total + $shipping_cost;
                            ?>

                            <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <strong>
                                    <?php if ($shipping_cost == 0): ?>
                                    <span class="text-success">FREE</span>
                                    <?php else: ?>
                                            <?php echo format_price($shipping_cost); ?>
                              <?php endif; ?>
                            </strong>
                        </div>

                        <?php if ($cart_total < FREE_SHIPPING_THRESHOLD): ?>
                            <div class="alert alert-info small">
                                Add <?php echo format_price(FREE_SHIPPING_THRESHOLD - $cart_total); ?> more for FREE shipping!
                            </div>
                      <?php endif; ?>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-primary"><?php echo format_price($total); ?></h5>
                        </div>

                        <a href="<?php echo SITE_URL; ?>/checkout" class="btn btn-primary btn-lg w-100 mb-2">
                            Proceed to Checkout
                        </a>

                        <!-- Coupon Code -->
                        <div class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Coupon Code" id="couponCode">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyCoupon()">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <small class="text-muted">We Accept</small>
                        <div class="mt-2">
                            <img src="<?php echo SITE_URL; ?>/assets/images/payment-methods.png" alt="Payment Methods"
                                height="30" class="opacity-75">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to get started!</p>
            <a href="<?php echo SITE_URL; ?>/shop" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateQuantity(cartId, quantity) {
        if (quantity < 1) {
            if (confirm('Remove this item from cart?')) {
                removeFromCart(cartId);
            }
            return;
        }

        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/update-cart.php',
            method: 'POST',
            data: {
                cart_id: cartId,
                quantity: quantity
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    showToast('Error', response.message, 'error');
                }
            }
        });
    }

    function removeFromCart(cartId) {
        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/remove-from-cart.php',
            method: 'POST',
            data: {
                cart_id: cartId
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    showToast('Error', response.message, 'error');
                }
            }
        });
    }

    function applyCoupon() {
        const code = $('#couponCode').val();
        if (!code) {
            showToast('Error', 'Please enter a coupon code', 'error');
            return;
        }

        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/apply-coupon.php',
            method: 'POST',
            data: {
                coupon_code: code
            },
            success: function (response) {
                if (response.success) {
                    showToast('Success', 'Coupon applied successfully!', 'success');
                    location.reload();
                } else {
                    showToast('Error', response.message, 'error');
                }
            }
        });
    }
</script>

<?php include 'includes/footer.php'; ?>