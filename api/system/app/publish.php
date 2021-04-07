<?php

function publish($connection, $data) {

    extract($data);
    
    if($title!='') {

        $path=str_replace(' ', '_', $path);

        if($path!='') {

            if($checkPath=mysqli_query($connection, "SELECT app_id FROM ".TABLE_PREFIX."app WHERE app_path='{$path}'")) {

                if(mysqli_num_rows($checkPath)==0) {

                    // App is public or not select by user in frontend

                    $is_public=((isset($visiblity)) ? 1 : 0 );

                    // insert new app in app table in database

                    if($addNewApp=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_title, app_description, app_path, app_status, app_visiblity) VALUES('{$title}', '{$description}', '{$path}', 1, '{$is_public}')")) {

                        $appID=mysqli_insert_id($connection);
                        $redirect='/dashboard/app/?type=add&id='.$appID;
                        $msg='New app is now ready to install by users';
                        $status=200;

                    } else {

                        $msg='Something is wrong while adding new app';

                    }

                } else {

                    $msg='Please change the app path';

                }

            }

        } else {
    
            $msg='Please enter app directory title';
    
        }

    } else {

        $msg='Please enter app title';

    }

    // Response of the api

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        'redirect' => ((isset($redirect)) ? $redirect : '' )
    );

    echo json_encode($result, TRUE);

}