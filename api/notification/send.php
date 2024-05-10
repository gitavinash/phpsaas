<?php


if ($selectDevice = mysqli_query($connection, "SELECT device_token FROM " . TABLE_PREFIX . "device WHERE device_site_id=" . SITE_ID . " AND device_user_id='{$userId}' AND device_status=1")) {

    if (mysqli_num_rows($selectDevice) != 0) {

        $deviceTokens = array();

        while ($deviceToken = mysqli_fetch_assoc($selectDevice)) {
            $deviceTokens[] = $deviceToken["device_token"];
        }

        /** Send Push Notification to devices */

        $serverKey = NOTIFICATION_KEY;

        // Notification payload
        $data = [
            "registration_ids" => $deviceTokens,
            "notification" => [
                "title" => ((isset($title)) ? $title : 'Notification title'),
                "body" => ((isset($body)) ? $body : 'Notification body'),
                "icon" => ((isset($icon)) ? $icon : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png"),
                "image" => ((isset($image)) ? $image : "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_push_notification.png"),
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, TRUE));
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
                'devices' => $deviceTokens
            ), TRUE);
        }

        // Close cURL session
        curl_close($ch);
    }
}
