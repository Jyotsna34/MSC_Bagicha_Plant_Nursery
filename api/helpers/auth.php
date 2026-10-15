<?php
/**
 * Bagicha - Auth Middleware Helper
 */

require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/../../config/database.php';

function getAuthUser(): ?array {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authHeader = '';

    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }

    if (empty($authHeader) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (empty($authHeader) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (empty($authHeader) && isset($_COOKIE['bagicha_token'])) {
        $authHeader = 'Bearer ' . $_COOKIE['bagicha_token'];
    }

    if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
        return null;
    }

    $token = trim(substr($authHeader, 7));
    $payload = JWTHelper::decodeToken($token);

    if (!$payload || !isset($payload['sub'])) {
        return null;
    }

    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, full_name, email, phone, role, avatar FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $payload['sub']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function requireAuth(): array {
    $user = getAuthUser();
    if (!$user) {
        jsonResponse('error', 'Unauthorized access. Please login.', null, 401);
    }
    return $user;
}

function requireAdmin(): array {
    $user = requireAuth();
    if ($user['role'] !== 'admin') {
        jsonResponse('error', 'Forbidden. Admin privileges required.', null, 403);
    }
    return $user;
}

function requireAdminPage(): ?array {
    $user = getAuthUser();
    if (!$user || $user['role'] !== 'admin') {
        // Check if token exists in cookie or session
        $token = $_COOKIE['bagicha_token'] ?? '';
        if (empty($token)) {
            // Render client-side session checker & redirect script
            echo '<!DOCTYPE html><html><head><title>Admin Session Check</title><script>';
            echo 'const user = localStorage.getItem("bagicha_user") ? JSON.parse(localStorage.getItem("bagicha_user")) : null;';
            echo 'const token = localStorage.getItem("bagicha_token");';
            echo 'if(!user || user.role !== "admin" || !token) {';
            echo '  localStorage.removeItem("bagicha_token"); localStorage.removeItem("bagicha_user");';
            echo '  document.cookie = "bagicha_token=; path=/; max-age=0; SameSite=Lax";';
            echo '  window.location.href = "' . BASE_URL . '/pages/login.php";';
            echo '} else {';
            echo '  const urlParams = new URLSearchParams(window.location.search);';
            echo '  if(urlParams.has("retry")) {';
            echo '    localStorage.removeItem("bagicha_token"); localStorage.removeItem("bagicha_user");';
            echo '    document.cookie = "bagicha_token=; path=/; max-age=0; SameSite=Lax";';
            echo '    window.location.href = "' . BASE_URL . '/pages/login.php";';
            echo '  } else {';
            echo '    document.cookie = "bagicha_token=" + token + "; path=/; max-age=604800; SameSite=Lax";';
            echo '    window.location.href = window.location.pathname + "?retry=1";';
            echo '  }';
            echo '}';
            echo '</script></head><body>';
            echo '<div style="font-family:sans-serif; text-align:center; padding: 50px;"><h2>Authenticating Admin...</h2><p>Syncing session credentials...</p></div>';
            echo '</body></html>';
            exit(0);
        } else {
            // Token was present in cookie but user not found or not admin
            header('Location: ' . BASE_URL . '/pages/login.php');
            exit(0);
        }
    }
    return $user;
}
