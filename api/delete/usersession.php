<?php

function usersession($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkUserSession = mysqli_query($connection, "SELECT us_user_id FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$id}' AND us_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkUserSession) != 0) {

                extract(mysqli_fetch_assoc($checkUserSession));

                if ($deleteUserSession = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$id}' AND us_site_id=" . SITE_ID)) {
                    $status = 200;
                    $msg = 'User session delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = 'Something is wrong while deleting user session';
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