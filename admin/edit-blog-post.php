<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Edit Blog Post';
$id = (int)($_GET['id'] ?? 0);

if (!$id) { header('Location: blog-posts.php'); exit; }

// Fetch Post
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { header('Location: blog-posts.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $slug = sanitize_input($_POST['slug']);
    $category_id = (int)$_POST['category_id'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $image = $post['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/blog/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = $slug . '-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image = $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, category_id=?, content=?, image=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$title, $slug, $category_id, $content, $image, $status, $id]);
        $_SESSION['success'] = 'Post updated successfully';
        header('Location: blog-posts.php');
        exit;
    } catch (Exception $e) {
        $error = 'Error updating post: ' . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6"><h1 class="h3">Edit Post</h1></div>
            <div class="col-md-6 text-end"><a href="blog-posts.php" class="btn btn-secondary">Back</a></div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
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
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="published" <?php echo $post['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <?php if ($post['image']): ?>
                                <img src="../uploads/blog/<?php echo $post['image']; ?>" class="img-fluid mb-2 rounded">
                                <?php endif; ?>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Post</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
