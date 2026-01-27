<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

http_response_code(404);
$page_title = '404 - Page Not Found';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <h1 style="font-size: 120px; font-weight: bold;" class="text-primary mb-4">404</h1>
            <h2 class="mb-3">Page Not Found</h2>
            <p class="lead text-muted mb-4">Sorry, the page you are looking for doesn't exist or has been moved.</p>
            
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
            </div>
            
            <div class="mt-5">
                <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="d-flex justify-content-center">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" class="form-control" name="q" placeholder="Search products..." required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
