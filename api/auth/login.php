<?php

/* login module */

function login($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $identifierRaw = isset($email) ? trim((string) $email) : '';
    $passRaw = isset($pass) ? (string) $pass : '';
    $escIdentifier = mysqli_real_escape_string($connection, $identifierRaw);
    $siteIdInt = (int) SITE_ID;
    $saSiteFilter = in_array($siteIdInt, [49, 234], true)
        ? "sa.site_access_site_id IN (49, 234)"
        : "sa.site_access_site_id = {$siteIdInt}";

    $loginUserSql = "SELECT u.user_id, u.user_email, u.user_mobile, u.user_password, u.user_salt_key, u.user_status
                     FROM " . TABLE_PREFIX . "user u
                     LEFT JOIN " . TABLE_PREFIX . "site_access sa
                            ON sa.site_access_user_id = u.user_id
                           AND {$saSiteFilter}
                     WHERE (u.user_email='{$escIdentifier}' OR u.user_mobile='{$escIdentifier}')
                     ORDER BY (sa.site_access_status = 1) DESC,
                              (sa.site_access_site_id = {$siteIdInt}) DESC,
                              (sa.site_access_id IS NOT NULL) DESC,
                              (u.user_status = 1) DESC,
                              u.user_id ASC
                     LIMIT 5";

    if ($checkUser = mysqli_query($connection, $loginUserSql)) {

        if (mysqli_num_rows($checkUser) != 0) {

            $candidateRows = [];
            while ($uRow = mysqli_fetch_assoc($checkUser)) {
                $candidateRows[] = $uRow;
            }

            $matchedRow = null;
            foreach ($candidateRows as $uRow) {
                if ($uRow['user_password'] == hash('sha256', $uRow['user_salt_key'] . $passRaw)) {
                    $matchedRow = $uRow;
                    $candResolved = function_exists('iswResolveSiteAccessForUser')
                        ? iswResolveSiteAccessForUser($connection, (int) $uRow['user_id'], $siteIdInt)
                        : null;
                    if ($candResolved && !empty($candResolved['has_access'])) {
                        break;
                    }
                }
            }

            $user_type = '';

            if ($matchedRow !== null) {
                extract($matchedRow);

                $resolved = function_exists('iswResolveSiteAccessForUser')
                    ? iswResolveSiteAccessForUser($connection, (int) $user_id, $siteIdInt)
                    : null;

                $hasSiteAccess = ($resolved && !empty($resolved['has_access']));
                if ($hasSiteAccess) {
                    $user_type = (string) ($resolved['access']['site_access_type'] ?? '');
                }

                /** User has access of this site */

                if ($hasSiteAccess && (int) $user_status === 1) {

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

                    $status = 'success';
                    $msg = 'Login Successfully';
                    $defaultRedirect = '/dashboard/';
                    if (function_exists('iswNormalizeRedirectPath')) {
                        $url = iswNormalizeRedirectPath($redirect_url ?? $defaultRedirect, $defaultRedirect);
                    } else {
                        $url = urldecode((isset($redirect_url)) ? $redirect_url : $defaultRedirect);
                    }
                } elseif (!$hasSiteAccess) {

                    $status = 'error';
                    $msg = ($resolved && ($resolved['reason'] ?? '') === 'disabled')
                        ? 'Your workspace access has been deactivated.'
                        : 'You do not have access to this workspace.';
                } else {

                    if ((int) $user_status === 0) {

                        /** User verification url using the user salt key */

                        $verifyURL = SITE_DOMAIN . '/verify/email/?user_id=' . $user_salt_key . '&email=' . base64_encode($user_email);

                        require_once EMAIL_TEMPLATE . 'email-verification.php';

                        $msg = 'Please verify your account';
                    } else {

                        $msg = 'Something is wrong with user profile';
                    }
                }
            } else {

                $status = 'error';
                $msg = 'Invalid Password';
            }

            if ($user_type == 'travel_agent' && SITE_ID == 50) {
                $url = '/our-hotels/';
                $hardRedirectURL = $url;
            } else {
                $hardRedirectURL = ((isset($status) && $status == 'success') ? $url : '/login/?sso_done=1&alertMsg=' . urlencode($msg));
            }
        } else {
            $status = 'error';
            $msg = 'No user found';
            $hardRedirectURL = '/login/?sso_done=1&alertMsg=' . urlencode($msg);
        }
    } else {
        $status = 'error';
        $msg = 'Technical error';
        $hardRedirectURL = '/login/?sso_done=1&alertMsg=' . urlencode($msg);
    }

    $return = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid Username'),
        'url' => ((isset($url)) ? $url : '#'),
    );

    if (isset($hardRedirect)) {
        if (isset($baseUrl)) {
            header("Content-Type: text/html; charset=UTF-8");

?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
                <title>Redirect to new url.</title>
                <script>
                    window.onload = function() {
                        console.log("Form submission initiated."); // Debugging log
                        document.getElementById("autoSubmitForm").submit();
                    };
                </script>
            </head>

            <body>
                <form id="autoSubmitForm" action="<?php echo htmlspecialchars((string) $baseUrl, ENT_QUOTES, 'UTF-8'); ?>" method="<?php echo htmlspecialchars((string) ($method ?? 'POST'), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($id ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars((string) ($hardRedirectURL ?? '/dashboard/'), ENT_QUOTES, 'UTF-8'); ?>">
                    <noscript>
                        <p>JavaScript is disabled. Please click the submit button.</p>
                        <button type="submit">Submit</button>
                    </noscript>
                </form>
            </body>

            </html>

<?php

        } else {
            header("Location: " . ($hardRedirectURL ?? '/login/'), true, 302);
        }
    } else {
        echo json_encode($return, true);
    }
    exit();
}
