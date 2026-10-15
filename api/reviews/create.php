<?php
/**
 * Bagicha 2.0 - Submit Product Review API Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
$input = Validator::getJsonInput();

$productId = (int)($input['product_id'] ?? 0);
$rating = min(5, max(1, (int)($input['rating'] ?? 5)));
$comment = Validator::sanitizeString($input['comment'] ?? '');

if ($productId <= 0) {
    jsonResponse('error', 'Invalid product ID', null, 400);
}

try {
    $db = Database::getInstance();

    // Fetch product name for the notification message
    $prodStmt = $db->prepare("SELECT name FROM products WHERE id = :pid LIMIT 1");
    $prodStmt->execute([':pid' => $productId]);
    $product = $prodStmt->fetch();
    $productName = $product ? $product['name'] : 'Plant Product';

    $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, is_approved) VALUES (:prodId, :userId, :rating, :comment, 1)");
    $stmt->execute([':prodId' => $productId, ':userId' => $user['id'], ':rating' => $rating, ':comment' => $comment]);

    // Recalculate rating average for product (approved reviews only)
    $avgStmt = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = :pid AND is_approved = 1");
    $avgStmt->execute([':pid' => $productId]);
    $stats = $avgStmt->fetch();

    $avg = ($stats && $stats['avg_rating']) ? round($stats['avg_rating'], 2) : 5.00;
    $cnt = ($stats && $stats['cnt']) ? (int)$stats['cnt'] : 0;

    $updStmt = $db->prepare("UPDATE products SET rating_avg = :avg, rating_count = :cnt WHERE id = :pid");
    $updStmt->execute([':avg' => $avg, ':cnt' => $cnt, ':pid' => $productId]);

    // Trigger Admin notification
    notifyAdmins('New Product Review', "{$user['full_name']} submitted a {$rating}-star review for '{$productName}'.", 'review', $productId);

    jsonResponse('success', 'Review submitted successfully!');

} catch (Exception $e) {
    error_log("Submit Review Error: " . $e->getMessage());
    jsonResponse('error', 'Failed to submit review', null, 500);
}
