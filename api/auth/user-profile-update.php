<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $rawCookieUserData = isset($_COOKIE['user_data']) ? trim((string) $_COOKIE['user_data']) : '';
    $decodedKey = ($rawCookieUserData !== '') ? (base64_decode($rawCookieUserData, true) ?: base64_decode($rawCookieUserData)) : '';
    $userKey = ($decodedKey !== '') ? mysqli_real_escape_string($connection, trim((string) $decodedKey)) : '';
    $profileName = isset($name) ? trim((string) $name) : trim((string) ($_POST['name'] ?? ''));

    if ($userKey != '') {
        if ($checkMobile = mysqli_query($connection, "SELECT user_id, user_salt_key as saltKey, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_salt_key='{$userKey}' LIMIT 1")) {
            if (mysqli_num_rows($checkMobile) == 0) {
                $status = 'error';
                $msg = 'Invalid user token found';
                $httpStatus = 400;
            } else {
                extract(mysqli_fetch_assoc($checkMobile));
                $uidInt = (int) $user_id;
                $resolvedAccess = function_exists('iswResolveSiteAccessForUser')
                    ? iswResolveSiteAccessForUser($connection, $uidInt, (int) SITE_ID)
                    : null;
                $uStat = (int) ($resolvedAccess['user']['user_status'] ?? 0);
                $sApp  = (int) ($resolvedAccess['site']['site_app_id'] ?? 0);

                if (!$resolvedAccess || empty($resolvedAccess['has_access']) || ($uStat !== 1 && $sApp !== 3)) {
                    $status = 'error';
                    if ($resolvedAccess && ($resolvedAccess['reason'] ?? '') === 'disabled') {
                        $msg = 'Your workspace access has been deactivated.';
                    } elseif ($resolvedAccess && !empty($resolvedAccess['has_access'])) {
                        $msg = 'Your account is inactive or pending verification.';
                    } else {
                        $msg = 'You do not have access to this workspace.';
                    }
                    $httpStatus = 403;
                } else {
                    $updateProfile = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='" . mysqli_real_escape_string($connection, $profileName) . "' WHERE profile_user_id={$uidInt}");
                    if ($updateProfile) {
                        $status = 'success';
                        $msg = 'Profile updated successfully';
                        $httpStatus = 200;

                        ob_start();

                        $expiry = time() + (30 * 24 * 60 * 60);
                        if (function_exists('iswBuildCookieOptions')) {
                            $cookieOptions = iswBuildCookieOptions($expiry, false);
                        } else {
                            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
                            $cleanDomain = preg_replace('/:\d+$/', '', (string) MAIN_DOMAIN);
                            $cookieOptions = [
                                'expires'  => $expiry,
                                'path'     => '/',
                                'domain'   => ($cleanDomain !== '' && $cleanDomain !== 'localhost' && !filter_var($cleanDomain, FILTER_VALIDATE_IP)) ? '.' . $cleanDomain : '',
                                'secure'   => $isSecure,
                                'httponly' => false,
                                'samesite' => $isSecure ? 'None' : 'Lax',
                            ];
                        }

                        setcookie('user_name', base64_encode($profileName), $cookieOptions);
                        $_COOKIE['user_name'] = base64_encode($profileName);

                        if (headers_sent($file, $line)) {
                            error_log("Headers already sent in $file on line $line. Cookies may not work.");
                        }

                        require_once API_MODULES . 'analytics/session-record.php';
                        $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                        if (function_exists('iswSyncGroupUserSessions') && $activeDeviceId !== '') {
                            iswSyncGroupUserSessions($connection, $uidInt, $activeDeviceId, (int) SITE_ID, (int) ($analyticsId ?? 0));
                        }
                        ob_end_flush();
                    }
                }
            }
        } else {
            $status = 'error';
            $msg = 'Something is wrong while trying to register';
            mysqli_query($connection, 'ROLLBACK');
        }
    } else {
        $status = 'error';
        $msg = 'User token is missing';
        $httpStatus = 400;
    }
} else {
    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something is wrong while trying to register');
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'otpRef' => ((isset($response['otpRef'])) ? base64_encode($response['otpRef']) : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
