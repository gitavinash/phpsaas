<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

mysqli_query($connection, "START TRANSACTION");

$resolvedUserId = 0;
$existingUser = false;
$genarateToken = false;
$tokenStatus = 'error';
$tokenMsg = 'You do not have access to this workspace.';

$candUid = isset($user_id) ? (int) $user_id : 0;
$resolved = null;

if ($candUid > 0 && function_exists('iswResolveSiteAccessForUser')) {
    $resolved = iswResolveSiteAccessForUser($connection, $candUid, (int) SITE_ID);
} elseif (!empty($mobile) && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
    $lookup = iswFindUserAndSiteAccessByIdentifier($connection, (string) $mobile, (int) SITE_ID, 'email_or_mobile');
    $resolved = $lookup['resolved'] ?? null;
}

if ($resolved && !empty($resolved['has_access'])) {
    $userStatusInt = (int) ($resolved['user']['user_status'] ?? 0);
    $siteAppInt    = (int) ($resolved['site']['site_app_id'] ?? 0);

    if ($userStatusInt === 1 || $siteAppInt === 3) {
        $resolvedUserId = (int) ($resolved['user']['user_id'] ?? 0);
        $user_id = $resolvedUserId;
        $user_salt_key = (string) ($resolved['user']['user_salt_key'] ?? '');
        $userEmail = (string) ($resolved['user']['user_email'] ?? '');
        $mobile = !empty($mobile) ? (string) $mobile : (string) ($resolved['user']['user_mobile'] ?? '');
        $user_type = (string) ($resolved['access']['site_access_type'] ?? 'visitor');
        $site_access_meta = (string) ($resolved['access']['site_access_meta'] ?? '');
        $profile_display_name = (string) ($resolved['user']['logginUserName'] ?? '');
        $profile_image = (string) ($resolved['user']['profile_image'] ?? '');
        $existingUser = true;
        $genarateToken = true;
    } else {
        $tokenMsg = 'Your account is inactive or pending verification.';
    }
} elseif ($resolved && ($resolved['reason'] ?? '') === 'disabled') {
    $tokenMsg = 'Your workspace access has been deactivated.';
}

if ($genarateToken === true && $resolvedUserId > 0) {
    mysqli_query($connection, "COMMIT");

    ob_start();
    $_COOKIE['user_data'] = base64_encode($user_salt_key);

    if (headers_sent($file, $line)) {
        error_log("Headers already sent in $file on line $line. Cookies may not work.");
    }

    require_once API_MODULES . 'analytics/session-record.php';

    $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
    if (function_exists('iswSyncGroupUserSessions') && $activeDeviceId !== '') {
        iswSyncGroupUserSessions($connection, $resolvedUserId, $activeDeviceId, (int) SITE_ID, (int) ($analyticsId ?? 0));
    }

    if (function_exists('iswSetSiteAuthCookies')) {
        iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
    }

    ob_end_flush();
    $tokenStatus = 'success';
    $tokenMsg = 'Token generated';
} else {
    mysqli_query($connection, "ROLLBACK");
    $tokenStatus = 'error';
}
