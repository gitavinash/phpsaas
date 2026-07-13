<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';
$product_id = $_POST['product_id'] ?? '';
$qty = $_POST['qty'] ?? '1';
$old_product_id = $_POST['old_product_id'] ?? '';

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

$updated = false;

// If we are replacing an old product (edit mode)
if (!empty($old_product_id)) {
    $idx = array_search($old_product_id, $currentProducts);
    if ($idx !== false) {
        $currentProducts[$idx] = $product_id;
        $currentQtys[$idx] = $qty;
        $updated = true;
    }
}

if (!$updated) {
    // Check if the product is already in the order
    $idx = array_search($product_id, $currentProducts);
    if ($idx !== false) {
        $currentQtys[$idx] = $qty;
    } else {
        $currentProducts[] = $product_id;
        $currentQtys[] = $qty;
    }
}

$newProductsStr = implode(',', $currentProducts);
$newQtysStr = implode(',', $currentQtys);

$updateQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id = '{$newProductsStr}', order_product_qty = '{$newQtysStr}' WHERE order_id = '{$id}'");

if ($updateQuery) {
    echo json_encode(['status' => 200, 'msg' => 'Product assigned successfully.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Failed to assign product.']);
}
