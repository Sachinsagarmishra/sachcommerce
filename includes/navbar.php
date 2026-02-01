<?php
/**
 * Frontend Navigation Bar
 * TrendsOne eCommerce
 */

// Get cart count
$cart_count = get_cart_count();

// Get categories for dropdown
$nav_categories = get_menu_categories(8);

// Get user info if logged in
$current_user = is_logged_in() ? get_logged_user() : null;
?>

<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-2 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small>
                    <i class="fas fa-phone-alt me-2"></i>
                    <?php echo SITE_PHONE; ?>
                    <span class="mx-3">|</span>
                    <i class="fas fa-envelope me-2"></i>
                    <?php echo SITE_EMAIL; ?>
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small>
                    <a href="<?php echo SITE_URL; ?>/track-order" class="text-white text-decoration-none me-3">
                        <i class="fas fa-truck me-1"></i>Track Order
                    </a>
                    <a href="<?php echo SITE_URL; ?>/faq" class="text-white text-decoration-none">
                        <i class="fas fa-question-circle me-1"></i>Help
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>">
            <?php
            $logo = get_site_setting('site_logo');
            if ($logo):
                ?>
                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $logo; ?>" alt="<?php echo SITE_NAME; ?>" height="40">
            <?php else: ?>
                <i class="fas fa-store me-2" style="color: var(--primary-color);"></i>
                <?php echo SITE_NAME; ?>
            <?php endif; ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search Form (Desktop) -->
            <form class="d-none d-lg-flex mx-auto" style="width: 400px;" action="<?php echo SITE_URL; ?>/shop"
                method="GET">
                <div class="input-group">
                    <input type="text" class="form-control border-end-0" name="q" placeholder="Search products..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button class="btn btn-outline-secondary border-start-0" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Nav Links -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                </li>

                <!-- Categories Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Categories
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (!empty($nav_categories)): ?>
                            <?php foreach ($nav_categories as $cat): ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?php echo SITE_URL; ?>/shop?category=<?php echo $cat['slug']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        <?php endif; ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo SITE_URL; ?>/shop">
                                <i class="fas fa-th-large me-2"></i>All Categories
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/shop">Shop</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/contact">Contact</a>
                </li>

                <!-- Divider (Mobile) -->
                <li class="nav-item d-lg-none">
                    <hr class="dropdown-divider">
                </li>

                <!-- Search (Mobile) -->
                <li class="nav-item d-lg-none py-2">
                    <form action="<?php echo SITE_URL; ?>/shop" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Search...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </li>

                <!-- Wishlist -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/wishlist" title="Wishlist">
                        <i class="far fa-heart fa-lg"></i>
                        <span class="d-lg-none ms-2">Wishlist</span>
                    </a>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/cart" title="Cart">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge badge bg-danger rounded-pill">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                        <span class="d-lg-none ms-2">Cart (
                            <?php echo $cart_count; ?>)
                        </span>
                    </a>
                </li>

                <!-- User Account -->
                <?php if ($current_user): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg me-1"></i>
                            <span class="d-lg-none">
                                <?php echo htmlspecialchars($current_user['first_name'] ?? 'Account'); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text">
                                    <strong>Hello,
                                        <?php echo htmlspecialchars($current_user['first_name'] ?? 'User'); ?>
                                    </strong>
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/my-account">
                                    <i class="fas fa-user me-2"></i>My Account
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders">
                                    <i class="fas fa-shopping-bag me-2"></i>My Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/wishlist">
                                    <i class="fas fa-heart me-2"></i>Wishlist
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login" title="Login">
                            <i class="fas fa-user fa-lg"></i>
                            <span class="d-lg-none ms-2">Login / Register</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>