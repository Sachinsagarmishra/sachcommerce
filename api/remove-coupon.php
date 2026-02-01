<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (isset($_SESSION['applied_coupon'])) {
    unset($_SESSION['applied_coupon']);
    echo json_encode([
        'success' => true,
        'message' => 'Coupon removed successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No coupon applied'
    ]);
}
