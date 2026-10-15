<?php
/**
 * Bagicha 2.0 - Update Cart Item Quantity Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$cartId = (int)($input['cart_id'] ?? 0);
$quantity = (int)($input['quantity'] ?? 0);

if ($cartId <= 0) {
    jsonResponse('error', 'Invalid cart item ID', null, 400);
}

try {
    $db = Database::getInstance();

    if ($quantity <= 0) {
        $delStmt = $db->prepare("DELETE FROM cart WHERE id = :id AND user_id = :userId");
        $delStmt->execute([':id' => $cartId, ':userId' => $user['id']]);
        jsonResponse('success', 'Item removed from cart');
    } else {
        // Verify stock quantity
        $chkStmt = $db->prepare("
            SELECT c.id, p.stock_quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.id = :id AND c.user_id = :userId
        ");
        $chkStmt->execute([':id' => $cartId, ':userId' => $user['id']]);
        $row = $chkStmt->fetch();

        if (!$row) {
            jsonResponse('error', 'Cart item not found', null, 404);
        }

        if ($quantity > $row['stock_quantity']) {
            jsonResponse('error', 'Quantity exceeds stock limit (' . $row['stock_quantity'] . ')', null, 400);
        }

        $updStmt = $db->prepare("UPDATE cart SET quantity = :qty WHERE id = :id AND user_id = :userId");
        $updStmt->execute([':qty' => $quantity, ':id' => $cartId, ':userId' => $user['id']]);

        jsonResponse('success', 'Cart quantity updated');
    }
} catch (Exception $e) {
    error_log("Cart Update Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to update cart item', null, 500);
}
