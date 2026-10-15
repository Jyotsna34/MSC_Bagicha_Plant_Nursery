<?php
/**
 * Bagicha 2.0 - Secure Logout API Endpoint
 * Destroys server sessions, clears authentication cookies, and confirms sign out.
 */

require_once __DIR__ . '/../helpers/response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Invalidate persistent bagicha_token cookie
setcookie('bagicha_token', '', [
    'expires' => time() - 86400,
    'path' => '/',
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax'
]);

jsonResponse('success', 'Logged out successfully');
