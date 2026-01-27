<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$page_title = 'My Addresses';

// Get user addresses
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/my-account">My Account</a></li>
                <li class="breadcrumb-item active">Addresses</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Addresses</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="fas fa-plus me-2"></i>Add New Address
        </button>
    </div>

    <?php if (!empty($addresses)): ?>
        <div class="row g-4">
            <?php foreach ($addresses as $address): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="mb-0">
                                    <?php echo htmlspecialchars($address['full_name']); ?>
                                    <?php if ($address['is_default']): ?>
                                        <span class="badge bg-primary ms-2">Default</span>
                                    <?php endif; ?>
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"
                                                onclick="editAddress(<?php echo $address['id']; ?>)">Edit</a></li>
                                        <?php if (!$address['is_default']): ?>
                                            <li><a class="dropdown-item"
                                                    href="<?php echo SITE_URL; ?>/api/set-default-address.php?id=<?php echo $address['id']; ?>">Set
                                                    as Default</a></li>
                                        <?php endif; ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                                onclick="if(confirm('Delete this address?')) location.href='<?php echo SITE_URL; ?>/api/delete-address.php?id=<?php echo $address['id']; ?>'">Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($address['address_line1']); ?></p>
                            <?php if ($address['address_line2']): ?>
                                <p class="mb-1"><?php echo htmlspecialchars($address['address_line2']); ?></p>
                            <?php endif; ?>
                            <p class="mb-1"><?php echo htmlspecialchars($address['city']); ?>,
                                <?php echo htmlspecialchars($address['state']); ?> -
                                <?php echo htmlspecialchars($address['pincode']); ?></p>
                            <p class="mb-0 text-muted">Phone: <?php echo htmlspecialchars($address['phone']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-4x text-muted mb-4"></i>
            <h4>No addresses saved</h4>
            <p class="text-muted mb-4">Add your shipping address for faster checkout</p>
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-2"></i>Add Address
            </button>
        </div>
    <?php endif; ?>
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