<?php
/**
 * Bagicha 2.0 - Reset Password API Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$input = Validator::getJsonInput();
$email = Validator::sanitizeEmail($input['email'] ?? '');
$token = Validator::sanitizeString($input['token'] ?? '');
$newPassword = $input['password'] ?? '';

if (empty($email) || empty($token) || empty($newPassword)) {
    jsonResponse('error', 'Email, reset token, and new password are required', null, 422);
}

if (!Validator::isStrongPassword($newPassword)) {
    jsonResponse('error', 'New password must be at least 6 characters long', null, 422);
}

try {
    $db = Database::getInstance();

    // Auto-create password_resets table if it does not exist yet
    $db->exec("
        CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(150) NOT NULL,
            `token` VARCHAR(255) NOT NULL,
            `expires_at` DATETIME NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Verify token validity with case-insensitive email matching
    $stmt = $db->prepare("
        SELECT id, email, expires_at 
        FROM password_resets 
        WHERE LOWER(email) = LOWER(:email) AND token = :token AND expires_at > NOW() 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->execute([':email' => $email, ':token' => $token]);
    $resetReq = $stmt->fetch();

    if (!$resetReq) {
        jsonResponse('error', 'Invalid or expired password reset token. Please request a new token.', null, 400);
    }

    // Update user password
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $updStmt = $db->prepare("UPDATE users SET password = :pass WHERE LOWER(email) = LOWER(:email)");
    $updStmt->execute([':pass' => $hashed, ':email' => $email]);

    // Clean up reset token
    $delStmt = $db->prepare("DELETE FROM password_resets WHERE LOWER(email) = LOWER(:email)");
    $delStmt->execute([':email' => $email]);

    jsonResponse('success', 'Password reset successfully! You can now login with your new password.');

} catch (Exception $e) {
    error_log("Reset Password Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to reset password: ' . $e->getMessage(), null, 500);
}
