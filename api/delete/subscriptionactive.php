<?php

function subscriptionactive($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkSubscription = mysqli_query($connection, "SELECT subscription_user_id FROM " . TABLE_PREFIX . "subscription WHERE subscription_id='{$id}' AND subscription_status=2 AND subscription_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkSubscription) != 0) {

                extract(mysqli_fetch_assoc($checkSubscription));

                if ($deleteSubscription = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "subscription SET subscription_status=1 WHERE subscription_id='{$id}' AND subscription_site_id=" . SITE_ID)) {
                    $status = 200;
                    $msg = 'Subscription activated successfully';
                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = 'Something is wrong while activating user subscription';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Invalid request found';
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