<?php
/**
 * Care guidance and recommendations based on the customer's completed purchases.
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();

try {
    $db = Database::getInstance();
    $careStmt = $db->prepare("SELECT p.id, p.name, p.slug, p.main_image, p.water_requirement, p.light_requirement, p.maintenance_level, recent.last_ordered FROM (SELECT oi.product_id, MAX(o.created_at) AS last_ordered FROM order_items oi INNER JOIN orders o ON o.id = oi.order_id WHERE o.user_id = :userId AND o.order_status != 'cancelled' GROUP BY oi.product_id) recent INNER JOIN products p ON p.id = recent.product_id WHERE (p.is_active = 1 OR p.is_active IS NULL) ORDER BY recent.last_ordered DESC LIMIT 3");
    $careStmt->execute([':userId' => $user['id']]);
    $careItems = $careStmt->fetchAll();

    $categoryStmt = $db->prepare("SELECT p.category_id FROM order_items oi INNER JOIN orders o ON o.id = oi.order_id INNER JOIN products p ON p.id = oi.product_id WHERE o.user_id = :userId AND o.order_status != 'cancelled' GROUP BY p.category_id ORDER BY COUNT(*) DESC LIMIT 1");
    $categoryStmt->execute([':userId' => $user['id']]);
    $favouriteCategory = $categoryStmt->fetchColumn();

    if ($favouriteCategory) {
        $recStmt = $db->prepare("SELECT p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, p.stock_quantity, p.main_image, p.light_requirement, p.water_requirement, p.is_pet_friendly, p.rating_avg, p.rating_count, c.name AS category_name, c.slug AS category_slug FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.category_id = :categoryId AND (p.is_active = 1 OR p.is_active IS NULL) AND p.id NOT IN (SELECT oi.product_id FROM order_items oi INNER JOIN orders o ON o.id = oi.order_id WHERE o.user_id = :userId) ORDER BY p.rating_avg DESC, p.id DESC LIMIT 4");
        $recStmt->execute([':categoryId' => $favouriteCategory, ':userId' => $user['id']]);
        $recommendations = $recStmt->fetchAll();
    } else {
        $recStmt = $db->query("SELECT p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, p.stock_quantity, p.main_image, p.light_requirement, p.water_requirement, p.is_pet_friendly, p.rating_avg, p.rating_count, c.name AS category_name, c.slug AS category_slug FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE (p.is_active = 1 OR p.is_active IS NULL) ORDER BY p.is_featured DESC, p.rating_avg DESC LIMIT 4");
        $recommendations = $recStmt->fetchAll();
    }

    jsonResponse('success', 'Customer dashboard data retrieved', ['care_items' => $careItems, 'recommendations' => $recommendations]);
} catch (Exception $e) {
    error_log('Customer dashboard API Error: ' . $e->getMessage());
    jsonResponse('error', 'Unable to load your dashboard suggestions.', null, 500);
}
