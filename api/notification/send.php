<?php

if ($selectDevice = mysqli_query($connection, "SELECT device_token FROM " . TABLE_PREFIX . "device WHERE device_site_id=" . SITE_ID . " AND device_user_id='{$userId}' AND device_status=1")) {

    if (mysqli_num_rows($selectDevice) != 0) {

        $deviceToken = array();

        while ($deviceList = mysqli_fetch_assoc($selectDevice)) {
            $deviceToken[] = $deviceList['device_token'];
        }

        $deviceList = json_encode($deviceToken);

        /** Send Push Notification to devices */

        $serverKey = NOTIFICATION_KEY;

        // Notification payload
        $data = [
            "registration_ids" => [
                "clixBUHskt77vBkweXP3yd:APA91bGzHA7mNgVzUgFSgJZ-ZLdS_8CGno7rk2HNNQu-UBDKgsDEE1Rgbp_CzQF309w-ddTNQX4nzD3UKMUEaPNgQ_T0GTrywWz2BUod-irf3Cp2rOlgjyV9YsgiLiP5d0H-EuckqO-s",
                "dBxQ5NKr0wICZfkzL9n83D:APA91bGbAELtlXtjfkgdFIsja06VKBcLljJiJQPOT2TU1wFrGlFkrGnbLisfgYfIicCMhl_kbHquk-X5RnGeEIpP2Q79DglHFJpXrnwO-BuiEdjg_ngPTIherD88y8AAHy9Xoqm67bb4"
            ],
            "notification" => [
                "title" => ((isset($title)) ? $title : 'Notification title'),
                "body" => ((isset($body)) ? $body : 'Notification body'),
                "icon" => ((isset($icon)) ? $icon : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png"),
                "image" => ((isset($image)) ? $image : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.jpg"),
                "click_action" => ((isset($redirectURL)) ? $redirectURL : "https://blumox.com"),
                "sound" => "default"
            ]
        ];

        // Initialize cURL
        $ch = curl_init('https://fcm.googleapis.com/fcm/send');

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            echo json_encode(curl_error($ch), TRUE);
        } else {
            echo json_encode(array(
                'msg' => 'Notification send successfully',
                'devices' => $deviceList
            ), TRUE);
        }

        // Close cURL session
        curl_close($ch);
    } else {
        echo  "SELECT device_token FROM " . TABLE_PREFIX . "device WHERE device_site_id=" . SITE_ID . " AND device_user_id='{$userId}' AND device_status=1";
    }
} else {
    echo "Something went wrong";
}
