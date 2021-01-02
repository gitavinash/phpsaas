<?php

    function record($connection, $data) {

        extract($data);

        // track traffic resource

        $trafficSource=((isset($_GET['utm_source']) ? $_GET['utm_source'] : $trafficDomain=parse_url($_SERVER['HTTP_REFERER']) ));
        $trafficSource=mysqli_real_escape_string($connection, ((isset($trafficDomain['host'])) ? (($trafficDomain['host']!=SITE_DOMAIN) ? $trafficDomain['host'] : '' ) : $trafficSource ));

        $trafficMedium=mysqli_real_escape_string($connection, ((isset($_GET['utm_medium']) ? $_GET['utm_medium'] : '' )));
        $trafficCampaign=mysqli_real_escape_string($connection, ((isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : '' )));
        
        if($recordAnalytics=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."analytics(analytics_site_id, analytics_link_url, analytics_session_id, analytics_site_version, analytics_ip, analytics_os, analytics_source, analytics_medium, analytics_campaign) VALUE(".SITE_ID.", '".ACTIVE_URL_NON_QUERY."', '".SESSION_ID."', '".SITE_VERSION."', '".IP."', '".DEVICE_OS."', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}')")) {

            $msg=200;
            $msg='Capture';

        } else {

            $msg='Not capture';

        }

        $result = array(
			'status' => ((isset($status)) ? $status : 'error'),
			'msg' => ((isset($msg)) ? $msg : '')
		);

        echo json_encode($result, true);

    }