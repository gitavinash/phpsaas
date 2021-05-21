<?php

    function add($connection, $data) {

        extract($data);

        $cartType=((isset($cartType)) ? $cartType : 'product' );

        $checkAPIModule=API_LIB_MODULES.$type.'/'.$action.'/'.$cartType.'.php';

        if(file_exists($checkAPIModule)) {

            require_once $checkAPIModule;

        } else {

            if(DEVELOPMENT_MODE==true) {

                $msg=$checkAPIModule.' file is missing';

            } else {

                $msg='Something went wrong while trying to call API';

            }

        }
    
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
			'categoryID' => ((isset($categoryID)) ? $categoryID : 0 ),
			'postType' => ((isset($postType)) ? $postType : 'post' )
        );

        echo json_encode($result, TRUE);

    }