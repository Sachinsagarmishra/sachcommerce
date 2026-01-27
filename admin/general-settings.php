<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'General Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save settings logic would go here
    $_SESSION['success'] = 'Settings saved';
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">General Settings</h1>
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Store Name</label>
                        <input type="text" class="form-control" name="site_name" value="<?php echo defined('SITE_NAME') ? SITE_NAME : 'My Store'; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support Email</label>
                        <input type="email" class="form-control" name="support_email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <button class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
