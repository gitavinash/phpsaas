<?php

require_once LOGIN_CHECK;
$responseCode = 500;
if ($name != '') {

	if ($checkRoomType = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "category WHERE category_id='{$id}' AND category_type='room'")) {
		if (mysqli_num_rows($checkRoomType) != 0) {
			if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "category SET category_title='{$name}', category_description='{$description}', category_owner_id=" . USER_ID . " WHERE category_id='{$id}'")) {
				$status = 'success';
				$responseCode = 201;
				$msg = 'Room type updated successfully';
			} else {
				$msg = mysqli_error($connection);
			}
		} else {
			$msg = 'Invalid room type request found';
		}
	}
} else {

	$msg = 'Please enter room type name';
}

$result = array(
	'status' => (isset($status)) ? $status : 'error',
	'msg' => ((isset($msg)) ? $msg : 'Invalid response')
);

http_response_code($responseCode);
echo json_encode($result, TRUE);