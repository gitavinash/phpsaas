<?php

    function slider($connection, $data) {

        require_once LOGIN_CHECK;

        // extract($data);
        print_r($data);

        $result=array(
            'status' => ((isset($status)) ? $status : 'error'),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to update')
        );

        echo json_encode($result, TRUE);

    }