<?php

    function constituency($connection, $data) {

        extract($data);

        /** Check constituency is alreay exists or not */

        if($checkConstituency=mysqli_query($connection, "SELECT constituency_id FROM ".TABLE_PREFIX."constituency WHERE constituency_name='{$name}' AND constituency_state='{$state}' AND constituency_type='{$category}'")) {

            if(mysqli_num_rows($checkConstituency)==0) {

                /** Create new constituency */

                if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."constituency SET constituency_name='{$name}, constituency_state='{$state}', constituency_type='{$type}''")) {

                    $status=200;
                    $id=mysqli_insert_id($connection);
                    $msg='Constituency has been successfully updated';

                }

            } else {

                $msg="This constituency has been already exits";

            }

        }

		$result=array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong when updatig constituency' ),
			'id' => ((isset($insertID)) ? $insertID : 0 )
		);

		echo json_encode($result, TRUE);

    }