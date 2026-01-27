<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('Location: blog.php');
    exit;
}

// Get blog post
$stmt = $pdo->prepare("SELECT bp.*, bc.name as category_name, bc.slug as category_slug, u.name as author_name 
                       FROM blog_posts bp 
                       LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                       LEFT JOIN users u ON bp.author_id = u.id 
                       WHERE bp.slug = ? AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: 404.php');
    exit;
}

// Update views - Use 'views_count' column as per SQL schema
$stmt = $pdo->prepare("UPDATE blog_posts SET views_count = views_count + 1 WHERE id = ?");
$stmt->execute([$post['id']]);

$page_title = $post['title'];
$meta_description = $post['meta_description'] ?? ($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 160));

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/blog.php">Blog</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article>
                <!-- Post Header -->
                <div class="mb-4">
                    <?php if(isset($post['category_name'])): ?>
                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($post['category_name']); ?></span>
                    <?php endif; ?>
                    <h1 class="mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-user me-2"></i>
                        <span class="me-3"><?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></span>
                        <i class="fas fa-calendar me-2"></i>
                        <span class="me-3"><?php echo date('F d, Y', strtotime($post['published_at'])); ?></span>
                        <i class="fas fa-eye me-2"></i>
                        <!-- Display views_count from database -->
                        <span><?php echo $post['views_count']; ?> views</span>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <?php if ($post['featured_image']): ?>
                <!-- Ensure correct path for blog images -->
                <img src="<?php echo 'uploads/blog/' . $post['featured_image']; ?>" class="img-fluid rounded mb-4 w-100" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Tags -->
                <?php if ($post['tags']): ?>
                <div class="mt-4">
                    <strong>Tags:</strong>
                    <?php
                    $tags = explode(',', $post['tags']);
                    foreach ($tags as $tag):
                    ?>
                    <span class="badge bg-light text-dark me-1"><?php echo trim($tag); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </article>
            
            <hr class="my-5">

            <!-- Share Buttons -->
            <div class="my-4">
                <h5>Share this post</h5>
                <div class="d-flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-primary">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-outline-info">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' ' . SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-success">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
