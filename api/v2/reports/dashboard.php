<?php

    if(isset($startDate)) {

        if(isset($endDate)) {

            

        } else {

            $msg='End date is missing';

        }

    } else {

        $msg='Start date is missing';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
    );

    echo json_encode($result, TRUE);