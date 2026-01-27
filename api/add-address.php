<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = sanitize_input($_POST['full_name']);
    $phone = sanitize_input($_POST['phone']);
    $address_line1 = sanitize_input($_POST['address_line1']);
    $address_line2 = sanitize_input($_POST['address_line2'] ?? '');
    $city = sanitize_input($_POST['city']);
    $state = sanitize_input($_POST['state']);
    $pincode = sanitize_input($_POST['pincode']);
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    // Validation
    if (empty($full_name) || empty($phone) || empty($address_line1) || empty($city) || empty($state) || empty($pincode)) {
        $_SESSION['error'] = 'Please fill all required fields';
        header('Location: ' . SITE_URL . '/addresses.php');
        exit;
    }
    
    try {
        // If this is set as default, unset other defaults
        if ($is_default) {
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->execute([$user_id]);
        }
        
        // Insert new address
        $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, full_name, phone, address_line1, address_line2, city, state, pincode, is_default, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $full_name, $phone, $address_line1, $address_line2, $city, $state, $pincode, $is_default]);
        
        $_SESSION['success'] = 'Address added successfully';
        header('Location: ' . SITE_URL . '/addresses.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to add address';
        header('Location: ' . SITE_URL . '/addresses.php');
        exit;
    }
} else {
    header('Location: ' . SITE_URL . '/addresses.php');
    exit;
}
