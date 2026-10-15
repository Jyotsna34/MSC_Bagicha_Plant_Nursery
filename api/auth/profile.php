<?php
/**
 * Bagicha 2.0 - Customer profile update endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();
$fullName = Validator::sanitizeString($input['full_name'] ?? '');
$phone = Validator::sanitizeString($input['phone'] ?? '');

if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
    jsonResponse('error', 'Please enter a name between 2 and 100 characters.', null, 422);
}

if ($phone !== '' && !preg_match('/^[0-9+()\-\s]{7,20}$/', $phone)) {
    jsonResponse('error', 'Please enter a valid phone number.', null, 422);
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare('UPDATE users SET full_name = :fullName, phone = :phone WHERE id = :id');
    $stmt->execute([
        ':fullName' => $fullName,
        ':phone' => $phone !== '' ? $phone : null,
        ':id' => $user['id']
    ]);

    $updatedUser = getAuthUser();
    jsonResponse('success', 'Profile updated successfully.', ['user' => $updatedUser]);
} catch (Exception $e) {
    error_log('Profile update error: ' . $e->getMessage());
    jsonResponse('error', 'Unable to update your profile right now.', null, 500);
}
