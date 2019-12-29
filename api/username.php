<?php

    include_once MODULES.'isLogin.php';

    /** Check username is available or not */

    if(isset($_GET['username'])) {

        $username=mysqli_real_escape_string($connection, $_GET['username']);

        $status=206;
        $msg='';

        if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {

            if(mysqli_num_rows($checkUsername)==0) {

                /** Return status 200 is username is availabel */
                
                $status=200;

            } else {

                $msg='Not Found';
                
            }

        } else {
            $msg = mysqli_error($connection);
        }

        $result=array(
            'status' => $status,
            'msg'   => $msg
        );

        echo json_encode($result, TRUE);

    }