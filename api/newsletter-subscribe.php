<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';

if (empty($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter your email'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

// Check if already subscribed
$stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Email already subscribed'
    ]);
    exit;
}

// Add to newsletter
try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, status, subscribed_at) VALUES (?, 'active', NOW())");
    $stmt->execute([$email]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing!'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to subscribe. Please try again.'
    ]);
}
