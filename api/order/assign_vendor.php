<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;
require_once UTIL_FUNCTIONS;

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

$id = mysqli_real_escape_string($connection, $id);
$assigned_vendor = mysqli_real_escape_string($connection, $assigned_vendor);

mysqli_query($connection, "START TRANSACTION");

// Check if order exists
$orderCheck = mysqli_query($connection, "SELECT order_id, order_status as initialOrderStatus FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' AND order_site_id = " . SITE_ID);
if (!$orderCheck || mysqli_num_rows($orderCheck) === 0) {
    mysqli_query($connection, "ROLLBACK");
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
} else {
    extract(mysqli_fetch_assoc($orderCheck));
    if ($initialOrderStatus != 0) {
        mysqli_query($connection, "ROLLBACK");
        echo json_encode(['status' => 'error', 'msg' => 'Order is already assigned to a vendor.']);
        exit;
    }
}

if ($checkVendor = mysqli_query($connection, "SELECT vendor_title FROM " . TABLE_PREFIX . "vendor WHERE vendor_id = '{$assigned_vendor}' AND vendor_site_id = " . SITE_ID)) {
    if (mysqli_num_rows($checkVendor) > 0) {
        extract(mysqli_fetch_assoc($checkVendor));
        $vendorName = $vendor_title;

        $updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_vendor_id = '{$assigned_vendor}', order_status=2 WHERE order_id = '{$id}' AND order_status=0 AND order_site_id = " . SITE_ID);

        $insertLogQuery = createLog(
            $connection,
            $id,
            'order',
            'assign_vendor',
            'Assigned Order to vendor',
            "Order assigned to {$vendorName}"
        );

        if ($updateQuery && $insertLogQuery) {
            mysqli_query($connection, "COMMIT");
            echo json_encode(['status' => 200, 'msg' => 'Vendor assigned successfully.']);
        } else {
            mysqli_query($connection, "ROLLBACK");
            echo json_encode(['status' => 'error', 'msg' => 'Failed to assign vendor.']);
        }
    } else {
        mysqli_query($connection, "ROLLBACK");
        echo json_encode(['status' => 'error', 'msg' => 'Vendor not found.']);
    }
}
