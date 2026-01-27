<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Customer Report';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Customer Insights</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Top Customers</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">No data</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">New Registrations (Last 30 Days)</div>
                    <div class="card-body">
                         <h3>0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
