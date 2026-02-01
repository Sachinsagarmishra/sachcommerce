<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Bulk Upload Products';
$error = null;
$success = null;

// Handle CSV download
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_products.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['name', 'slug', 'sku', 'category_name', 'short_description', 'long_description', 'price', 'sale_price', 'stock_quantity', 'status', 'is_featured', 'is_new_arrival', 'is_best_seller']);
    fputcsv($output, ['iPhone 15 Pro', 'iphone-15-pro', 'IPH15P', 'Electronics', 'Latest iPhone with titanium design', 'Full description here...', '999.99', '899.99', '50', 'active', '1', '1', '0']);
    fclose($output);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bulk_file'])) {
    $file = $_FILES['bulk_file'];

    if ($file['error'] === 0) {
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file_extension === 'csv') {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
                // Get header
                $header = fgetcsv($handle, 1000, ",");

                // Fetch all categories for mapping
                $categories_list = get_menu_categories();
                $category_map = [];
                foreach ($categories_list as $cat) {
                    $category_map[strtolower($cat['name'])] = $cat['id'];
                }

                $row_count = 0;
                $success_count = 0;
                $error_rows = [];

                try {
                    $pdo->beginTransaction();

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row_count++;
                        if (count($data) < count($header))
                            continue;

                        // Map data
                        $row = array_combine($header, $data);

                        $name = sanitize_input($row['name'] ?? '');
                        $slug = sanitize_input($row['slug'] ?? '');
                        $sku = sanitize_input($row['sku'] ?? '');
                        $cat_name = strtolower(trim($row['category_name'] ?? ''));
                        $short_desc = sanitize_input($row['short_description'] ?? '');
                        $long_desc = $row['long_description'] ?? '';
                        $price = (float) ($row['price'] ?? 0);
                        $sale_price = !empty($row['sale_price']) ? (float) $row['sale_price'] : null;
                        $stock = (int) ($row['stock_quantity'] ?? 0);
                        $status = sanitize_input($row['status'] ?? 'active');
                        $is_feat = (int) ($row['is_featured'] ?? 0);
                        $is_new = (int) ($row['is_new_arrival'] ?? 0);
                        $is_best = (int) ($row['is_best_seller'] ?? 0);

                        // Get category ID
                        $category_id = $category_map[$cat_name] ?? null;
                        if (!$category_id) {
                            $error_rows[] = "Row $row_count: Category '$cat_name' not found.";
                            continue;
                        }

                        // Calculate discount
                        $discount = 0;
                        if ($sale_price && $sale_price < $price) {
                            $discount = round((($price - $sale_price) / $price) * 100);
                        }

                        // Insert product
                        $stmt = $pdo->prepare("INSERT INTO products (
                            category_id, name, slug, sku, short_description, long_description,
                            price, sale_price, discount_percentage, stock_quantity,
                            is_featured, is_new_arrival, is_best_seller, status, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

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
                            $status
                        ]);

                        $success_count++;
                    }

                    $pdo->commit();
                    $success = "Successfully imported $success_count products.";
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
    } else {
        $error = "Error uploading file.";
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

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

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Upload CSV File</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label">Select CSV File</label>
                                <input type="file" name="bulk_file" class="form-control" accept=".csv" required>
                                <div class="form-text mt-2">Upload your product list in CSV format.</div>
                            </div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-upload me-2"></i>Start Upload
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p>Follow these steps to upload products in bulk:</p>
                        <ol class="ps-3">
                            <li class="mb-2">Download the sample CSV file to see the required format.</li>
                            <li class="mb-2">Ensure all categories mentioned in the CSV already exist in the system.
                            </li>
                            <li class="mb-2">Use <code>1</code> for Yes/True and <code>0</code> for No/False in
                                Featured, New Arrival, etc.</li>
                            <li class="mb-2">Price and Stock should be numeric values.</li>
                        </ol>
                        <a href="?download_sample=1" class="btn btn-outline-primary mt-3">
                            <i class="fas fa-download me-2"></i>Download Sample CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>