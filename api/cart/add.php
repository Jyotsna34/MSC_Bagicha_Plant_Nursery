<?php
/**
 * Bagicha 2.0 - Add Item to Cart Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$productId = (int)($input['product_id'] ?? 0);
$quantity = max(1, (int)($input['quantity'] ?? 1));

if ($productId <= 0) {
    jsonResponse('error', 'Invalid product ID', null, 400);
}

try {
    $db = Database::getInstance();

    // Verify product existence and stock
    $prodStmt = $db->prepare("SELECT id, name, stock_quantity FROM products WHERE id = :id AND is_active = 1 LIMIT 1");
    $prodStmt->execute([':id' => $productId]);
    $product = $prodStmt->fetch();

    if (!$product) {
        jsonResponse('error', 'Product not found or unavailable', null, 404);
    }

    if ($product['stock_quantity'] < $quantity) {
        jsonResponse('error', 'Requested quantity exceeds available stock (' . $product['stock_quantity'] . ')', null, 400);
    }

    // Check if already in cart
    $cartStmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = :userId AND product_id = :prodId LIMIT 1");
    $cartStmt->execute([':userId' => $user['id'], ':prodId' => $productId]);
    $existing = $cartStmt->fetch();

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        if ($newQty > $product['stock_quantity']) {
            jsonResponse('error', 'Cannot add more. Reached stock limit.', null, 400);
        }

        $updateStmt = $db->prepare("UPDATE cart SET quantity = :qty WHERE id = :id");
        $updateStmt->execute([':qty' => $newQty, ':id' => $existing['id']]);
    } else {
        $insertStmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:userId, :prodId, :qty)");
        $insertStmt->execute([':userId' => $user['id'], ':prodId' => $productId, ':qty' => $quantity]);
    }

    jsonResponse('success', 'Item added to cart successfully');

} catch (Exception $e) {
    error_log("Cart Add Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to add item to cart', null, 500);
}
