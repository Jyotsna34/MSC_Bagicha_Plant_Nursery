<?php
/**
 * Bagicha 2.0 - Customer/User Change Password Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$currentPassword = $input['current_password'] ?? '';
$newPassword = $input['new_password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    jsonResponse('error', 'All password fields are required.', null, 422);
}

if ($newPassword !== $confirmPassword) {
    jsonResponse('error', 'New password and confirm password do not match.', null, 422);
}

if (!Validator::isStrongPassword($newPassword)) {
    jsonResponse('error', 'New password must be at least 6 characters long.', null, 422);
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT id, password FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $user['id']]);
    $userRecord = $stmt->fetch();

    if (!$userRecord || !password_verify($currentPassword, $userRecord['password'])) {
        jsonResponse('error', 'Your current password is incorrect.', null, 400);
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updStmt = $db->prepare('UPDATE users SET password = :hash, updated_at = NOW() WHERE id = :id');
    $updStmt->execute([
        ':hash' => $newHash,
        ':id' => $user['id']
    ]);

    jsonResponse('success', 'Your password has been changed successfully.');
} catch (Exception $e) {
    error_log('User password update error: ' . $e->getMessage());
    jsonResponse('error', 'An internal error occurred while updating your password.', null, 500);
}
