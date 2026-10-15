<?php
/**
 * Bagicha 2.0 - Request Payload Input Validator & Sanitizer
 */

class Validator {
    public static function getJsonInput(): array {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return $_POST;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function sanitizeString(mixed $val): string {
        if ($val === null) return '';
        return trim(htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8'));
    }

    public static function sanitizeEmail(mixed $val): string {
        $email = filter_var($val, FILTER_SANITIZE_EMAIL);
        return is_string($email) ? strtolower(trim($email)) : '';
    }

    public static function isValidEmail(string $email): bool {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isStrongPassword(string $password): bool {
        return strlen($password) >= 6;
    }

    public static function validateImageUpload(array $fileArray): bool {
        if (!isset($fileArray['tmp_name']) || $fileArray['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return false;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        return in_array($mime, $allowedMimes, true);
    }
}

