<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Blog Posts';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Post deleted successfully';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to delete post';
    }
    header('Location: blog-posts.php');
    exit;
}

// Fetch posts
$sql = "SELECT b.*, c.name as category_name, u.name as author_name 
        FROM blog_posts b 
        LEFT JOIN blog_categories c ON b.category_id = c.id 
        LEFT JOIN users u ON b.author_id = u.id 
        ORDER BY b.created_at DESC";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    // For debugging: echo $e->getMessage();
    $posts = [];
    $_SESSION['error'] = "Error loading posts: " . $e->getMessage();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Blog Posts</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="add-blog-post.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Post
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $post['image'] ? '../uploads/blog/' . $post['image'] : 'https://via.placeholder.com/50'; ?>" 
                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($post['title']); ?></td>
                                <td><?php echo htmlspecialchars($post['category_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($post['author_name'] ?? 'Unknown'); ?></td>
                                <td>
                                    <span class="badge <?php echo $post['status'] == 'published' ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <a href="edit-blog-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($posts)): ?>
                            <tr><td colspan="7" class="text-center text-muted">No posts found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
