<?php

if ($selectDevice = mysqli_query($connection, "SELECT category_title, category_description FROM " . TABLE_PREFIX . "category WHERE category_site_id='" . SITE_ID . "' AND category_type='notification' AND category_status=1")) {

    if (mysqli_num_rows($selectDevice) != 0) {

        $deviceToken = array();

        while ($deviceList = mysqli_fetch_assoc($selectDevice)) {

            $deviceToken[] = $deviceList['category_description'];
        }

        /** Send Push Notification to devices */

        $url = "https://fcm.googleapis.com/fcm/send";
        $deviceToken = implode(',', $deviceToken);

        $serverKey = NOTIFICATION_KEY;

        // Notification payload
        $data = [
            'notification' => [
                'title' => 'Title',
                'body' => 'Body',
                'click_action' => 'OPEN_ACTIVITY_1'
            ],
            'to' => '/topics/' . $deviceToken
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
                'group' => '/topics/' . $deviceToken
            ), TRUE);
        }

        // Close cURL session
        curl_close($ch);
    }
}
