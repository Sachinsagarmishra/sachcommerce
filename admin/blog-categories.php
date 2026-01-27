<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Blog Categories';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    
    if (isset($_POST['id']) && $_POST['id']) {
        // Edit
        $stmt = $pdo->prepare("UPDATE blog_categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $_POST['id']]);
        $_SESSION['success'] = 'Category updated';
    } else {
        // Add
        $stmt = $pdo->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        $_SESSION['success'] = 'Category added';
    }
    header('Location: blog-categories.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM blog_categories WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $_SESSION['success'] = 'Category deleted';
    header('Location: blog-categories.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4">Blog Categories</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Add New Category</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <button class="btn btn-primary w-100">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
