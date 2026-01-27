<?php
require_once '../config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth-check.php';

// Fix HTML entities in product names and descriptions
$stmt = $pdo->query("SELECT id, name, short_description, long_description FROM products");
$products = $stmt->fetchAll();

$fixed_count = 0;

foreach ($products as $product) {
    $name = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');
    $short_desc = html_entity_decode($product['short_description'], ENT_QUOTES, 'UTF-8');
    $long_desc = html_entity_decode($product['long_description'], ENT_QUOTES, 'UTF-8');
    
    if ($name !== $product['name'] || $short_desc !== $product['short_description'] || $long_desc !== $product['long_description']) {
        $update_stmt = $pdo->prepare("UPDATE products SET name = ?, short_description = ?, long_description = ? WHERE id = ?");
        $update_stmt->execute([$name, $short_desc, $long_desc, $product['id']]);
        $fixed_count++;
    }
}

$_SESSION['success'] = "Fixed HTML entities in $fixed_count products";
header('Location: products.php');
?>
