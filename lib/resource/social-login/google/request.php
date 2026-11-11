<?php
$ssoModuleFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 3) . '/modules/sso.php';
if (file_exists($ssoModuleFile)) {
    require_once $ssoModuleFile;
}

if (!defined('ENABLE_GOOGLE_AUTH')) {
    // Update the following variables
    $google_oauth_client_id = DEFAULT_GOOGLE_AUTH_CLIENT_ID;
    $google_oauth_client_secret = DEFAULT_GOOGLE_AUTH_CLIENT_SECRET;
} else {
    // Update the following variables
    $google_oauth_client_id = GOOGLE_AUTH_CLIENT_ID;
    $google_oauth_client_secret = GOOGLE_AUTH_CLIENT_SECRET;
}

$defaultGoogleRedirect = (defined('SITE_APP_ID') && (int) SITE_APP_ID === 3) ? '/manage-subscription/' : '/dashboard/';
$redirectUrl = iswNormalizeRedirectPath($redirect_url ?? $defaultGoogleRedirect, $defaultGoogleRedirect);

if (DEVELOPMENT_MODE == true) {
    $google_oauth_redirect_uri = 'http://localhost/oauth/google/';
} else {
    $google_oauth_redirect_uri = 'https://' . MAIN_DOMAIN . '/oauth/google/';
}

$google_oauth_version = 'v3';

// If the captured code param exists and is valid
if (isset($_GET['code']) && !empty($_GET['code'])) {
    // Execute cURL request to retrieve the access token
    $params = [
        'code' => $_GET['code'],
        'client_id' => $google_oauth_client_id,
        'client_secret' => $google_oauth_client_secret,
        'redirect_uri' => $google_oauth_redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    // Make sure access token is valid
    if (isset($response['access_token']) && !empty($response['access_token'])) {
        // Execute cURL request to retrieve the user info associated with the Google account
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $google_oauth_version . '/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);
        // Make sure the profile data exists
        if (isset($profile['email'])) {
            $google_name_parts = [];
            $first_name = isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['given_name']) : '';
            $last_name = isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['family_name']) : '';
            // Authenticate the user
            session_regenerate_id();
            $_SESSION['google_loggedin'] = TRUE;
            $_SESSION['google_email'] = $profile['email'];
            $_SESSION['google_name'] = $first_name . ' ' . $last_name;
            $_SESSION['google_picture'] = isset($profile['picture']) ? $profile['picture'] : '';
            // Redirect to profile page

            // Check user is exist in database
            $safeGoogleEmail = mysqli_real_escape_string($connection, trim((string) $_SESSION['google_email']));
            $safeGoogleName  = mysqli_real_escape_string($connection, trim((string) $_SESSION['google_name']));
            $safeGoogleFirst = mysqli_real_escape_string($connection, trim((string) $first_name));
            $safeGoogleLast  = mysqli_real_escape_string($connection, trim((string) $last_name));
            $safeGooglePic   = mysqli_real_escape_string($connection, trim((string) $_SESSION['google_picture']));

            if ($checkSiteAccess = mysqli_query($connection, "SELECT user_id, user_mobile, user_password, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_meta, site_access_status FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id=site_access_site_id INNER JOIN " . TABLE_PREFIX . "user_type ON user_type_code=site_access_type AND site_access_site_id=" . SITE_ID . " WHERE user_email='{$safeGoogleEmail}' LIMIT 1")) {
                if (mysqli_num_rows($checkSiteAccess) == 0) {
                    // Check user site access
                    $isPublicNewsApp = (defined('SITE_APP_ID') && (int) SITE_APP_ID === 3);
                    if ($checkUser = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user WHERE user_email='{$safeGoogleEmail}' LIMIT 1")) {
                        if (mysqli_num_rows($checkUser) == 0) {
                            if (!$isPublicNewsApp) {
                                $redirectUrl = '/login/?sso_done=1&alertMsg=' . urlencode('You do not have access to this workspace.');
                            } else {
                                // Create new subscriber profile on public storefront/news app only
                                $username = strtolower(str_replace(' ', '', $_SESSION['google_name']));
                                $safeUsername = mysqli_real_escape_string($connection, $username);

                                /** Generate automatic password of 8 chatracter */
                                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789!@#$%&()_+=-<>/?'), 0, 8);

                                /** Generating salt key of the user */
                                $saltKey = hash('sha256', uniqid(mt_rand(), true) . $_SESSION['google_email'] . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                                $dbPassword = hash('sha256', $saltKey . $password);

                                if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$safeUsername}'")) {
                                    if (mysqli_num_rows($checkUsername) != 0) {
                                        $username = strtolower($first_name) . time();
                                        $safeUsername = mysqli_real_escape_string($connection, $username);
                                    }
                                }

                                $mobile = time();

                                /** Start mysql transaction */

                                mysqli_query($connection, "START TRANSACTION");

                                $addUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES(0, '{$safeUsername}', '{$safeGoogleEmail}', '{$mobile}', '{$dbPassword}', '{$saltKey}', 1, NOW(), '" . IP . "')");

                                $userId = (int) mysqli_insert_id($connection);

                                $createProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_image, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$userId}', '{$safeGooglePic}', '{$safeGoogleFirst}', '{$safeGoogleLast}', '{$safeGoogleName}', '{$mobile}', '{$safeGoogleEmail}')");

                                $userSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$userId}', 'subscriber', 1)");

                                if ($addUser && $createProfile && $userSiteAccess) {

                                    /** Add all data into database */
                                    mysqli_query($connection, 'COMMIT');

                                    ob_start();
                                    $_COOKIE['user_data'] = base64_encode($saltKey);
                                    require_once API_MODULES . 'analytics/session-record.php';
                                    $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                                    $resolved = iswResolveSiteAccessForUser($connection, $userId, (int) SITE_ID);
                                    if ($resolved && !empty($resolved['has_access'])) {
                                        iswSyncGroupUserSessions($connection, $userId, $activeDeviceId, (int) SITE_ID, (int) ($analyticsId ?? 0));
                                        iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
                                    }
                                    ob_end_flush();

                                    $redirectUrl = iswNormalizeRedirectPath($_SESSION['redirect_after_login'] ?? $redirectUrl, $defaultGoogleRedirect);
                                } else {
                                    /** Rollback mysql query if query is fail */
                                    mysqli_query($connection, 'ROLLBACK');
                                    $redirectUrl = '/login/';
                                }
                            }
                        } else {
                            extract(mysqli_fetch_assoc($checkUser));
                            $existingUid = (int) $user_id;
                            $resolved = iswResolveSiteAccessForUser($connection, $existingUid, (int) SITE_ID);

                            // Only public news/subscription app allows self-registering an existing unlinked user (never re-enabling disabled rows)
                            if (($resolved && ($resolved['reason'] ?? '') === 'no_access') && $isPublicNewsApp) {
                                if (!mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('" . SITE_ID . "', '{$existingUid}', 'subscriber', 1)")) {
                                    echo "Something went wrong while trying to create site access";
                                    exit();
                                }
                                $resolved = iswResolveSiteAccessForUser($connection, $existingUid, (int) SITE_ID);
                            }

                            if (!$resolved || empty($resolved['has_access'])) {
                                $denyMsg = (($resolved['reason'] ?? '') === 'disabled')
                                    ? 'Your workspace access has been deactivated.'
                                    : 'You do not have access to this workspace.';
                                $redirectUrl = '/login/?sso_done=1&alertMsg=' . urlencode($denyMsg);
                            } elseif ((int) $user_status !== 1 && (int) SITE_APP_ID !== 3) {
                                $redirectUrl = '/verify-account/?email=' . urlencode(base64_encode((string) $_SESSION['google_email']));
                            } else {
                                ob_start();
                                $_COOKIE['user_data'] = base64_encode((string) ($resolved['user']['user_salt_key'] ?? $user_salt_key));
                                require_once API_MODULES . 'analytics/session-record.php';
                                $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                                iswSyncGroupUserSessions($connection, $existingUid, $activeDeviceId, (int) SITE_ID, (int) ($analyticsId ?? 0));
                                iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
                                ob_end_flush();

                                $redirectUrl = iswNormalizeRedirectPath($_SESSION['redirect_after_login'] ?? $redirectUrl, $defaultGoogleRedirect);
                            }
                        }
                    }
                } else {

                    extract(mysqli_fetch_assoc($checkSiteAccess));
                    $existingUid = (int) $user_id;

                    if ((int) $site_access_status !== 1) {
                        $redirectUrl = '/login/?sso_done=1&alertMsg=' . urlencode('Your workspace access has been deactivated.');
                    } elseif ((int) $user_status !== 1 && (int) $site_app_id !== 3) {
                        $redirectUrl = '/verify-account/?email=' . urlencode(base64_encode((string) $_SESSION['google_email']));
                    } else {

                        ob_start();
                        $_COOKIE['user_data'] = base64_encode($user_salt_key);
                        require_once API_MODULES . 'analytics/session-record.php';

                        $activeDeviceId = trim((string) ($_COOKIE['device_id'] ?? ''));
                        $resolved = iswResolveSiteAccessForUser($connection, $existingUid, (int) SITE_ID);
                        if ($resolved && !empty($resolved['has_access'])) {
                            iswSyncGroupUserSessions($connection, $existingUid, $activeDeviceId, (int) SITE_ID, (int) ($analyticsId ?? 0));
                            iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
                        }
                        ob_end_flush();

                        $status = 'success';
                        $msg = 'Login Successfully';
                        $redirectUrl = iswNormalizeRedirectPath($_SESSION['redirect_after_login'] ?? $redirectUrl, $defaultGoogleRedirect);
                    }
                }
            }

            // echo $redirectUrl;

            if (SITE_APP_ID == 3 && !empty($_SESSION['redirect_after_login'])) {
                $redirectUrl = iswNormalizeRedirectPath($_SESSION['redirect_after_login'], '/manage-subscription/');
            } else {
                $redirectUrl = iswNormalizeRedirectPath($redirectUrl, $defaultGoogleRedirect);
            }

            if (isset($_SESSION['base_url'])) { ?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
                    <title>Redirect to new url.</title>
                    <meta name="robots" content="noindex,nofollow">
                    <script>
                        window.onload = function() {
                            document.getElementById("autoSubmitForm").submit();
                        };
                    </script>
                </head>

                <body>
                    <form id="autoSubmitForm" action="<?php echo htmlspecialchars((string) $_SESSION['base_url'], ENT_QUOTES, 'UTF-8'); ?>" method="<?php echo htmlspecialchars((string) ($_SESSION['method'] ?? 'POST'), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($_SESSION['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="redirect_url"
                            value="<?php echo htmlspecialchars(iswNormalizeRedirectPath($_SESSION['redirect_after_login'] ?? '/', '/'), ENT_QUOTES, 'UTF-8'); ?>">
                        <noscript>
                            <p>JavaScript is disabled. Please click the submit button.</p>
                            <button type="submit">Submit</button>
                        </noscript>
                    </form>
                </body>

                </html>

<?php

            } else {
                header("Location: {$redirectUrl}", TRUE, 302);
            }
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
    // Initialize the session
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['redirect_after_login'] = iswNormalizeRedirectPath($redirect_url ?? '/manage-subscription/', '/manage-subscription/');


    if (isset($baseUrl)) {
        $_SESSION['base_url'] = isset($baseUrl) ? $baseUrl : '';
        $_SESSION['method'] = isset($method) ? $method : '';
        $_SESSION['id'] = isset($id) ? $id : '';
    }

    // Define params and redirect to Google Authentication page
    $params = [
        'response_type' => 'code',
        'client_id' => $google_oauth_client_id,
        'redirect_uri' => $google_oauth_redirect_uri,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit;
}
