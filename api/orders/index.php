<?php
/**
 * Bagicha 2.0 - Customer Orders History Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();

try {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("
        SELECT id, order_number, total_amount, discount_amount, final_amount, 
               order_status, payment_status, payment_method, created_at
        FROM orders
        WHERE user_id = :userId
        ORDER BY id DESC
    ");
    $stmt->execute([':userId' => $user['id']]);
    $orders = $stmt->fetchAll();

    jsonResponse('success', 'Orders fetched successfully', ['orders' => $orders]);

} catch (Exception $e) {
    error_log("Orders Index Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch order history', null, 500);
}
