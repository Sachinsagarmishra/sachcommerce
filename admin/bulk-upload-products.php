<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Bulk Upload Products';
$error = null;
$success = null;
$warnings = [];

// Create upload directory if it doesn't exist
$upload_dir = '../uploads/products/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle CSV download
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_products.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['name', 'slug', 'sku', 'category_name', 'short_description', 'long_description', 'price', 'sale_price', 'stock_quantity', 'status', 'is_featured', 'is_new_arrival', 'is_best_seller', 'image_name']);
    fputcsv($output, ['iPhone 15 Pro', 'iphone-15-pro', 'IPH15P', 'Electronics', 'Latest iPhone with titanium design', 'Full description here...', '999.99', '899.99', '50', 'active', '1', '1', '0', 'iphone-15-pro.jpg']);
    fputcsv($output, ['Samsung Galaxy S24', 'samsung-galaxy-s24', 'SGS24', 'Smartphones', 'Latest Samsung flagship phone', 'Full description here...', '899.99', '799.99', '30', 'active', '0', '1', '1', 'samsung-s24.png']);
    fclose($output);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle bulk images upload first
    $uploaded_images = [];
    if (isset($_FILES['bulk_images']) && !empty($_FILES['bulk_images']['name'][0])) {
        $image_count = count($_FILES['bulk_images']['name']);

        for ($i = 0; $i < $image_count; $i++) {
            if ($_FILES['bulk_images']['error'][$i] === 0) {
                $original_name = $_FILES['bulk_images']['name'][$i];
                $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                // Check if valid image
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($extension, $allowed_extensions)) {
                    // Generate unique filename but keep original name for matching
                    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $original_name);
                    $target_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['bulk_images']['tmp_name'][$i], $target_path)) {
                        // Store with original name (without extension) as key for matching
                        $name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);
                        $uploaded_images[strtolower($name_without_ext)] = $new_filename;
                    }
                }
            }
        }

        if (count($uploaded_images) > 0) {
            $warnings[] = "Uploaded " . count($uploaded_images) . " images successfully.";
        }
    }

    // Handle CSV upload
    if (isset($_FILES['bulk_file']) && $_FILES['bulk_file']['error'] === 0) {
        $file = $_FILES['bulk_file'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file_extension === 'csv') {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
                // Get header
                $header = fgetcsv($handle, 2000, ",");

                // Clean header BOM if present
                if (!empty($header[0])) {
                    $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
                }

                // Fetch all categories for mapping
                $categories_list = get_menu_categories();
                $category_map = [];
                foreach ($categories_list as $cat) {
                    $category_map[strtolower(trim($cat['name']))] = $cat['id'];
                }

                $row_count = 0;
                $success_count = 0;
                $created_categories = 0;
                $error_rows = [];

                try {
                    $pdo->beginTransaction();

                    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                        $row_count++;
                        if (count($data) < 4)
                            continue; // Need at least name, sku, category, price

                        // Map data
                        $row = array_combine($header, array_pad($data, count($header), ''));

                        $name = sanitize_input($row['name'] ?? '');
                        $slug = sanitize_input($row['slug'] ?? '');
                        $sku = sanitize_input($row['sku'] ?? '');
                        $cat_name = trim($row['category_name'] ?? '');
                        $cat_name_lower = strtolower($cat_name);
                        $short_desc = sanitize_input($row['short_description'] ?? '');
                        $long_desc = $row['long_description'] ?? '';
                        $price = (float) ($row['price'] ?? 0);
                        $sale_price = !empty($row['sale_price']) ? (float) $row['sale_price'] : null;
                        $stock = (int) ($row['stock_quantity'] ?? 0);
                        $status = sanitize_input($row['status'] ?? 'active');
                        $is_feat = (int) ($row['is_featured'] ?? 0);
                        $is_new = (int) ($row['is_new_arrival'] ?? 0);
                        $is_best = (int) ($row['is_best_seller'] ?? 0);
                        $image_name = trim($row['image_name'] ?? '');

                        // Skip if no name
                        if (empty($name)) {
                            $error_rows[] = "Row $row_count: Product name is required.";
                            continue;
                        }

                        // Auto-create slug if empty
                        if (empty($slug)) {
                            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
                            $slug = trim($slug, '-');
                        }

                        // Auto-generate SKU if empty
                        if (empty($sku)) {
                            $sku = 'SKU' . strtoupper(substr(md5($name . time()), 0, 8));
                        }

                        // Get or Create category
                        $category_id = $category_map[$cat_name_lower] ?? null;
                        if (!$category_id && !empty($cat_name)) {
                            // Create new category
                            $cat_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $cat_name));
                            $cat_slug = trim($cat_slug, '-');

                            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, status, created_at) VALUES (?, ?, 'active', NOW())");
                            $stmt->execute([$cat_name, $cat_slug]);
                            $category_id = $pdo->lastInsertId();

                            // Add to map for future rows
                            $category_map[$cat_name_lower] = $category_id;
                            $created_categories++;
                        }

                        // Calculate discount
                        $discount = 0;
                        if ($sale_price && $sale_price < $price) {
                            $discount = round((($price - $sale_price) / $price) * 100);
                        }

                        // Find matching image
                        $primary_image = null;

                        // Method 1: Check if image_name column has a value
                        if (!empty($image_name)) {
                            $image_name_without_ext = pathinfo($image_name, PATHINFO_FILENAME);
                            $image_key = strtolower($image_name_without_ext);
                            if (isset($uploaded_images[$image_key])) {
                                $primary_image = $uploaded_images[$image_key];
                            }
                        }

                        // Method 2: Try matching by product name
                        if (!$primary_image) {
                            $product_name_key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
                            foreach ($uploaded_images as $img_key => $img_filename) {
                                $clean_img_key = preg_replace('/[^a-zA-Z0-9]/', '', $img_key);
                                if ($clean_img_key === $product_name_key || strpos($clean_img_key, $product_name_key) !== false || strpos($product_name_key, $clean_img_key) !== false) {
                                    $primary_image = $img_filename;
                                    break;
                                }
                            }
                        }

                        // Method 3: Try matching by slug
                        if (!$primary_image) {
                            $slug_key = strtolower(str_replace('-', '', $slug));
                            foreach ($uploaded_images as $img_key => $img_filename) {
                                $clean_img_key = str_replace(['-', '_'], '', $img_key);
                                if ($clean_img_key === $slug_key) {
                                    $primary_image = $img_filename;
                                    break;
                                }
                            }
                        }

                        // Insert product
                        $stmt = $pdo->prepare("INSERT INTO products (
                            category_id, name, slug, sku, short_description, long_description,
                            price, sale_price, discount_percentage, stock_quantity,
                            is_featured, is_new_arrival, is_best_seller, status, primary_image, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

                        $stmt->execute([
                            $category_id,
                            $name,
                            $slug,
                            $sku,
                            $short_desc,
                            $long_desc,
                            $price,
                            $sale_price,
                            $discount,
                            $stock,
                            $is_feat,
                            $is_new,
                            $is_best,
                            $status,
                            $primary_image
                        ]);

                        $success_count++;
                    }

                    $pdo->commit();

                    $success = "Successfully imported $success_count products.";
                    if ($created_categories > 0) {
                        $success .= " Created $created_categories new categories.";
                    }

                    if (!empty($error_rows)) {
                        $error = implode("<br>", $error_rows);
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Database Error: " . $e->getMessage();
                }

                fclose($handle);
            } else {
                $error = "Could not open the file.";
            }
        } else {
            $error = "Please upload a valid CSV file.";
        }
    } elseif (empty($uploaded_images)) {
        $error = "Please upload a CSV file.";
    } else {
        $success = "Images uploaded successfully. Now upload CSV to create products.";
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }

    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: var(--primary-color, #0d6efd);
        background: rgba(13, 110, 253, 0.05);
    }

    .upload-zone i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .image-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .feature-item i {
        color: #28a745;
        margin-right: 10px;
        margin-top: 3px;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Bulk Upload Products</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?php echo implode('<br>', $warnings); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Left Column: Upload Forms -->
                <div class="col-lg-7">
                    <!-- CSV Upload -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-file-csv me-2 text-success"></i>Step 1: Upload CSV File
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select CSV File <span class="text-danger">*</span></label>
                                <input type="file" name="bulk_file" class="form-control" accept=".csv" required>
                                <div class="form-text mt-2">
                                    Upload your product list in CSV format. Categories will be auto-created if they
                                    don't exist.
                                </div>
                            </div>
                            <a href="?download_sample=1" class="btn btn-outline-success">
                                <i class="fas fa-download me-2"></i>Download Sample CSV
                            </a>
                        </div>
                    </div>

                    <!-- Images Upload -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-images me-2 text-primary"></i>Step 2: Upload Product
                                Images (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="upload-zone" id="dropZone"
                                onclick="document.getElementById('bulk_images').click()">
                                <i class="fas fa-cloud-upload-alt d-block"></i>
                                <h5>Drag & Drop Images Here</h5>
                                <p class="text-muted mb-0">or click to browse</p>
                                <small class="text-muted">Supports: JPG, JPEG, PNG, GIF, WEBP</small>
                            </div>
                            <input type="file" name="bulk_images[]" id="bulk_images" class="d-none" multiple
                                accept="image/*" onchange="previewImages(this)">

                            <div class="image-preview-container" id="imagePreviewContainer"></div>

                            <div class="alert alert-info mt-3 mb-0">
                                <strong>Auto-Sync Tip:</strong> Name your images to match product names in CSV.<br>
                                Example: If CSV has product <code>iPhone 15 Pro</code>, name image as
                                <code>iphone-15-pro.jpg</code> or <code>iPhone 15 Pro.png</code>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-upload me-2"></i>Upload Products
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Instructions -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Features & Instructions
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3">✨ New Features:</h6>

                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Auto-Create Categories</strong><br>
                                    <small class="text-muted">Categories in CSV that don't exist will be automatically
                                        created</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Bulk Image Upload</strong><br>
                                    <small class="text-muted">Upload multiple images at once</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Auto Image Sync</strong><br>
                                    <small class="text-muted">Images automatically matched to products by name</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Auto-Generate SKU & Slug</strong><br>
                                    <small class="text-muted">Leave empty in CSV to auto-generate</small>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">📋 CSV Columns:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Column</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>name</td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td>category_name</td>
                                            <td><span class="badge bg-warning">Recommended</span></td>
                                        </tr>
                                        <tr>
                                            <td>price</td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td>slug</td>
                                            <td><span class="badge bg-secondary">Auto</span></td>
                                        </tr>
                                        <tr>
                                            <td>sku</td>
                                            <td><span class="badge bg-secondary">Auto</span></td>
                                        </tr>
                                        <tr>
                                            <td>sale_price</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                        <tr>
                                            <td>stock_quantity</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                        <tr>
                                            <td>short_description</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                        <tr>
                                            <td>long_description</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                        <tr>
                                            <td>status</td>
                                            <td><span class="badge bg-secondary">Default: active</span></td>
                                        </tr>
                                        <tr>
                                            <td>is_featured</td>
                                            <td><span class="badge bg-secondary">0 or 1</span></td>
                                        </tr>
                                        <tr>
                                            <td>is_new_arrival</td>
                                            <td><span class="badge bg-secondary">0 or 1</span></td>
                                        </tr>
                                        <tr>
                                            <td>is_best_seller</td>
                                            <td><span class="badge bg-secondary">0 or 1</span></td>
                                        </tr>
                                        <tr>
                                            <td>image_name</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('bulk_images');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
    });

    dropZone.addEventListener('drop', function (e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        previewImages(fileInput);
    });

    function previewImages(input) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';

        if (input.files) {
            const count = Math.min(input.files.length, 20); // Show max 20 previews

            for (let i = 0; i < count; i++) {
                const file = input.files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.style.position = 'relative';
                        div.innerHTML = `
                        <img src="${e.target.result}" class="image-preview" title="${file.name}">
                        <div style="font-size: 10px; text-align: center; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</div>
                    `;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            }

            if (input.files.length > 20) {
                const moreDiv = document.createElement('div');
                moreDiv.className = 'align-self-center text-muted';
                moreDiv.textContent = `+${input.files.length - 20} more`;
                container.appendChild(moreDiv);
            }
        }
    }
</script>

<?php include 'includes/footer.php'; ?>