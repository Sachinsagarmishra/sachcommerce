<?php
$user = get_logged_user();
?>
<nav class="admin-navbar">
    <!-- Left: Page Title vs Search (UI shows search) -->
    <div class="search-box d-none d-md-block">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search data, users, or reports">
    </div>

    <!-- Mobile Toggle -->
    <button class="btn d-md-none me-3" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Right: Actions -->
    <div class="navbar-actions">
        <!-- Notifications -->
        <div class="action-icon">
            <i class="far fa-bell"></i>
            <span class="badge-dot"></span>
        </div>

        <!-- Light/Dark Toggle (Static for now as per UI) -->
        <div class="action-icon">
            <i class="far fa-moon"></i>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="user-profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if ($user['avatar']): ?>
                    <img src="<?php echo AVATAR_IMAGE_URL . $user['avatar']; ?>" class="user-img" alt="Profile">
                <?php else: ?>
                    <div class="user-img bg-primary text-white d-flex align-items-center justify-content-center"
                        style="font-weight: 700;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <span class="d-none d-lg-inline"><?php echo htmlspecialchars($user['name']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-sm mt-3" style="border-radius: 12px;">
                <li><a class="dropdown-item rounded-3" href="#"><i class="far fa-user me-2"></i> Profile</a></li>
                <li><a class="dropdown-item rounded-3" href="general-settings.php"><i class="fas fa-cog me-2"></i>
                        Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item rounded-3 text-danger" href="logout.php"><i
                            class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>