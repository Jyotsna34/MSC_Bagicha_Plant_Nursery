<?php
/**
 * Bagicha 2.0 - Admin Coupon CRUD Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

$admin = requireAdmin();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM coupons ORDER BY id DESC");
        jsonResponse('success', 'Coupons retrieved', ['coupons' => $stmt->fetchAll()]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $input = Validator::getJsonInput();
        $id = (int)($input['id'] ?? 0);
        $code = strtoupper(Validator::sanitizeString($input['code'] ?? ''));
        $type = Validator::sanitizeString($input['discount_type'] ?? 'percentage');
        $amount = (float)($input['discount_amount'] ?? 0);
        $minOrder = max(0, (float)($input['min_order_amount'] ?? 0));
        $expiresAt = !empty($input['expires_at']) ? Validator::sanitizeString($input['expires_at']) : null;
        $isActive = isset($input['is_active']) ? (int)$input['is_active'] : 1;

        if (empty($code) || $amount <= 0) {
            jsonResponse('error', 'Coupon code and positive discount amount are required', null, 422);
        }

        if ($type === 'percentage' && $amount > 100) {
            jsonResponse('error', 'Percentage discount cannot exceed 100%', null, 422);
        }

        if ($id > 0) {
            // Update existing coupon
            $stmt = $db->prepare("
                UPDATE coupons 
                SET code = :code, discount_type = :type, discount_amount = :amt, min_order_amount = :min, expires_at = :expires, is_active = :active 
                WHERE id = :id
            ");
            $stmt->execute([
                ':code' => $code,
                ':type' => $type,
                ':amt' => $amount,
                ':min' => $minOrder,
                ':expires' => $expiresAt,
                ':active' => $isActive,
                ':id' => $id
            ]);
            jsonResponse('success', 'Coupon updated successfully');
        } else {
            // Create new coupon
            $stmt = $db->prepare("INSERT INTO coupons (code, discount_type, discount_amount, min_order_amount, expires_at, is_active) VALUES (:code, :type, :amt, :min, :expires, :active)");
            $stmt->execute([':code' => $code, ':type' => $type, ':amt' => $amount, ':min' => $minOrder, ':expires' => $expiresAt, ':active' => $isActive]);
            jsonResponse('success', 'Coupon created successfully', ['id' => $db->lastInsertId()], 201);
        }
    }

    if ($method === 'DELETE') {
        $input = Validator::getJsonInput();
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) jsonResponse('error', 'Invalid coupon ID', null, 400);

        $stmt = $db->prepare("DELETE FROM coupons WHERE id = :id");
        $stmt->execute([':id' => $id]);
        jsonResponse('success', 'Coupon deleted');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (PDOException $e) {
    if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062')) {
        jsonResponse('error', 'A coupon with this code already exists', null, 422);
    }
    error_log("Admin Coupon Error: " . $e->getMessage());
    jsonResponse('error', 'Database error encountered', null, 500);
} catch (Exception $e) {
    error_log("Admin Coupon Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process coupon request', null, 500);
}
