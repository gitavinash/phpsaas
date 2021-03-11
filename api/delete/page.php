<?php

    function page($connection, $data) {

        extract($data);

        if(isset($id)) {

            $id=base64_decode($id);

            if($checkPage=mysqli_query($connection, "SELECT page_id, link_id FROM ".TABLE_PREFIX."page INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=page_id WHERE link_type='page' AND link_site_id=".SITE_ID." AND page_id='{$id}'")) {

                if(mysqli_num_rows($checkPage)!=0) {

                    extract(mysqli_fetch_assoc($checkPage));

                    mysqli_query($connection, "START TRANSACTION");

                    $deletePage=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."page WHERE page_id='{$page_id}'");
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='page'");
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='page' AND meta_site_id=".SITE_ID);

                    if($deletePage && $deleteLink && $deleteMeta) {

                        $status=200;
                        $msg='Page delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting page';
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