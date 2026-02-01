<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user = get_logged_user();

// Check which dropdown should be open
$products_pages = ['products.php', 'product-add.php', 'product-edit.php', 'bulk-upload-products.php'];
$reports_pages = ['sales-report.php', 'product-report.php', 'customer-report.php'];
$settings_pages = ['general-settings.php', 'payment-settings.php', 'email-settings.php', 'shipping-settings.php', 'tax-settings.php', 'seo-settings.php', 'theme-settings.php'];

$products_open = in_array($current_page, $products_pages);
$reports_open = in_array($current_page, $reports_pages);
$settings_open = in_array($current_page, $settings_pages) || strpos($current_page, 'settings') !== false;
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-store"></i> <?php echo SITE_NAME; ?></h4>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <!-- Products -->
            <li class="nav-item">
                <a class="nav-link <?php echo $products_open ? 'active' : ''; ?>" data-bs-toggle="collapse"
                    href="#productsMenu" role="button" aria-expanded="<?php echo $products_open ? 'true' : 'false'; ?>">
                    <i class="fas fa-box"></i> Products <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo $products_open ? 'show' : ''; ?>" id="productsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>"
                                href="products.php"><i class="fas fa-list"></i> All Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'product-add.php') ? 'active' : ''; ?>"
                                href="product-add.php"><i class="fas fa-plus"></i> Add Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'bulk-upload-products.php') ? 'active' : ''; ?>"
                                href="bulk-upload-products.php"><i class="fas fa-upload"></i> Bulk
                                Upload</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['categories.php', 'add-category.php', 'edit-category.php'])) ? 'active' : ''; ?>"
                    href="categories.php">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>

            <!-- Orders -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['orders.php', 'order-detail.php', 'view-order.php'])) ? 'active' : ''; ?>"
                    href="orders.php">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
            </li>

            <!-- Transactions -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>"
                    href="transactions.php">
                    <i class="fas fa-exchange-alt"></i> Transactions
                </a>
            </li>

            <!-- Customers -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['customers.php', 'view-customer.php', 'edit-customer.php'])) ? 'active' : ''; ?>"
                    href="customers.php">
                    <i class="fas fa-users"></i> Customers
                </a>
            </li>

            <!-- Coupons -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['coupons.php', 'add-coupon.php', 'edit-coupon.php'])) ? 'active' : ''; ?>"
                    href="coupons.php">
                    <i class="fas fa-ticket-alt"></i> Coupons
                </a>
            </li>

            <!-- Banners (Slider) -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['manage-banners.php', 'add-banner.php', 'edit-banner.php'])) ? 'active' : ''; ?>"
                    href="manage-banners.php">
                    <i class="fas fa-images"></i> Homepage Banners
                </a>
            </li>

            <!-- Reviews -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>" href="reviews.php">
                    <i class="fas fa-star"></i> Reviews
                </a>
            </li>


            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link <?php echo $reports_open ? 'active' : ''; ?>" data-bs-toggle="collapse"
                    href="#reportsMenu" role="button" aria-expanded="<?php echo $reports_open ? 'true' : 'false'; ?>">
                    <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo $reports_open ? 'show' : ''; ?>" id="reportsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'sales-report.php') ? 'active' : ''; ?>"
                                href="sales-report.php"><i class="fas fa-dollar-sign"></i> Sales
                                Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'product-report.php') ? 'active' : ''; ?>"
                                href="product-report.php"><i class="fas fa-box"></i> Product Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'customer-report.php') ? 'active' : ''; ?>"
                                href="customer-report.php"><i class="fas fa-users"></i> Customer
                                Report</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link <?php echo $settings_open ? 'active' : ''; ?>" data-bs-toggle="collapse"
                    href="#settingsMenu" role="button" aria-expanded="<?php echo $settings_open ? 'true' : 'false'; ?>">
                    <i class="fas fa-cog"></i> Settings <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo $settings_open ? 'show' : ''; ?>" id="settingsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'general-settings.php') ? 'active' : ''; ?>"
                                href="general-settings.php"><i class="fas fa-sliders-h"></i> General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'payment-settings.php') ? 'active' : ''; ?>"
                                href="payment-settings.php"><i class="fas fa-credit-card"></i>
                                Payment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'email-settings.php') ? 'active' : ''; ?>"
                                href="email-settings.php"><i class="fas fa-envelope"></i> Email</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'shipping-settings.php') ? 'active' : ''; ?>"
                                href="shipping-settings.php"><i class="fas fa-truck"></i> Shipping</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'tax-settings.php') ? 'active' : ''; ?>"
                                href="tax-settings.php"><i class="fas fa-percent"></i> Tax</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'seo-settings.php') ? 'active' : ''; ?>"
                                href="seo-settings.php"><i class="fas fa-search"></i> SEO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'theme-settings.php') ? 'active' : ''; ?>"
                                href="theme-settings.php"><i class="fas fa-palette"></i> Theme
                                Settings</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</aside>