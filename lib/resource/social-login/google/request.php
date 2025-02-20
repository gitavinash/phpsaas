<?php

session_destroy();

if (!defined('ENABLE_GOOGLE_AUTH')) {
    echo "<h2>Google oAuth is disable for this domain: " . SITE_URL . "</h2>";
    exit();
}

// Initialize the session
session_start();
$redirectUrl = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');
// Update the following variables
$google_oauth_client_id = GOOGLE_AUTH_CLIENT_ID;
$google_oauth_client_secret = GOOGLE_AUTH_CLIENT_SECRET;

if (DEVELOPMENT_MODE == true) {
    $google_oauth_redirect_uri =
        'http://localhost/oauth/google/?redirect_url=' . $redirectUrl;
} else {
    $google_oauth_redirect_uri = SITE_URL . '/oauth/google/?redirect_url=' . $redirectUrl;
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

            if ($checkSiteAccess = mysqli_query($connection, "SELECT user_id, user_password, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_meta, site_access_status FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id=site_access_site_id INNER JOIN " . TABLE_PREFIX . "user_type ON user_type_code=site_access_type AND site_access_site_id=" . SITE_ID . " WHERE user_email='{$_SESSION['google_email']}' LIMIT 1")) {
                if (mysqli_num_rows($checkSiteAccess) == 0) {
                    // Check user site access
                    if ($checkUser = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user WHERE user_email='{$_SESSION['google_email']}'")) {
                        if (mysqli_num_rows($checkUser) == 0) {
                            // Create new user profile

                            $username = strtolower(str_replace(' ', '', $_SESSION['google_name']));

                            /** Generate automatic password of 8 chatracter */
                            $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789!@#$%&()_+=-<>/?'), 0, 8);

                            /** Generating salt key of the user */
                            $saltKey = hash('sha256', uniqid(mt_rand(), true) . $_SESSION['google_email'] . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                            $dbPassword = hash('sha256', $saltKey . $password);

                            if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                                if (mysqli_num_rows($checkUsername) != 0) {
                                    $username = strtolower($first_name) . time();
                                }
                            }

                            $mobile = time();

                            /** Start mysql transaction */

                            mysqli_query($connection, "START TRANSACTION");

                            $addUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES(0, '{$username}', '{$_SESSION['google_email']}', '{$mobile}', '{$dbPassword}', '{$saltKey}', 1, NOW(), '" . IP . "')");

                            $userId = mysqli_insert_id($connection);

                            $createProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_image, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$userId}', '{$_SESSION['google_picture']}', '{$first_name}', '{$last_name}', '{$_SESSION['google_name']}', '{$mobile}', '{$_SESSION['google_email']}')");

                            $userSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$userId}', 'subscriber', 1)");

                            if ($addUser && $createProfile && $userSiteAccess) {

                                /** Add all data into database */
                                mysqli_query($connection, 'COMMIT');

                                ob_start();

                                setcookie('user_name', base64_encode($_SESSION['google_name']), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('user_email', base64_encode($_SESSION['google_email']), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('user_image', base64_encode($_SESSION['google_picture']), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('user_data', base64_encode($saltKey), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('user_meta', '', time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('user_type', base64_encode('subscriber'), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                                setcookie('site_app', base64_encode(SITE_APP), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);

                                ob_end_flush();
                            } else {
                                echo $addUser . " && " . $createProfile . " && " . $userSiteAccess;
                                /** Rollback mysql query if query is fail */
                                mysqli_query($connection, 'ROLLBACK');
                            }
                            $redirectUrl = '/login/';
                        } else {
                            extract(mysqli_fetch_assoc($checkUser));
                            // define site access
                            if (!mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('" . SITE_ID . "', '{$user_id}', 'subscriber', 1)")) {
                                echo "Something went wrong while trying to create site access";
                                exit();
                            }
                            $redirectUrl = '/login/';
                        }
                    }
                } else {

                    extract(mysqli_fetch_assoc($checkSiteAccess));

                    if ($site_access_status == 1 && $user_status == 1) {

                        ob_start();

                        setcookie('user_name', base64_encode($profile_display_name), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('user_email', base64_encode($email), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('user_image', base64_encode($profile_image), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('user_data', base64_encode($user_salt_key), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('user_meta', base64_encode($site_access_meta), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('user_type', base64_encode($user_type), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
                        setcookie('site_app', base64_encode(SITE_APP), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);

                        ob_end_flush();
                        $status = 'success';
                        $msg = 'Login Successfully';
                        $redirectUrl = urldecode('/dashboard/');
                    }
                }
            }

            // echo $redirectUrl;
            header("Location: {$redirectUrl}", TRUE, 301);
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
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
