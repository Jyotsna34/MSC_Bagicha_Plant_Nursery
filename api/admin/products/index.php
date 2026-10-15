<?php
/**
 * Bagicha 2.0 - Admin Product CRUD Endpoint (List, Add, Delete)
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

$admin = requireAdmin();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ");
        jsonResponse('success', 'Products retrieved', ['products' => $stmt->fetchAll()]);
    }

    if ($method === 'POST') {
        $input = $_POST;
        $name = Validator::sanitizeString($input['name'] ?? '');
        $categoryId = (int)($input['category_id'] ?? 0);
        $price = (float)($input['price'] ?? 0);
        $salePrice = !empty($input['sale_price']) ? (float)$input['sale_price'] : null;
        $stock = max(0, (int)($input['stock_quantity'] ?? 0));
        $shortDesc = Validator::sanitizeString($input['short_description'] ?? '');
        $description = Validator::sanitizeString($input['description'] ?? '');
        $light = Validator::sanitizeString($input['light_requirement'] ?? 'Indirect Sunlight');
        $water = Validator::sanitizeString($input['water_requirement'] ?? 'Weekly');
        $maintenance = Validator::sanitizeString($input['maintenance_level'] ?? 'Low');
        $isPetFriendly = isset($input['is_pet_friendly']) ? 1 : 0;
        $isFeatured = isset($input['is_featured']) ? 1 : 0;

        if (empty($name) || $categoryId <= 0 || $price <= 0) {
            jsonResponse('error', 'Name, Category, and positive Price are required', null, 422);
        }

        if ($salePrice !== null && $salePrice >= $price) {
            jsonResponse('error', 'Sale price must be lower than the regular price', null, 422);
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . rand(100, 999);
        $imageUrl = 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=800&q=80';

        // Handle uploaded image file
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            if (!Validator::validateImageUpload($_FILES['image_file'])) {
                jsonResponse('error', 'Invalid image file type. Only JPG, PNG, WEBP, and GIF are allowed.', null, 422);
            }
            $uploadDir = UPLOAD_DIR . 'plants/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            $fileName = 'plant_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
                $imageUrl = UPLOAD_URL . 'plants/' . $fileName;
            }
        } elseif (!empty($input['main_image'])) {

            $imageUrl = Validator::sanitizeString($input['main_image']);
        }

        $sku = Validator::sanitizeString($input['sku'] ?? '');
        if (empty($sku)) {
            $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
            $prefix = substr($clean, 0, 3);
            if (strlen($prefix) < 3) $prefix = 'PLT';
            $sku = 'PLT-' . $prefix . '-' . rand(10, 99);
        }

        $insStmt = $db->prepare("
            INSERT INTO products (category_id, name, slug, sku, short_description, description, price, sale_price, stock_quantity, main_image, light_requirement, water_requirement, maintenance_level, is_pet_friendly, is_featured)
            VALUES (:catId, :name, :slug, :sku, :sDesc, :desc, :price, :salePrice, :stock, :image, :light, :water, :maint, :pet, :feat)
        ");
        $insStmt->execute([
            ':catId' => $categoryId,
            ':name' => $name,
            ':slug' => $slug,
            ':sku' => $sku,
            ':sDesc' => $shortDesc,
            ':desc' => $description,
            ':price' => $price,
            ':salePrice' => $salePrice,
            ':stock' => $stock,
            ':image' => $imageUrl,
            ':light' => $light,
            ':water' => $water,
            ':maint' => $maintenance,
            ':pet' => $isPetFriendly,
            ':feat' => $isFeatured
        ]);

        jsonResponse('success', 'Product created successfully', ['id' => $db->lastInsertId()], 201);
    }

    if ($method === 'DELETE') {
        $input = Validator::getJsonInput();
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }

        $delStmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $delStmt->execute([':id' => $id]);
        jsonResponse('success', 'Product deleted');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (Exception $e) {
    error_log("Admin Product Index Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process product request', null, 500);
}
