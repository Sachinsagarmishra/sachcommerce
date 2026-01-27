<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$page_title = 'Blog';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$category_slug = $_GET['category'] ?? '';

$sql = "SELECT b.*, c.name as category_name, c.slug as category_slug, u.name as author_name 
        FROM blog_posts b 
        LEFT JOIN blog_categories c ON b.category_id = c.id 
        LEFT JOIN users u ON b.author_id = u.id 
        WHERE b.status = 'published'";
$params = [];

if ($search) {
    $sql .= " AND (b.title LIKE ? OR b.content LIKE ? OR b.excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_slug) {
    $sql .= " AND c.slug = ?";
    $params[] = $category_slug;
}

$sql .= " ORDER BY b.published_at DESC LIMIT $limit OFFSET $offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    // Get total posts for pagination
    $count_sql = "SELECT COUNT(*) FROM blog_posts b 
                  LEFT JOIN blog_categories c ON b.category_id = c.id 
                  WHERE b.status = 'published'";
    $count_params = [];
    
    if ($search) {
        $count_sql .= " AND (b.title LIKE ? OR b.content LIKE ? OR b.excerpt LIKE ?)";
        $count_params[] = "%$search%";
        $count_params[] = "%$search%";
        $count_params[] = "%$search%";
    }
    if ($category_slug) {
        $count_sql .= " AND c.slug = ?";
        $count_params[] = $category_slug;
    }
    
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($count_params);
    $total_posts = $stmt_count->fetchColumn();
    $total_pages = ceil($total_posts / $limit);
} catch (Exception $e) {
    $posts = [];
    $total_pages = 0;
    echo "<!-- Debug Error: " . htmlspecialchars($e->getMessage()) . " -->";
}

// Get categories for sidebar
try {
    $categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Blog</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Blog Content -->
        <div class="col-lg-8">
            <h1 class="mb-4"><?php echo $category_slug ? 'Category: ' . htmlspecialchars($category_slug) : 'Latest Blog Posts'; ?></h1>
            
            <?php if (!empty($posts)): ?>
                <div class="row g-4">
                    <?php foreach ($posts as $post): ?>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- Updated image path to 'featured_image' and uploads/blog/ -->
                            <a href="blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php if($post['featured_image']): ?>
                                <img src="<?php echo 'uploads/blog/' . $post['featured_image']; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/600x400?text=No+Image" 
                                     class="card-img-top" alt="No Image"
                                     style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                            </a>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?></span>
                                    <small class="text-muted ms-2"><i class="far fa-calendar-alt me-1"></i><?php echo date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?></small>
                                </div>
                                <h5 class="card-title">
                                    <!-- Updated link to blog-post.php -->
                                    <a href="blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    <?php echo substr(strip_tags($post['excerpt'] ?? $post['content']), 0, 100) . '...'; ?>
                                </p>
                                <div class="mt-auto pt-3">
                                    <a href="blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-outline-primary btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $category_slug ? '&category='.$category_slug : ''; ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $category_slug ? '&category='.$category_slug : ''; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $category_slug ? '&category='.$category_slug : ''; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info">
                    No blog posts found.
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Search</h5>
                    <form action="" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Categories</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <a href="blog.php" class="text-decoration-none text-dark">All Categories</a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <a href="?category=<?php echo $cat['slug']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
