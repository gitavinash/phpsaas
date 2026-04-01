<?php

require_once COMMUNICATION_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($otpRefId)) {

        // decode base64 ref id safely
        $decodedRef = base64_decode($otpRefId, true);
        $otpRefId   = ($decodedRef !== false) ? intval($decodedRef) : 0;

        if ($otpRefId > 0 && isset($otpCode) && $checkOTP = mysqli_query($connection, "SELECT otp_comment, otp_code FROM " . TABLE_PREFIX . "otp WHERE otp_id='{$otpRefId}' AND otp_status=0 AND otp_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkOTP) != 0) {

                extract(mysqli_fetch_assoc($checkOTP)); // gives $otp_comment

                $otpInfo = json_decode($otp_comment, true);

                // handle legacy double-encoded durations if needed
                if (isset($otpInfo['visitDuration']) && !is_array($otpInfo['visitDuration'])) {
                    $tmp = json_decode($otpInfo['visitDuration'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $otpInfo['visitDuration'] = $tmp;
                    }
                }

                if ($otpCode == $otp_code) {
                    $membershipId = (isset($otpInfo['membership_id']) ? (int)$otpInfo['membership_id'] : 0);
                    $durArr       = (isset($otpInfo['visitDuration']) && is_array($otpInfo['visitDuration'])) ? $otpInfo['visitDuration'] : array();

                    if ($membershipId > 0 && count($durArr) > 0) {

                        if ($checkMembership = mysqli_query($connection, "SELECT membership_avl_visit FROM " . TABLE_PREFIX . "membership WHERE membership_id='{$membershipId}' AND membership_status=1")) {
                            if (mysqli_num_rows($checkMembership) != 0) {

                                extract(mysqli_fetch_assoc($checkMembership));

                                // build multi-row VALUES tuples like: (mId, siteId, duration, NULL, NULL, NULL, 1)
                                $visitRecord = array();
                                foreach ($durArr as $durationLength) {
                                    $d = (int)$durationLength;
                                    if ($d > 0) {
                                        $visitRecord[] = "(" . $membershipId . ", " . SITE_ID . ", " . $d . ", 0, 0, 0, 1)";
                                    }
                                }

                                $totalVisit = count($visitRecord);

                                if ($totalVisit > 0) {

                                    mysqli_query($connection, "START TRANSACTION");

                                    // bulk insert
                                    $insertVisit = mysqli_query(
                                        $connection,
                                        "INSERT INTO " . TABLE_PREFIX . "membership_visits(visit_membership_id, visit_site_id, visit_duration, visit_room_number, visit_staff_one_id, visit_staff_two_id, visit_status) VALUES " . implode(',', $visitRecord)
                                    );

                                    $balanceVisit = $membership_avl_visit - $totalVisit;

                                    $changeMembershipStatus = (($balanceVisit == 0) ? " , membership_status=2 " : '');

                                    // decrement available visits only if strictly greater than requested
                                    $updateMembership = mysqli_query(
                                        $connection,
                                        "UPDATE " . TABLE_PREFIX . "membership SET membership_avl_visit=membership_avl_visit-'{$totalVisit}' {$changeMembershipStatus} WHERE membership_avl_visit>={$totalVisit} AND membership_id='{$membershipId}' AND membership_site_id=" . SITE_ID
                                    );

                                    // mark OTP as used so it can't be reused
                                    $updateOTP = mysqli_query(
                                        $connection,
                                        "UPDATE " . TABLE_PREFIX . "otp SET otp_status=1 WHERE otp_id='{$otpRefId}' AND otp_status=0 AND otp_site_id=" . SITE_ID
                                    );

                                    if ($insertVisit && $updateMembership && $updateOTP) {
                                        $httpStatus = 201;
                                        $status = 'success';
                                        $msg = 'OTP verified successfully';
                                        mysqli_query($connection, "COMMIT");
                                    } else {
                                        $msg = 'Something went wrong while verifying OTP';
                                        mysqli_query($connection, "ROLLBACK");
                                    }
                                } else {
                                    $msg = 'Invalid durations found';
                                }
                            } else {
                                $status = 'warning';
                                $msg = 'Membership details not found';
                            }
                        } else {
                            $msg = 'Something went wrong while updating the membership';
                        }
                    } else {
                        $msg = 'Invalid OTP data';
                    }
                } else {
                    $msg = 'Invalid OTP code';
                }
            } else {

                $msg = 'Invalid request found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {
        $msg = 'Something is wrong while sending request to server';
    }
} else {
    $msg = 'Invalid request method';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
    'data' => ((isset($data)) ? $data : '')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);