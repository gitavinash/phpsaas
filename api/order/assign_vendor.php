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

$updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_vendor_id = '{$assigned_vendor}' WHERE order_id = '{$id}'");

if ($updateQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Vendor assigned successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to assign vendor.']);
}
