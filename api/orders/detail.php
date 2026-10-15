<?php
/**
 * Bagicha 2.0 - Order Details & Items Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$orderId = (int)($_GET['id'] ?? 0);
$orderNumber = Validator::sanitizeString($_GET['order_number'] ?? '');

if ($orderId <= 0 && empty($orderNumber)) {
    jsonResponse('error', 'Order ID or Order Number required', null, 400);
}

try {
    $db = Database::getInstance();

    $stmt = $db->prepare("
        SELECT * FROM orders 
        WHERE (id = :id OR order_number = :num) AND user_id = :userId 
        LIMIT 1
    ");
    $stmt->execute([':id' => $orderId, ':num' => $orderNumber, ':userId' => $user['id']]);
    $order = $stmt->fetch();

    if (!$order) {
        jsonResponse('error', 'Order not found', null, 404);
    }

    // Fetch order items
    $itemStmt = $db->prepare("
        SELECT oi.*, p.slug, p.main_image 
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :orderId
    ");
    $itemStmt->execute([':orderId' => $order['id']]);
    $items = $itemStmt->fetchAll();

    jsonResponse('success', 'Order details retrieved', [
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log("Order Detail API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch order details', null, 500);
}
