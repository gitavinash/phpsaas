<?php

/* login module */

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$startRedirection = false;
$escEmail = mysqli_real_escape_string($connection, trim((string) ($email ?? '')));
$siteIdInt = (int) SITE_ID;
$saSiteFilter = in_array($siteIdInt, [49, 234], true)
    ? "sa.site_access_site_id IN (49, 234)"
    : "sa.site_access_site_id = {$siteIdInt}";

if ($checkUser = mysqli_query($connection, "SELECT u.user_id, u.user_mobile, u.user_password, u.user_salt_key, u.user_status FROM " . TABLE_PREFIX . "user u LEFT JOIN " . TABLE_PREFIX . "site_access sa ON sa.site_access_user_id = u.user_id AND {$saSiteFilter} WHERE u.user_email='{$escEmail}' ORDER BY (sa.site_access_status = 1) DESC, (sa.site_access_site_id = {$siteIdInt}) DESC, (sa.site_access_id IS NOT NULL) DESC, (u.user_status = 1) DESC, u.user_id ASC LIMIT 1")) {

    if (mysqli_num_rows($checkUser) != 0) {

        extract(mysqli_fetch_assoc($checkUser));

        if ($user_password == hash('sha256', $user_salt_key . ($password ?? ''))) {

            $resolved = function_exists('iswResolveSiteAccessForUser')
                ? iswResolveSiteAccessForUser($connection, (int) $user_id, $siteIdInt)
                : null;

            /** User has access of this site */

            if ($resolved && !empty($resolved['has_access']) && ((int) $user_status === 1 || (int) ($resolved['site']['site_app_id'] ?? 0) === 3)) {

                ob_start();
                $_COOKIE['user_data'] = base64_encode($user_salt_key);
                require_once API_MODULES . 'analytics/session-record.php';

                $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                if (function_exists('iswSyncGroupUserSessions') && $activeDeviceId !== '') {
                    iswSyncGroupUserSessions($connection, (int) $user_id, $activeDeviceId, $siteIdInt, (int) ($analyticsId ?? 0));
                }
                if (function_exists('iswSetSiteAuthCookies')) {
                    iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
                }
                ob_end_flush();

                $startRedirection = true;
                $status = 'success';
                $msg = 'Login Successfully';
            } elseif (!$resolved || empty($resolved['has_access'])) {
                $status = 'error';
                $msg = (($resolved['reason'] ?? '') === 'disabled')
                    ? 'Your workspace access has been deactivated.'
                    : 'You do not have access to this workspace.';
            } else {
                $status = 'error';
                $msg = 'Please verify your account first.';
            }
        } else {

            $status = 'error';
            $msg = 'Invalid Password';
        }
    } else {
        $status = 'error';
        $msg = 'No user found';
    }
} else {
    $status = 'error';
    $msg = 'Technical error';
}

$return = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid Username'),
    'url' => ((isset($url)) ? $url : '#'),
);