<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Checkout';

// Get cart items - check for both session-based and user-based cart
$cart_items = get_cart_items();
if (empty($cart_items)) {
    header('Location: ' . SITE_URL . '/cart');
    exit;
}

$cart_total = get_cart_total();
$shipping_cost = $cart_total >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_CHARGE;
$total = $cart_total + $shipping_cost;

// Get user addresses if logged in
$addresses = [];
$user = null;
if (is_logged_in()) {
    $user = get_logged_user();
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $addresses = $stmt->fetchAll();
}

// Form will be submitted to API
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    .checkout-section {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 30px 0 60px;
    }

    .checkout-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 24px;
    }

    .checkout-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        padding: 20px 24px;
    }

    .checkout-card .card-header h5 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
    }

    .checkout-card .card-body {
        padding: 24px;
    }

    .guest-info-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .guest-info-section h5 {
        margin: 0;
        font-weight: 600;
    }

    .guest-info-section p {
        margin: 10px 0 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .form-floating label {
        color: #6c757d;
    }

    .form-floating .form-control:focus~label {
        color: var(--primary-color);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.15);
    }

    .address-option {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .address-option:hover {
        border-color: var(--primary-color);
    }

    .address-option.selected {
        border-color: var(--primary-color);
        background: rgba(var(--primary-rgb), 0.05);
    }

    .payment-option {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }

    .payment-option:hover {
        border-color: var(--primary-color);
    }

    .payment-option.selected {
        border-color: var(--primary-color);
        background: rgba(var(--primary-rgb), 0.05);
    }

    .payment-option input[type="radio"] {
        margin-right: 12px;
    }

    .order-summary-card {
        position: sticky;
        top: 20px;
    }

    .order-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 12px;
    }

    .order-item-details {
        flex: 1;
    }

    .order-item-name {
        font-weight: 500;
        color: #2c3e50;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .order-item-qty {
        font-size: 13px;
        color: #6c757d;
    }

    .order-item-price {
        font-weight: 600;
        color: #2c3e50;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
    }

    .summary-row.total {
        border-top: 2px solid #eee;
        margin-top: 10px;
        padding-top: 15px;
    }

    .summary-row.total span {
        font-size: 18px;
        font-weight: 700;
    }

    .btn-place-order {
        background: linear-gradient(135deg, var(--primary-color) 0%, #5a9c2e 100%);
        border: none;
        padding: 15px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 10px;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-place-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
        color: white;
    }

    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #6c757d;
        font-size: 13px;
        margin-top: 15px;
    }

    .secure-badge i {
        color: var(--primary-color);
    }

    .login-prompt {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .login-prompt p {
        margin: 0;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .checkout-section {
            padding: 15px 0 40px;
        }

        .login-prompt {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
    }
</style>

<div class="checkout-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/cart">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>

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

        <form method="POST" action="<?php echo SITE_URL; ?>/api/process-order.php" id="checkoutForm">
            <div class="row">
                <!-- Checkout Form -->
                <div class="col-lg-8">

                    <?php if (!is_logged_in()): ?>
                        <!-- Guest Checkout Info -->
                        <div class="guest-info-section">
                            <h5><i class="fas fa-user-clock me-2"></i>Guest Checkout</h5>
                            <p>No account needed! After your order, we'll create a temporary account so you can track your
                                order. Login credentials will be sent to your email.</p>
                        </div>

                        <!-- Login Prompt -->
                        <div class="login-prompt">
                            <p><i class="fas fa-user me-2"></i>Already have an account?</p>
                            <a href="<?php echo SITE_URL; ?>/login?redirect=checkout"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Contact & Shipping Information -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if (is_logged_in() && !empty($addresses)): ?>
                                <!-- Saved Addresses for Logged-in Users -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Choose a delivery address:</label>
                                    <div class="row g-3">
                                        <?php foreach ($addresses as $address): ?>
                                            <div class="col-md-6">
                                                <div class="address-option <?php echo $address['is_default'] ? 'selected' : ''; ?>"
                                                    onclick="selectAddress(this, <?php echo $address['id']; ?>)">
                                                    <input type="radio" name="address_id" value="<?php echo $address['id']; ?>"
                                                        <?php echo $address['is_default'] ? 'checked' : ''; ?>
                                                        style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <strong><?php echo htmlspecialchars($address['full_name']); ?></strong>
                                                        <?php if ($address['is_default']): ?>
                                                            <span class="badge bg-primary">Default</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="text-muted small mb-1">
                                                        <?php echo htmlspecialchars($address['address_line1']); ?>
                                                        <?php if ($address['address_line2']): ?>
                                                            <br><?php echo htmlspecialchars($address['address_line2']); ?>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="text-muted small mb-1">
                                                        <?php echo htmlspecialchars($address['city']); ?>,
                                                        <?php echo htmlspecialchars($address['state']); ?> -
                                                        <?php echo htmlspecialchars($address['pincode']); ?>
                                                    </p>
                                                    <p class="text-muted small mb-0">
                                                        <i
                                                            class="fas fa-phone me-1"></i><?php echo htmlspecialchars($address['phone']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="col-md-6">
                                            <div class="address-option d-flex align-items-center justify-content-center h-100"
                                                data-bs-toggle="modal" data-bs-target="#addAddressModal"
                                                style="min-height: 150px; border-style: dashed;">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                                    <p class="mb-0">Add New Address</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden fields for logged-in user -->
                                <input type="hidden" name="guest_checkout" value="0">
                                <input type="hidden" name="customer_email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>">
                                <input type="hidden" name="customer_name"
                                    value="<?php echo htmlspecialchars($user['name']); ?>">
                            <?php else: ?>
                                <!-- Guest Checkout Form -->
                                <input type="hidden" name="guest_checkout" value="1">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="full_name" name="full_name"
                                                placeholder="Full Name" required
                                                value="<?php echo is_logged_in() && $user ? htmlspecialchars($user['name']) : ''; ?>">
                                            <label for="full_name">Full Name *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="customer_email"
                                                placeholder="Email" required
                                                value="<?php echo is_logged_in() && $user ? htmlspecialchars($user['email']) : ''; ?>">
                                            <label for="email">Email Address *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                placeholder="Phone" required pattern="[0-9]{10}">
                                            <label for="phone">Phone Number *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="alt_phone" name="alt_phone"
                                                placeholder="Alternate Phone">
                                            <label for="alt_phone">Alternate Phone (Optional)</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address_line1" name="address_line1"
                                                placeholder="Address Line 1" required>
                                            <label for="address_line1">Address Line 1 *</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address_line2" name="address_line2"
                                                placeholder="Address Line 2">
                                            <label for="address_line2">Address Line 2 (Apartment, Suite, etc.)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="city" name="city" placeholder="City"
                                                required>
                                            <label for="city">City *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select class="form-select" id="state" name="state" required>
                                                <option value="">Select State</option>
                                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                                <option value="Assam">Assam</option>
                                                <option value="Bihar">Bihar</option>
                                                <option value="Chhattisgarh">Chhattisgarh</option>
                                                <option value="Goa">Goa</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Haryana">Haryana</option>
                                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                                <option value="Jharkhand">Jharkhand</option>
                                                <option value="Karnataka">Karnataka</option>
                                                <option value="Kerala">Kerala</option>
                                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Manipur">Manipur</option>
                                                <option value="Meghalaya">Meghalaya</option>
                                                <option value="Mizoram">Mizoram</option>
                                                <option value="Nagaland">Nagaland</option>
                                                <option value="Odisha">Odisha</option>
                                                <option value="Punjab">Punjab</option>
                                                <option value="Rajasthan">Rajasthan</option>
                                                <option value="Sikkim">Sikkim</option>
                                                <option value="Tamil Nadu">Tamil Nadu</option>
                                                <option value="Telangana">Telangana</option>
                                                <option value="Tripura">Tripura</option>
                                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                                <option value="Uttarakhand">Uttarakhand</option>
                                                <option value="West Bengal">West Bengal</option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                                <option value="Ladakh">Ladakh</option>
                                                <option value="Chandigarh">Chandigarh</option>
                                                <option value="Puducherry">Puducherry</option>
                                            </select>
                                            <label for="state">State *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="pincode" name="pincode"
                                                placeholder="Pincode" required pattern="[0-9]{6}">
                                            <label for="pincode">Pincode *</label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5><i class="fas fa-sticky-note me-2"></i>Order Notes (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-floating">
                                <textarea class="form-control" id="order_notes" name="order_notes"
                                    placeholder="Order Notes" style="height: 100px"></textarea>
                                <label for="order_notes">Special instructions for delivery</label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-option selected" onclick="selectPayment(this, 'razorpay')">
                                <input type="radio" name="payment_method" id="razorpay" value="razorpay" checked
                                    required>
                                <label for="razorpay" class="d-inline">
                                    <strong>Online Payment (UPI, Cards, Wallets)</strong>
                                    <br><small class="text-muted">Pay securely using Razorpay</small>
                                </label>
                            </div>
                            <div class="payment-option" onclick="selectPayment(this, 'cod')">
                                <input type="radio" name="payment_method" id="cod" value="cod" required>
                                <label for="cod" class="d-inline">
                                    <strong>Cash on Delivery (COD)</strong>
                                    <br><small class="text-muted">Pay when you receive the product</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="checkout-card order-summary-card">
                        <div class="card-header">
                            <h5><i class="fas fa-shopping-bag me-2"></i>Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <!-- Cart Items -->
                            <div class="order-items mb-3">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo $item['image'] ? PRODUCT_IMAGE_URL . $item['image'] : 'https://via.placeholder.com/60'; ?>"
                                            class="order-item-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <div class="order-item-details">
                                            <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                            <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?></div>
                                        </div>
                                        <div class="order-item-price">
                                            <?php echo format_price(($item['sale_price'] ?? $item['price']) * $item['quantity']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr>

                            <!-- Totals -->
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <strong><?php echo format_price($cart_total); ?></strong>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <strong>
                                    <?php if ($shipping_cost == 0): ?>
                                        <span class="text-success">FREE</span>
                                    <?php else: ?>
                                        <?php echo format_price($shipping_cost); ?>
                                    <?php endif; ?>
                                </strong>
                            </div>

                            <div class="summary-row total">
                                <span>Total:</span>
                                <span class="text-primary"><?php echo format_price($total); ?></span>
                            </div>

                            <button type="submit" class="btn-place-order mt-4" id="placeOrderBtn">
                                <i class="fas fa-lock me-2"></i>Place Order
                            </button>

                            <div class="secure-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Checkout - 256-bit SSL Encrypted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (is_logged_in()): ?>
    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo SITE_URL; ?>/api/add-address.php" method="POST">
                    <input type="hidden" name="redirect" value="checkout">
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
<?php endif; ?>

<script>
    function selectAddress(element, addressId) {
        // Remove selected class from all
        document.querySelectorAll('.address-option').forEach(el => {
            el.classList.remove('selected');
        });
        // Add selected class to clicked element
        element.classList.add('selected');
        // Check the radio button
        element.querySelector('input[type="radio"]').checked = true;
    }

    function selectPayment(element, method) {
        // Remove selected class from all
        document.querySelectorAll('.payment-option').forEach(el => {
            el.classList.remove('selected');
        });
        // Add selected class to clicked element
        element.classList.add('selected');
        // Check the radio button
        document.getElementById(method).checked = true;
    }

    // Form validation
    document.getElementById('checkoutForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('placeOrderBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        btn.disabled = true;
    });
</script>

<?php include 'includes/footer.php'; ?>