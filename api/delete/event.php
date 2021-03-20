<?php

    function event($connection, $data) {

        extract($data);

        if(isset($id)) {

            $id=base64_decode($id);

            if($checkPost=mysqli_query($connection, "SELECT event_id, link_id FROM ".TABLE_PREFIX."event INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=event_id WHERE link_type='event' AND link_site_id=".SITE_ID." AND event_id='{$id}'")) {

                if(mysqli_num_rows($checkPost)!=0) {

                    extract(mysqli_fetch_assoc($checkPost));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteEvent=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."event WHERE event_id='{$event_id}' AND event_site_id=".SITE_ID);
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_relation_id='{$event_id}' AND link_type='event' AND link_site_id=".SITE_ID);
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='event' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$event_id}' AND tag_type='event' AND tag_site_id=".SITE_ID);

                    if($deleteEvent && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='Event delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        // echo $deleteEvent.' && '.$deleteLink.' && '.$deleteMeta.' && '.$deleteTag;
                        echo "DELETE FROM ".TABLE_PREFIX."event WHERE event_id='{$event_id}' AND event_site_id=".SITE_ID;
                        $msg='Something is wrong while deleting event';
                        mysqli_query($connection, "ROLLBACK");
                        exit();

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