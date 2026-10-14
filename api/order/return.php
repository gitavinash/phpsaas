<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$return_reason = trim($_POST['return_reason'] ?? $_POST['returnReasonInput'] ?? '');
$comment = trim($_POST['comment'] ?? $_POST['actionComment'] ?? '');

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

$idEsc = mysqli_real_escape_string($connection, $id);
$reasonEsc = mysqli_real_escape_string($connection, $return_reason);
$commentEsc = mysqli_real_escape_string($connection, $comment);
$siteId = defined('SITE_ID') ? SITE_ID : 1;

// Verify order exists
$checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$idEsc}' LIMIT 1");
if (!$checkOrder || mysqli_num_rows($checkOrder) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

// If user clicks on "Unable to Deliver", mark order status as 11 (Delivery Failed) for all reasons
$newStatus = 11;
mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status = {$newStatus} WHERE order_id = '{$idEsc}'");

// Save return metadata without duplicate keys
$saveMeta = function ($key, $val) use ($connection, $siteId, $idEsc) {
    if (empty($key)) return;
    $keyEsc = mysqli_real_escape_string($connection, $key);
    $valEsc = mysqli_real_escape_string($connection, $val);

    $checkMeta = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$idEsc}' AND LOWER(order_meta_key) = LOWER('{$keyEsc}') LIMIT 1");
    if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
        $metaData = mysqli_fetch_assoc($checkMeta);
        $metaId = $metaData['order_meta_id'];
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$valEsc}' WHERE order_meta_id = '{$metaId}'");
    } else {
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES({$siteId}, '{$idEsc}', '{$keyEsc}', '{$valEsc}')");
    }
};

if (!empty($reasonEsc)) {
    $saveMeta('return_reason', $reasonEsc);
}
if (!empty($commentEsc)) {
    $saveMeta('return_comment', $commentEsc);
}

// Create activity log
$logAction = 'delivery_failed';
$logTitle = 'Delivery Failed';
$logInternal = "Unable to deliver (Delivery Failed) for Order #{$idEsc}" . (!empty($return_reason) ? ". Reason: {$return_reason}" : '') . (!empty($comment) ? ". Comment: {$comment}" : '');
createLog(
    $connection,
    $idEsc,
    'order',
    $logAction,
    $logTitle,
    $logInternal
);

echo json_encode([
    'status' => 200,
    'msg' => 'Order marked as Delivery Failed.'
]);
