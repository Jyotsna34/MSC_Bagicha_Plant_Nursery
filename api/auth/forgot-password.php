<?php
/**
 * Bagicha 2.0 - Forgot Password Request API Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$input = Validator::getJsonInput();
$email = Validator::sanitizeEmail($input['email'] ?? '');

if (empty($email) || !Validator::isValidEmail($email)) {
    jsonResponse('error', 'Please enter a valid registered email address', null, 422);
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

    // Check if user exists in database
    $stmt = $db->prepare("SELECT id, full_name, email FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse('error', 'No registered account found with email ' . htmlspecialchars($email) . '. Please check your email or Register a new account.', null, 404);
    }

    // Delete any old pending reset tokens for this email
    $delOld = $db->prepare("DELETE FROM password_resets WHERE LOWER(email) = LOWER(:email)");
    $delOld->execute([':email' => $email]);

    // Generate secure random reset token
    $token = bin2hex(random_bytes(32));

    // Store in password_resets table with 2-hour expiry using MySQL DATE_ADD
    $insStmt = $db->prepare("
        INSERT INTO password_resets (email, token, expires_at) 
        VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 2 HOUR))
    ");
    $insStmt->execute([
        ':email' => $email,
        ':token' => $token
    ]);

    jsonResponse('success', 'Password reset token generated successfully!', [
        'reset_token' => $token,
        'expires_in' => '2 hours',
        'email' => $email
    ]);

} catch (Exception $e) {
    error_log("Forgot Password Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process password reset request: ' . $e->getMessage(), null, 500);
}
