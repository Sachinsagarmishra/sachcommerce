<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$cart_count = 0;

if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
    $stmt->execute([session_id()]);
}

$result = $stmt->fetch();
$cart_count = $result['total'] ?? 0;

echo json_encode([
    'success' => true,
    'count' => $cart_count
]);
