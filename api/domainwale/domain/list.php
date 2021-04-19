<?php

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        'domain_list' => ((isset($checkDomain['domains'])) ? $checkDomain['domains'] : '' )
    );

    echo json_encode($result, TRUE);