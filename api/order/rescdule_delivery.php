<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$delivery_date = trim($_POST['delivery_date'] ?? $_POST['deliveryDateInput'] ?? '');
$raw_preferred_time = trim($_POST['preferred_time'] ?? $_POST['delivery_preferred_time'] ?? $_POST['deliveryPreferredTimeInput'] ?? $_POST['preferredTime'] ?? $_POST['slot'] ?? '');
$comment = trim($_POST['comment'] ?? $_POST['actionComment'] ?? '');

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

if (empty($delivery_date)) {
    echo json_encode(['status' => 'error', 'msg' => 'Delivery date is required for reschedule.']);
    exit;
}

// Convert 24-hour slot ranges (e.g. 09:00-12:00) into 12-hour AM/PM format
function formatDeliverySlotAMPM($slotStr) {
    if (empty($slotStr)) return '';
    $slotStr = trim($slotStr);
    if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $slotStr, $matches)) {
        $startTime = strtotime($matches[1]);
        $endTime = strtotime($matches[2]);
        if ($startTime !== false && $endTime !== false) {
            return date('h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
        }
    }
    return $slotStr;
}

$preferred_time = formatDeliverySlotAMPM($raw_preferred_time);

$idEsc = mysqli_real_escape_string($connection, $id);
$dateEsc = mysqli_real_escape_string($connection, $delivery_date);
$timeEsc = mysqli_real_escape_string($connection, $preferred_time);
$commentEsc = mysqli_real_escape_string($connection, $comment);
$siteId = defined('SITE_ID') ? SITE_ID : 1;

// Verify order exists
$checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$idEsc}' LIMIT 1");
if (!$checkOrder || mysqli_num_rows($checkOrder) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

// Helper to update or insert meta without creating duplicate keys
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

// 1. Save / Update deliverydate
$saveMeta('deliverydate', $dateEsc);

// 2. Save / Update delivery slot without creating duplicate keys
if (!empty($timeEsc)) {
    $existingSlotQuery = mysqli_query($connection, "SELECT order_meta_key FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$idEsc}' AND LOWER(order_meta_key) IN ('preferred_time', 'deliveryslot', 'delivery_slot', 'timeslot', 'time_slot', 'delivery_preferred_time', 'slot') LIMIT 1");

    if ($existingSlotQuery && mysqli_num_rows($existingSlotQuery) > 0) {
        $existingRow = mysqli_fetch_assoc($existingSlotQuery);
        $slotKeyToUpdate = $existingRow['order_meta_key'];
        $saveMeta($slotKeyToUpdate, $timeEsc);
    } else {
        $saveMeta('preferred_time', $timeEsc);
    }
}

// 3. Save / Update reschedule_comment if provided
if (!empty($commentEsc)) {
    $saveMeta('reschedule_comment', $commentEsc);
}

// 4. Update order_status to 9 (Rescheduled)
mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status = 9 WHERE order_id = '{$idEsc}'");

// Create activity log
$logInternal = "Rescheduled delivery for Order #{$idEsc} to {$delivery_date}" . (!empty($preferred_time) ? " ({$preferred_time})" : '') . (!empty($comment) ? ". Comment: {$comment}" : '');
createLog(
    $connection,
    $idEsc,
    'order',
    'reschedule_delivery',
    'Rescheduled Delivery',
    $logInternal
);

echo json_encode([
    'status' => 200,
    'msg' => 'Delivery rescheduled successfully.'
]);
