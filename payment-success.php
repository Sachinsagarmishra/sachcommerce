<?php
/**
 * Payment Success - Redirects to Order Confirmed page
 * This file handles the return from Razorpay payment gateway
 */
require_once 'config/config.php';

// Get order number from URL or session
$order_number = $_GET['order'] ?? $_SESSION['order_number'] ?? '';

if ($order_number) {
    // Redirect to the new order confirmed page
    header('Location: ' . SITE_URL . '/order-confirmed?order=' . urlencode($order_number));
} else {
    // No order number, redirect to home
    header('Location: ' . SITE_URL);
}
exit;
