<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$comment = trim($_POST['comment'] ?? $_POST['actionComment'] ?? '');

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

$idEsc = mysqli_real_escape_string($connection, $id);
$commentEsc = mysqli_real_escape_string($connection, $comment);
$siteId = defined('SITE_ID') ? SITE_ID : 1;

// Verify order exists
$checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$idEsc}' LIMIT 1");
if (!$checkOrder || mysqli_num_rows($checkOrder) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

// Update order status to 6 (Delivered)
$updateOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status = 6 WHERE order_id = '{$idEsc}'");

if (!$updateOrder) {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to update order status.']);
    exit;
}

// Save delivery photo if uploaded
$photoUrl = '';
$fileInput = $_FILES['actionPhoto'] ?? $_FILES['photo'] ?? null;
if ($fileInput && !empty($fileInput['name']) && $fileInput['error'] === UPLOAD_ERR_OK) {
    $uploadDir = ROOT . '/lib/resource/ui/uploads/delivery/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($fileInput['name'], PATHINFO_EXTENSION);
    $filename = 'delivery_' . $idEsc . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($fileInput['tmp_name'], $targetPath)) {
        $photoUrl = '/lib/resource/ui/uploads/delivery/' . $filename;
    }
}

// Save delivery metadata
$saveMeta = function($key, $val) use ($connection, $siteId, $idEsc) {
    if (empty($key) || empty($val)) return;
    $valEsc = mysqli_real_escape_string($connection, $val);
    $checkMeta = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$idEsc}' AND order_meta_key = '{$key}' LIMIT 1");
    if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$valEsc}' WHERE order_relation_id = '{$idEsc}' AND order_meta_key = '{$key}'");
    } else {
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES({$siteId}, '{$idEsc}', '{$key}', '{$valEsc}')");
    }
};

if (!empty($comment)) {
    $saveMeta('delivery_comment', $comment);
}
if (!empty($photoUrl)) {
    $saveMeta('delivery_photo', $photoUrl);
}

// Create activity log
$logInternal = "Order #{$idEsc} marked as delivered" . (!empty($comment) ? ". Comment: {$comment}" : '') . (!empty($photoUrl) ? ". Photo uploaded: {$photoUrl}" : '');
createLog(
    $connection,
    $idEsc,
    'order',
    'delivered',
    'Order Delivered',
    $logInternal
);

echo json_encode([
    'status' => 200,
    'msg' => 'Order marked as delivered successfully.'
]);
