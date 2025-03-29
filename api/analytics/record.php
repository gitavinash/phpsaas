<?php

function record($connection, $data)
{

    extract($data);

    // track traffic resource

    $trafficSource = ((isset($_GET['utm_source']) ? $_GET['utm_source'] : ((isset($_GET['fbclid'])) ? 'facebook' : ((isset($_GET['gad_source'])) ? 'Google Adwords' : (isset($_GET['gclid']) ? 'Google Adwords' : ((isset($_GET['gad_source']) ? 'Google Adwords' : ((isset($_GET['wbraid'])) ? 'Google Adwords' : $trafficDomain = parse_url($_SERVER['HTTP_REFERER'])))))))));
    $trafficSource = mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host'] != SITE_DOMAIN) ? $trafficDomain['host'] : '') : $trafficSource));

    $trafficMedium = mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource == 'facebook') ? 'social' : ''))));
    $trafficCampaign = mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '')));

    // Start MySQL transaction

    mysqli_query($connection, "START TRANSACTION");

    if (isset($_COOKIE['user_data'])) {
        $user_salt_key = base64_decode($_COOKIE['user_data']);
        if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}'")) {
            if (mysqli_num_rows($checkUserIsReal) != 0) {
                extract(mysqli_fetch_assoc($checkUserIsReal));
            }
        }
    } else {
        $user_id = 0;
    }

    // Check IP Location data is available or not

    if ($checkIPData = mysqli_query($connection, "SELECT ip_id FROM " . TABLE_PREFIX . "ip WHERE ip_address='" . IP . "'")) {

        if (mysqli_num_rows($checkIPData) == 0) {

            // Fetch IP location data from api

            $fetchIPData = file_get_contents("http://ip-api.com/json/" . ((IP=='127.0.0.1') ? '110.235.235.20' : IP ) . "?fields=status,message,continent,country,regionName,city,zip,lat,lon,isp,org");
            
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

    $recordAnalytics = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "analytics(analytics_site_id, analytics_user_id, analytics_link_url, analytics_link_original, analytics_session_id, analytics_site_version, analytics_ip, analytics_os, analytics_browser, analytics_source, analytics_medium, analytics_campaign) VALUE(" . SITE_ID . ", '{$user_id}', '" . ACTIVE_URL_NON_QUERY . "', '" . ACTIVE_URL . "', '" . SESSION_ID . "', '" . SITE_VERSION . "', '" . IP . "', '" . DEVICE_OS . "', '" . DEVICE_BROWSER . "', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}')");
    $analyticsId=mysqli_insert_id($connection);

    if (!isset($_COOKIE['device_id'])) {
        $deviceID = bin2hex(random_bytes(16)); // Generate a random unique ID
        setcookie('device_id', $deviceID, time() + (86400 * 365), "/"); // 1-year expiry
    } else {
        $deviceID = $_COOKIE['device_id'];
    }

    
    if($checkUserSession=mysqli_query($connection, "SELECT us_id FROM ".TABLE_PREFIX."user_sessions WHERE us_active_device='{$deviceID}' AND us_status=1 AND us_user_id='{$user_id}' AND us_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkUserSession)==0) {
            $recordUserSession=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user_sessions(us_site_id, us_user_id, us_analytics_id, us_active_device, us_status) VALUES('".SITE_ID."', '{$user_id}', '{$analyticsId}', '{$deviceID}', 1)");
        } else {
            $recordUserSession=true;
        }
    } else {
        echo "qujsdf";
        exit();
    }
    
    
    if ($recordIPData && $recordAnalytics && $recordUserSession) {
            $msg = 200;
            $msg = 'Capture';
            $status='success';
            mysqli_query($connection, "COMMIT");
    } else {
        // echo "Something went wrong while capturing the analytics records";
        echo $recordIPData.' && '.$recordAnalytics.' && '.$recordUserSession;
        $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection): "Analytics not caputre");
        mysqli_query($connection, "ROLLBACK");
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : '')
    );

    echo json_encode($result, true);
}