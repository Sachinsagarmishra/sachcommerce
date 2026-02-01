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

    $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC, name ASC";

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
        $html .= '<i class="fas fa-star text-warning"></i>';
    }

    // Half star
    if ($half_star) {
        $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<i class="far fa-star text-warning"></i>';
    }

    if ($show_number) {
        $html .= ' <span class="rating-number">(' . number_format($rating, 1) . ')</span>';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Send Order Confirmation Email to Customer
 * Uses PHPMailer with dynamic SMTP settings from admin panel
 * @param int $order_id Order ID
 * @param string $order_number Order number
 * @param string $email Customer email
 * @param string $name Customer name
 * @param float $total Order total
 * @param array $items Order items
 * @param array|null $guest_credentials Guest login credentials if new account created
 */
function sendOrderConfirmationEmail($order_id, $order_number, $email, $name, $total, $items, $guest_credentials = null)
{
    // Get site settings
    $site_name = get_site_setting('site_name', SITE_NAME ?? 'TrendsOne');
    $support_phone = get_site_setting('support_phone', '');
    $support_email = get_site_setting('support_email', '');

    // Format currency
    $formatted_total = '₹' . number_format($total, 2);

    // Build items HTML
    $items_html = '';
    foreach ($items as $item) {
        // Use product_name if available (from order_items table), otherwise look for 'name' key
        $p_name = $item['product_name'] ?? ($item['name'] ?? '');
        $item_total = '₹' . number_format($item['price'] * $item['quantity'], 2);
        $items_html .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$p_name}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$item_total}</td>
            </tr>
        ";
    }

    // Build guest credentials HTML if applicable
    $credentials_html = '';
    if ($guest_credentials) {
        $credentials_html = "
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                    <h4 style='margin: 0 0 10px 0; color: #856404;'>🔐 Your Account Has Been Created</h4>
                    <p style='margin: 0 0 5px 0; color: #856404;'>We've created an account for you to track orders and shop faster:</p>
                    <div style='background: #fff; padding: 10px; border-radius: 5px; margin-top: 10px;'>
                        <p style='margin: 0 0 5px 0;'><strong>Email:</strong> {$guest_credentials['email']}</p>
                        <p style='margin: 0;'><strong>Password:</strong> {$guest_credentials['password']}</p>
                    </div>
                    <p style='margin: 10px 0 0 0; font-size: 12px; color: #856404;'><em>⚠️ Please change your password after first login for security.</em></p>
                </div>
        ";
    }

    $subject = "Order Confirmed - #{$order_number} - {$site_name}";

    $message = "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #83b735 0%, #5a8f1d 100%); color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; background: #ffffff; }
            .order-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .order-number { font-size: 28px; color: #83b735; font-weight: bold; }
            .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .items-table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
            .total-row { font-size: 18px; font-weight: bold; background: #f8f9fa; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; background: #f8f9fa; }
            .success-icon { font-size: 48px; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='success-icon'>✓</div>
                <h1>Order Confirmed!</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>{$name}</strong>,</p>
                <p>Thank you for your order! We're getting it ready for you.</p>
                
                <div class='order-box'>
                    <p style='margin: 0 0 10px 0; color: #666;'>Order Number</p>
                    <p class='order-number'>#{$order_number}</p>
                </div>
                
                {$credentials_html}
                
                <h3 style='border-bottom: 2px solid #83b735; padding-bottom: 10px;'>Order Summary</h3>
                <table class='items-table'>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style='text-align: center;'>Qty</th>
                            <th style='text-align: right;'>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$items_html}
                        <tr class='total-row'>
                            <td colspan='2' style='padding: 15px;'>Total</td>
                            <td style='padding: 15px; text-align: right;'>{$formatted_total}</td>
                        </tr>
                    </tbody>
                </table>
                
                <p>We'll notify you when your order is shipped.</p>
                
                <p style='text-align: center; margin-top: 30px;'>
                    <a href='" . SITE_URL . "/orders' style='display: inline-block; padding: 12px 30px; background: #83b735; color: white; text-decoration: none; border-radius: 5px;'>Track Your Order</a>
                </p>
            </div>
            <div class='footer'>
                <p><strong>{$site_name}</strong></p>
                " . ($support_phone ? "<p>📞 {$support_phone}</p>" : "") . "
                " . ($support_email ? "<p>✉️ {$support_email}</p>" : "") . "
                <p>&copy; " . date('Y') . " {$site_name}. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Use the send_email function which uses PHPMailer with dynamic settings
    $result = send_email($email, $subject, '', ['body' => $message]);

    if ($result['success']) {
        error_log("Order confirmation email sent to: $email for order #$order_number");
    } else {
        error_log("Failed to send order confirmation email to: $email - " . $result['message']);
    }

    return $result['success'];
}
