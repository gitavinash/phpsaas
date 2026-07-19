<?php

require_once LOGIN_CHECK;

function company($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkCompany = mysqli_query($connection, "SELECT company_id FROM " . TABLE_PREFIX . "company WHERE company_id='{$id}' AND company_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkCompany) != 0) {

                extract(mysqli_fetch_assoc($checkCompany));

                if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "company WHERE company_id='{$company_id}' AND company_site_id=" . SITE_ID)) {
                    $status = 200;
                    $msg = 'Company delete successfully';
                } else {

                    $msg = 'Something is wrong while deleting company';
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
