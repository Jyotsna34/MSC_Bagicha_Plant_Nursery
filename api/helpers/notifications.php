<?php
/**
 * Bagicha 2.0 - Notification Helpers
 */

require_once __DIR__ . '/../../config/database.php';

/**
 * Create a notification for a specific user
 */
function createNotification(int $userId, string $title, string $message, string $type = 'info', ?int $relatedId = null): bool {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, title, message, type, related_id, is_read) 
            VALUES (:userId, :title, :message, :type, :relatedId, 0)
        ");
        return $stmt->execute([
            ':userId' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type,
            ':relatedId' => $relatedId
        ]);
    } catch (Exception $e) {
        error_log("Failed to create notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Send notification to a specific user
 */
function notifyUser(int $userId, string $title, string $message, string $type = 'info', ?int $relatedId = null): bool {
    return createNotification($userId, $title, $message, $type, $relatedId);
}

/**
 * Broadcast notification to all admin users
 */
function notifyAdmins(string $title, string $message, string $type = 'info', ?int $relatedId = null): bool {
    try {
        $db = Database::getInstance();
        // Get all admin user ids
        $stmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($admins)) {
            return false;
        }
        
        $success = true;
        foreach ($admins as $adminId) {
            if (!createNotification((int)$adminId, $title, $message, $type, $relatedId)) {
                $success = false;
            }
        }
        return $success;
    } catch (Exception $e) {
        error_log("Failed to notify admins: " . $e->getMessage());
        return false;
    }
}
