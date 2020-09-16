<?php

    function update($connection, $data) {

        extract($data);

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }