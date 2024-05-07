<?php

if ($selectDevice = mysqli_query($connection, "SELECT device_token FROM " . TABLE_PREFIX . "device WHERE device_site_id=" . SITE_ID . " AND device_status=1")) {

    if (mysqli_num_rows($selectDevice) != 0) {

        $deviceToken = array();

        while ($deviceList = mysqli_fetch_assoc($selectDevice)) {
            $deviceToken[] = $deviceList['device_token'];
        }

        $deviceList = json_encode($deviceToken);

        /** Send Push Notification to devices */

        $url = "https://fcm.googleapis.com/fcm/send";

        $serverKey = NOTIFICATION_KEY;

        // Notification payload
        $data = [
            'notification' => [
                'title' => 'Title',
                'body' => 'Body',
                'icon' => 'https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png',
                'image' => 'https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_logo.png',
            ],
            'data' => [
                'click_action' => 'https://www.youtube.com/' // URL you want to send with the notification
            ],
            'to' => $deviceList
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
    }
}
