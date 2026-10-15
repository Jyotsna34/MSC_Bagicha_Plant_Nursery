<?php
/**
 * Bagicha 2.0 - Remove Item from Cart Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$cartId = (int)($input['cart_id'] ?? $_GET['id'] ?? 0);

if ($cartId <= 0) {
    jsonResponse('error', 'Invalid cart item ID', null, 400);
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("DELETE FROM cart WHERE id = :id AND user_id = :userId");
    $stmt->execute([':id' => $cartId, ':userId' => $user['id']]);

    jsonResponse('success', 'Item removed from cart');
} catch (Exception $e) {
    error_log("Cart Remove Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to remove item', null, 500);
}
