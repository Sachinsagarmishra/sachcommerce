<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'recent';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 3;
$offset = ($page - 1) * $limit;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit;
}

try {
    // 0. Proactive DB check for helpful column
    $pdo->exec("ALTER TABLE reviews ADD COLUMN IF NOT EXISTS helpful_count INT DEFAULT 0");

    // 1. Get total count for pagination
    $count_sql = "SELECT COUNT(*) FROM reviews WHERE product_id = ? AND status = 'approved'";
    if ($filter === 'pics') {
        $count_sql = "SELECT COUNT(DISTINCT r.id) FROM reviews r 
                     JOIN review_images ri ON r.id = ri.review_id 
                     WHERE r.product_id = ? AND r.status = 'approved'";
    }
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute([$product_id]);
    $total_reviews = $stmt_count->fetchColumn();
    $total_pages = ceil($total_reviews / $limit);

    // 2. Fetch reviews with filtering and pagination
    $sql = "SELECT r.*, u.name as user_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? AND r.status = 'approved'";

    if ($filter === 'pics') {
        $sql .= " AND EXISTS (SELECT 1 FROM review_images ri WHERE ri.review_id = r.id)";
    }

    // Sorting logic
    switch ($filter) {
        case 'highest':
            $sql .= " ORDER BY r.rating DESC, r.created_at DESC";
            break;
        case 'lowest':
            $sql .= " ORDER BY r.rating ASC, r.created_at DESC";
            break;
        case 'helpful':
            $sql .= " ORDER BY r.helpful_count DESC, r.created_at DESC";
            break;
        case 'pics_first':
            $sql .= " ORDER BY (SELECT COUNT(*) FROM review_images ri WHERE ri.review_id = r.id) DESC, r.created_at DESC";
            break;
        case 'recent':
        default:
            $sql .= " ORDER BY r.created_at DESC";
            break;
    }

    $sql .= " LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch images and admin replies for each review
    foreach ($reviews as &$review) {
        // Remove 'Admin' from name if it exists
        if ($review['user_name'] === 'Admin User') {
            $review['user_name'] = 'Customer';
        }

        $stmt_img = $pdo->prepare("SELECT image_path FROM review_images WHERE review_id = ?");
        $stmt_img->execute([$review['id']]);
        $review['images'] = $stmt_img->fetchAll(PDO::FETCH_COLUMN);

        // Date formatting
        $review['formatted_date'] = date('d M Y', strtotime($review['created_at']));

        // Rating HTML (reusing display_rating logic here since we need it in JS)
        $review['rating_html'] = display_rating($review['rating'], false);

        // Review text consistency
        if (!isset($review['review_text']) && isset($review['comment'])) {
            $review['review_text'] = $review['comment'];
        }
    }

    echo json_encode([
        'success' => true,
        'reviews' => $reviews,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_reviews' => $total_reviews
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
