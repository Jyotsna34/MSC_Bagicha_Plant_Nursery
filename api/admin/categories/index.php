<?php
/**
 * Bagicha 2.0 - Admin Category CRUD Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

$admin = requireAdmin();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id, c.name, c.slug, c.description, c.image, c.is_featured, c.created_at ORDER BY c.id DESC");
        jsonResponse('success', 'Categories retrieved', ['categories' => $stmt->fetchAll()]);
    }

    if ($method === 'POST') {
        $input = $_POST;
        $id = (int)($input['id'] ?? 0);
        $name = Validator::sanitizeString($input['name'] ?? '');
        $description = Validator::sanitizeString($input['description'] ?? '');
        $isFeatured = isset($input['is_featured']) ? 1 : 0;

        if (empty($name)) {
            jsonResponse('error', 'Category name is required', null, 422);
        }

        $image = '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            if (!Validator::validateImageUpload($_FILES['image_file'])) {
                jsonResponse('error', 'Invalid image file type. Only JPG, PNG, WEBP, and GIF are allowed.', null, 422);
            }
            $uploadDir = UPLOAD_DIR . 'categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            $fileName = 'cat_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
                $image = UPLOAD_URL . 'categories/' . $fileName;
            }
        } elseif (!empty($input['image'])) {

            $image = Validator::sanitizeString($input['image']);
        }

        if ($id > 0) {
            // Update
            $sql = "UPDATE categories SET name = :name, description = :desc, is_featured = :feat";
            $params = [':name' => $name, ':desc' => $description, ':feat' => $isFeatured, ':id' => $id];
            if (!empty($image)) {
                $sql .= ", image = :img";
                $params[':img'] = $image;
            }
            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonResponse('success', 'Category updated successfully');
        } else {
            // Create
            if (empty($image)) {
                $image = 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=600&q=80';
            }
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . rand(10, 99);
            $stmt = $db->prepare("INSERT INTO categories (name, slug, description, image, is_featured) VALUES (:name, :slug, :desc, :img, :feat)");
            $stmt->execute([':name' => $name, ':slug' => $slug, ':desc' => $description, ':img' => $image, ':feat' => $isFeatured]);
            jsonResponse('success', 'Category created successfully', ['id' => $db->lastInsertId()], 201);
        }
    }

    if ($method === 'DELETE') {
        $input = Validator::getJsonInput();
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) jsonResponse('error', 'Invalid category ID', null, 400);

        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        jsonResponse('success', 'Category deleted');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (PDOException $e) {
    if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062')) {
        jsonResponse('error', 'A category with this name or slug already exists', null, 422);
    }
    error_log("Admin Category Error: " . $e->getMessage());
    jsonResponse('error', 'Database error encountered', null, 500);
} catch (Exception $e) {
    error_log("Admin Category Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process category request', null, 500);
}
