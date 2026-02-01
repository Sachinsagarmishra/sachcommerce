<?php
require_once 'config/config.php';

try {
    // 1. Create review_images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS review_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        review_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
    )");
    echo "Table 'review_images' created or already exists.\n";

    // 2. Add 'status' column to 'reviews' if not exists (already seems to exist but good to check)
    try {
        $pdo->exec("ALTER TABLE reviews ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        echo "Column 'status' added to 'reviews'.\n";
    } catch (Exception $e) {
        // Column might already exist
    }

    // 3. Ensure 'comment' or 'review_text' consistency
    // Based on admin/reviews.php, it uses 'comment'.
    // Based on product-detail.php, it uses 'review_text'.
    // Let's check which one is in the table.
    $stmt = $pdo->query("DESCRIBE reviews");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('comment', $columns) && in_array('review_text', $columns)) {
        // Table has review_text, maybe rename or just be aware.
        // I'll stick to what the database HAS and update the code to match.
    }

    // 4. Add admin_reply if not exists
    try {
        $pdo->exec("ALTER TABLE reviews ADD COLUMN admin_reply TEXT DEFAULT NULL");
        echo "Column 'admin_reply' added to 'reviews'.\n";
    } catch (Exception $e) {
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
