<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user = get_logged_user();
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
                <a class="nav-link <?php echo (in_array($current_page, ['products.php', 'add-product.php', 'edit-product.php', 'bulk-upload-products.php'])) ? 'active' : ''; ?>"
                    data-bs-toggle="collapse" href="#productsMenu" role="button" aria-expanded="false">
                    <i class="fas fa-box"></i> Products <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="productsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="products.php"><i class="fas fa-list"></i> All Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product-add.php"><i class="fas fa-plus"></i> Add Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bulk-upload-products.php"><i class="fas fa-upload"></i> Bulk
                                Upload</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['categories.php', 'add-category.php', 'edit-category.php'])) ? 'active' : ''; ?>"
                    href="categories.php">
                    <i class="fas fa-tag"></i> Categories
                </a>
            </li>

            <!-- Orders -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['orders.php', 'view-order.php'])) ? 'active' : ''; ?>"
                    href="orders.php">
                    <i class="fas fa-cart"></i> Orders
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
                    <i class="fas fa-ticket"></i> Coupons
                </a>
            </li>

            <!-- Banners (Slider) -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['manage-banners.php', 'add-banner.php', 'edit-banner.php'])) ? 'active' : ''; ?>"
                    href="manage-banners.php">
                    <i class="fas fa-gallery"></i> Homepage Banners
                </a>
            </li>

            <!-- Reviews -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>" href="reviews.php">
                    <i class="fas fa-star-filled"></i> Reviews
                </a>
            </li>


            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['sales-report.php', 'product-report.php', 'customer-report.php'])) ? 'active' : ''; ?>"
                    data-bs-toggle="collapse" href="#reportsMenu" role="button" aria-expanded="false">
                    <i class="fas fa-bar-chart"></i> Reports <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="reportsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="sales-report.php"><i class="fas fa-revenue"></i> Sales
                                Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product-report.php"><i class="fas fa-box"></i> Product Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customer-report.php"><i class="fas fa-users"></i> Customer
                                Report</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($current_page, 'settings') !== false) ? 'active' : ''; ?>"
                    data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="false">
                    <i class="fas fa-cog"></i> Settings <i class="fas fa-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="settingsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="general-settings.php"><i class="fas fa-control-panel"></i> General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="payment-settings.php"><i class="fas fa-credit-cards"></i>
                                Payment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="email-settings.php"><i class="fas fa-envelope"></i> Email</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shipping-settings.php"><i class="fas fa-delivery"></i> Shipping</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tax-settings.php"><i class="fas fa-offer"></i> Tax</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="seo-settings.php"><i class="fas fa-search-alt"></i> SEO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="theme-settings.php"><i class="fas fa-palette"></i> Theme
                                Settings</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</aside>