<?php
/**
 * Add available products from a previous order back to the customer's cart.
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();
$orderId = (int)($input['order_id'] ?? 0);

if ($orderId <= 0) {
    jsonResponse('error', 'Invalid order ID', null, 400);
}

try {
    $db = Database::getInstance();
    $itemsStmt = $db->prepare('SELECT oi.product_id, oi.product_name, oi.quantity, p.stock_quantity, p.is_active FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id INNER JOIN orders o ON o.id = oi.order_id WHERE oi.order_id = :orderId AND o.user_id = :userId');
    $itemsStmt->execute([':orderId' => $orderId, ':userId' => $user['id']]);
    $items = $itemsStmt->fetchAll();

    if (!$items) {
        jsonResponse('error', 'Order not found or has no items', null, 404);
    }

    $db->beginTransaction();
    $addedCount = 0;
    $unavailable = [];
    $cartStmt = $db->prepare('SELECT id, quantity FROM cart WHERE user_id = :userId AND product_id = :productId LIMIT 1');
    $insertStmt = $db->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (:userId, :productId, :quantity)');
    $updateStmt = $db->prepare('UPDATE cart SET quantity = :quantity WHERE id = :id');

    foreach ($items as $item) {
        $requestedQty = max(1, (int)$item['quantity']);
        $stock = (int)($item['stock_quantity'] ?? 0);
        if (!(int)($item['is_active'] ?? 0) || $stock < 1) {
            $unavailable[] = $item['product_name'];
            continue;
        }

        $cartStmt->execute([':userId' => $user['id'], ':productId' => $item['product_id']]);
        $existing = $cartStmt->fetch();
        $quantityToAdd = min($requestedQty, $stock);
        if ($existing) {
            $newQuantity = min($stock, (int)$existing['quantity'] + $quantityToAdd);
            if ($newQuantity === (int)$existing['quantity']) {
                $unavailable[] = $item['product_name'];
                continue;
            }
            $updateStmt->execute([':quantity' => $newQuantity, ':id' => $existing['id']]);
        } else {
            $insertStmt->execute([':userId' => $user['id'], ':productId' => $item['product_id'], ':quantity' => $quantityToAdd]);
        }
        $addedCount++;
    }

    $db->commit();
    if ($addedCount === 0) {
        jsonResponse('error', 'None of the items in this order are currently available.', ['unavailable' => $unavailable], 409);
    }

    $message = $addedCount . ' item' . ($addedCount === 1 ? '' : 's') . ' added to your cart.';
    if ($unavailable) {
        $message .= ' Some previous items are currently unavailable.';
    }
    jsonResponse('success', $message, ['added_count' => $addedCount, 'unavailable' => $unavailable]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Reorder API Error: ' . $e->getMessage());
    jsonResponse('error', 'Unable to add these items to your cart.', null, 500);
}
