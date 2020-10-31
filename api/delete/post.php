<?php

    function  post($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkPost=mysqli_query($connection, "SELECT post_id, link_id FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id WHERE link_type='post' AND link_site_id=".SITE_ID." AND post_id='{$id}'")) {

                if(mysqli_num_rows($checkPost)!=0) {

                    extract(mysqli_fetch_assoc($checkPost));

                    mysqli_query($connection, "START TRANSACTION");

                    $deletePost=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."post WHERE post_id='{$post_id}'");
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='post'");
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='post' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$post_id}' AND tag_type='post' AND tag_site_id=".SITE_ID);

                    if($deletePost && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='Post delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting post';
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