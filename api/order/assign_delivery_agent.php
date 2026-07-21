<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$assigned_delivery_agent = $_POST['assigned_delivery_agent'] ?? '';

if (empty($id) || empty($assigned_delivery_agent)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID and Delivery Agent Code are required.']);
    exit;
}

// Check if order exists
$orderCheck = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' LIMIT 1");
if (!$orderCheck || mysqli_num_rows($orderCheck) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

$updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_assign_to = '{$assigned_delivery_agent}' WHERE order_id = '{$id}'");

if ($updateQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Delivery agent assigned successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to assign delivery agent.']);
}
