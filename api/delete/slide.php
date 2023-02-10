<?php

function slide($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkPost = mysqli_query($connection, "SELECT slide_id FROM " . TABLE_PREFIX . "slide WHERE slide_site_id = " . SITE_ID . " AND slide_id='{$id}'")) {

            if (mysqli_num_rows($checkPost) != 0) {

                extract(mysqli_fetch_assoc($checkPost));

                mysqli_query($connection, "START TRANSACTION");

                $deleteEvent = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "slide WHERE slide_id='{$slide_id}' AND slide_site_id=" . SITE_ID);

                if ($deleteEvent) {

                    $status = 200;
                    $msg = 'Event delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $msg = 'Something is wrong while deleting event';
                    mysqli_query($connection, "ROLLBACK");
                    exit();
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