<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($id) || isset($_GET['id'])) {
        $rawId = isset($id) ? $id : $_GET['id'];
        $userId = (int) base64_decode((string) $rawId);
        $siteIdInt = (int) SITE_ID;
        $resolved = ($userId > 0 && function_exists('iswResolveSiteAccessForUser'))
            ? iswResolveSiteAccessForUser($connection, $userId, $siteIdInt)
            : null;

        if ($userId <= 0) {
            $httpStatus = 400;
            $msg = 'Invalid user id';
        } elseif (!$resolved) {
            $httpStatus = 204;
            $msg = 'No user found';
        } elseif (empty($resolved['has_access'])) {
            $httpStatus = 403;
            $status = 'error';
            $msg = (($resolved['reason'] ?? '') === 'disabled')
                ? 'Your workspace access has been deactivated.'
                : 'You do not have access to this workspace.';
        } else {
            $uRow = $resolved['user'];
            $aRow = $resolved['access'];
            $user_mobile = (string) ($uRow['user_mobile'] ?? '');
            $user_email = (string) ($uRow['user_email'] ?? '');
            $site_access_type = (string) ($aRow['site_access_type'] ?? '');
            $site_access_meta = (string) ($aRow['site_access_meta'] ?? '');
            $profile_first_name = (string) ($uRow['profile_first_name'] ?? '');
            $profile_last_name = (string) ($uRow['profile_last_name'] ?? '');
            $profile_display_name = (string) ($uRow['logginUserName'] ?? '');

            require_once EMAIL_TEMPLATE . 'request-kyc.php';
            $status = 'success';
            $msg = 'KYC request sent successfully';
            $httpStatus = 201;
        }
    } else {
        $httpStatus = 400;
        $msg = 'User id is required';
    }
}
$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);