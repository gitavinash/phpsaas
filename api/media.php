<?php

$mediaName = (($_GET['name'] != '') ? 'AND media_title LIKE "%' . mysqli_real_escape_string($connection, $_GET['name']) . '%"' : '');
$mediaType = (($_GET['media'] != '') ? ' AND media_type="' . mysqli_real_escape_string($connection, $_GET['media']) . '"' : '');

$query = "SELECT media_title, media_url FROM " . TABLE_PREFIX . "media WHERE media_status=1 AND media_site_id='" . SITE_ID . "' {$mediaType} {$mediaName} ORDER BY media_id DESC LIMIT 0, 100";

if ($checkImage = mysqli_query($connection, $query)) {

	if (mysqli_num_rows($checkImage) != 0) {

		while ($image = mysqli_fetch_assoc($checkImage)) {
			$imageList[] = $image['media_url'];
		}

		$result = array(
			'status' => 200,
			'msg' => 'data received successfully',
			'images' => $imageList
		);
	} else {

		$result = array(
			'status' => 404,
			'msg' => 'Not Found ' . $mediaType
		);
	}
} else {

	$result = array(
		'status' => 500,
		'msg' => $query
	);
}

echo json_encode($result, true);
