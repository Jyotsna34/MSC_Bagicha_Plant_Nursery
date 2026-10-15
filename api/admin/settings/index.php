<?php
/**
 * Bagicha 2.0 - Admin Website Settings API Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';

$db = Database::getInstance();

// Auto-create settings table if it doesn't exist yet
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT DEFAULT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (Exception $e) {
    error_log("Settings table auto-create notice: " . $e->getMessage());
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        jsonResponse('success', 'Settings retrieved', ['settings' => $settings]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $admin = requireAdmin();
        $input = Validator::getJsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        if (!empty($input['contact_email']) && !filter_var($input['contact_email'], FILTER_VALIDATE_EMAIL)) {
            jsonResponse('error', 'Invalid customer support email address', null, 422);
        }

        if (isset($input['flat_shipping_rate']) && (float)$input['flat_shipping_rate'] < 0) {
            jsonResponse('error', 'Flat shipping rate cannot be negative', null, 422);
        }

        if (isset($input['free_shipping_threshold']) && (float)$input['free_shipping_threshold'] < 0) {
            jsonResponse('error', 'Free shipping threshold cannot be negative', null, 422);
        }

        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (:key, :val) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($input as $key => $val) {
            $sanitizedKey = Validator::sanitizeString($key);
            $sanitizedVal = Validator::sanitizeString($val);
            if (!empty($sanitizedKey)) {
                $stmt->execute([':key' => $sanitizedKey, ':val' => $sanitizedVal]);
            }
        }

        jsonResponse('success', 'Website settings updated successfully');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (Exception $e) {
    error_log("Admin Settings API Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process settings request: ' . $e->getMessage(), null, 500);
}
