<?php
/**
 * Bagicha 2.0 - Live Search Autocomplete API Endpoint
 * Fast lookup for live search preview dropdown.
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

try {
    $db = Database::getInstance();
    $q = Validator::sanitizeString($_GET['q'] ?? '');

    if (empty($q) || strlen($q) < 2) {
        jsonResponse('success', 'Empty query', ['results' => []]);
    }

    $stmt = $db->prepare("
        SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.main_image, p.stock_quantity,
               c.name as category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE (p.is_active = 1 OR p.is_active IS NULL)
          AND (p.name LIKE :query1 OR p.short_description LIKE :query2 OR c.name LIKE :query3)
        ORDER BY p.is_featured DESC, p.id DESC
        LIMIT 6
    ");
    $term = '%' . $q . '%';
    $stmt->execute([
        ':query1' => $term,
        ':query2' => $term,
        ':query3' => $term
    ]);
    $results = $stmt->fetchAll();

    jsonResponse('success', 'Search results retrieved', ['results' => $results]);

} catch (Exception $e) {
    error_log("Search API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to perform search', null, 500);
}
