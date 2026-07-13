<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$assigned_vendor = $_POST['assigned_vendor'] ?? '';

if (empty($id) || empty($assigned_vendor)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID and Vendor Code are required.']);
    exit;
}

// Check if order exists
$orderCheck = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' LIMIT 1");
if (!$orderCheck || mysqli_num_rows($orderCheck) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

// Check if order meta already exists
$metaCheck = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$id}' AND order_meta_key = 'assigned_vendor' AND order_site_id = " . SITE_ID . " LIMIT 1");

if ($metaCheck && mysqli_num_rows($metaCheck) > 0) {
    $meta = mysqli_fetch_assoc($metaCheck);
    $metaId = $meta['order_meta_id'];
    $updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$assigned_vendor}' WHERE order_meta_id = '{$metaId}'");
} else {
    $updateQuery = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta (order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES (" . SITE_ID . ", '{$id}', 'assigned_vendor', '{$assigned_vendor}')");
}

if ($updateQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Vendor assigned successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to assign vendor.']);
}
