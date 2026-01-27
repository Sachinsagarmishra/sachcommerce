<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Email Settings';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Email Configuration (SMTP)</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="mb-3"><label>SMTP Host</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label>SMTP Port</label><input type="text" class="form-control" value="587"></div>
                    <div class="mb-3"><label>SMTP Username</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label>SMTP Password</label><input type="password" class="form-control"></div>
                    <button class="btn btn-primary">Save Configuration</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
