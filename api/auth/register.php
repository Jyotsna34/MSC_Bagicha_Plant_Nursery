<?php
/**
 * Bagicha 2.0 - Customer Registration Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$input = Validator::getJsonInput();
$fullName = Validator::sanitizeString($input['full_name'] ?? '');
$email = Validator::sanitizeEmail($input['email'] ?? '');
$password = $input['password'] ?? '';
$phone = Validator::sanitizeString($input['phone'] ?? '');

if (empty($fullName) || empty($email) || empty($password)) {
    jsonResponse('error', 'Full name, email, and password are required', null, 422);
}

if (!Validator::isValidEmail($email)) {
    jsonResponse('error', 'Invalid email address', null, 422);
}

if (!Validator::isStrongPassword($password)) {
    jsonResponse('error', 'Password must be at least 6 characters long', null, 422);
}

try {
    $db = Database::getInstance();

    // Check existing email
    $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetch()) {
        jsonResponse('error', 'An account with this email already exists', null, 409);
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $insertStmt = $db->prepare("
        INSERT INTO users (full_name, email, password, phone, role) 
        VALUES (:full_name, :email, :password, :phone, 'customer')
    ");
    $insertStmt->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':phone' => $phone
    ]);

    $userId = $db->lastInsertId();

    $token = JWTHelper::generateToken([
        'sub' => $userId,
        'email' => $email,
        'role' => 'customer'
    ]);

    setcookie('bagicha_token', $token, time() + 604800, '/');

    jsonResponse('success', 'Account created successfully', [
        'token' => $token,
        'user' => [
            'id' => (int)$userId,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'role' => 'customer'
        ]
    ], 201);

} catch (Exception $e) {
    error_log("Register Error: " . $e->getMessage());
    jsonResponse('error', 'An unexpected error occurred during registration', null, 500);
}
