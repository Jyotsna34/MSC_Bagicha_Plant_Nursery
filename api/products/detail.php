<?php
/**
 * Bagicha 2.0 - Product Details API Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$slugOrId = Validator::sanitizeString($_GET['slug'] ?? $_GET['id'] ?? '');

if (empty($slugOrId)) {
    jsonResponse('error', 'Product slug or ID is required', null, 400);
}

try {
    $db = Database::getInstance();

    $sql = "
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE (p.slug = :slug OR p.id = :id) AND (p.is_active = 1 OR p.is_active IS NULL)
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':slug' => $slugOrId, ':id' => (int)$slugOrId]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse('error', 'Product not found', null, 404);
    }

    // Fetch Image Gallery
    $imgStmt = $db->prepare("SELECT image_url FROM product_images WHERE product_id = :pid ORDER BY sort_order ASC");
    $imgStmt->execute([':pid' => $product['id']]);
    $gallery = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($gallery)) {
        $gallery = [$product['main_image']];
    } else if (!in_array($product['main_image'], $gallery)) {
        array_unshift($gallery, $product['main_image']);
    }

    // Fetch Related Products (same category)
    $relStmt = $db->prepare("
        SELECT id, name, slug, price, sale_price, stock_quantity, main_image, light_requirement, water_requirement, rating_avg, rating_count
        FROM products 
        WHERE category_id = :catId AND id != :pid AND (is_active = 1 OR is_active IS NULL)
        LIMIT 4
    ");
    $relStmt->execute([':catId' => $product['category_id'], ':pid' => $product['id']]);
    $related = $relStmt->fetchAll();

    // Fetch Customer Reviews (approved reviews)
    $revStmt = $db->prepare("
        SELECT r.*, u.full_name as reviewer_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = :pid AND (r.is_approved = 1 OR r.is_approved IS NULL)
        ORDER BY r.created_at DESC
    ");
    $revStmt->execute([':pid' => $product['id']]);
    $reviews = $revStmt->fetchAll();

    jsonResponse('success', 'Product details retrieved', [
        'product' => $product,
        'gallery' => $gallery,
        'related' => $related,
        'reviews' => $reviews
    ]);

} catch (Exception $e) {
    error_log("Product Detail API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to retrieve product details', null, 500);
}
