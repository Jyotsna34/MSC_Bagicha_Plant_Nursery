<?php
/**
 * Bagicha 2.0 - Admin Review Management API Endpoint
 */

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/notifications.php';

$admin = requireAdmin();
$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

function updateProductRatingStats($db, $productId) {
    $avgStmt = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = :pid AND is_approved = 1");
    $avgStmt->execute([':pid' => $productId]);
    $stats = $avgStmt->fetch();
    
    $cnt = ($stats && $stats['cnt']) ? (int)$stats['cnt'] : 0;
    $avg = ($cnt > 0 && $stats['avg_rating']) ? round($stats['avg_rating'], 2) : 5.0;

    $updStmt = $db->prepare("UPDATE products SET rating_avg = :avg, rating_count = :cnt WHERE id = :pid");
    $updStmt->execute([':avg' => $avg, ':cnt' => $cnt, ':pid' => $productId]);
}

try {
    if ($method === 'GET') {
        $stmt = $db->query("
            SELECT r.*, p.name as product_name, u.full_name as reviewer_name, u.email as reviewer_email
            FROM reviews r
            JOIN products p ON r.product_id = p.id
            JOIN users u ON r.user_id = u.id
            ORDER BY r.id DESC
        ");
        jsonResponse('success', 'Reviews retrieved', ['reviews' => $stmt->fetchAll()]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $input = Validator::getJsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse('error', 'Invalid review ID', null, 400);
        }

        $revStmt = $db->prepare("SELECT user_id, product_id, is_approved FROM reviews WHERE id = :id LIMIT 1");
        $revStmt->execute([':id' => $id]);
        $review = $revStmt->fetch();

        if (!$review) {
            jsonResponse('error', 'Review not found', null, 404);
        }

        $isApproved = isset($input['is_approved']) ? (int)$input['is_approved'] : ($review['is_approved'] == 1 ? 0 : 1);

        $upd = $db->prepare("UPDATE reviews SET is_approved = :app WHERE id = :id");
        $upd->execute([':app' => $isApproved, ':id' => $id]);

        updateProductRatingStats($db, $review['product_id']);

        if ($isApproved === 1 && (int)$review['is_approved'] === 0) {
            notifyUser((int)$review['user_id'], 'Review Approved!', 'Your product review has been approved and is now live.', 'review', $review['product_id']);
        }

        $statusLabel = $isApproved == 1 ? 'Approved' : 'Unapproved';
        jsonResponse('success', "Review status updated to {$statusLabel}");
    }

    if ($method === 'DELETE') {
        $input = Validator::getJsonInput();
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) jsonResponse('error', 'Invalid review ID', null, 400);

        $revStmt = $db->prepare("SELECT product_id FROM reviews WHERE id = :id LIMIT 1");
        $revStmt->execute([':id' => $id]);
        $review = $revStmt->fetch();

        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($review) {
            updateProductRatingStats($db, $review['product_id']);
        }

        jsonResponse('success', 'Review deleted');
    }

    jsonResponse('error', 'Method not allowed', null, 405);

} catch (Exception $e) {
    error_log("Admin Reviews Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to process reviews request', null, 500);
}

