<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'Sales Report';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Sales Report</h1>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="date" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" placeholder="End Date">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100">Filter Report</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Sales</h5>
                        <h3>$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Orders</h5>
                        <h3>0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Average Order Value</h5>
                        <h3>$0.00</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <p>Chart visualization would appear here</p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
