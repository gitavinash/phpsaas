<?php

    function gallery($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkGallery=mysqli_query($connection, "SELECT gallery_id, link_id FROM ".TABLE_PREFIX."gallery INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=gallery_id WHERE link_type='gallery' AND link_site_id=".SITE_ID." AND gallery_id='{$id}'")) {

                if(mysqli_num_rows($checkGallery)!=0) {

                    extract(mysqli_fetch_assoc($checkGallery));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteGallery=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."gallery WHERE gallery_id='{$gallery_id}'");
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='gallery'");
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='gallery' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$gallery_id}' AND tag_type='gallery' AND tag_site_id=".SITE_ID);

                    if($deleteGallery && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='Gallery delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting gallery';
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