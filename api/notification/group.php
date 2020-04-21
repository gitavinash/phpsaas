<?php

    /** Register new notification group */

    if($checkGroupToken=mysqli_query($connection, "SELECT category_id as group_id, category_title as group_name, category_description as group_token FROM ".TABLE_PREFIX."category WHERE category_site_id=".SITE_ID." AND category_status=1")) {
        
        if(mysqli_num_rows($checkGroupToken)==0) {

            $group_name=((!isset($group_name)) ? SITE_DOMAIN.'_general_test' : SITE_DOMAIN.'_'.$group_name);

            // Create New Notification Group

            $data=array(
                'operation' => 'create',
                'notification_key_name' => $group_name,
                'registration_ids' => ["{$token}"]
            
            );

            $addNewGroup=TRUE;

        } else {
        
            extract(mysqli_fetch_assoc($checkGroupToken));

            // Add New Device to Existing Notification Group

            $data=array(
                'operation' => 'add',
                'notification_key_name' => $group_name,
                'notification_key' => $group_token,
                'registration_ids' => ["{$token}"]
            );
        
        }

        $url='https://fcm.googleapis.com/fcm/notification';
        $urlHeader=array(
            'Content-Type:application/json',
            'Authorization: key='.NOTIFICATION_KEY,
            'project_id: '.NOTIFICATION_SENDER
        );

        $groupData=json_encode($data, TRUE);

        $data=json_encode($data, TRUE);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $groupData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $urlHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

        $response=json_decode($response, TRUE);

        if(isset($response['notification_key'])) {

            if(isset($addNewGroup)) {

                
                /** Save group token to category table */
                
                $group_token=$response['notification_key'];
                $generateNewGroupToken=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."category(category_title, category_description, category_site_id, category_type, category_status) VALUES('{$group_name}', '{$group_token}', ".SITE_ID.", 'notification', 1)");
                $group_id=mysqli_insert_id($connection);

            } else {

                $generateNewGroupToken=TRUE;
                
            }

            $updateDevice=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."device SET device_category='{$group_id}' WHERE device_id='{$device_id}'");

            if($registerDevice && $generateNewGroupToken && $updateDevice) {

                mysqli_query($connection, 'COMMIT');

                $status=200;
                $msg='Device is registered successfully';

            }

        } else {

            $msg=$response['error'];

        }

    } else {

        echo mysqli_error($connection);

    }