<?php
/**
 * Bagicha 2.0 - Admin Order & Payment Status Update Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$admin = requireAdmin();
$input = Validator::getJsonInput();

$orderId = (int)($input['order_id'] ?? 0);
$orderStatus = Validator::sanitizeString($input['order_status'] ?? '');
$paymentStatus = Validator::sanitizeString($input['payment_status'] ?? '');

if ($orderId <= 0) {
    jsonResponse('error', 'Invalid order ID', null, 400);
}

$allowedOrderStatus = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$allowedPaymentStatus = ['pending', 'paid', 'failed'];

try {
    $db = Database::getInstance();
    $db->beginTransaction();
    
    // Fetch order details first
    $ordStmt = $db->prepare("SELECT user_id, order_number, order_status FROM orders WHERE id = :id LIMIT 1");
    $ordStmt->execute([':id' => $orderId]);
    $order = $ordStmt->fetch();

    if (!$order) {
        $db->rollBack();
        jsonResponse('error', 'Order not found', null, 404);
    }

    $oldStatus = $order['order_status'];

    $updates = [];
    $params = [':id' => $orderId];

    if (!empty($orderStatus) && in_array($orderStatus, $allowedOrderStatus)) {
        $updates[] = "order_status = :ordStatus";
        $params[':ordStatus'] = $orderStatus;
    }

    if (!empty($paymentStatus) && in_array($paymentStatus, $allowedPaymentStatus)) {
        $updates[] = "payment_status = :payStatus";
        $params[':payStatus'] = $paymentStatus;
    }

    if (empty($updates)) {
        $db->rollBack();
        jsonResponse('error', 'No valid status provided', null, 400);
    }

    $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    // If order status changed to 'cancelled' from a non-cancelled state, restore product stock
    if (!empty($orderStatus) && $orderStatus === 'cancelled' && $oldStatus !== 'cancelled') {
        $itemsStmt = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :orderId");
        $itemsStmt->execute([':orderId' => $orderId]);
        $items = $itemsStmt->fetchAll();

        $restoreStockStmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :pid");
        foreach ($items as $item) {
            $restoreStockStmt->execute([':qty' => (int)$item['quantity'], ':pid' => (int)$item['product_id']]);
        }
    } 
    // If order was cancelled and is now un-cancelled, re-deduct stock
    elseif (!empty($orderStatus) && $oldStatus === 'cancelled' && $orderStatus !== 'cancelled') {
        $itemsStmt = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :orderId");
        $itemsStmt->execute([':orderId' => $orderId]);
        $items = $itemsStmt->fetchAll();

        $deductStockStmt = $db->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - :qty) WHERE id = :pid");
        foreach ($items as $item) {
            $deductStockStmt->execute([':qty' => (int)$item['quantity'], ':pid' => (int)$item['product_id']]);
        }
    }

    $db->commit();

    // Notify user of order update
    if ($order) {
        $statusStr = '';
        if (!empty($orderStatus)) {
            $statusStr = "order status is now '" . ucfirst($orderStatus) . "'";
        }
        if (!empty($paymentStatus)) {
            if (!empty($statusStr)) $statusStr .= " and ";
            $statusStr .= "payment status is now '" . ucfirst($paymentStatus) . "'";
        }
        notifyUser((int)$order['user_id'], 'Order Status Update', "Your order #{$order['order_number']} has been updated: {$statusStr}.", 'order', $orderId);
    }

    jsonResponse('success', 'Order status updated successfully');

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Admin Order Status Update Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to update order status', null, 500);
}
