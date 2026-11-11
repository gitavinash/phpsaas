<?php

require_once UTIL_FUNCTIONS;
$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$user_id = 0;
$analyticsId = 0;

// track traffic resource

// Default traffic source
$trafficSource = '';

// Priority-based detection
if (isset($_GET['utm_source'])) {
    $trafficSource = $_GET['utm_source'];
} elseif (isset($_GET['fbclid'])) {
    $trafficSource = 'facebook';
} elseif (isset($_GET['gad_source']) || isset($_GET['gclid']) || isset($_GET['wbraid'])) {
    $trafficSource = 'Google Adwords';
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    $trafficDomain = parse_url($referer);
    if (isset($trafficDomain['host']) && $trafficDomain['host'] !== SITE_DOMAIN) {
        $trafficSource = $trafficDomain['host'];
    }
}

// Sanitize for database
$trafficSource = mysqli_real_escape_string($connection, $trafficSource);

$trafficMedium = mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource == 'facebook') ? 'social' : ''))));
$trafficCampaign = mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '')));

// Start MySQL transaction

mysqli_query($connection, "START TRANSACTION");

$user_id = 0;
if (isset($_COOKIE['user_data'])) {
    $rawCookieUserData = trim((string) $_COOKIE['user_data']);
    $user_salt_key = base64_decode($rawCookieUserData, true);
    if ($user_salt_key === false || $user_salt_key === '') {
        $user_salt_key = base64_decode($rawCookieUserData);
    }
    $user_salt_key = mysqli_real_escape_string($connection, trim((string) $user_salt_key));
    if ($user_salt_key !== '' && ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}' LIMIT 1"))) {
        if (mysqli_num_rows($checkUserIsReal) != 0) {
            $candUserRow = mysqli_fetch_assoc($checkUserIsReal);
            $candUserId = (int) ($candUserRow['user_id'] ?? 0);
            if ($candUserId > 0 && function_exists('iswResolveSiteAccessForUser')) {
                $resolvedAccess = iswResolveSiteAccessForUser($connection, $candUserId, (int) SITE_ID);
                $candUserStatus = (int) ($resolvedAccess['user']['user_status'] ?? 0);
                $candSiteAppId = (int) ($resolvedAccess['site']['site_app_id'] ?? 0);
                if ($resolvedAccess && !empty($resolvedAccess['has_access']) && ($candUserStatus === 1 || $candSiteAppId === 3)) {
                    $user_id = $candUserId;
                }
            }
        }
    }
}

// Check IP Location data is available or not

if (DEVELOPMENT_MODE == false) {
    if ($checkIPData = mysqli_query($connection, "SELECT ip_id FROM " . TABLE_PREFIX . "ip WHERE ip_address='" . IP . "'")) {

        if (mysqli_num_rows($checkIPData) == 0) {

            // Fetch IP location data from api

            $fetchIPData = file_get_contents("http://ip-api.com/json/" . ((IP == '127.0.0.1') ? '110.235.235.20' : IP) . "?fields=status,message,continent,country,regionName,city,zip,lat,lon,isp,org");

            extract(json_decode($fetchIPData, TRUE));

            // Add IP location data into IP table

            if ($status == 'success') {

                $recordIPData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "ip(ip_address, ip_continent, ip_country, ip_state, ip_city, ip_postal, ip_latitude, ip_longitude, ip_isp, ip_company) VALUES('" . IP . "', '{$continent}', '{$country}', '{$regionName}', '{$city}', '{$zip}', '{$lat}', '{$lon}', '{$isp}', '{$org}')");
            } else {

                $msg = 'Something is wrong while trying to fetch IP details';
                $recordIPData = false;
            }
        } else {

            // IP location data is already exist into IP table

            $recordIPData = true;
        }
    }
} else {
    $recordIPData = true;
}

$recordAnalytics = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "analytics(analytics_site_id, analytics_user_id, analytics_link_url, analytics_link_original, analytics_session_id, analytics_site_version, analytics_ip, analytics_os, analytics_browser, analytics_source, analytics_medium, analytics_campaign) VALUE(" . SITE_ID . ", '{$user_id}', '" . ACTIVE_URL_NON_QUERY . "', '" . ACTIVE_URL . "', '" . SESSION_ID . "', '" . SITE_VERSION . "', '" . IP . "', '" . DEVICE_OS . "', '" . DEVICE_BROWSER . "', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}')");
$analyticsId = mysqli_insert_id($connection);

if ($user_id != 0) {
    $cookieExpiry = time() + (30 * 24 * 60 * 60);
    if (function_exists('iswBuildCookieOptions')) {
        $cookieOptions = iswBuildCookieOptions($cookieExpiry, true);
    } else {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
        $cleanMain = preg_replace('/:\d+$/', '', (string) (defined('MAIN_DOMAIN') ? MAIN_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '')));
        $dom = ($cleanMain !== '' && $cleanMain !== 'localhost' && !filter_var($cleanMain, FILTER_VALIDATE_IP) && strpos($cleanMain, '.') !== false)
            ? '.' . ltrim($cleanMain, '.')
            : '';
        $cookieOptions = [
            'expires'  => $cookieExpiry,
            'path'     => '/',
            'domain'   => $dom,
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ];
    }

    if (!isset($_COOKIE['device_id']) || trim((string) $_COOKIE['device_id']) === '') {
        $deviceID = bin2hex(random_bytes(16));
        if (!headers_sent()) {
            if (!empty($cookieOptions['domain'])) {
                $pastOpts = $cookieOptions;
                $pastOpts['expires'] = time() - 3600;
                $pastOpts['domain'] = '';
                @setcookie('device_id', '', $pastOpts);
            }
            @setcookie('device_id', $deviceID, $cookieOptions);
        }
        $_COOKIE['device_id'] = $deviceID;
    } else {
        $deviceID = trim((string) $_COOKIE['device_id']);
    }

    $escDeviceID = mysqli_real_escape_string($connection, $deviceID);
    $uidInt = (int) $user_id;
    $sidInt = (int) SITE_ID;
    $aidInt = (int) $analyticsId;

    $recordUserSession = false;
    if ($checkUserSession = mysqli_query(
        $connection,
        "SELECT us_id FROM " . TABLE_PREFIX . "user_sessions
         WHERE us_active_device='{$escDeviceID}' AND us_user_id='{$uidInt}' AND us_site_id={$sidInt}
         ORDER BY us_id DESC LIMIT 1"
    )) {
        if (mysqli_num_rows($checkUserSession) == 0) {
            $recordUserSession = mysqli_query(
                $connection,
                "INSERT INTO " . TABLE_PREFIX . "user_sessions
                 (us_site_id, us_user_id, us_analytics_id, us_active_device, us_status, us_active_time)
                 VALUES('{$sidInt}', '{$uidInt}', '{$aidInt}', '{$escDeviceID}', 1, NOW())"
            );
        } else {
            $existingUsRow = mysqli_fetch_assoc($checkUserSession);
            $usIdInt = (int) $existingUsRow['us_id'];
            $recordUserSession = mysqli_query(
                $connection,
                "UPDATE " . TABLE_PREFIX . "user_sessions
                 SET us_status=1, us_active_time=NOW()
                 WHERE us_id={$usIdInt}"
            );
        }
    }
} else {
    $recordUserSession = true;
}

if ($recordIPData && $recordAnalytics && $recordUserSession) {
    $msg = 200;
    $msg = 'Capture';
    $status = 'success';
    mysqli_query($connection, "COMMIT");
} else {
    echo "Something went wrong while capturing the session analytics records";
    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : "Analytics not caputre");
    mysqli_query($connection, "ROLLBACK");
}

$sessionResult = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : '')
);