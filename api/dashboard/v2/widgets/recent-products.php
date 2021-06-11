<?php

    require_once LOGIN_CHECK;
    
    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
    );

    echo json_encode($result, TRUE);