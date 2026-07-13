<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use GET.']);
    exit;
}

$id = $_GET['id'] ?? '';
$product_id = $_GET['product_id'] ?? '';

if (empty($id) || empty($product_id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID and Product ID are required.']);
    exit;
}

// Fetch current order product details
$query = mysqli_query($connection, "SELECT order_product_id, order_product_qty FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' LIMIT 1");
if (!$query || mysqli_num_rows($query) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

$order = mysqli_fetch_assoc($query);
$currentProducts = array_values(array_filter(explode(',', str_replace(' ', '', $order['order_product_id']))));
$currentQtys = array_values(array_filter(explode(',', str_replace(' ', '', $order['order_product_qty']))));

// Align arrays length in case of mismatches
while (count($currentQtys) < count($currentProducts)) {
    $currentQtys[] = '1';
}

$idx = array_search($product_id, $currentProducts);
if ($idx !== false) {
    unset($currentProducts[$idx]);
    unset($currentQtys[$idx]);
}

$newProductsStr = implode(',', $currentProducts);
$newQtysStr = implode(',', $currentQtys);

$updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id = '{$newProductsStr}', order_product_qty = '{$newQtysStr}' WHERE order_id = '{$id}'");

if ($updateQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Product removed successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to remove product.']);
}
