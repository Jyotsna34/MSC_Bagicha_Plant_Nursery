<?php
/**
 * Bagicha 2.0 - Admin Update Product Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();
$db = Database::getInstance();
$input = $_POST;

$id = (int)($input['id'] ?? 0);
if ($id <= 0) {
    jsonResponse('error', 'Invalid product ID', null, 400);
}

try {
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
    $isActive = isset($input['is_active']) ? 1 : 0;

    if (empty($name) || $categoryId <= 0 || $price <= 0) {
        jsonResponse('error', 'Name, Category, and positive Price are required', null, 422);
    }

    if ($salePrice !== null && $salePrice >= $price) {
        jsonResponse('error', 'Sale price must be lower than the regular price', null, 422);
    }

    $imageUrl = Validator::sanitizeString($input['main_image'] ?? '');

    // Handle image upload if provided
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
    }


    $sql = "
        UPDATE products SET
            category_id = :catId,
            name = :name,
            short_description = :sDesc,
            description = :desc,
            price = :price,
            sale_price = :salePrice,
            stock_quantity = :stock,
            light_requirement = :light,
            water_requirement = :water,
            maintenance_level = :maint,
            is_pet_friendly = :pet,
            is_featured = :feat,
            is_active = :active
    ";

    $params = [
        ':catId' => $categoryId,
        ':name' => $name,
        ':sDesc' => $shortDesc,
        ':desc' => $description,
        ':price' => $price,
        ':salePrice' => $salePrice,
        ':stock' => $stock,
        ':light' => $light,
        ':water' => $water,
        ':maint' => $maintenance,
        ':pet' => $isPetFriendly,
        ':feat' => $isFeatured,
        ':active' => $isActive,
        ':id' => $id
    ];

    if (!empty($imageUrl)) {
        $sql .= ", main_image = :img";
        $params[':img'] = $imageUrl;
    }

    $sql .= " WHERE id = :id";

    $updStmt = $db->prepare($sql);
    $updStmt->execute($params);

    jsonResponse('success', 'Product updated successfully');

} catch (Exception $e) {
    error_log("Admin Product Update Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to update product', null, 500);
}
