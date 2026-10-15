<?php
/**
 * Bagicha 2.0 - Admin Orders Listing Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();

try {
    $db = Database::getInstance();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $status = $_GET['status'] ?? '';

    if ($id > 0) {
        $ordStmt = $db->prepare("
            SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = :id
            LIMIT 1
        ");
        $ordStmt->execute([':id' => $id]);
        $order = $ordStmt->fetch();

        if (!$order) {
            jsonResponse('error', 'Order not found', null, 404);
        }

        $itemsStmt = $db->prepare("
            SELECT oi.*, p.main_image 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :orderId
        ");
        $itemsStmt->execute([':orderId' => $id]);
        $order['items'] = $itemsStmt->fetchAll();

        jsonResponse('success', 'Order details retrieved', ['order' => $order]);
    }

    $sql = "
        SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
    ";
    
    $params = [];
    if (!empty($status)) {
        $sql .= " WHERE o.order_status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY o.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    jsonResponse('success', 'Admin orders list retrieved', ['orders' => $orders]);

} catch (Exception $e) {
    error_log("Admin Orders List Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch admin orders', null, 500);
}
