<?php

    function record($connection, $data) {

        extract($data);

        // track traffic resource

        $trafficSource=((isset($_GET['utm_source']) ? $_GET['utm_source'] : ((isset($_GET['fbclid'])) ? 'facebook' : $trafficDomain=parse_url($_SERVER['HTTP_REFERER'])) ));
        $trafficSource=mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host']!=SITE_DOMAIN) ? $trafficDomain['host'] : '' ) : $trafficSource ));

        $trafficMedium=mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : (($trafficSource=='facebook') ? 'social' : '' ) )));
        $trafficCampaign=mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '' )));

        // Start MySQL transaction

        mysqli_query($connection, "START TRANSACTION");

        // Check IP Location data is available or not

        if($checkIPData=mysqli_query($connection, "SELECT ip_id FROM ".TABLE_PREFIX."ip WHERE ip_address='".IP."'")) {

            if(mysqli_num_rows($checkIPData)==0) {

                // Fetch IP location data from api
                
                $fetchIPData=file_get_contents("https://geolocation-db.com/json/".GEOLOCATION_API."/".IP);
                extract(json_decode($fetchIPData, TRUE));

                // Add IP location data into IP table

                $recordIPData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."ip(ip_address, ip_country, ip_state, ip_city, ip_postal, ip_latitude, ip_longitude) VALUES('".IP."', '{$country_name}', '{$state}', '{$city}', '{$postal}', '{$latitude}', '{$longitude}')");

            } else {

                // IP location data is already exist into IP table

                $recordIPData=true;

            }

        }

        $recordAnalytics=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."analytics(analytics_site_id, analytics_link_url, analytics_link_original, analytics_session_id, analytics_site_version, analytics_ip, analytics_os, analytics_source, analytics_medium, analytics_campaign) VALUE(".SITE_ID.", '".ACTIVE_URL_NON_QUERY."', '".ACTIVE_URL."', '".SESSION_ID."', '".SITE_VERSION."', '".IP."', '".DEVICE_OS."', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}')");

        if($recordIPData && $recordAnalytics) {

            $msg=200;
            $msg='Capture';
            mysqli_query($connection, "COMMIT");

        } else {

            $msg='Not capture';
            mysqli_query($connection, "ROLLBACK");

        }

        $result = array(
			'status' => ((isset($status)) ? $status : 'error'),
			'msg' => ((isset($msg)) ? $msg : '')
		);

        echo json_encode($result, true);

    }