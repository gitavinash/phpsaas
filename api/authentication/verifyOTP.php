<?php

function verifyOTP($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $siteIdInt = (int) SITE_ID;
    $otp_id = (int) base64_decode((string) ($otp_id ?? ''));
    $rawMobile = isset($mobile) ? trim((string) $mobile) : '';

    if ($otp_id > 0 && ($checkOtpId = mysqli_query($connection, "SELECT otp_id, otp_user_id, otp_code, otp_status, otp_expire, otp_destination FROM " . TABLE_PREFIX . "otp WHERE otp_id='{$otp_id}' AND (otp_site_id={$siteIdInt} OR otp_site_id=0) LIMIT 1"))) {

        if (mysqli_num_rows($checkOtpId) != 0) {

            extract(mysqli_fetch_assoc($checkOtpId));

            if (time() <= strtotime($otp_expire)) {

                if ($otp_status == 0) {

                    if ($otp_code == ($otp ?? '')) {

                        $resolved = null;
                        $accessReason = 'no_access';
                        $otpUserIdInt = (int) ($otp_user_id ?? 0);

                        if ($otpUserIdInt > 0 && function_exists('iswResolveSiteAccessForUser')) {
                            $cand = iswResolveSiteAccessForUser($connection, $otpUserIdInt, $siteIdInt);
                            if ($cand && !empty($cand['has_access'])) {
                                $uStat = (int) ($cand['user']['user_status'] ?? 0);
                                $sApp  = (int) ($cand['site']['site_app_id'] ?? 0);
                                if ($uStat === 1 || $sApp === 3) {
                                    $resolved = $cand;
                                    $accessReason = 'ok';
                                } else {
                                    $accessReason = 'inactive_user';
                                }
                            } elseif ($cand && ($cand['reason'] ?? '') === 'disabled') {
                                $accessReason = 'disabled';
                            }
                        }

                        if ($accessReason !== 'ok' && $rawMobile !== '' && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
                            $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $rawMobile, $siteIdInt, 'mobile');
                            if ($lookup['reason'] === 'ok' || ($resolved === null && !empty($lookup['resolved']))) {
                                $resolved = $lookup['resolved'];
                                $accessReason = $lookup['reason'];
                            }
                        }

                        if ($accessReason !== 'ok' || !$resolved || empty($resolved['has_access'])) {
                            if ($accessReason === 'disabled') {
                                $status = 'warning';
                                $msg = 'Your account was deactivated';
                            } elseif ($accessReason === 'inactive_user') {
                                $status = 'error';
                                $msg = 'Your account is inactive or pending verification.';
                            } else {
                                $status = 'error';
                                $msg = 'You do not have access to this workspace.';
                            }
                        } elseif ($updateOTP = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_status=1 WHERE otp_id='{$otp_id}'")) {
                            $verifiedUid = (int) ($resolved['user']['user_id'] ?? 0);
                            $userSaltKey = (string) ($resolved['user']['user_salt_key'] ?? '');

                            /** User Session Data */
                            ob_start();
                            $_COOKIE['user_data'] = base64_encode($userSaltKey);
                            require_once API_MODULES . 'analytics/session-record.php';

                            $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                            if (function_exists('iswSyncGroupUserSessions') && $activeDeviceId !== '') {
                                iswSyncGroupUserSessions($connection, $verifiedUid, $activeDeviceId, $siteIdInt, (int) ($analyticsId ?? 0));
                            }
                            if (function_exists('iswSetSiteAuthCookies')) {
                                iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
                            }
                            ob_end_flush();

                            $status = 200;
                            $msg = 'Login Successfully';
                            $url = '/dashboard/';
                        } else {
                            $msg = 'Something is wrong while verifying OTP';
                        }
                    } else {

                        $msg = 'Invalid OTP';
                    }
                } else {

                    $status = 'warning';
                    $msg = 'OTP is already verfied';
                }
            } else {

                $msg = 'OTP is expired';
            }
        } else {

            $msg = 'Invalid OTP transaction found';
        }
    } else {

        $msg = 'Technical error';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : ''),
        'url' => ((isset($url)) ? $url : '')
    );

    echo json_encode($result, true);
}