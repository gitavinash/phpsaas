<?php

    if($selectDevice=mysqli_query($connection, "SELECT device_token FROM ".TABLE_PREFIX."device WHERE device_site_id=".SITE_ID." AND device_status=1")) {

        if(mysqli_num_rows($selectDevice)!=0) {

            $deviceToken=array();

            while($deviceList=mysqli_fetch_assoc($selectDevice)) {

                $deviceToken[]=$deviceList['device_token'];

            }

            /** Send Push Notification to devices */

            $url = "https://fcm.googleapis.com/fcm/send";
            $token = implode(',', $deviceToken);
            $serverKey = 'AAAAjm__7wg:APA91bE09Yzo6K0GidEpDzpkbTRdWpkAcjXSwi3mL7gasl4ZMi0tkpBUhFzSFdBORh-SWDsgWo-OU7xO3epzU7b85zBSku8LIQWfnjJkUTK-ngp-WRjJIE9sUdd4yjf1cjwlkRFwJkns';
            $title = "Notification title";
            $body = "Hello I am from Your php server";
            $notification = array('title' =>$title , 'body' => $body, 'icon' => 'https://isw.one/upload/2020/03/1585061746_its_soft_world_logo.png', 'sound' => 'default', 'badge' => '1');
            $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
            $json = json_encode($arrayToSend);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);

        }

    }