<?php
/**
 * Bagicha 2.0 - Features Migration
 * Creates contact_messages table and updates schema.
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();

    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(30) DEFAULT NULL,
        `category` VARCHAR(60) NOT NULL DEFAULT 'general',
        `subject` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `status` ENUM('unread', 'replied', 'archived') DEFAULT 'unread',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);

    echo json_encode([
        'status' => 'success',
        'message' => 'contact_messages table created successfully.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
