<?php

    function category($connection, $data) {

        extract($data);
        
        $result = array(
            'status' => intval((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }