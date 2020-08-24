<?php

    require_once LOGIN_CHECK;

    function add($connection, $data) {

        print_r($data);

        $result=array(
            'status' => ((isset($status)) ? $status : 206 ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while adding new building' )
        );
    
        echo json_encode($result, TRUE);

    }