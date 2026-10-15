<?php
/**
 * Bagicha 2.0 - User Address Management Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

$user = requireAuth();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->prepare("SELECT * FROM addresses WHERE user_id = :userId ORDER BY is_default DESC, id DESC");
        $stmt->execute([':userId' => $user['id']]);
        jsonResponse('success', 'Addresses retrieved', ['addresses' => $stmt->fetchAll()]);
    }

    if ($method === 'POST') {
        $input = Validator::getJsonInput();
        $line1 = Validator::sanitizeString($input['address_line1'] ?? '');
        $line2 = Validator::sanitizeString($input['address_line2'] ?? '');
        $city = Validator::sanitizeString($input['city'] ?? '');
        $state = Validator::sanitizeString($input['state'] ?? '');
        $pincode = Validator::sanitizeString($input['pincode'] ?? '');
        $isDefault = isset($input['is_default']) && $input['is_default'] ? 1 : 0;

        if (empty($line1) || empty($city) || empty($state) || empty($pincode)) {
            jsonResponse('error', 'Address line 1, city, state, and pincode are required', null, 422);
        }

        if ($isDefault) {
            $resetStmt = $db->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = :userId");
            $resetStmt->execute([':userId' => $user['id']]);
        }

        $insStmt = $db->prepare("
            INSERT INTO addresses (user_id, address_line1, address_line2, city, state, pincode, is_default)
            VALUES (:userId, :line1, :line2, :city, :state, :pincode, :isDefault)
        ");
        $insStmt->execute([
            ':userId' => $user['id'],
            ':line1' => $line1,
            ':line2' => $line2,
            ':city' => $city,
            ':state' => $state,
            ':pincode' => $pincode,
            ':isDefault' => $isDefault
        ]);

        jsonResponse('success', 'Address saved successfully', ['id' => $db->lastInsertId()]);
    }

    if ($method === 'DELETE') {
        $input = Validator::getJsonInput();
        $addressId = (int)($input['id'] ?? $_GET['id'] ?? 0);

        if ($addressId <= 0) {
            jsonResponse('error', 'Invalid address ID', null, 400);
        }

        $delStmt = $db->prepare("DELETE FROM addresses WHERE id = :id AND user_id = :userId");
        $delStmt->execute([':id' => $addressId, ':userId' => $user['id']]);

        jsonResponse('success', 'Address deleted');
    }

    if ($method === 'PUT') {
        $input = Validator::getJsonInput();
        $addressId = (int)($input['id'] ?? 0);
        $line1 = Validator::sanitizeString($input['address_line1'] ?? '');
        $line2 = Validator::sanitizeString($input['address_line2'] ?? '');
        $city = Validator::sanitizeString($input['city'] ?? '');
        $state = Validator::sanitizeString($input['state'] ?? '');
        $pincode = Validator::sanitizeString($input['pincode'] ?? '');
        $isDefault = isset($input['is_default']) && $input['is_default'] ? 1 : 0;

        if ($addressId <= 0) {
            jsonResponse('error', 'Invalid address ID', null, 400);
        }
        if (empty($line1) || empty($city) || empty($state) || empty($pincode)) {
            jsonResponse('error', 'Address line 1, city, state, and pincode are required', null, 422);
        }

        $db->beginTransaction();
        if ($isDefault) {
            $resetStmt = $db->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :userId');
            $resetStmt->execute([':userId' => $user['id']]);
        }
        $updateStmt = $db->prepare('UPDATE addresses SET address_line1 = :line1, address_line2 = :line2, city = :city, state = :state, pincode = :pincode, is_default = :isDefault WHERE id = :id AND user_id = :userId');
        $updateStmt->execute([':line1' => $line1, ':line2' => $line2, ':city' => $city, ':state' => $state, ':pincode' => $pincode, ':isDefault' => $isDefault, ':id' => $addressId, ':userId' => $user['id']]);
        if ($updateStmt->rowCount() === 0) {
            $db->rollBack();
            jsonResponse('error', 'Address not found', null, 404);
        }
        $db->commit();
        jsonResponse('success', 'Address updated successfully');
    }

    if ($method === 'PATCH') {
        $input = Validator::getJsonInput();
        $addressId = (int)($input['id'] ?? 0);
        if ($addressId <= 0) {
            jsonResponse('error', 'Invalid address ID', null, 400);
        }
        $db->beginTransaction();
        $resetStmt = $db->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :userId');
        $resetStmt->execute([':userId' => $user['id']]);
        $setStmt = $db->prepare('UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :userId');
        $setStmt->execute([':id' => $addressId, ':userId' => $user['id']]);
        if ($setStmt->rowCount() === 0) {
            $db->rollBack();
            jsonResponse('error', 'Address not found', null, 404);
        }
        $db->commit();
        jsonResponse('success', 'Default delivery address updated');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Addresses API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process address request', null, 500);
}
