<?php
/**
 * Bagicha 2.0 - Products List & Filter Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

try {
    $db = Database::getInstance();

    $categorySlug = Validator::sanitizeString($_GET['category'] ?? '');
    $search = Validator::sanitizeString($_GET['search'] ?? '');
    $featured = isset($_GET['featured']) && $_GET['featured'] === '1';
    $sort = Validator::sanitizeString($_GET['sort'] ?? 'latest');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 12)));
    $offset = ($page - 1) * $limit;

    $where = ["(p.is_active = 1 OR p.is_active IS NULL)"];
    $params = [];

    if (!empty($categorySlug)) {
        $where[] = "(c.slug = :catSlug OR c.id = :catId)";
        $params[':catSlug'] = $categorySlug;
        $params[':catId'] = (int)$categorySlug;
    }

    if (!empty($search)) {
        $where[] = "(p.name LIKE :search1 OR p.short_description LIKE :search2)";
        $params[':search1'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }

    if ($featured) {
        $where[] = "p.is_featured = 1";
    }

    $petFriendly = isset($_GET['pet_friendly']) ? (int)$_GET['pet_friendly'] : null;
    if ($petFriendly !== null) {
        $where[] = "p.is_pet_friendly = :petFriendly";
        $params[':petFriendly'] = $petFriendly;
    }

    $light = Validator::sanitizeString($_GET['light'] ?? '');
    if (!empty($light)) {
        $where[] = "p.light_requirement LIKE :lightReq";
        $params[':lightReq'] = '%' . $light . '%';
    }

    $maintenance = Validator::sanitizeString($_GET['maintenance'] ?? '');
    if (!empty($maintenance)) {
        $where[] = "p.maintenance_level LIKE :maintReq";
        $params[':maintReq'] = '%' . $maintenance . '%';
    }

    $whereSql = implode(" AND ", $where);

    // Count Total
    $countSql = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id WHERE $whereSql";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $totalProducts = (int)$countStmt->fetch()['total'];

    // Order By
    $orderBy = match ($sort) {
        'price_asc' => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'rating' => 'p.rating_avg DESC',
        default => 'p.id DESC'
    };

    $sql = "
        SELECT p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, 
               p.stock_quantity, p.main_image, p.light_requirement, p.water_requirement,
               p.is_pet_friendly, p.is_featured, p.rating_avg, p.rating_count,
               c.name as category_name, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE $whereSql
        ORDER BY $orderBy
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll();

    jsonResponse('success', 'Products fetched successfully', [
        'products' => $products,
        'pagination' => [
            'total' => $totalProducts,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalProducts / $limit)
        ]
    ]);

} catch (Exception $e) {
    error_log("Products API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch products', null, 500);
}
