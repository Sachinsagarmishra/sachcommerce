<?php
require_once 'config/config.php';

try {
    // Add columns to orders table
    $sql = "ALTER TABLE orders 
            ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(50) DEFAULT NULL AFTER shipping_pincode,
            ADD COLUMN IF NOT EXISTS coupon_discount DECIMAL(10, 2) DEFAULT 0.00 AFTER coupon_code";
    $pdo->exec($sql);
    echo "Database updated successfully: columns added to orders table.\n";

    // Check if used_count exists in coupons
    $sql = "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS used_count INT DEFAULT 0 AFTER status";
    $pdo->exec($sql);
    echo "Database updated successfully: used_count added to coupons table.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
unlink(__FILE__);
