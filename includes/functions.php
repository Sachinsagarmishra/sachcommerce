<?php
/**
 * Frontend Helper Functions
 */

/**
 * Get featured products
 */
function get_featured_products($limit = 8)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.* 
                          FROM products p 
                          WHERE p.is_featured = 1 AND p.status = 'active' 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get new arrival products
 */
function get_new_arrivals($limit = 8)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.* 
                          FROM products p 
                          WHERE p.is_new_arrival = 1 AND p.status = 'active' 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get best seller products
 */
function get_best_sellers($limit = 8)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.* 
                          FROM products p 
                          WHERE p.is_best_seller = 1 AND p.status = 'active' 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get product by slug
 */
function get_product_by_slug($slug)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.slug = ? AND p.status = 'active'");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get product images
 */
if (!function_exists('get_product_images')) {
    function get_product_images($product_id)
    {
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll();
    }
}

/**
 * Get category by slug
 */
function get_category_by_slug($slug)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active'");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get products by category
 */
function get_products_by_category($category_id, $limit = null, $offset = 0)
{
    global $pdo;

    $sql = "SELECT p.*, 
            (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p 
            WHERE p.category_id = ? AND p.status = 'active' 
            ORDER BY p.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id, $limit, $offset]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id]);
    }

    return $stmt->fetchAll();
}

/**
 * Get all products with pagination
 */
function get_all_products($limit = PRODUCTS_PER_PAGE, $offset = 0, $filters = [])
{
    global $pdo;

    $sql = "SELECT p.* 
            FROM products p 
            WHERE p.status = 'active'";

    $params = [];

    // Apply filters
    if (!empty($filters['category'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filters['category'];
    }

    if (!empty($filters['min_price'])) {
        $sql .= " AND (COALESCE(p.sale_price, p.price)) >= ?";
        $params[] = $filters['min_price'];
    }

    if (!empty($filters['max_price'])) {
        $sql .= " AND (COALESCE(p.sale_price, p.price)) <= ?";
        $params[] = $filters['max_price'];
    }

    if (!empty($filters['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }

    // Sorting
    $sort = $filters['sort'] ?? 'newest';
    switch ($sort) {
        case 'price_low':
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
            break;
        case 'price_high':
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
            break;
        case 'name':
            $sql .= " ORDER BY p.name ASC";
            break;
        default:
            $sql .= " ORDER BY p.created_at DESC";
    }

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get total product count
 */
function get_total_products($filters = [])
{
    global $pdo;

    $sql = "SELECT COUNT(*) as count FROM products p WHERE p.status = 'active'";
    $params = [];

    if (!empty($filters['category'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filters['category'];
    }

    if (!empty($filters['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['count'];
}

/**
 * Get product reviews
 */
function get_product_reviews($product_id, $limit = null)
{
    global $pdo;

    $sql = "SELECT r.*, u.name as user_name, u.avatar 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? AND r.status = 'approved' 
            ORDER BY r.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $limit]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
    }

    return $stmt->fetchAll();
}

/**
 * Get average product rating
 */
function get_product_rating($product_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                          FROM reviews 
                          WHERE product_id = ? AND status = 'approved'");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

/**
 * Get related products
 */
function get_related_products($product_id, $category_id, $limit = 4)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.* 
                          FROM products p 
                          WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
                          ORDER BY RAND() 
                          LIMIT ?");
    $stmt->execute([$category_id, $product_id, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get cart items
 */
function get_cart_items()
{
    global $pdo;

    if (is_logged_in()) {
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.slug, p.stock_quantity,
                              (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                              FROM cart c 
                              JOIN products p ON c.product_id = p.id 
                              WHERE c.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $session_id = session_id();
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.slug, p.stock_quantity,
                              (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                              FROM cart c 
                              JOIN products p ON c.product_id = p.id 
                              WHERE c.session_id = ?");
        $stmt->execute([$session_id]);
    }

    return $stmt->fetchAll();
}

/**
 * Calculate cart total
 */
function get_cart_total()
{
    $items = get_cart_items();
    $total = 0;

    foreach ($items as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $total += $price * $item['quantity'];
    }

    return $total;
}

/**
 * Get wishlist items
 */
function get_wishlist_items()
{
    global $pdo;

    if (!is_logged_in()) {
        return [];
    }

    $stmt = $pdo->prepare("SELECT w.*, p.name, p.price, p.sale_price, p.slug,
                          (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                          FROM wishlist w 
                          JOIN products p ON w.product_id = p.id 
                          WHERE w.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

/**
 * Check if product is in wishlist
 */
function is_in_wishlist($product_id)
{
    global $pdo;

    if (!is_logged_in()) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $result = $stmt->fetch();
    return $result['count'] > 0;
}

/**
 * Get blog posts
 */
function get_blog_posts($limit = BLOG_PER_PAGE, $offset = 0, $category_id = null)
{
    global $pdo;

    $sql = "SELECT p.*, u.name as author_name, c.name as category_name, c.slug as category_slug
            FROM blog_posts p 
            JOIN users u ON p.author_id = u.id 
            LEFT JOIN blog_categories c ON p.category_id = c.id 
            WHERE p.status = 'published'";

    $params = [];

    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    $sql .= " ORDER BY p.published_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get blog post by slug
 */
function get_blog_post_by_slug($slug)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.*, u.name as author_name, u.avatar as author_avatar, 
                          c.name as category_name, c.slug as category_slug
                          FROM blog_posts p 
                          JOIN users u ON p.author_id = u.id 
                          LEFT JOIN blog_categories c ON p.category_id = c.id 
                          WHERE p.slug = ? AND p.status = 'published'");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get active categories for menu
 */
function get_menu_categories($limit = null)
{
    global $pdo;

    $sql = "SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY display_order ASC, name ASC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
    } else {
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * Display star rating
 */
function display_rating($rating, $show_number = true)
{
    $rating = round($rating * 2) / 2; // Round to nearest 0.5
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;

    $html = '<div class="product-rating">';

    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<i class="lni lni-star-filled text-warning"></i>';
    }

    // Half star
    if ($half_star) {
        $html .= '<i class="lni lni-star-filled-half-alt text-warning"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<i class="lni lni-star text-warning"></i>';
    }

    if ($show_number) {
        $html .= ' <span class="rating-number">(' . number_format($rating, 1) . ')</span>';
    }

    $html .= '</div>';

    return $html;
}
