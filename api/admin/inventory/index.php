<?php
/**
 * Bagicha - Admin Inventory Management API Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

$admin = requireAdmin();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $filter = $_GET['filter'] ?? 'all';
        $categoryId = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'stock_asc';

        // 1. Calculate Aggregate Metrics Across All Products
        $statsStmt = $db->query("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN stock_quantity > 5 THEN 1 ELSE 0 END) as healthy_count,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 5 THEN 1 ELSE 0 END) as low_stock_count,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                COALESCE(SUM(stock_quantity), 0) as total_stock_units,
                COALESCE(SUM(stock_quantity * COALESCE(sale_price, price)), 0) as total_valuation
            FROM products
        ");
        $stats = $statsStmt->fetch();

        // 2. Fetch Categories for Filter Dropdown
        $categoriesStmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $categoriesStmt->fetchAll();

        // 3. Build Filtered Products Query
        $whereClauses = [];
        $params = [];

        if ($filter === 'low_stock') {
            $whereClauses[] = "p.stock_quantity <= 5 AND p.stock_quantity > 0";
        } elseif ($filter === 'out_of_stock') {
            $whereClauses[] = "p.stock_quantity = 0";
        } elseif ($filter === 'healthy') {
            $whereClauses[] = "p.stock_quantity > 5";
        }

        if ($categoryId > 0) {
            $whereClauses[] = "p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if (!empty($search)) {
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $whereClauses[] = "(p.name LIKE :search OR p.sku LIKE :search)";
            $params[':search'] = '%' . $escapedSearch . '%';
        }

        $sql = "
            SELECT p.id, p.category_id, p.name, p.sku, p.price, p.sale_price, p.stock_quantity, p.main_image, p.is_active, c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
        ";

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        // Order By Logic
        switch ($sort) {
            case 'stock_desc':
                $sql .= " ORDER BY p.stock_quantity DESC, p.id DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
                break;
            case 'stock_asc':
            default:
                $sql .= " ORDER BY p.stock_quantity ASC, p.id DESC";
                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $inventory = $stmt->fetchAll();

        jsonResponse('success', 'Inventory data fetched', [
            'inventory' => $inventory,
            'stats' => $stats,
            'categories' => $categories
        ]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $input = Validator::getJsonInput();
        $id = (int)($input['product_id'] ?? 0);

        if ($id <= 0) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }

        // Check if product exists
        $checkStmt = $db->prepare("SELECT id, name, stock_quantity FROM products WHERE id = :id");
        $checkStmt->execute([':id' => $id]);
        $existing = $checkStmt->fetch();

        if (!$existing) {
            jsonResponse('error', 'Product not found', null, 404);
        }

        // Handle direct stock set vs relative adjustment
        if (isset($input['adjustment']) && is_numeric($input['adjustment'])) {
            $delta = (int)$input['adjustment'];
            $newStock = max(0, (int)$existing['stock_quantity'] + $delta);
        } else {
            if (!isset($input['stock_quantity']) || !is_numeric($input['stock_quantity'])) {
                jsonResponse('error', 'Invalid or missing stock quantity', null, 400);
            }
            $newStock = max(0, (int)$input['stock_quantity']);
        }

        $updateStmt = $db->prepare("UPDATE products SET stock_quantity = :stock WHERE id = :id");
        $updateStmt->execute([':stock' => $newStock, ':id' => $id]);

        // If stock hit 0, create notification for admins
        if ($newStock === 0 && (int)$existing['stock_quantity'] > 0) {
            $notifStmt = $db->prepare("
                INSERT INTO notifications (user_id, title, message, type, related_id) 
                VALUES (:user_id, :title, :message, 'stock', :related_id)
            ");
            $notifStmt->execute([
                ':user_id' => $admin['id'],
                ':title' => 'Stock Alert: Out of Stock',
                ':message' => "{$existing['name']} is now out of stock (0 units remaining).",
                ':related_id' => $id
            ]);
        }

        // Fetch updated product row
        $fetchUpdated = $db->prepare("
            SELECT p.id, p.category_id, p.name, p.sku, p.price, p.sale_price, p.stock_quantity, p.main_image, p.is_active, c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
        $fetchUpdated->execute([':id' => $id]);
        $updatedProduct = $fetchUpdated->fetch();

        // Fetch fresh stats
        $statsStmt = $db->query("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN stock_quantity > 5 THEN 1 ELSE 0 END) as healthy_count,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 5 THEN 1 ELSE 0 END) as low_stock_count,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                COALESCE(SUM(stock_quantity), 0) as total_stock_units,
                COALESCE(SUM(stock_quantity * COALESCE(sale_price, price)), 0) as total_valuation
            FROM products
        ");
        $stats = $statsStmt->fetch();

        jsonResponse('success', "Stock updated for {$existing['name']} to {$newStock} units", [
            'product' => $updatedProduct,
            'stats' => $stats
        ]);
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (Exception $e) {
    error_log("Admin Inventory API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process inventory request: ' . $e->getMessage(), null, 500);
}
