<?php

function newsletter($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkPost = mysqli_query($connection, "SELECT newsletter_id FROM " . TABLE_PREFIX . "newsletter WHERE newsletter_site_id = " . SITE_ID . " AND newsletter_id='{$id}'")) {

            if (mysqli_num_rows($checkPost) != 0) {

                extract(mysqli_fetch_assoc($checkPost));

                mysqli_query($connection, "START TRANSACTION");

                $deleteNewsletter = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "newsletter WHERE newsletter_id='{$newsletter_id}' AND newsletter_site_id=" . SITE_ID);

                if ($deleteNewsletter) {

                    $status = 200;
                    $msg = 'Newsletter delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $msg = 'Something is wrong while deleting newsletter';
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