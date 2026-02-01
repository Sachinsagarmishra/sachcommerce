<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$title = isset($_POST['title']) ? sanitize_input($_POST['title']) : ''; // Optional title
$comment = isset($_POST['review_text']) ? sanitize_input($_POST['review_text']) : ''; // We'll keep 'review_text' name for POST but save to 'comment'

if (!$product_id || !$rating || !$comment) {
    echo json_encode(['success' => false, 'message' => 'Please provide rating and review text.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert review
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, title, comment, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$user_id, $product_id, $rating, $title, $comment]);
    $review_id = $pdo->lastInsertId();

    // Handle Image Uploads
    if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $upload_dir = '../uploads/reviews/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $files = $_FILES['review_images'];
        $file_count = count($files['name']);

        // Limit to 5 images per review (optional but good practice)
        $file_count = min($file_count, 5);

        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === 0) {
                if ($files['size'][$i] > $max_size) {
                    throw new Exception("File " . ($i + 1) . " is too large. Max 5MB allowed.");
                }

                if (!in_array($files['type'][$i], $allowed_types)) {
                    throw new Exception("File " . ($i + 1) . " has an invalid format. Only JPEG, PNG, and WEBP are allowed.");
                }

                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = uniqid('rev_') . '_' . time() . '.' . $ext;
                $target_path = $upload_dir . $filename;

                if (move_uploaded_file($files['tmp_name'][$i], $target_path)) {
                    $stmt_img = $pdo->prepare("INSERT INTO review_images (review_id, image_path) VALUES (?, ?)");
                    $stmt_img->execute([$review_id, $filename]);
                }
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully! It will be live after approval.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
