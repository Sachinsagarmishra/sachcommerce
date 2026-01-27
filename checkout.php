<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = SITE_URL . '/checkout';
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$page_title = 'Checkout';

// Get cart items
$cart_items = get_cart_items();
if (empty($cart_items)) {
    header('Location: ' . SITE_URL . '/cart');
    exit;
}

$cart_total = get_cart_total();
$shipping_cost = $cart_total >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
$total = $cart_total + $shipping_cost;

// Get user addresses
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll();

// Form will be submitted to API
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/cart">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">

    <!-- Notifications Section -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo SITE_URL; ?>/api/process-order.php">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <!-- Shipping Address -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Shipping Address</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-1"></i>Add New
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($addresses)): ?>
                            <div class="row g-3">
                                <?php foreach ($addresses as $address): ?>
                                    <div class="col-md-6">
                                        <div class="form-check border rounded p-3">
                                            <input class="form-check-input" type="radio" name="address_id"
                                                id="address<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>"
                                                <?php echo $address['is_default'] ? 'checked' : ''; ?> required>
                                            <label class="form-check-label w-100" for="address<?php echo $address['id']; ?>">
                                                <strong><?php echo htmlspecialchars($address['full_name']); ?></strong>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="badge bg-primary ms-2">Default</span>
                                                <?php endif; ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($address['address_line1']); ?><br>
                                                    <?php if ($address['address_line2']): ?>
                                                        <?php echo htmlspecialchars($address['address_line2']); ?><br>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($address['city']); ?>,
                                                    <?php echo htmlspecialchars($address['state']); ?> -
                                                    <?php echo htmlspecialchars($address['pincode']); ?><br>
                                                    Phone: <?php echo htmlspecialchars($address['phone']); ?>
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No saved addresses. Please add a new address.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="razorpay"
                                value="razorpay" checked required>
                            <label class="form-check-label" for="razorpay">
                                <strong>Razorpay (UPI, Cards, Wallets)</strong>
                                <br><small class="text-muted">Pay securely using Razorpay payment gateway</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod"
                                required>
                            <label class="form-check-label" for="cod">
                                <strong>Cash on Delivery (COD)</strong>
                                <br><small class="text-muted">Pay when you receive the product</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex mb-2">
                                    <img src="<?php echo $item['image'] ? PRODUCT_IMAGE_URL . $item['image'] : 'https://via.placeholder.com/50'; ?>"
                                        class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <small class="d-block"><?php echo htmlspecialchars($item['name']); ?></small>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <strong
                                        class="text-nowrap"><?php echo format_price(($item['sale_price'] ?? $item['price']) * $item['quantity']); ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <!-- Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?php echo format_price($cart_total); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <strong>
                                <?php if ($shipping_cost == 0): ?>
                                    <span class="text-success">FREE</span>
                                <?php else: ?>
                                    <?php echo format_price($shipping_cost); ?>
                                <?php endif; ?>
                            </strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-primary"><?php echo format_price($total); ?></h5>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>

                        <p class="text-center text-muted small mt-3 mb-0">
                            <i class="fas fa-shield-alt me-1"></i>Secure Checkout
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo SITE_URL; ?>/api/add-address.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 1 *</label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City *</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pincode *</label>
                        <input type="text" class="form-control" name="pincode" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault">
                        <label class="form-check-label" for="isDefault">
                            Set as default address
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>