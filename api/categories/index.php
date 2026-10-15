<?php
/**
 * Bagicha 2.0 - Categories List Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

try {
    $db = Database::getInstance();
    $featuredOnly = isset($_GET['featured']) && $_GET['featured'] === '1';

    $sql = "SELECT id, name, slug, description, image, is_featured FROM categories";
    if ($featuredOnly) {
        $sql .= " WHERE is_featured = 1";
    }
    $sql .= " ORDER BY name ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();

    jsonResponse('success', 'Categories retrieved successfully', ['categories' => $categories]);
} catch (Exception $e) {
    error_log("Categories API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to fetch categories', null, 500);
}
