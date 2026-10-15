<?php
/**
 * Bagicha 2.0 - Notifications List & Mark As Read API
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

$user = requireAuth();
$db = Database::getInstance();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = :userId 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $stmt->execute([':userId' => $user['id']]);
        $notifications = $stmt->fetchAll();
        
        jsonResponse('success', 'Notifications fetched successfully', $notifications);
    } catch (Exception $e) {
        error_log("Fetch Notifications Error: " . $e->getMessage());
        jsonResponse('error', 'Failed to fetch notifications', null, 500);
    }
} elseif ($method === 'POST') {
    $input = Validator::getJsonInput();
    $notificationId = (int)($input['notification_id'] ?? 0);
    $markAll = (bool)($input['mark_all'] ?? false);
    
    try {
        if ($markAll) {
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :userId");
            $stmt->execute([':userId' => $user['id']]);
            jsonResponse('success', 'All notifications marked as read');
        } elseif ($notificationId > 0) {
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :userId");
            $stmt->execute([':id' => $notificationId, ':userId' => $user['id']]);
            jsonResponse('success', 'Notification marked as read');
        } else {
            jsonResponse('error', 'Invalid arguments. Provide notification_id or mark_all.', null, 400);
        }
    } catch (Exception $e) {
        error_log("Update Notifications Error: " . $e->getMessage());
        jsonResponse('error', 'Failed to update notification status', null, 500);
    }
} else {
    jsonResponse('error', 'Method not allowed', null, 405);
}
