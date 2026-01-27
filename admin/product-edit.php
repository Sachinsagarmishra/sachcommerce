<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Edit Product';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: products.php');
    exit;
}

// Get product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    header('Location: products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $slug = sanitize_input($_POST['slug']);
    $sku = sanitize_input($_POST['sku']);
    $category_id = (int)$_POST['category_id'];
    $short_description = sanitize_input($_POST['short_description']);
    $long_description = $_POST['long_description'];
    $price = (float)$_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock_quantity = (int)$_POST['stock_quantity'];
    $status = sanitize_input($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new_arrival = isset($_POST['is_new_arrival']) ? 1 : 0;
    $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
    
    // Calculate discount percentage
    $discount_percentage = 0;
    if ($sale_price && $sale_price < $price) {
        $discount_percentage = round((($price - $sale_price) / $price) * 100);
    }
    
    // Handle image upload
    $primary_image = $product['primary_image'];
    if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === 0) {
        $upload_dir = '../uploads/products/';
        $file_extension = strtolower(pathinfo($_FILES['primary_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = $slug . '-1.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $upload_path)) {
                $primary_image = $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET 
            category_id = ?, name = ?, slug = ?, sku = ?, 
            short_description = ?, long_description = ?,
            price = ?, sale_price = ?, discount_percentage = ?, 
            stock_quantity = ?, primary_image = ?,
            is_featured = ?, is_new_arrival = ?, is_best_seller = ?, 
            status = ?, updated_at = NOW()
            WHERE id = ?");
        
        $stmt->execute([
            $category_id, $name, $slug, $sku, 
            $short_description, $long_description,
            $price, $sale_price, $discount_percentage, 
            $stock_quantity, $primary_image,
            $is_featured, $is_new_arrival, $is_best_seller, 
            $status, $product_id
        ]);
        
        $_SESSION['success'] = 'Product updated successfully';
        header('Location: products.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to update product: ' . $e->getMessage();
    }
}

// Get categories
$categories = get_menu_categories();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Edit Product</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SKU *</label>
                                    <input type="text" class="form-control" name="sku" value="<?php echo htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category *</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="2"><?php echo htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Long Description</label>
                                <textarea class="form-control" name="long_description" rows="6"><?php echo htmlspecialchars($product['long_description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Pricing & Stock</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Regular Price *</label>
                                    <input type="number" class="form-control" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Sale Price</label>
                                    <input type="number" class="form-control" name="sale_price" step="0.01" value="<?php echo $product['sale_price']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Image</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($product['primary_image']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo PRODUCT_IMAGE_URL . $product['primary_image']; ?>" 
                                         class="img-fluid rounded" 
                                         alt="Current Image">
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Change Image</label>
                                <input type="file" class="form-control" name="primary_image" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Product Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="out_of_stock" <?php echo $product['status'] === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                </select>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_new_arrival" id="is_new_arrival" <?php echo $product['is_new_arrival'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_new_arrival">New Arrival</label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_best_seller" id="is_best_seller" <?php echo $product['is_best_seller'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_best_seller">Best Seller</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
