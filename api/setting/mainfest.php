<?php

    if($updateFile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_setting SET site_mainfest_json='{$data}' WHERE site_setting_id=".SITE_ID)) {

        $status=200;
        $msg='Mainfest.json file update successfully';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 206 ),
        'msg' => ((isset($msg)) ? $msg : 'Unable to update Mainfest.json' )
    );

    echo json_encode($result, TRUE);
    exit();