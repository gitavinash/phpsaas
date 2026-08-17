<?php
header('Content-Type: application/json');

require_once LOGIN_CHECK;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method. Use POST.']);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(['status' => 'error', 'msg' => 'Order ID is required.']);
    exit;
}

$siteId = defined('SITE_ID') ? SITE_ID : 1;

// Fetch existing order details
$checkOrderSql = "SELECT order_id, order_user_id, order_address_id FROM " . TABLE_PREFIX . "order WHERE order_id = '{$id}' LIMIT 1";
$query = mysqli_query($connection, $checkOrderSql);

if (!$query || mysqli_num_rows($query) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Order not found.']);
    exit;
}

$order = mysqli_fetch_assoc($query);
$userId = $order['order_user_id'];
$addressId = $order['order_address_id'];

// 1. Update Customer Profile details if user ID exists
$first_name = mysqli_real_escape_string($connection, $_POST['first_name'] ?? '');
$last_name = mysqli_real_escape_string($connection, $_POST['last_name'] ?? '');
$email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
$mobile = mysqli_real_escape_string($connection, $_POST['mobile'] ?? '');
$display_name = trim($first_name . ' ' . $last_name);

if (!empty($userId) && $userId > 0) {
    // Update isw_profile
    $updateProfileSql = "UPDATE " . TABLE_PREFIX . "profile SET 
        profile_first_name = '{$first_name}',
        profile_last_name = '{$last_name}',
        profile_display_name = '{$display_name}',
        profile_email = '{$email}',
        profile_mobile = '{$mobile}'
        WHERE profile_user_id = '{$userId}'";
    mysqli_query($connection, $updateProfileSql);

    // Update isw_user
    $updateUserSql = "UPDATE " . TABLE_PREFIX . "user SET 
        user_email = '{$email}',
        user_mobile = '{$mobile}'
        WHERE user_id = '{$userId}'";
    mysqli_query($connection, $updateUserSql);
}

// 2. Update Address & Receiver details if address ID exists
$address_house_no = mysqli_real_escape_string($connection, $_POST['address_house_no'] ?? '');
$address_area = mysqli_real_escape_string($connection, $_POST['address_area'] ?? '');
$address_landmark = mysqli_real_escape_string($connection, $_POST['address_landmark'] ?? '');
$city_name = mysqli_real_escape_string($connection, trim($_POST['city'] ?? ''));
$state_id = mysqli_real_escape_string($connection, $_POST['state'] ?? '');
$address_pin_code = mysqli_real_escape_string($connection, $_POST['address_pin_code'] ?? '');
$address_type = mysqli_real_escape_string($connection, $_POST['address_type'] ?? '');
$address_name = mysqli_real_escape_string($connection, $_POST['address_name'] ?? '');
$address_mobile = mysqli_real_escape_string($connection, $_POST['address_mobile'] ?? '');

$city_id = 0;
if (!empty($city_name)) {
    // Check if city exists in isw_city
    $cityCheck = mysqli_query($connection, "SELECT city_id FROM " . TABLE_PREFIX . "city WHERE LOWER(city_name) = LOWER('{$city_name}') LIMIT 1");
    if ($cityCheck && mysqli_num_rows($cityCheck) > 0) {
        $cityRow = mysqli_fetch_assoc($cityCheck);
        $city_id = $cityRow['city_id'];
    } else {
        // Insert new city entry if needed
        $stateFilter = !empty($state_id) ? $state_id : 1;
        $insertCity = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "city(city_state_id, city_name, city_status) VALUES('{$stateFilter}', '{$city_name}', 1)");
        if ($insertCity) {
            $city_id = mysqli_insert_id($connection);
        }
    }
}

if (!empty($addressId) && $addressId > 0) {
    $updateAddressFields = array();
    $updateAddressFields[] = "address_name = '{$address_name}'";
    $updateAddressFields[] = "address_mobile = '{$address_mobile}'";
    $updateAddressFields[] = "address_house_no = '{$address_house_no}'";
    $updateAddressFields[] = "address_area = '{$address_area}'";
    $updateAddressFields[] = "address_landmark = '{$address_landmark}'";
    if ($city_id > 0) {
        $updateAddressFields[] = "address_city_id = '{$city_id}'";
    }
    if (!empty($state_id)) {
        $updateAddressFields[] = "address_state_id = '{$state_id}'";
    }
    $updateAddressFields[] = "address_pin_code = '{$address_pin_code}'";
    $updateAddressFields[] = "address_type = '{$address_type}'";

    $updateAddressSql = "UPDATE " . TABLE_PREFIX . "address SET " . implode(', ', $updateAddressFields) . " WHERE address_id = '{$addressId}'";
    mysqli_query($connection, $updateAddressSql);
} else {
    // If address record does not exist for this order, create one and update order
    $insertAddressSql = "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_house_no, address_area, address_landmark, address_city_id, address_state_id, address_country_id, address_pin_code, address_mobile, address_type, address_status) 
        VALUES({$siteId}, '{$userId}', '{$address_name}', '{$address_house_no}', '{$address_area}', '{$address_landmark}', '{$city_id}', '{$state_id}', 101, '{$address_pin_code}', '{$address_mobile}', '{$address_type}', 1)";
    if (mysqli_query($connection, $insertAddressSql)) {
        $newAddressId = mysqli_insert_id($connection);
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_address_id = '{$newAddressId}' WHERE order_id = '{$id}'");
    }
}

// 3. Update order meta if present
if (isset($_POST['order_meta']) && is_array($_POST['order_meta'])) {
    foreach ($_POST['order_meta'] as $metaKey => $metaValue) {
        $keyEsc = mysqli_real_escape_string($connection, $metaKey);
        $valEsc = mysqli_real_escape_string($connection, $metaValue);

        $checkMeta = mysqli_query($connection, "SELECT order_meta_id FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$id}' AND order_meta_key = '{$keyEsc}' LIMIT 1");
        if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order_meta SET order_meta_value = '{$valEsc}' WHERE order_relation_id = '{$id}' AND order_meta_key = '{$keyEsc}'");
        } else {
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_site_id, order_relation_id, order_meta_key, order_meta_value) VALUES({$siteId}, '{$id}', '{$keyEsc}', '{$valEsc}')");
        }
    }
}

echo json_encode([
    'status' => 200,
    'msg' => 'Order details updated successfully.'
]);
