<?php

    function publish($connection, $data) {

        $result=array(
            'status' => ((isset($status)) ? $status : 206 ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while registering device' ),
        );
    
        echo json_encode($result, TRUE);

    }