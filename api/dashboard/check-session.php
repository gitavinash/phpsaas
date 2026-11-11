<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    /** Accept header request */

    foreach (getallheaders() as $name => $value) {

        ${'header_' . $name} = $value;
    }

    if (isset($header_user_token)) {

        $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
        if (file_exists($ssoFile)) {
            require_once $ssoFile;
        }

        $escSalt = mysqli_real_escape_string($connection, (string) base64_decode((string) $header_user_token));

        if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$escSalt}' LIMIT 1")) {

            if (mysqli_num_rows($checkUserIsReal) != 0) {

                $uRow = mysqli_fetch_assoc($checkUserIsReal);
                $user_id = (int) ($uRow['user_id'] ?? 0);
                $resolved = ($user_id > 0 && function_exists('iswResolveSiteAccessForUser'))
                    ? iswResolveSiteAccessForUser($connection, $user_id, (int) SITE_ID)
                    : null;

                if (!$resolved) {
                    $status = 404;
                    $msg = 'No user found';
                } elseif (empty($resolved['has_access'])) {
                    $status = 403;
                    $msg = (($resolved['reason'] ?? '') === 'disabled')
                        ? 'Your workspace access has been deactivated.'
                        : 'Unauthorized access';
                } elseif ((int) ($resolved['user']['user_status'] ?? 0) !== 1 && (int) ($resolved['site']['site_app_id'] ?? 0) !== 3) {
                    $status = 204;
                    $msg = 'Account is not verified';
                } else {
                    $user_email = (string) ($resolved['user']['user_email'] ?? '');
                    $company_id = $resolved['user']['company_id'] ?? '';
                    $vendor_id = $resolved['user']['vendor_id'] ?? '';
                    $site_status = (int) ($resolved['site']['site_status'] ?? 0);
                    $site_name = (string) ($resolved['site']['site_name'] ?? '');
                    $site_icon = (string) ($resolved['site']['site_icon'] ?? '');
                    $site_app = (string) ($resolved['site']['site_app'] ?? 'system');
                    $site_access_type = (string) ($resolved['access']['site_access_type'] ?? 'visitor');
                    $encryptEmail = base64_encode($user_email);

                    /** Define new constant variable */

                    if (!defined('USER_ID')) {
                        define('USER_ID', $user_id);
                    }
                    if (!defined('COMPANY_ID')) {
                        define('COMPANY_ID', (($company_id != '') ? $company_id : '0'));
                    }
                    if (!defined('VENDOR_ID')) {
                        define('VENDOR_ID', (($vendor_id != '') ? $vendor_id : '0'));
                    }

                    /** Define new cookie for logged in user */

                    $uiOpts = function_exists('iswBuildCookieOptions')
                        ? iswBuildCookieOptions(time() + 30 * 24 * 60 * 60, false)
                        : (time() + 30 * 24 * 60 * 60);

                    if (!isset($_COOKIE['site_app'])) {

                        $appForCookie = defined('SITE_APP') ? SITE_APP : $site_app;
                        setcookie('site_app', base64_encode($appForCookie), $uiOpts);
                        $status = 200;
                        $msg = 'valid user';
                    } else {

                        /** Check website status */

                        if ($site_status != 1) {

                            if ($site_access_type == 'administrator') {

                                /** if user type is Administrator Redirect to website setup page */

                                $status = 200;
                                $msg = 'Complete website setup to continue';
                            } else {

                                /** Show Access Forbidden page to the use */

                                $status = 403;
                                $msg = 'Unauthoried access';
                            }
                        } else {

                            $status = 200;
                            $msg = 'valid user';
                        }

                        /** Redirect to complete profile page */

                        if ($site_name == '' && $site_icon == '' && ACTIVE_URL != '/dashboard/setting/?type=configuration') {

                            $status = 204;
                            $msg = 'Profile incomplete';
                            $redirect = '/dashboard/setting/?type=configuration';
                        }


                        /** Set login is valid */

                        setcookie('isLogin', '1', $uiOpts);
                    }
                }
            } else {

                $status = 404;
                $msg = 'No user found';
            }
        } else {

            $status = 502;
            $msg = 'something is wrong while checking user session';
        }
    } else {

        $status = 404;
        $msg = 'No token found';
    }
} else {

    $msg = 'GET method is not support with API';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'domain_list' => ((isset($checkDomain['domains'])) ? $checkDomain['domains'] : '')
);

echo json_encode($result, TRUE);
