<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use GET.']);
    exit;
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

// Delete the assigned vendor meta
$deleteQuery = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$id}' AND order_meta_key = 'assigned_vendor' AND order_site_id = " . SITE_ID);

if ($deleteQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Vendor removed successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to remove vendor.']);
}
