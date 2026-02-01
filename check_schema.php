<?php
require_once 'config/config.php';

try {
    echo "--- reviews table --- \n";
    $stmt = $pdo->query("DESCRIBE reviews");
    while ($row = $stmt->fetch()) {
        print_r($row);
    }

    echo "\n--- check if review_images exists --- \n";
    try {
        $stmt = $pdo->query("DESCRIBE review_images");
        while ($row = $stmt->fetch()) {
            print_r($row);
        }
    } catch (Exception $e) {
        echo "review_images table does not exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
