<?php
/**
 * Bagicha 2.0 - Contact & Support API Endpoint
 * Handles public submissions and admin review of customer queries.
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/notifications.php';

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $input = Validator::getJsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        // Check if admin is updating status via POST
        if (isset($input['action']) && $input['action'] === 'update_status') {
            $admin = requireAdmin();
            $id = (int)($input['id'] ?? 0);
            $status = trim($input['status'] ?? 'unread');
            if (!in_array($status, ['unread', 'replied', 'archived'])) {
                jsonResponse('error', 'Invalid status', null, 400);
            }
            $stmt = $db->prepare("UPDATE contact_messages SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            jsonResponse('success', 'Message status updated successfully');
        }

        // Public Contact Submission
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $category = trim($input['category'] ?? 'general');
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');

        if (empty($name) || strlen($name) < 2) {
            jsonResponse('error', 'Please provide a valid name', null, 422);
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse('error', 'Please provide a valid email address', null, 422);
        }
        if (empty($subject) || strlen($subject) < 3) {
            jsonResponse('error', 'Please provide a subject line', null, 422);
        }
        if (empty($message) || strlen($message) < 5) {
            jsonResponse('error', 'Please provide message details', null, 422);
        }

        $stmt = $db->prepare("
            INSERT INTO contact_messages (name, email, phone, category, subject, message, status)
            VALUES (:name, :email, :phone, :category, :subject, :message, 'unread')
        ");
        $stmt->execute([
            ':name' => htmlspecialchars($name),
            ':email' => htmlspecialchars($email),
            ':phone' => htmlspecialchars($phone),
            ':category' => htmlspecialchars($category),
            ':subject' => htmlspecialchars($subject),
            ':message' => htmlspecialchars($message),
        ]);
        $msgId = $db->lastInsertId();

        // Notify Admins
        try {
            $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin'");
            $admins = $adminStmt->fetchAll();
            foreach ($admins as $adm) {
                createNotification(
                    $adm['id'],
                    "New " . ucfirst($category) . " Inquiry",
                    "From {$name} ({$email}): {$subject}",
                    "contact",
                    $msgId
                );
            }
        } catch (Exception $e) {
            // Notification logging non-blocking
        }

        jsonResponse('success', 'Your inquiry has been received! Our support team will contact you shortly.', [
            'inquiry_id' => $msgId
        ]);
    }

    if ($method === 'GET') {
        $admin = requireAdmin();
        $status = $_GET['status'] ?? '';
        $sql = "SELECT * FROM contact_messages";
        $params = [];
        if (!empty($status)) {
            $sql .= " WHERE status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();

        jsonResponse('success', 'Messages retrieved', ['messages' => $messages]);
    }

    if ($method === 'DELETE') {
        $admin = requireAdmin();
        $input = Validator::getJsonInput();
        $id = (int)($_GET['id'] ?? $input['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse('error', 'Invalid message ID', null, 400);
        }
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        jsonResponse('success', 'Message deleted successfully');
    }

    jsonResponse('error', 'Method not allowed', null, 405);
} catch (Exception $e) {
    error_log("Contact API Error: " . $e->getMessage());
    jsonResponse('error', 'An error occurred while processing inquiry', null, 500);
}
