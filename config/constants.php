<?php
/**
 * Bagicha - System Constants & Environment Configuration
 */

if (!defined('BAGICHA_INIT')) {
    define('BAGICHA_INIT', true);
}

// Database Credentials (supports Azure Application Settings / Environment Variables with local XAMPP fallbacks)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'bagicha_db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_SSL_CA', getenv('DB_SSL_CA') ?: '');

// Application Constants
define('APP_NAME', 'Bagicha');
define('APP_TAGLINE', 'Bring Nature Home');

if (!defined('BASE_URL')) {
    // Check for standard HTTPS or cloud reverse proxy headers (e.g. Azure App Service / Cloudflare)
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    
    if (!empty($docRoot) && strpos($projectRoot, $docRoot) === 0) {
        $basePath = substr($projectRoot, strlen($docRoot));
    } else {
        $basePath = '/Bagicha';
    }
    
    define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
}

// JWT Security Secret (Supports Azure App Service config or local fallback)
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'BagichaSecretKey_2026_Biophilic_ECommerce_Secure');
define('JWT_EXPIRATION', 86400 * 7); // 7 days in seconds

// Upload Paths
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB

// Razorpay Credentials (Supports Azure App Service config or local fallback)
define('RAZORPAY_KEY_ID', getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_Bagicha2026Secret');
define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: '');

