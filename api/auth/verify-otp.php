<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
];
$httpStatus = 400;

// ==== Validate GET Request ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // ✅ Sanitize inputs
    $otpRefId = isset($_GET['otp_ref_id']) ? (int)base64_decode((string)$_GET['otp_ref_id']) : 0;
    $otpCode = isset($_GET['otp_code']) ? preg_replace('/\D/', '', (string)$_GET['otp_code']) : '';
    $otpMode = isset($_GET['otp_mode']) ? trim((string)$_GET['otp_mode']) : '';

    // ✅ Basic validations
    if ($otpRefId <= 0) {
        $response['msg'] = 'Invalid or missing OTP reference ID';
    } elseif (strlen($otpCode) !== 6) {
        $response['msg'] = 'Invalid OTP code';
    } elseif (!in_array($otpMode, ['sms', 'email'])) {
        $response['msg'] = 'Invalid or missing OTP mode';
    } else {
        $siteIdInt = (int) SITE_ID;
        // ✅ Check OTP in database scoped to this site
        $sql = "SELECT * FROM " . TABLE_PREFIX . "otp 
                WHERE otp_id = {$otpRefId} 
                  AND (otp_site_id = {$siteIdInt} OR otp_site_id = 0)
                  AND otp_mode = '" . mysqli_real_escape_string($connection, $otpMode) . "' 
                  AND otp_status = 0
                LIMIT 1";

        $result = mysqli_query($connection, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $otpData = mysqli_fetch_assoc($result);

            // ✅ Validate OTP status & expiry
            $otpStatus = $otpData['otp_status'];
            $otpTime = strtotime($otpData['otp_time']);
            $otpExpire = strtotime($otpData['otp_expire']);
            $currentTime = time();

            if ($otpStatus != 0) {
                $response['msg'] = 'OTP has already been used or is invalid.';
            } elseif ($otpExpire < $currentTime) {
                $response['msg'] = 'OTP has expired. Please request a new one.';
            } elseif ($otpData['otp_code'] !== $otpCode) {
                $response['msg'] = 'Incorrect OTP.';
            } else {
                // Verify user & active site_access on SITE_ID BEFORE consuming OTP
                $otpUserId = (int) ($otpData['otp_user_id'] ?? 0);
                $otpDest   = (string) ($otpData['otp_destination'] ?? '');
                $resolvedAccess = null;
                $accessReason = 'no_access';

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
                }

                if ($accessReason !== 'ok' && trim($otpDest) !== '' && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
                    $lookupMode = ($otpMode === 'sms') ? 'mobile' : 'email';
                    $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $otpDest, $siteIdInt, $lookupMode);
                    if ($lookup['reason'] === 'ok' || ($resolvedAccess === null && !empty($lookup['resolved']))) {
                        $resolvedAccess = $lookup['resolved'];
                        $accessReason = $lookup['reason'];
                    }
                }

                if ($accessReason !== 'ok' || !$resolvedAccess || empty($resolvedAccess['has_access'])) {
                    $httpStatus = 403;
                    $response['status'] = 'error';
                    if ($accessReason === 'disabled') {
                        $response['msg'] = 'Your workspace access has been deactivated.';
                    } elseif ($accessReason === 'inactive_user') {
                        $response['msg'] = 'Your account is inactive or pending verification.';
                    } else {
                        $response['msg'] = 'You do not have access to this workspace.';
                    }
                } else {
                    // ✅ OTP & site_access verified successfully
                    $update = "DELETE FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otpRefId}";
                    mysqli_query($connection, $update);

                    $user_id = (int) ($resolvedAccess['user']['user_id'] ?? $otpUserId);
                    $mobile = $otpDest;

                    require_once 'generate-token-by-mobile.php';

                    if (isset($tokenStatus) && $tokenStatus === 'success') {
                        $response['status'] = 'success';
                        $response['msg'] = 'OTP verified successfully';
                        $response['userInfo'] = $otpDest;
                        $httpStatus = 200;
                        $response['existingUser'] = ((isset($existingUser) && $existingUser) ? $existingUser : true);
                    } else {
                        $response['status'] = 'error';
                        $response['msg'] = !empty($tokenMsg) ? $tokenMsg : 'You do not have access to this workspace.';
                        $httpStatus = 403;
                    }
                }
            }
        } else {
            $response['msg'] = 'OTP record not found';
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
    $httpStatus = 405;
}

// ==== Output Response ====
http_response_code($httpStatus);
echo json_encode($response);
