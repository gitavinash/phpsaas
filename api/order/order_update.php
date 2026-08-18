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

// Fetch existing order details
$checkOrderSql = "SELECT order_id, order_user_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$idEsc}' LIMIT 1";
$query = mysqli_query($connection, $checkOrderSql);

if (!$query || mysqli_num_rows($query) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

$order = mysqli_fetch_assoc($query);
$userId = $order['order_user_id'];

// 1. Update Customer Profile details (ignore address)
$first_name = mysqli_real_escape_string($connection, $_POST['first_name'] ?? '');
$last_name = mysqli_real_escape_string($connection, $_POST['last_name'] ?? '');
$email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
$mobile = mysqli_real_escape_string($connection, $_POST['mobile'] ?? '');
$display_name = trim($first_name . ' ' . $last_name);

if (!empty($userId) && $userId > 0) {
    $updateProfileSql = "UPDATE " . TABLE_PREFIX . "profile SET 
        profile_first_name = '{$first_name}',
        profile_last_name = '{$last_name}',
        profile_display_name = '{$display_name}',
        profile_email = '{$email}',
        profile_mobile = '{$mobile}'
        WHERE profile_user_id = '{$userId}'";
    mysqli_query($connection, $updateProfileSql);

    $updateUserSql = "UPDATE " . TABLE_PREFIX . "user SET 
        user_email = '{$email}',
        user_mobile = '{$mobile}'
        WHERE user_id = '{$userId}'";
    mysqli_query($connection, $updateUserSql);
}

// 2. Update order meta if present (ignore address)
if (isset($_POST['order_meta']) && is_array($_POST['order_meta'])) {
    foreach ($_POST['order_meta'] as $metaKey => $metaValue) {
        $keyEsc = mysqli_real_escape_string($connection, $metaKey);
        $valEsc = mysqli_real_escape_string($connection, $metaValue);

        $checkMeta = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$idEsc}' AND order_meta_key = '{$keyEsc}' LIMIT 1");
        if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$valEsc}' WHERE order_relation_id = '{$idEsc}' AND order_meta_key = '{$keyEsc}'");
        } else {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES({$siteId}, '{$idEsc}', '{$keyEsc}', '{$valEsc}')");
        }
    }
}

// Create activity log
createLog(
    $connection,
    $idEsc,
    'order',
    'order_update',
    'Updated customer profile',
    "Updated customer details and metadata for Order #{$idEsc}"
);

echo json_encode([
    'status' => 200,
    'msg' => 'Order details updated successfully.'
]);
