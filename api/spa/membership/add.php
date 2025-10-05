<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Trim inputs
    $warehouseId    = trim($warehouseId ?? '');
    $name = trim($name ?? '');
    $mobile    = trim($mobile ?? '');
    $package   = trim($package ?? '');
    $price = trim($price ?? '');
    $dueAmount   = trim($dueAmount ?? '');
    $visit  = trim($visit ?? '');
    $avlVisit  = trim($avlVisit ?? '');
    $requirements  = trim($requirements ?? '');

    // List of required fields
    $required = [
        'warehouseId'    => "Branch information is mising",
        'name'    => "Kindly enter name",
        'mobile'    => "Kindly enter mobile number",
        'package'    => "Kindly select room type",
        'price'    => "Kindly enter membership price",
        'visit'    => "Kindly enter total number of visits",
        'avlVisit'    => "Kindly enter total available number of visits",
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

    if ($checkUser = mysqli_query($connection, "SELECT user_id as userID FROM " . TABLE_PREFIX . "user WHERE
user_mobile='{$mobile}'")) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
        } else {
            $userID = registerUserByMobile($connection, $mobile, $name);
        }
    }

    if ($categoryInfo = mysqli_query($connection, "SELECT category_title FROM " . TABLE_PREFIX . "category WHERE category_id='{$package}' AND category_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($categoryInfo) != 0) {

            extract(mysqli_fetch_assoc($categoryInfo));

            if ($userID != '') {

                // Create a membership

                if ($generateMembership = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "membership(membership_site_id, membership_user_id, membership_profile_name, membership_branch_id, membership_type, membership_type_id, membership_total_visit, membership_avl_visit, membership_price, membership_amount_due, membership_comment, membership_status, membership_created, membership_expire) VALUES('" . SITE_ID . "', '{$userID}', '{$name}', '{$warehouseId}', 'category', '{$package}', '{$visit}', '{$avlVisit}', '{$price}', '{$dueAmount}', '{$requirements}', 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))")) {

                    $membershipId = mysqli_insert_id($connection);

                    if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                        if (ENABLE_WHATSAPP_BUSINESS_API == 'yes') {
                            require_once WHATSAPP_TEMPLATE_ADMIN . 'new_membership.php';
                        }
                    }

                    $httpStatus = 201;
                    $status = 'success';
                    $msg = 'New membership package is generated successfully';
                } else {
                    $msg = 'Something went wrong while trying to create a membership';
                }
            } else {
                $msg = 'Something went wrong while trying to create a membership';
            }
        } else {
            $msg = 'Invalid category information found';
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);