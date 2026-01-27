<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Shipping Settings';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Shipping Settings</h1>
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">Shipping zones and flat rates configuration.</div>
                <button class="btn btn-primary mb-3">Add Shipping Zone</button>
                <table class="table">
                    <thead><tr><th>Zone Name</th><th>Regions</th><th>Rate</th><th>Actions</th></tr></thead>
                    <tbody><tr><td colspan="4" class="text-center">No zones configured</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
