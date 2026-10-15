<?php
/**
 * Bagicha 2.0 - Toggle Wishlist Item Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$productId = (int)($input['product_id'] ?? 0);

if ($productId <= 0) {
    jsonResponse('error', 'Invalid product ID', null, 400);
}

try {
    $db = Database::getInstance();

    $checkStmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = :userId AND product_id = :prodId LIMIT 1");
    $checkStmt->execute([':userId' => $user['id'], ':prodId' => $productId]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        $delStmt = $db->prepare("DELETE FROM wishlist WHERE id = :id");
        $delStmt->execute([':id' => $existing['id']]);
        jsonResponse('success', 'Removed from wishlist', ['in_wishlist' => false]);
    } else {
        $prodCheck = $db->prepare("SELECT id FROM products WHERE id = :prodId AND is_active = 1 LIMIT 1");
        $prodCheck->execute([':prodId' => $productId]);
        if (!$prodCheck->fetch()) {
            jsonResponse('error', 'Product not found', null, 404);
        }

        $insStmt = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:userId, :prodId)");
        $insStmt->execute([':userId' => $user['id'], ':prodId' => $productId]);
        jsonResponse('success', 'Added to wishlist', ['in_wishlist' => true]);
    }

} catch (Exception $e) {
    error_log("Wishlist Toggle Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to toggle wishlist item', null, 500);
}
