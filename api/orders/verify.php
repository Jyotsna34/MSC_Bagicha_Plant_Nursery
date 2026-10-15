<?php
/**
 * Bagicha 2.0 - Verify Razorpay Payment Signature Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$orderId = (int)($input['order_id'] ?? 0);
$razorpayPaymentId = Validator::sanitizeString($input['razorpay_payment_id'] ?? '');
$razorpaySignature = Validator::sanitizeString($input['razorpay_signature'] ?? '');

if ($orderId <= 0 || empty($razorpayPaymentId)) {
    jsonResponse('error', 'Invalid order ID or payment ID', null, 400);
}

try {
    $db = Database::getInstance();

    $stmt = $db->prepare("SELECT id, order_number, payment_status, razorpay_order_id, final_amount FROM orders WHERE id = :id AND user_id = :userId LIMIT 1");
    $stmt->execute([':id' => $orderId, ':userId' => $user['id']]);
    $order = $stmt->fetch();

    if (!$order) {
        jsonResponse('error', 'Order not found', null, 404);
    }

    if ($order['payment_status'] === 'paid') {
        jsonResponse('success', 'Order has already been verified and paid.', [
            'order_id' => $orderId,
            'order_number' => $order['order_number'],
            'payment_id' => $order['razorpay_payment_id'] ?? $razorpayPaymentId
        ]);
    }

    // Razorpay HMAC SHA256 Signature Verification if Secret Key is configured
    $secret = defined('RAZORPAY_KEY_SECRET') ? RAZORPAY_KEY_SECRET : '';
    if (!empty($secret) && !empty($razorpaySignature) && !empty($order['razorpay_order_id'])) {
        $expectedSignature = hash_hmac('sha256', $order['razorpay_order_id'] . '|' . $razorpayPaymentId, $secret);
        if (!hash_equals($expectedSignature, $razorpaySignature)) {
            jsonResponse('error', 'Invalid Razorpay payment signature', null, 400);
        }
    }

    // Update order status to paid and processing
    $updStmt = $db->prepare("
        UPDATE orders 
        SET payment_status = 'paid', order_status = 'processing', razorpay_payment_id = :payId 
        WHERE id = :id
    ");
    $updStmt->execute([':payId' => $razorpayPaymentId, ':id' => $orderId]);

    // Clear user shopping cart
    $clearCartStmt = $db->prepare("DELETE FROM cart WHERE user_id = :userId");
    $clearCartStmt->execute([':userId' => $user['id']]);

    // Trigger Real-time notifications
    notifyUser($user['id'], 'Order Placed Successfully!', "Your order #{$order['order_number']} has been placed and is currently processing.", 'order', $orderId);
    notifyAdmins('New Order Received', "Order #{$order['order_number']} of value ₹" . number_format($order['final_amount'], 2) . " has been placed by {$user['full_name']}.", 'order', $orderId);

    // Check stock quantity of the items in this order for low-stock warning
    try {
        $itemsStmt = $db->prepare("
            SELECT oi.product_id, oi.product_name, p.stock_quantity 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :orderId
        ");
        $itemsStmt->execute([':orderId' => $orderId]);
        $orderItems = $itemsStmt->fetchAll();
        foreach ($orderItems as $item) {
            if ((int)$item['stock_quantity'] <= 5) {
                notifyAdmins('Low Stock Alert', "The plant '{$item['product_name']}' is running low on stock! Only {$item['stock_quantity']} units remaining.", 'stock', (int)$item['product_id']);
            }
        }
    } catch (Exception $ex) {
        error_log("Error triggering low stock warning: " . $ex->getMessage());
    }

    jsonResponse('success', 'Payment verified and order placed successfully!', [
        'order_id' => $orderId,
        'order_number' => $order['order_number'],
        'payment_id' => $razorpayPaymentId
    ]);

} catch (Exception $e) {
    error_log("Verify Order Payment Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to verify payment', null, 500);
}
