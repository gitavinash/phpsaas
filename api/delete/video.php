<?php

    function video($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkVideo=mysqli_query($connection, "SELECT video_id, link_id FROM ".TABLE_PREFIX."video INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=video_id WHERE link_type='video' AND link_site_id=".SITE_ID." AND video_id='{$id}'")) {

                if(mysqli_num_rows($checkVideo)!=0) {

                    extract(mysqli_fetch_assoc($checkVideo));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteVideo=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."video WHERE video_id='{$video_id}'");
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='video'");
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='video' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$video_id}' AND tag_type='video' AND tag_site_id=".SITE_ID);

                    if($deleteVideo && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='Video delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting video';
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