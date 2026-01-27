<?php
require_once '../config/config.php';
require_once 'includes/auth-check.php';
$page_title = 'SEO Settings';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">SEO Settings</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label>Default Meta Title</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Default Meta Description</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Google Analytics ID</label>
                        <input type="text" class="form-control" placeholder="UA-XXXXX-Y">
                    </div>
                    <button class="btn btn-primary">Save SEO Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
