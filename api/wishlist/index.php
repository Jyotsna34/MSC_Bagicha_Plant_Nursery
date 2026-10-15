<?php
/**
 * Bagicha 2.0 - User Wishlist Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();

try {
    $db = Database::getInstance();
    $sql = "
        SELECT w.id as wishlist_id, w.created_at,
               p.id as product_id, p.name, p.slug, p.price, p.sale_price, 
               p.main_image, p.stock_quantity, p.rating_avg, cat.name as category_name
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        LEFT JOIN categories cat ON p.category_id = cat.id
        WHERE w.user_id = :userId AND p.is_active = 1
        ORDER BY w.id DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':userId' => $user['id']]);
    $items = $stmt->fetchAll();

    jsonResponse('success', 'Wishlist items retrieved', ['items' => $items]);

} catch (Exception $e) {
    error_log("Wishlist Index Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch wishlist', null, 500);
}
