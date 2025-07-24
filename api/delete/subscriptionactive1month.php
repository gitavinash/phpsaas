<?php

function subscriptionactive1month($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);
        $subscriptionProductId = 3910;

        if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_id='{$id}'")) {

            if (mysqli_num_rows($checkUser) != 0) {

                if ($checkSubscription = mysqli_query($connection, "SELECT subscription_id FROM " . TABLE_PREFIX . "subscription WHERE subscription_user_id='{$id}' AND subscription_status=1 AND subscription_site_id=" . SITE_ID)) {
                    $subscriptionExpiry = date('Y-m-d 23:59:59', strtotime('+1 month'));
                    if (mysqli_num_rows($checkSubscription) != 0) {
                        extract(mysqli_fetch_assoc($checkSubscription));
                        if ($deleteSubscription = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "subscription SET subscription_status=1, subscription_expiry='{$subscriptionExpiry}' WHERE subscription_id='{$subscription_id}' AND subscription_site_id=" . SITE_ID)) {
                            $status = 200;
                            $msg = 'Subscription updated successfully';
                            mysqli_query($connection, "COMMIT");
                        } else {
                            $msg = 'Something is wrong while updating user subscription';
                            mysqli_query($connection, "ROLLBACK");
                        }
                    } else {
                        if ($deleteSubscription = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "subscription(subscription_site_id, subscription_user_id, subscription_relation_id, subscription_mode, subscription_type, subscription_status, subscription_time, subscription_expiry) VALUES(" . SITE_ID . ", '{$id}', '{$subscriptionProductId}', 'month', 'product), 1, time(), '{$subscriptionExpiry}'")) {
                            $status = 200;
                            $msg = 'New subscription created successfully';
                            mysqli_query($connection, "COMMIT");
                        } else {
                            $msg = 'Something is wrong while creating user subscription';
                            mysqli_query($connection, "ROLLBACK");
                        }
                    }
                } else {

                    $msg = 'Technical error';
                }
            } else {

                $msg = 'Invalid user request found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {

        $msg = 'Something is wrong while sending request to server';
    }

    $result = array(
        'status' => (isset($status)) ? $status : 'error',
        'msg' => ((isset($msg)) ? $msg : 'Invalid response')
    );

    echo json_encode($result, TRUE);
}