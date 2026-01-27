<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('Location: blog.php');
    exit;
}

// Get category
$stmt = $pdo->prepare("SELECT * FROM blog_categories WHERE slug = ?");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: 404.php');
    exit;
}

$page_title = $category['name'] . ' - Blog';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Get posts in this category
$stmt = $pdo->prepare("SELECT bp.*, u.name as author_name 
                       FROM blog_posts bp 
                       LEFT JOIN users u ON bp.author_id = u.id 
                       WHERE bp.category_id = ? AND bp.status = 'published' 
                       ORDER BY bp.published_at DESC 
                       LIMIT ? OFFSET ?");
$stmt->bindValue(1, $category['id'], PDO::PARAM_INT);
$stmt->bindValue(2, $per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Get total posts
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM blog_posts WHERE category_id = ? AND status = 'published'");
$stmt->execute([$category['id']]);
$total_posts = $stmt->fetch()['count'];
$total_pages = ceil($total_posts / $per_page);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/blog.php">Blog</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <h1 class="text-center mb-2"><?php echo htmlspecialchars($category['name']); ?></h1>
    <?php if (!empty($category['description'])): ?>
    <p class="text-center text-muted mb-5"><?php echo htmlspecialchars($category['description']); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($posts)): ?>
    <div class="row g-4">
        <?php foreach ($posts as $post): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <?php if ($post['featured_image']): ?>
                <img src="<?php echo 'uploads/blog/' . $post['featured_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 200px; object-fit: cover;">
                <?php else: ?>
                <img src="https://via.placeholder.com/600x400?text=No+Image" class="card-img-top" style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <small class="text-muted mb-2">
                        <i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                    </small>
                    <h5 class="card-title">
                        <a href="<?php echo SITE_URL; ?>/blog-post.php?slug=<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h5>
                    <p class="card-text flex-grow-1"><?php echo truncate_text($post['excerpt'], 150); ?></p>
                    <a href="<?php echo SITE_URL; ?>/blog-post.php?slug=<?php echo $post['slug']; ?>" class="btn btn-outline-primary btn-sm mt-3">
                        Read More <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page - 1; ?>">Previous</a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page + 1; ?>">Next</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-blog fa-4x text-muted mb-4"></i>
        <h4>No posts in this category yet</h4>
        <a href="<?php echo SITE_URL; ?>/blog.php" class="btn btn-primary">View All Posts</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
