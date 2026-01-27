<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Product Report';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Product Performance Report</h1>
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>Product</th><th>Units Sold</th><th>Revenue</th><th>Stock Status</th></tr></thead>
                    <tbody>
                        <tr><td colspan="4" class="text-center">No data available</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
