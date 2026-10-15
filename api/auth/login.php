<?php
/**
 * Bagicha 2.0 - Customer & Admin Login Endpoint
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$input = Validator::getJsonInput();
$email = Validator::sanitizeEmail($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse('error', 'Email and password are required', null, 422);
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, full_name, email, phone, password, role, avatar FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse('error', 'Invalid email or password', null, 401);
    }

    $token = JWTHelper::generateToken([
        'sub' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ]);

    setcookie('bagicha_token', $token, time() + 604800, '/');

    jsonResponse('success', 'Logged in successfully', [
        'token' => $token,
        'user' => [
            'id' => (int)$user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'avatar' => $user['avatar']
        ]
    ]);

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse('error', 'An unexpected error occurred during login', null, 500);
}
