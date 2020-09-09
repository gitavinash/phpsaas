<?php

    function record($connection, $data) {

        extract($data);

        if($recordAnalytics=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."analytics(analytics_site_id, analytics_link_url, analytics_session_id, analytics_site_version, analytics_ip) VALUE(".SITE_ID.", '{$url}', '".SESSION_ID."', '".SITE_VERSION."', '".IP."')")) {

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