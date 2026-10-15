<?php
/**
 * Bagicha 2.0 - Admin Reports & Sales Analytics Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();

try {
    $db = Database::getInstance();

    // 1. Order Status Breakdown
    $statusStmt = $db->query("SELECT order_status, COUNT(*) as count, SUM(final_amount) as total FROM orders GROUP BY order_status");
    $statusBreakdown = $statusStmt->fetchAll();

    // 2. Top 5 Selling Products
    $topStmt = $db->query("
        SELECT oi.product_name, SUM(oi.quantity) as total_qty, SUM(oi.subtotal) as total_revenue
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.payment_status = 'paid'
        GROUP BY oi.product_id, oi.product_name
        ORDER BY total_qty DESC
        LIMIT 5
    ");
    $topProducts = $topStmt->fetchAll();

    // 3. Category Sales Share
    $catStmt = $db->query("
        SELECT c.name as category_name, SUM(oi.subtotal) as category_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.payment_status = 'paid'
        GROUP BY c.id, c.name
    ");
    $categoryShare = $catStmt->fetchAll();

    // 4. Monthly Revenue Growth
    $revStmt = $db->query("
        SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(final_amount) as revenue
        FROM orders
        WHERE payment_status = 'paid'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
        ORDER BY MIN(created_at) ASC
        LIMIT 12
    ");
    $monthlyRevenue = $revStmt->fetchAll();

    jsonResponse('success', 'Reports data retrieved', [
        'status_breakdown' => $statusBreakdown,
        'top_products' => $topProducts,
        'category_share' => $categoryShare,
        'monthly_revenue' => $monthlyRevenue
    ]);

} catch (Exception $e) {
    error_log("Admin Reports Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to retrieve reports data', null, 500);
}
