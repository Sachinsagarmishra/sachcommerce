<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Tax Settings';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Tax Settings</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Enable Tax Calculation</label>
                    </div>
                    <div class="mb-3">
                        <label>Default Tax Rate (%)</label>
                        <input type="number" class="form-control" value="18">
                    </div>
                    <button class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
