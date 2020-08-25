<?php

    require_once LOGIN_CHECK;

    function add($connection, $data) {

        extract($data);

        mysqli_query($connection, "START TRANSACTION");

        $addAddress=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."address() VALUES()");

        // $addCompany=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."company(company_site_id, company_user_id) VALUES()") {

        // }

        $result=array(
            'status' => ((isset($status)) ? $status : 206 ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while adding new building' )
        );
    
        echo json_encode($result, TRUE);

    }