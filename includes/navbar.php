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

// Ensure site_settings is available (loaded in header.php)
if (!isset($site_settings)) {
    $site_settings = [
        'site_name' => get_site_setting('site_name', SITE_NAME ?? 'TrendsOne'),
        'site_logo' => get_site_setting('site_logo', ''),
        'support_email' => get_site_setting('support_email', SITE_EMAIL ?? ''),
        'support_phone' => get_site_setting('support_phone', SITE_PHONE ?? ''),
    ];
}
?>

<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-2 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small>
                    <?php if ($site_settings['support_phone']): ?>
                        <i class="fas fa-phone-alt me-2"></i>
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $site_settings['support_phone']); ?>"
                            class="text-white text-decoration-none">
                            <?php echo htmlspecialchars($site_settings['support_phone']); ?>
                        </a>
                        <span class="mx-3">|</span>
                    <?php endif; ?>
                    <?php if ($site_settings['support_email']): ?>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?php echo htmlspecialchars($site_settings['support_email']); ?>"
                            class="text-white text-decoration-none">
                            <?php echo htmlspecialchars($site_settings['support_email']); ?>
                        </a>
                    <?php endif; ?>
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
            <?php if ($site_settings['site_logo']): ?>
                <img id="mainLogo" src="<?php echo SITE_URL; ?>/uploads/logos/<?php echo $site_settings['site_logo']; ?>"
                    alt="<?php echo htmlspecialchars($site_settings['site_name']); ?>">
            <?php else: ?>
                <i class="fas fa-store me-2" style="color: var(--primary-color);"></i>
                <?php echo htmlspecialchars($site_settings['site_name']); ?>
            <?php endif; ?>
        </a>

        <!-- Mobile Action Icons (Visible only on mobile) -->
        <div class="d-flex d-lg-none align-items-center gap-3 ms-auto me-2">
            <a href="<?php echo SITE_URL; ?>/search" class="text-dark"><i class="fas fa-search"></i></a>
            <a href="<?php echo SITE_URL; ?>/wishlist" class="text-dark position-relative">
                <i class="far fa-heart"></i>
                <?php if (get_wishlist_count() > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                        <?php echo get_wishlist_count(); ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="<?php echo SITE_URL; ?>/cart" class="text-dark position-relative">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 p-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search Form (Desktop) -->
            <form class="d-none d-lg-flex ms-lg-5 me-auto" style="max-width: 450px; width: 100%;"
                action="<?php echo SITE_URL; ?>/shop" method="GET">
                <div class="input-group search-group">
                    <input type="text" class="form-control border-end-0 bg-light" name="q"
                        placeholder="Search products..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button class="btn btn-light border border-start-0" type="submit">
                        <i class="fas fa-search text-muted"></i>
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

                <!-- Search (Mobile - Inside Collapse) -->
                <li class="nav-item d-lg-none mt-3">
                    <form action="<?php echo SITE_URL; ?>/shop" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Search for products...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </li>

                <!-- Desktop Action Icons -->
                <li class="nav-item d-none d-lg-block ms-3">
                    <a class="nav-link px-2" href="<?php echo SITE_URL; ?>/wishlist" title="Wishlist">
                        <i class="far fa-heart fa-lg"></i>
                        <?php if (get_wishlist_count() > 0): ?>
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 10px;"><?php echo get_wishlist_count(); ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link px-2" href="<?php echo SITE_URL; ?>/cart" title="Cart">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge bg-danger rounded-pill"
                                style="font-size: 10px;"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- User Account -->
                <?php if ($current_user): ?>
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg me-1"></i>
                            <span class="d-lg-none">
                                <?php echo htmlspecialchars($current_user['first_name'] ?? 'Account'); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li>
                                <span class="dropdown-item-text">
                                    <small class="text-muted">Hello,</small><br>
                                    <strong><?php echo htmlspecialchars($current_user['first_name'] ?? 'User'); ?></strong>
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/my-account"><i
                                        class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders"><i
                                        class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/wishlist"><i
                                        class="fas fa-heart me-2"></i>Wishlist</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/logout"><i
                                        class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link d-flex align-items-center" href="<?php echo SITE_URL; ?>/login">
                            <i class="fas fa-user-circle fa-lg me-2"></i>
                            <span>Login</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        height: 80px;
    }

    #mainLogo {
        height: 45px;
        width: auto;
        display: block;
    }

    .search-group input {
        border-radius: 20px 0 0 20px !important;
        padding-left: 20px;
    }

    .search-group button {
        border-radius: 0 20px 20px 0 !important;
        padding-right: 15px;
    }

    @media (max-width: 991px) {
        .navbar {
            height: 70px;
        }

        #mainLogo {
            height: 35px;
        }

        .navbar-collapse {
            background: #fff;
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-top: 5px;
        }

        .nav-link {
            padding: 12px 0 !important;
            border-bottom: 1px solid #f8f9fa;
        }

        .dropdown-menu {
            box-shadow: none;
            border: none;
            padding-left: 15px;
        }
    }
</style>