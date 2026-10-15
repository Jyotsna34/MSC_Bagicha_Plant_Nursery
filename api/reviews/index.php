<?php
/**
 * Bagicha 2.0 - Public Customer Reviews Listing Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

try {
    $db = Database::getInstance();
    $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
    $limit = min(20, max(1, (int)($_GET['limit'] ?? 10)));

    $sql = "
        SELECT r.id, r.product_id, r.rating, r.comment, r.created_at,
               p.name as product_name, p.slug as product_slug, p.main_image as product_image,
               u.full_name as reviewer_name, u.avatar as reviewer_avatar
        FROM reviews r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.user_id = u.id
        WHERE r.is_approved = 1
    ";

    $params = [];
    if ($productId > 0) {
        $sql .= " AND r.product_id = :pid";
        $params[':pid'] = $productId;
    }

    $sql .= " ORDER BY r.id DESC LIMIT :limit";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $reviews = $stmt->fetchAll();

    jsonResponse('success', 'Approved reviews fetched successfully', ['reviews' => $reviews]);

} catch (Exception $e) {
    error_log("Public Reviews List Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch customer reviews', null, 500);
}
