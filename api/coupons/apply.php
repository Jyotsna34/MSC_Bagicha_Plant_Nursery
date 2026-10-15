<?php
/**
 * Bagicha 2.0 - Coupon Validation Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();
$code = strtoupper(Validator::sanitizeString($input['code'] ?? ''));
$subtotal = (float)($input['subtotal'] ?? 0);

if (empty($code)) {
    jsonResponse('error', 'Coupon code is required', null, 400);
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = :code AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
    $stmt->execute([':code' => $code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        // Fallback standard promos if table isn't populated
        if ($code === 'GREEN10') {
            $discount = round($subtotal * 0.10, 2);
            jsonResponse('success', 'Coupon GREEN10 applied! (10% Off)', [
                'code' => 'GREEN10',
                'discount_type' => 'percentage',
                'discount_amount' => 10,
                'calculated_discount' => $discount
            ]);
        } else if ($code === 'BAGICHA100' && $subtotal >= 499) {
            $discount = 100.00;
            jsonResponse('success', 'Coupon BAGICHA100 applied! (Flat ₹100 Off)', [
                'code' => 'BAGICHA100',
                'discount_type' => 'fixed',
                'discount_amount' => 100.00,
                'calculated_discount' => $discount
            ]);
        } else {
            jsonResponse('error', 'Invalid or expired coupon code', null, 400);
        }
    }

    if ($subtotal < (float)$coupon['min_order_amount']) {
        jsonResponse('error', 'Order subtotal must be at least ₹' . $coupon['min_order_amount'] . ' to use this coupon', null, 400);
    }

    $discount = 0.00;
    if ($coupon['discount_type'] === 'percentage') {
        $discount = round(($subtotal * (float)$coupon['discount_amount']) / 100, 2);
    } else {
        $discount = min($subtotal, (float)$coupon['discount_amount']);
    }

    jsonResponse('success', 'Coupon applied successfully!', [
        'code' => $coupon['code'],
        'discount_type' => $coupon['discount_type'],
        'discount_amount' => (float)$coupon['discount_amount'],
        'calculated_discount' => $discount
    ]);

} catch (Exception $e) {
    error_log("Coupon Apply Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to validate coupon', null, 500);
}
