<?php
/**
 * Bagicha 2.0 - Admin Customer Management API
 */

require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();

try {
    $db = Database::getInstance();
    $search = trim($_GET['search'] ?? '');
    $customerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

    if ($customerId > 0) {
        $ordStmt = $db->prepare("
            SELECT o.id, o.order_number, o.final_amount, o.order_status, o.payment_status, o.created_at 
            FROM orders o 
            WHERE o.user_id = :cid 
            ORDER BY o.id DESC
        ");
        $ordStmt->execute([':cid' => $customerId]);
        jsonResponse('success', 'Customer order history retrieved', ['orders' => $ordStmt->fetchAll()]);
    }

    $whereClauses = ["u.role = 'customer'"];
    $params = [];

    if (!empty($search)) {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
        $whereClauses[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
        $params[':search'] = '%' . $escaped . '%';
    }

    $sql = "
        SELECT u.id, u.full_name, u.email, u.phone, u.created_at,
               COUNT(o.id) as total_orders,
               COALESCE(SUM(o.final_amount), 0) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'paid'
        WHERE " . implode(" AND ", $whereClauses) . "
        GROUP BY u.id, u.full_name, u.email, u.phone, u.created_at
        ORDER BY u.id DESC
        LIMIT 100
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $customers = $stmt->fetchAll();

    jsonResponse('success', 'Customer accounts retrieved', ['customers' => $customers]);

} catch (Exception $e) {
    error_log("Admin Users List Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch customer list', null, 500);
}
