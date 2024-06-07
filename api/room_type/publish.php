<?php

require_once LOGIN_CHECK;
$responseCode = 500;
if ($name != '') {

	if ($checkRoomType = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "category WHERE category_title='{$name}'")) {
		if (mysqli_num_rows($checkRoomType) == 0) {
			if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "category(category_title, category_description, category_site_id, category_owner_id, category_type) VALUES('{$name}', '{$description}', '" . SITE_ID . "', '" . USER_ID . "', 'room')")) {
				$status = 'success';
				$responseCode = 200;
				$msg = 'Room type updated successfully';
			} else {
				$msg = mysqli_error($connection);
			}
		} else {
			$responseCode = 409;
			$msg = 'Room type is already defined';
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