<?php

    function redirection($connection, $data) {

        extract($data);

        if(isset($id)) {

            $id=base64_decode($id);

            if($checkRedirection=mysqli_query($connection, "SELECT redirection_id, link_id FROM ".TABLE_PREFIX."redirection INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=redirection_id WHERE link_type='redirect' AND link_site_id=".SITE_ID." AND redirection_id='{$id}'")) {

                if(mysqli_num_rows($checkRedirection)!=0) {

                    extract(mysqli_fetch_assoc($checkRedirection));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteRedirection=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."redirection WHERE redirection_id='{$redirection_id}'");
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='redirect'");

                    if($deleteRedirection && $deleteLink) {

                        $status=200;
                        $msg='Redirection delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting redirection';
                        mysqli_query($connection, "ROLLBACK");

                    }

                } else {

                    $msg='Invalid request found';

                }

            } else {

                $msg='Technical error';
    
            }

        } else {

            $msg='Something is wrong while sending request to server';

        }
        
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }