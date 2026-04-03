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
    $package   = trim($package ?? '');
    $price = trim($price ?? '');
    $dueAmount   = trim($dueAmount ?? '');
    $visit  = trim($visit ?? '');
    $avlVisit  = trim($avlVisit ?? '');
    $requirements  = trim($requirements ?? '');

    // List of required fields
    $required = [
        'warehouseId'    => "Branch information is mising",
        'membershipId'    => "Membership information is mising",
        'name'    => "Kindly enter name",
        'package'    => "Kindly select room type",
        'price'    => "Kindly enter membership price",
        'visit'    => "Kindly enter total number of visis",
        'avlVisit'    => "Kindly enter avaiable total number of visis",
    ];

    // Generic empty checks
    foreach ($required as $field => $message) {
        if ($$field === '') {
            $status = 'warning';
            $msg = $message;
            break;
        }
    }

    $membershipId = base64_decode($membershipId);

    $membershipStatus = (($avlVisit != 0) ? 1 : 2);

    mysqli_query($connection, "START TRANSACTION");

    if ($checkMembership = mysqli_query($connection, "SELECT membership_id, membership_avl_visit as oldAvailableVisits FROM " . TABLE_PREFIX . "membership WHERE
membership_id='{$membershipId}' AND membership_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkMembership) != 0) {
            extract(mysqli_fetch_assoc($checkMembership));

            $deductVisit = $oldAvailableVisits - $avlVisit;

            if ($deductVisit > 0) {
                $visitValues = "";
                for ($i = 0; $i < $deductVisit; $i++) {
                    $visitValues .= "('{$membershipId}', '" . SITE_ID . "', '60', NOW(), 1, 'Deducted by Admin'),";
                }
                $visitValues = rtrim($visitValues, ',');
                $insertVisit = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "membership_visits (visit_membership_id, visit_site_id, visit_duration, visit_time, visit_status, visit_comment) VALUES {$visitValues}");
            } else {
                $insertVisit = true;
            }

            $updateMembership = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "membership SET membership_profile_name='{$name}', membership_branch_id='{$warehouseId}', membership_type_id='{$package}', membership_type='category', membership_total_visit='{$visit}', membership_avl_visit='{$avlVisit}', membership_price='{$price}', membership_amount_due='{$dueAmount}', membership_comment='{$requirements}', membership_status='{$membershipStatus}' WHERE membership_id='{$membershipId}' AND membership_site_id=" . SITE_ID);


            // Update a membership

            if ($insertVisit && $updateMembership) {
                mysqli_query($connection, "COMMIT");
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Membership package is update successfully';
            } else {
                mysqli_query($connection, "ROLLBACK");
                $msg = 'Something went wrong while trying to update a membership';
            }
        } else {
            $msg = 'Something went wrong while trying to update a membership';
        }
    } else {
        $userID = registerUserByMobile($connection, $mobile, $name);
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