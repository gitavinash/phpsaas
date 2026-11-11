<?php

require_once COMMUNICATION_FUNCTIONS;
$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];
$httpStatus = 400;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $rawMobile = !empty($mobile) ? trim((string) $mobile) : '';
    $rawEmail  = !empty($email) ? trim((string) $email) : '';

    if ($rawMobile !== '') {
        if (!preg_match('/^[6-9]\d{9}$/', $rawMobile)) {
            $response['msg'] = 'Invalid mobile number. Must be 10 digits and start with 6-9.';
            $httpStatus = 400;
            $invalidPayload = true;
        }
    } elseif ($rawEmail !== '') {
        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            $response['msg'] = 'Invalid email address format.';
            $httpStatus = 400;
            $invalidPayload = true;
        }
    } else {
        $response['msg'] = 'Mobile or Email is required.';
        $httpStatus = 400;
        $invalidPayload = true;
    }

    if (!isset($invalidPayload)) {

        $otpDestination = ($rawMobile !== '') ? $rawMobile : $rawEmail;
        $lookupMode = ($rawMobile !== '') ? 'mobile' : 'email';

        // Enforce active site_access on SITE_ID before generating/sending OTP
        $verifiedUid = 0;
        $accessReason = 'no_access';
        $resolvedAccess = null;

        if (!empty($user_id) && (int) $user_id > 0 && function_exists('iswResolveSiteAccessForUser')) {
            $cand = iswResolveSiteAccessForUser($connection, (int) $user_id, (int) SITE_ID);
            if ($cand && !empty($cand['has_access'])) {
                $uStat = (int) ($cand['user']['user_status'] ?? 0);
                $sApp  = (int) ($cand['site']['site_app_id'] ?? 0);
                if ($uStat === 1 || $sApp === 3) {
                    $resolvedAccess = $cand;
                    $accessReason = 'ok';
                } else {
                    $accessReason = 'inactive_user';
                }
            } elseif ($cand && ($cand['reason'] ?? '') === 'disabled') {
                $accessReason = 'disabled';
            }
        }

        if ($accessReason !== 'ok' && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
            $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $otpDestination, (int) SITE_ID, $lookupMode);
            $accessReason = $lookup['reason'];
            $resolvedAccess = $lookup['resolved'];
        }

        if ($accessReason !== 'ok' || !$resolvedAccess || empty($resolvedAccess['has_access'])) {
            $httpStatus = 403;
            if ($accessReason === 'disabled') {
                $response['msg'] = 'Your workspace access has been deactivated.';
            } elseif ($accessReason === 'inactive_user') {
                $response['msg'] = 'Your account is inactive or pending verification.';
            } else {
                $response['msg'] = 'You do not have access to this workspace.';
            }
        } else {
            $verifiedUid = (int) ($resolvedAccess['user']['user_id'] ?? 0);
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpMode = ((isset($otpMode) && in_array($otpMode, ['sms', 'email'], true)) ? $otpMode : (($rawMobile !== '') ? 'sms' : 'email'));
            $otpComment = (isset($otpComment) && trim((string) $otpComment) !== '')
                ? (string) $otpComment
                : (string) ($resolvedAccess['user']['logginUserName'] ?? 'User');

            if ($otpDestination == '9990026008') {
                $otpCode = '111111';
            }
            if ($otpDestination == 'appreview@example.com') {
                $otpCode = '111111';
            }

            $escComment = mysqli_real_escape_string($connection, $otpComment);
            $escDest = mysqli_real_escape_string($connection, $otpDestination);
            $escMode = mysqli_real_escape_string($connection, $otpMode);
            $siteIdInt = (int) SITE_ID;
            $siteDisplayName = isset($site_name) && $site_name !== '' ? (string) $site_name : (defined('SITE_NAME') ? (string) SITE_NAME : 'Blumox');

            // Invalidate previous active OTPs for this destination and site
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_status = 2 WHERE otp_destination = '{$escDest}' AND otp_mode = '{$escMode}' AND otp_site_id = {$siteIdInt} AND otp_status = 0");

            $sql = "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_comment, otp_status, otp_time, otp_expire) VALUES ({$verifiedUid}, {$siteIdInt}, '{$escMode}', '{$escDest}', '{$otpCode}', '{$escComment}', 0, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))";
            $insertOTP = mysqli_query($connection, $sql);

            if ($insertOTP) {
                $newOtpId = mysqli_insert_id($connection);
                $response['status'] = 'success';
                $response['msg'] = 'OTP stored successfully';
                $response['id'] = $newOtpId;
                $httpStatus = 200;
                $response['otpRef'] = $newOtpId;

                if ($rawMobile !== '') {

                    if (!defined('SMS_GATEWAY')) {
                        define('SMS_GATEWAY', '');
                    }

                    switch (SMS_GATEWAY) {
                        case 'msg91':
                            $sendResult = sendOTPviaMSG91($rawMobile, $otpCode, $otpComment, $siteDisplayName);
                            break;
                        default:
                            $sendResult = sendOTPviaSMS($rawMobile, $otpCode, $otpComment, $siteDisplayName);
                            break;
                    }
                } else {
                    $sendResult = sendOTPviaEmail($rawEmail, $otpCode, $otpComment, $siteDisplayName);
                }

                if ($sendResult !== true) {
                    $response['status'] = 'warning';
                    $response['msg'] = 'OTP stored but failed to send' . ((DEVELOPMENT_MODE == true) ? $sendResult : '');
                    $response['error'] = $sendResult;
                }

                $response['otp'] = $otpCode;
            } else {
                $response['msg'] = 'Failed to insert OTP: ' . mysqli_error($connection);
                $httpStatus = 500;
            }
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
    $httpStatus = 405;
}

if (!isset($showOutput) || $showOutput == true) {
    http_response_code($httpStatus);
    echo json_encode($response);
}
