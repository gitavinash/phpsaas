<?php

require_once COMMUNICATION_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($membershipId)) {

        $membershipId = base64_decode($membershipId);

        if ($checkPost = mysqli_query($connection, "SELECT membership_id, user_id, user_mobile FROM " . TABLE_PREFIX . "membership INNER JOIN " . TABLE_PREFIX . "user ON user_id=membership_user_id WHERE membership_site_id=" . SITE_ID . " AND membership_id='{$membershipId}'")) {

            if (mysqli_num_rows($checkPost) != 0) {

                extract(mysqli_fetch_assoc($checkPost));

                $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $durations = $_POST['duration'] ?? [];
                if (!is_array($durations)) {
                    $durations = [$durations];
                }
                $durations = array_values(array_map('intval', $durations));

                $otpCommentArr = [
                    'membership_id' => (int)$membershipId,
                    'visitDuration' => $durations
                ];

                $otpComment = mysqli_real_escape_string(
                    $connection,
                    json_encode($otpCommentArr, JSON_UNESCAPED_UNICODE)
                );

                $sql = "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_comment, otp_status, otp_time, otp_expire) VALUES ('{$user_id}', " . SITE_ID . ", 'sms', '{$user_mobile}', '{$otpCode}', '{$otpComment}', 0, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))";
                $insertOTP = mysqli_query($connection, $sql);
                $otpRefId = base64_encode(mysqli_insert_id($connection));
                $sendResult = sendOTPviaSMS($user_mobile, $otpCode);

                if ($sendResult !== true) {
                    $status = 'warning';
                    $msg = 'OTP stored but failed to send' . ((DEVELOPMENT_MODE == true) ? $sendResult : '');
                } else {
                    $httpStatus = 201;
                    $status = 'success';
                    $msg = 'OTP send successfully';
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
    'otpRefId' => ((isset($otpRefId)) ? $otpRefId : '')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);