<?php

function user($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_id='{$id}'")) {

            if (mysqli_num_rows($checkUser) != 0) {

                extract(mysqli_fetch_assoc($checkUser));

                mysqli_query($connection, "START TRANSACTION");

                $deleteUser = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user WHERE user_id='{$user_id}'");
                $deleteProfile = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "profile WHERE profile_user_id='{$user_id}'");
                $deleteSubscription = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "subscription WHERE subscription_user_id='{$user_id}'");
                $deleteSiteAccess = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "site_access WHERE site_access_user_id='{$user_id}'");

                if ($deleteUser && $deleteProfile && $deleteSubscription && $deleteSiteAccess) {

                    $status = 200;
                    $msg = 'User delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something is wrong while deleting user');
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