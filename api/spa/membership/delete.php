<?php
require_once LOGIN_CHECK;
if (isset($id)) {

    $id = base64_decode($id);

    if ($checkPost = mysqli_query($connection, "SELECT membership_id FROM " . TABLE_PREFIX . "membership WHERE membership_site_id=" . SITE_ID . " AND membership_id='{$id}'")) {

        if (mysqli_num_rows($checkPost) != 0) {

            extract(mysqli_fetch_assoc($checkPost));

            mysqli_query($connection, "START TRANSACTION");

            $deleteMembership = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "membership WHERE membership_id='{$membership_id}' AND membership_site_id=" . SITE_ID);

            if ($deleteMembership) {

                $status = 'success';
                $msg = 'Membership delete successfully';
                mysqli_query($connection, "COMMIT");
            } else {

                $msg = 'Something is wrong while deleting membership';
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