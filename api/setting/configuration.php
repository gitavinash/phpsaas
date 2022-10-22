<?php

function configuration($connection, $data)
{

    require_once LOGIN_CHECK;

    extract($data);

    if (!isset($site_index)) {
        $site_index = 'noindex, nofollow';
    }

    mysqli_query($connection, "START TRANSACTION");

    $updateSite = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site SET site_name='{$site_name}', site_description='{$site_description}', site_keyword='{$site_keyword}', site_index='{$site_index}' WHERE site_id=" . SITE_ID);

    $updateSiteSetting = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_setting SET site_theme_color='{$site_theme}' WHERE site_setting_site_id=" . SITE_ID);

    if ($updateSite && $updateSiteSetting) {

        $status = 'success';
        $msg = 'Your website information has been saved';

        mysqli_query($connection, "COMMIT");
    } else {

        $msg = 'Unable to update website configuration';

        mysqli_query($connection, "ROLLBACK");
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to update')
    );

    echo json_encode($result, TRUE);
}