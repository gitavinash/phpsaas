<?php

    function news($connection, $data) {

        extract($data);

        if(isset($id)) {

            $id=base64_decode($id);

            if($checkNews=mysqli_query($connection, "SELECT news_id, link_id FROM ".TABLE_PREFIX."news INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id WHERE link_type='news' AND link_site_id=".SITE_ID." AND news_id='{$id}'")) {

                if(mysqli_num_rows($checkNews)!=0) {

                    extract(mysqli_fetch_assoc($checkNews));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteNews=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."news WHERE news_id='{$news_id}' AND news_site_id=".SITE_ID);
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_relation_id='{$news_id}' AND link_type='news' AND link_site_id=".SITE_ID);
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='news' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$news_id}' AND tag_type='news' AND tag_site_id=".SITE_ID);

                    if($deleteNews && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='News delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {
                        
                        $msg='Something is wrong while deleting news';
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