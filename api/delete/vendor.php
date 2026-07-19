<?php

require_once LOGIN_CHECK;

function vendor($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkVendor = mysqli_query($connection, "SELECT vendor_id FROM " . TABLE_PREFIX . "vendor WHERE vendor_id='{$id}' AND vendor_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkVendor) != 0) {

                extract(mysqli_fetch_assoc($checkVendor));

                if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "vendor WHERE vendor_id='{$vendor_id}' AND vendor_site_id=" . SITE_ID)) {
                    $status = 200;
                    $msg = 'Vendor delete successfully';
                } else {

                    $msg = 'Something is wrong while deleting vendor';
                }
            }
        }
    }

    $result = array(
        'status' => (isset($status)) ? $status : 'error',
        'msg' => ((isset($msg)) ? $msg : 'Invalid response')
    );

    echo json_encode($result, TRUE);
}
