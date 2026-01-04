<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Trim inputs
    $name = trim($name ?? '');
    $mobile    = trim($mobile ?? '');

    // List of required fields
    $required = [
        'mobile'    => "Kindly enter mobile number",
    ];

    // Generic empty checks
    foreach ($required as $field => $message) {
        if ($$field === '') {
            $status = 'warning';
            $msg = $message;
            break;
        }
    }

    // Extra validations if not already failed
    if (!isset($msg)) {
        if (!preg_match('/^(\+91[\-\s]?)?[0]?(91)?[6-9]\d{9}$/', $mobile)) {
            $status = 'warning';
            $msg = "Kindly enter a valid mobile number";
        }
    }

    if ($warehouseId != '') {
        $warehouseFilter = "membership_branch_id='{$warehouseId}' AND ";
    } else {
        $warehouseFilter = "";
    }

    if ($checkMembership = mysqli_query($connection, "SELECT membership_id, membership_comment, membership_profile_name, membership_total_visit, membership_avl_visit, membership_amount_due, category_title as packageName, membership_created, membership_update, membership_status FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "membership ON membership_user_id=user_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=membership_type_id AND category_site_id='" . SITE_ID . "' AND category_type='spa_room' WHERE {$warehouseFilter} membership_status IN (1,2) AND user_mobile='{$mobile}' AND membership_site_id=" . SITE_ID . " ORDER BY membership_expire ASC")) {
        if (mysqli_num_rows($checkMembership) != 0) {
            $status = 'success';
            $httpStatus = 200;
            $msg = "Membership found";
            while ($membership = mysqli_fetch_assoc($checkMembership)) {
                $data[] = $membership;
            }
        } else {
            $httpStatus = 400;
            $status = 'warning';
            $msg = 'No active membership found';
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
    'data' => ((isset($data)) ? $data : '')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);