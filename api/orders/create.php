<?php
/**
 * Bagicha 2.0 - Order Creation & Razorpay Order API Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$shippingAddress = Validator::sanitizeString($input['shipping_address'] ?? '');
$couponCode = strtoupper(Validator::sanitizeString($input['coupon_code'] ?? ''));

if (empty($shippingAddress)) {
    jsonResponse('error', 'Shipping address is required to place an order', null, 422);
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    // 1. Fetch user cart items
    $cartStmt = $db->prepare("
        SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.sale_price, p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :userId AND p.is_active = 1
    ");
    $cartStmt->execute([':userId' => $user['id']]);
    $cartItems = $cartStmt->fetchAll();

    if (empty($cartItems)) {
        $db->rollBack();
        jsonResponse('error', 'Your shopping cart is empty', null, 400);
    }

    $subtotal = 0.00;
    foreach ($cartItems as $item) {
        if ($item['quantity'] > $item['stock_quantity']) {
            $db->rollBack();
            jsonResponse('error', "Stock unavailable for item {$item['name']}. Available: {$item['stock_quantity']}", null, 400);
        }
        $price = $item['sale_price'] ? (float)$item['sale_price'] : (float)$item['price'];
        $subtotal += $price * (int)$item['quantity'];
    }

    // Server-side coupon discount calculation
    $discountAmount = 0.00;
    if (!empty($couponCode)) {
        $cStmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = :code AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
        $cStmt->execute([':code' => $couponCode]);
        $coupon = $cStmt->fetch();

        if (!$coupon) {
            // Check hardcoded promotional fallbacks
            if ($couponCode === 'GREEN10') {
                $discountAmount = round($subtotal * 0.10, 2);
            } else if ($couponCode === 'BAGICHA100' && $subtotal >= 499) {
                $discountAmount = 100.00;
            } else {
                $db->rollBack();
                jsonResponse('error', 'Invalid or expired coupon code', null, 400);
            }
        } else {
            if ($subtotal < (float)$coupon['min_order_amount']) {
                $db->rollBack();
                jsonResponse('error', 'Order subtotal does not meet minimum order amount for coupon ' . $coupon['code'], null, 400);
            }
            if ($coupon['discount_type'] === 'percentage') {
                $discountAmount = round(($subtotal * (float)$coupon['discount_amount']) / 100, 2);
            } else {
                $discountAmount = min($subtotal, (float)$coupon['discount_amount']);
            }
        }
    }

    $setStmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('free_shipping_threshold', 'flat_shipping_rate')");
    $settingsMap = $setStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $threshold = (float)($settingsMap['free_shipping_threshold'] ?? 999.00);
    $flatRate = (float)($settingsMap['flat_shipping_rate'] ?? 99.00);

    $shippingFee = ($subtotal >= $threshold) ? 0.00 : $flatRate;
    $finalAmount = max(0, ($subtotal + $shippingFee) - $discountAmount);

    $orderNumber = 'BAG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    $razorpayOrderId = 'order_' . bin2hex(random_bytes(8));

    // 2. Insert into orders table
    $insOrderStmt = $db->prepare("
        INSERT INTO orders (order_number, user_id, total_amount, discount_amount, final_amount, order_status, payment_status, payment_method, razorpay_order_id, shipping_address)
        VALUES (:orderNum, :userId, :total, :discount, :final, 'pending', 'pending', 'razorpay', :rzpId, :address)
    ");
    $insOrderStmt->execute([
        ':orderNum' => $orderNumber,
        ':userId' => $user['id'],
        ':total' => $subtotal + $shippingFee,
        ':discount' => $discountAmount,
        ':final' => $finalAmount,
        ':rzpId' => $razorpayOrderId,
        ':address' => $shippingAddress
    ]);

    $orderId = $db->lastInsertId();

    // 3. Insert order items & reduce product stock
    $insItemStmt = $db->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
        VALUES (:orderId, :prodId, :prodName, :price, :qty, :subtotal)
    ");

    $updStockStmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id");

    foreach ($cartItems as $item) {
        $price = $item['sale_price'] ? (float)$item['sale_price'] : (float)$item['price'];
        $itemSubtotal = $price * (int)$item['quantity'];

        $insItemStmt->execute([
            ':orderId' => $orderId,
            ':prodId' => $item['product_id'],
            ':prodName' => $item['name'],
            ':price' => $price,
            ':qty' => $item['quantity'],
            ':subtotal' => $itemSubtotal
        ]);

        $updStockStmt->execute([
            ':qty' => $item['quantity'],
            ':id' => $item['product_id']
        ]);
    }

    $db->commit();

    jsonResponse('success', 'Order created successfully', [
        'order_id' => (int)$orderId,
        'order_number' => $orderNumber,
        'final_amount' => $finalAmount,
        'razorpay_order_id' => $razorpayOrderId,
        'key_id' => defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : 'rzp_test_Bagicha2026Secret'
    ], 201);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Create Order Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to create order', null, 500);
}
