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

// ==== Validate GET Request ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($otp_ref_id)) {
        $otpRefIdInt = (int) base64_decode((string) $otp_ref_id);
        $siteIdInt = (int) SITE_ID;
        if ($otpRefIdInt <= 0) {
            $response['status'] = 'error';
            $response['msg'] = 'Invalid OTP reference ID';
            $httpStatus = 400;
        } elseif ($findOTP = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otpRefIdInt} AND otp_site_id = {$siteIdInt} AND otp_status = 0 LIMIT 1")) {
            if (mysqli_num_rows($findOTP) > 0) {
                $otpData = mysqli_fetch_assoc($findOTP);
                $otpUserId = (int) ($otpData['otp_user_id'] ?? 0);
                $otpDest   = trim((string) ($otpData['otp_destination'] ?? ''));
                $modeReq   = isset($otp_mode) ? strtolower(trim((string) $otp_mode)) : strtolower(trim((string) ($otpData['otp_mode'] ?? 'email')));
                $resolvedAccess = null;
                $accessReason   = 'no_access';

                if ($otpUserId > 0 && function_exists('iswResolveSiteAccessForUser')) {
                    $cand = iswResolveSiteAccessForUser($connection, $otpUserId, $siteIdInt);
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
                } elseif ($otpDest !== '' && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
                    $lookupMode = ($modeReq === 'sms') ? 'mobile' : 'email';
                    $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $otpDest, $siteIdInt, $lookupMode);
                    $accessReason = $lookup['reason'];
                    $resolvedAccess = $lookup['resolved'];
                }

                if ($accessReason !== 'ok' || !$resolvedAccess || empty($resolvedAccess['has_access'])) {
                    $response['status'] = 'error';
                    if ($accessReason === 'disabled') {
                        $response['msg'] = 'Your workspace access has been deactivated.';
                    } elseif ($accessReason === 'inactive_user') {
                        $response['msg'] = 'Your account is inactive or pending verification.';
                    } else {
                        $response['msg'] = 'You do not have access to this workspace.';
                    }
                    $httpStatus = 403;
                } elseif (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_expire=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE otp_id = {$otpRefIdInt} AND otp_site_id = {$siteIdInt}")) {
                    $otpCode = $otpData['otp_code'];
                    $otpComment = $otpData['otp_comment'];
                    $siteDisplayName = isset($site_name) && $site_name !== '' ? (string) $site_name : (defined('SITE_NAME') ? (string) SITE_NAME : 'Blumox');
                    $siteDomainLabel = isset($site_domain) && $site_domain !== '' ? (string) $site_domain : (defined('SITE_DOMAIN') ? (string) SITE_DOMAIN : $siteDisplayName);

                    switch ($modeReq) {
                        case 'sms':
                            if (!defined('SMS_GATEWAY')) {
                                define('SMS_GATEWAY', '');
                            }

                            switch (SMS_GATEWAY) {
                                case 'msg91':
                                    $sendStatus = sendOTPviaMSG91($otpData['otp_destination'], $otpCode, $otpComment, $siteDisplayName);
                                    break;
                                default:
                                    $sendStatus = sendOTPviaNimbus($otpData['otp_destination'], $otpCode, $otpComment, $siteDisplayName);
                                    break;
                            }
                            break;
                        default:
                            $sendStatus = sendOTPviaEmail($otpData['otp_destination'], $otpCode, $otpComment, $siteDomainLabel);
                            break;
                    }

                    if ($sendStatus === true) {
                        $response['status'] = 'success';
                        $response['msg'] = 'OTP re-sent successfully';
                        $httpStatus = 200;
                    } else {
                        $response['status'] = 'error';
                        $response['msg'] = 'Failed to resend OTP: ' . ((DEVELOPMENT_MODE == true) ? $sendStatus : '');
                        $httpStatus = 500;
                    }
                } else {
                    $response['msg'] = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Unable to regerate OTP');
                }
            } else {
                $response['status'] = 'error';
                $response['msg'] = 'Invalid OTP reference ID';
                $httpStatus = 400;
            }
        } else {
            $response['status'] = 'error';
            $response['msg'] = 'Database query failed';
            $httpStatus = 500;
        }
    } else {
        $response['status'] = 'warning';
        $response['msg'] = 'No otp_ref_id provided';
        $httpStatus = 400;
    }
} else {
    $response['msg'] = 'Invalid request method';
    $httpStatus = 405;
}

// ==== Output Response ====
if (isset($showOutput)) {
    if ($showOutput == true) {
        http_response_code($httpStatus);
        echo json_encode($response);
    }
} else {
    http_response_code($httpStatus);
    echo json_encode($response);
}