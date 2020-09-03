<?php

    function sendOTP($connection, $data) {

        if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

            

        }

		$result = array(
			'status' => $status,
			'msg' => ((isset($msg)) ? $msg : '')
		);

    }

	echo json_encode($result, true);