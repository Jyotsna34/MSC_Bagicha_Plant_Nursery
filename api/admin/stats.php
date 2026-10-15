<?php
/**
 * Bagicha 2.0 - Admin Executive Dashboard Statistics API
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();

try {
    $db = Database::getInstance();

    // 1. Total Revenue
    $revStmt = $db->query("SELECT SUM(final_amount) as total_revenue FROM orders WHERE payment_status = 'paid'");
    $totalRevenue = (float)($revStmt->fetch()['total_revenue'] ?? 0);

    // 2. Total Orders
    $ordStmt = $db->query("SELECT COUNT(*) as total_orders FROM orders");
    $totalOrders = (int)($ordStmt->fetch()['total_orders'] ?? 0);

    // 3. Active Products
    $prodStmt = $db->query("SELECT COUNT(*) as total_products FROM products WHERE is_active = 1");
    $totalProducts = (int)($prodStmt->fetch()['total_products'] ?? 0);

    // 4. Total Customers
    $userStmt = $db->query("SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'");
    $totalCustomers = (int)($userStmt->fetch()['total_customers'] ?? 0);

    // 5. Low Stock Alert Count (< 5 units)
    $stockStmt = $db->query("SELECT COUNT(*) as low_stock_count FROM products WHERE stock_quantity <= 5 AND is_active = 1");
    $lowStockCount = (int)($stockStmt->fetch()['low_stock_count'] ?? 0);

    // 6. Recent Orders (Last 5)
    $recStmt = $db->query("
        SELECT o.id, o.order_number, o.final_amount, o.order_status, o.payment_status, o.created_at, u.full_name as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.id DESC
        LIMIT 5
    ");
    $recentOrders = $recStmt->fetchAll();

    jsonResponse('success', 'Admin statistics retrieved', [
        'total_revenue' => $totalRevenue,
        'total_orders' => $totalOrders,
        'total_products' => $totalProducts,
        'total_customers' => $totalCustomers,
        'low_stock_count' => $lowStockCount,
        'recent_orders' => $recentOrders
    ]);

} catch (Exception $e) {
    error_log("Admin Stats API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to retrieve admin stats', null, 500);
}
