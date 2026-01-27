<?php
$user = get_logged_user();
?>
<!-- Top Navbar -->
<nav class="navbar navbar-light bg-white border-bottom admin-navbar">
    <div class="container-fluid">
        <!-- Left: Hamburger Menu -->
        <button class="btn btn-link navbar-toggler-btn p-0" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        
        <!-- Center: Site Title (Mobile Only) -->
        <div class="navbar-brand d-lg-none mb-0">
            <strong><?php echo SITE_NAME; ?></strong>
        </div>
        
        <!-- Right: Actions -->
        <div class="d-flex align-items-center navbar-actions">
            <!-- Visit Site (Hidden on mobile) -->
            <a href="<?php echo SITE_URL; ?>" class="btn btn-sm btn-outline-primary me-2 d-none d-md-inline-flex" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i>
                <span class="d-none d-lg-inline">Visit Site</span>
            </a>
            
            <!-- Notifications -->
            <div class="dropdown me-2">
                <button class="btn btn-link position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg text-secondary"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                        3
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-box text-warning me-2"></i> 5 products low in stock</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart text-info me-2"></i> 3 new orders</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-star text-success me-2"></i> 2 new reviews</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center small" href="#">View All Notifications</a></li>
                </ul>
            </div>
            
            <!-- User Profile -->
            <div class="dropdown">
                <button class="btn btn-link d-flex align-items-center p-2 user-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if ($user['avatar']): ?>
                        <img src="<?php echo AVATAR_IMAGE_URL . $user['avatar']; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" class="rounded-circle" width="32" height="32">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center user-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="ms-2 d-none d-md-inline user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                    <i class="fas fa-chevron-down ms-2 d-none d-md-inline"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header"><?php echo htmlspecialchars($user['name']); ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li><a class="dropdown-item d-md-none" href="<?php echo SITE_URL; ?>" target="_blank"><i class="fas fa-external-link-alt me-2"></i> Visit Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
