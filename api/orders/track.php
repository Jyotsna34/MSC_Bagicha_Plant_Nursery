<?php
/**
 * Bagicha 2.0 - Public Order Tracking API Endpoint
 * Allows customers to track live plant transit status by Order Number.
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/validator.php';

header('Content-Type: application/json');

$db = Database::getInstance();

try {
    $orderNumber = trim($_GET['order_number'] ?? '');
    if (empty($orderNumber)) {
        jsonResponse('error', 'Please enter a valid order number (e.g., BAG-...)', null, 400);
    }

    $stmt = $db->prepare("
        SELECT o.id, o.order_number, o.total_amount, o.order_status, o.payment_status, 
               o.created_at, o.updated_at, u.full_name, u.email, u.phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.order_number = :ord
        LIMIT 1
    ");
    $stmt->execute([':ord' => $orderNumber]);
    $order = $stmt->fetch();

    if (!$order) {
        jsonResponse('error', 'No order found with the provided order number. Please verify and try again.', null, 404);
    }

    // Fetch order items
    $itemStmt = $db->prepare("
        SELECT oi.product_name, oi.price, oi.quantity, oi.subtotal, p.main_image, p.slug
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :oid
    ");
    $itemStmt->execute([':oid' => $order['id']]);
    $items = $itemStmt->fetchAll();

    // Generate Transit Milestones Timeline
    $statusMap = [
        'pending' => 1,
        'processing' => 2,
        'shipped' => 4,
        'delivered' => 5,
        'cancelled' => 0
    ];
    $currentStep = $statusMap[$order['order_status']] ?? 1;

    $createdAt = date('d M Y, h:i A', strtotime($order['created_at']));
    $updatedAt = date('d M Y, h:i A', strtotime($order['updated_at']));

    $timeline = [
        [
            'step' => 1,
            'title' => 'Order Received & Confirmed',
            'desc' => 'Order received in our nursery system. Pre-watering started.',
            'time' => $createdAt,
            'completed' => $currentStep >= 1
        ],
        [
            'step' => 2,
            'title' => 'Botanical Health Inspection',
            'desc' => '5-stage foliage, root vitality, and pest inspection completed.',
            'time' => $currentStep >= 2 ? $updatedAt : 'Pending',
            'completed' => $currentStep >= 2
        ],
        [
            'step' => 3,
            'title' => 'Eco-Shield Pod Packaging',
            'desc' => 'Potted in organic coir and sealed in ventilated shockproof pods.',
            'time' => $currentStep >= 3 ? $updatedAt : 'Pending',
            'completed' => $currentStep >= 3
        ],
        [
            'step' => 4,
            'title' => 'Dispatched with Express Courier',
            'desc' => 'Foliage in air-conditioned transit. Tracking code active.',
            'time' => $currentStep >= 4 ? $updatedAt : 'Estimated 24-48 hrs',
            'completed' => $currentStep >= 4
        ],
        [
            'step' => 5,
            'title' => 'Delivered to Your Doorstep',
            'desc' => 'Arrive-Alive Guarantee active for 7 days.',
            'time' => $currentStep >= 5 ? $updatedAt : 'Estimated 2-4 days',
            'completed' => $currentStep >= 5
        ]
    ];

    jsonResponse('success', 'Order status retrieved', [
        'order' => [
            'order_number' => $order['order_number'],
            'status' => $order['order_status'],
            'payment_status' => $order['payment_status'],
            'total_amount' => $order['total_amount'],
            'customer_name' => $order['full_name'],
            'created_at' => $createdAt,
            'is_cancelled' => $order['order_status'] === 'cancelled'
        ],
        'items' => $items,
        'timeline' => $timeline
    ]);

} catch (Exception $e) {
    error_log("Order Tracking API Error: " . $e->getMessage());
    jsonResponse('error', 'Unable to retrieve tracking details at this time', null, 500);
}
