<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user = get_logged_user();
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <i class="fas fa-shopping-bag"></i>
            <span>DEALPORT</span>
        </a>
        <button class="btn d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-nav-container">
        <!-- Main Menu Section -->
        <div class="sidebar-section-label">Main menu</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="dashboard.php"
                    class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="orders.php"
                    class="nav-link <?php echo (strpos($current_page, 'order') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Order Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="customers.php"
                    class="nav-link <?php echo (strpos($current_page, 'customer') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="coupons.php"
                    class="nav-link <?php echo (strpos($current_page, 'coupon') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Coupon Code</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="categories.php"
                    class="nav-link <?php echo (strpos($current_page, 'category') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="sales-report.php"
                    class="nav-link <?php echo (strpos($current_page, 'report') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Transactions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="manage-banners.php"
                    class="nav-link <?php echo (strpos($current_page, 'banner') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-flag"></i>
                    <span>Banners</span>
                </a>
            </li>
        </ul>

        <!-- Product Section -->
        <div class="sidebar-section-label">Product</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="product-add.php"
                    class="nav-link <?php echo ($current_page == 'product-add.php') ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="products.php"
                    class="nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                    <i class="fas fa-list-ul"></i>
                    <span>Product List</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reviews.php" class="nav-link <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i>
                    <span>Product Reviews</span>
                </a>
            </li>
        </ul>

        <!-- Admin Section -->
        <div class="sidebar-section-label">Admin</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="general-settings.php"
                    class="nav-link <?php echo (strpos($current_page, 'settings') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin role</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="payment-settings.php"
                    class="nav-link <?php echo (strpos($current_page, 'payment') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Control Authority</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-bottom">
        <div class="user-card">
            <?php if ($user['avatar']): ?>
                <img src="<?php echo AVATAR_IMAGE_URL . $user['avatar']; ?>" class="user-img" alt="User">
            <?php else: ?>
                <div class="user-img bg-primary text-white d-flex align-items-center justify-content-center"
                    style="font-weight: 700;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            <div class="user-info">
                <div style="font-weight: 700; font-size: 14px;"><?php echo htmlspecialchars($user['name']); ?></div>
                <div style="font-size: 12px; color: var(--grey);"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <a href="logout.php" class="ms-auto text-grey"><i class="fas fa-sign-out-alt"></i></a>
        </div>
        <a href="<?php echo SITE_URL; ?>" target="_blank" class="shop-link">
            <i class="fas fa-external-link-alt me-2"></i> Your Shop
        </a>
    </div>
</aside>