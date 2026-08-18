<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

$idEsc = mysqli_real_escape_string($connection, $id);
$siteId = defined('SITE_ID') ? SITE_ID : 1;

// Verify order exists
$checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$idEsc}' LIMIT 1");
if (!$checkOrder || mysqli_num_rows($checkOrder) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

// Update order status to 3 (Cancelled/Rejected)
$updateOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status = 3 WHERE order_id = '{$idEsc}'");

if (!$updateOrder) {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to update order status.']);
    exit;
}

// Save cancellation comment or reason if provided
$comment = trim($_POST['comment'] ?? $_POST['actionComment'] ?? $_POST['reason'] ?? '');
if (!empty($comment)) {
    $commentEsc = mysqli_real_escape_string($connection, $comment);
    $checkMeta = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$idEsc}' AND order_meta_key = 'cancel_reason' LIMIT 1");
    if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$commentEsc}' WHERE order_relation_id = '{$idEsc}' AND order_meta_key = 'cancel_reason'");
    } else {
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES({$siteId}, '{$idEsc}', 'cancel_reason', '{$commentEsc}')");
    }
}

// Create activity log
$logInternal = !empty($comment) ? "Order #{$idEsc} rejected with reason: {$comment}" : "Order #{$idEsc} was rejected";
createLog(
    $connection,
    $idEsc,
    'order',
    'order_reject',
    'Order is Rejected by Vendor',
    $logInternal
);

echo json_encode([
    'status' => 200,
    'msg' => 'Order rejected successfully.'
]);
