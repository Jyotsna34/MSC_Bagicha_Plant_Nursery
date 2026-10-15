<?php
/**
 * Bagicha 2.0 - Real-Time Server-Sent Events Notification Stream
 */

// Allow token fallback in query parameter
if (isset($_GET['token'])) {
    $_COOKIE['bagicha_token'] = $_GET['token'];
}

require_once __DIR__ . '/../helpers/auth.php';

// Disable default PHP buffering behavior and compression
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);

// Authenticate user
$user = getAuthUser();
if (!$user) {
    echo "event: error\n";
    echo "data: " . json_encode(['message' => 'Unauthorized']) . "\n\n";
    ob_flush();
    flush();
    exit();
}

$db = Database::getInstance();
$userId = (int)$user['id'];

// Determine initial last ID
// If the client supplies last_id, use that, otherwise default to the current maximum notification ID.
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
if ($lastId <= 0) {
    $maxStmt = $db->prepare("SELECT MAX(id) as max_id FROM notifications WHERE user_id = :userId");
    $maxStmt->execute([':userId' => $userId]);
    $maxRow = $maxStmt->fetch();
    $lastId = $maxRow['max_id'] ? (int)$maxRow['max_id'] : 0;
}

// Close active session lock if present to allow parallel HTTP requests
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

try {
    // Retrieve any new notifications
    $stmt = $db->prepare("
        SELECT id, title, message, type, related_id, is_read, created_at 
        FROM notifications 
        WHERE user_id = :userId AND id > :lastId 
        ORDER BY id ASC
    ");
    $stmt->execute([':userId' => $userId, ':lastId' => $lastId]);
    $newNotifications = $stmt->fetchAll();

    if (!empty($newNotifications)) {
        foreach ($newNotifications as $notif) {
            echo "data: " . json_encode($notif) . "\n\n";
        }
    } else {
        echo ": heartbeat\n\n";
    }
    if (ob_get_level() > 0) ob_flush();
    flush();
} catch (Exception $e) {
    echo "event: error\n";
    echo "data: " . json_encode(['message' => 'Database error encountered']) . "\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}
