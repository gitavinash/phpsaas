<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

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

mysqli_query($connection, "START TRANSACTION");

// Check if order exists
$orderCheck = mysqli_query($connection, "SELECT order_id, order_vendor_id, order_status FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' AND order_site_id = " . SITE_ID);
if (!$orderCheck || mysqli_num_rows($orderCheck) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
} else {
    extract(mysqli_fetch_assoc($orderCheck));
    if ($order_vendor_id == 0) {
        echo json_encode(['status' => 'error', 'msg' => 'No vendor assigned to this order.']);
        exit;
    }
    if ($order_status != 1) {
        echo json_encode(['status' => 'error', 'msg' => 'Order is not yet processed or accepted by Vendor.']);
        exit;
    }
}

// Continue with the rest of the code

if ($checkAgent = mysqli_query($connection, "SELECT profile_display_name FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id WHERE site_access_type = 'delivery_agent' AND site_access_user_id = '{$assigned_delivery_agent}' AND site_access_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkAgent) === 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Delivery agent not found.']);
        mysqli_query($connection, "ROLLBACK");
        exit;
    } else {
        $assigned_delivery_agent_name = mysqli_fetch_assoc($checkAgent)['profile_display_name'];
    }
} else {
    mysqli_query($connection, "ROLLBACK");
    echo json_encode(['status' => 'error', 'msg' => 'Failed to check delivery agent.']);
    exit;
}

$updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_assign_to = '{$assigned_delivery_agent}' WHERE order_id = '{$id}' AND order_site_id = " . SITE_ID);

$updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status = '4' WHERE order_id = '{$id}' AND order_status=1 AND order_site_id = " . SITE_ID);

$insertLogQuery = createLog(
    $connection,
    $id,
    'order',
    'assign_delivery_agent',
    'Out for Delivery',
    "Order assigned to Delivery Partner - {$assigned_delivery_agent_name}"
);

if ($updateQuery && $updateOrderStatus && $insertLogQuery) {
    mysqli_query($connection, "COMMIT");
    echo json_encode(['status' => 200, 'msg' => 'Delivery agent assigned successfully.']);
} else {
    mysqli_query($connection, "ROLLBACK");
    echo json_encode(['status' => 'error', 'msg' => 'Failed to assign delivery agent.']);
}
