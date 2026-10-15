<?php
/**
 * Bagicha 2.0 - Cart Items List Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();

try {
    $db = Database::getInstance();
    
    $sql = "
        SELECT c.id as cart_id, c.quantity, c.created_at,
               p.id as product_id, p.name, p.slug, p.price, p.sale_price, 
               p.main_image, p.stock_quantity, cat.name as category_name
        FROM cart c
        JOIN products p ON c.product_id = p.id
        JOIN categories cat ON p.category_id = cat.id
        WHERE c.user_id = :userId AND p.is_active = 1
        ORDER BY c.id DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':userId' => $user['id']]);
    $items = $stmt->fetchAll();

    $subtotal = 0;
    $totalItems = 0;

    foreach ($items as &$item) {
        $effectivePrice = $item['sale_price'] ? (float)$item['sale_price'] : (float)$item['price'];
        $item['effective_price'] = $effectivePrice;
        $item['item_subtotal'] = $effectivePrice * (int)$item['quantity'];
        $subtotal += $item['item_subtotal'];
        $totalItems += (int)$item['quantity'];
    }

    // Fetch dynamic shipping parameters from settings table
    $setStmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('free_shipping_threshold', 'flat_shipping_rate')");
    $settingsMap = $setStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $threshold = (float)($settingsMap['free_shipping_threshold'] ?? 999.00);
    $flatRate = (float)($settingsMap['flat_shipping_rate'] ?? 99.00);

    $shippingFee = ($subtotal >= $threshold || $subtotal == 0) ? 0.00 : $flatRate;

    jsonResponse('success', 'Cart items retrieved', [
        'items' => $items,
        'summary' => [
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $subtotal + $shippingFee
        ]
    ]);

} catch (Exception $e) {
    error_log("Cart Index Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to retrieve cart items', null, 500);
}
