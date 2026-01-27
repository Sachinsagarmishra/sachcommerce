<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
// Remove duplicate include if it exists or keep if needed for admin specific functions, 
// but generally one functions file is better.
// require_once 'includes/functions.php'; 
require_once 'includes/auth-check.php';

$page_title = 'Add Blog Post';
$user_id = $_SESSION['user_id'] ?? 1; // Fallback or current user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $slug = !empty($_POST['slug']) ? sanitize_input($_POST['slug']) : strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    $content = $_POST['content']; // Allow HTML
    $excerpt = $_POST['excerpt'] ?? null;
    $status = $_POST['status'];
    
    // Image Upload
    $featured_image = null; // variable name to match DB column concept
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/blog/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = $slug . '-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $featured_image = $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, category_id, content, excerpt, featured_image, status, author_id, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $slug, $category_id, $content, $excerpt, $featured_image, $status, $user_id]);
        $_SESSION['success'] = 'Post added successfully';
        header('Location: blog-posts.php');
        exit;
    } catch (Exception $e) {
        $error = 'Error adding post: ' . $e->getMessage();
    }
}

// Get categories
try {
    $categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6"><h1 class="h3">Add New Post</h1></div>
            <div class="col-md-6 text-end"><a href="blog-posts.php" class="btn btn-secondary">Back</a></div>
        </div>

        <?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" required id="title" onkeyup="generateSlug(this.value)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" name="slug" required id="slug">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control" name="content" rows="10" required></textarea>
                            </div>
                            <!-- Added Excerpt Field -->
                            <div class="mb-3">
                                <label class="form-label">Excerpt</label>
                                <textarea class="form-control" name="excerpt" rows="3"></textarea>
                                <div class="form-text">Short summary for listing pages.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Publish Post</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function generateSlug(text) {
    document.getElementById('slug').value = text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
}
</script>

<?php include 'includes/footer.php'; ?>
