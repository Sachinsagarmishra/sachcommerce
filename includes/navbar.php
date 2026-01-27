<?php
// Get cart count
$cart_count = 0;
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cart_count = $result['total'] ?? 0;
} else {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
    $stmt->execute([session_id()]);
    $result = $stmt->fetch();
    $cart_count = $result['total'] ?? 0;
}

// Get categories for menu
$menu_categories = get_menu_categories(8);
?>

<!-- Top Bar -->
<div class="top-bar bg-primary text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small>
                    <i class="fas fa-phone me-2"></i><?php echo SITE_PHONE; ?>
                    <span class="ms-3"><i class="fas fa-envelope me-2"></i><?php echo SITE_EMAIL; ?></span>
                </small>
            </div>
            <div class="col-md-6 text-md-end">
                <small>
                    <a href="<?php echo FACEBOOK_URL; ?>" class="text-white me-2"><i class="fab fa-facebook"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo TWITTER_URL; ?>" class="text-white"><i class="fab fa-twitter"></i></a>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>">
            <i class="fas fa-store text-primary"></i> <?php echo SITE_NAME; ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search Bar (Desktop) -->
            <form class="d-none d-lg-flex mx-auto" style="width: 40%;" action="<?php echo SITE_URL; ?>/search.php"
                method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Search products..." required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Right Menu -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <!-- Search (Mobile) -->
                <li class="nav-item d-lg-none">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/search.php">
                        <i class="fas fa-search"></i> Search
                    </a>
                </li>

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                </li>

                <!-- Shop Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Shop
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/shop.php">All Products</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php foreach ($menu_categories as $category): ?>
                            <li><a class="dropdown-item"
                                    href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $category['slug']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>


                <!-- Contact -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                </li>

                <!-- Wishlist -->
                <?php if (is_logged_in()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/wishlist.php">
                            <i class="fas fa-heart"></i>
                            <span class="d-lg-none">Wishlist</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Cart -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                        <span class="d-lg-none">Cart</span>
                    </a>
                </li>

                <!-- User Account -->
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/my-account.php">My Account</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/wishlist.php">Wishlist</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>