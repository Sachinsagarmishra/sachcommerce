<?php
/**
 * Admin-specific helper functions
 */

/**
 * Get total counts for dashboard
 */
function get_dashboard_stats() {
    global $pdo;
    
    $stats = [];
    
    // Today's sales
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as today_sales 
                        FROM orders 
                        WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'");
    $stats['today_sales'] = $stmt->fetch()['today_sales'];
    
    // This month's sales
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as month_sales 
                        FROM orders 
                        WHERE MONTH(created_at) = MONTH(CURDATE()) 
                        AND YEAR(created_at) = YEAR(CURDATE()) 
                        AND payment_status = 'paid'");
    $stats['month_sales'] = $stmt->fetch()['month_sales'];
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
    $stats['total_orders'] = $stmt->fetch()['total_orders'];
    
    // Pending orders
    $stmt = $pdo->query("SELECT COUNT(*) as pending_orders FROM orders WHERE order_status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch()['pending_orders'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products WHERE status = 'active'");
    $stats['total_products'] = $stmt->fetch()['total_products'];
    
    // Low stock products
    $stmt = $pdo->query("SELECT COUNT(*) as low_stock FROM products WHERE stock_quantity <= low_stock_alert AND status = 'active'");
    $stats['low_stock'] = $stmt->fetch()['low_stock'];
    
    // Total customers
    $stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'");
    $stats['total_customers'] = $stmt->fetch()['total_customers'];
    
    // Total categories
    $stmt = $pdo->query("SELECT COUNT(*) as total_categories FROM categories WHERE status = 'active'");
    $stats['total_categories'] = $stmt->fetch()['total_categories'];
    
    return $stats;
}

/**
 * Get recent orders
 */
function get_recent_orders($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get low stock products
 */
function get_low_stock_products($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM products 
                          WHERE stock_quantity <= low_stock_alert 
                          AND status = 'active' 
                          ORDER BY stock_quantity ASC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get order status badge class
 */
function get_order_status_badge($status) {
    $badges = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'secondary'
    ];
    return $badges[$status] ?? 'secondary';
}

/**
 * Get payment status badge class
 */
function get_payment_status_badge($status) {
    $badges = [
        'pending' => 'warning',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'secondary'
    ];
    return $badges[$status] ?? 'secondary';
}

/**
 * Generate CSV export
 */
function export_to_csv($filename, $data, $headers) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

/**
 * Validate product SKU uniqueness
 */
function is_sku_unique($sku, $product_id = null) {
    global $pdo;
    
    if ($product_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE sku = ? AND id != ?");
        $stmt->execute([$sku, $product_id]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE sku = ?");
        $stmt->execute([$sku]);
    }
    
    $result = $stmt->fetch();
    return $result['count'] == 0;
}

/**
 * Get product by ID
 */
function get_product_by_id($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get product images
 */
function get_product_images($product_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

/**
 * Get all categories for dropdown
 */
function get_all_categories() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC");
    return $stmt->fetchAll();
}

/**
 * Update order status
 */
function update_order_status($order_id, $new_status, $admin_notes = '') {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get current order
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        $old_status = $order['order_status'];
        
        // Update order
        $update_fields = ['order_status' => $new_status];
        
        if ($admin_notes) {
            $update_fields['admin_notes'] = $admin_notes;
        }
        
        if ($new_status === 'shipped' && !$order['shipped_date']) {
            $update_fields['shipped_date'] = date('Y-m-d H:i:s');
        }
        
        if ($new_status === 'delivered' && !$order['delivered_date']) {
            $update_fields['delivered_date'] = date('Y-m-d H:i:s');
        }
        
        $set_clause = [];
        $values = [];
        foreach ($update_fields as $key => $value) {
            $set_clause[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $order_id;
        
        $stmt = $pdo->prepare("UPDATE orders SET " . implode(', ', $set_clause) . " WHERE id = ?");
        $stmt->execute($values);
        
        // Add to status history
        $stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, old_status, new_status, notes, updated_by) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $old_status, $new_status, $admin_notes, $_SESSION['user_id']]);
        
        $pdo->commit();
        
        // Send email notification
        send_order_status_email($order_id, $new_status);
        
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Send order status update email
 */
function send_order_status_email($order_id, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $data = [
            'order_number' => $order['order_number'],
            'customer_name' => $order['customer_name'],
            'status' => ucfirst($status),
            'order_url' => SITE_URL . '/order-detail.php?id=' . $order_id
        ];
        
        send_email($order['customer_email'], 'Order Status Update', 'order-status-update', $data);
    }
}
