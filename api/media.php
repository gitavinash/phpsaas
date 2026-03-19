<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = isset($_POST['action']) ? trim($_POST['action']) : '';

	if ($action === 'update') {
		$mediaId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$title = isset($_POST['title']) ? mysqli_real_escape_string($connection, trim($_POST['title'])) : '';
		$alt = isset($_POST['alt']) ? mysqli_real_escape_string($connection, trim($_POST['alt'])) : '';
		$description = isset($_POST['description']) ? mysqli_real_escape_string($connection, trim($_POST['description'])) : '';

		if ($mediaId <= 0) {
			echo json_encode(array('status' => 400, 'msg' => 'Invalid media id'), true);
			exit;
		}

		if ($title === '') {
			echo json_encode(array('status' => 400, 'msg' => 'Title is required'), true);
			exit;
		}

		if (!mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "media SET media_title='{$title}' WHERE media_id='{$mediaId}' AND media_site_id='" . SITE_ID . "'")) {
			echo json_encode(array('status' => 500, 'msg' => mysqli_error($connection)), true);
			exit;
		}

		$metaCheckQuery = "SELECT meta_id FROM " . TABLE_PREFIX . "meta WHERE meta_relation_id='{$mediaId}' AND meta_site_id='" . SITE_ID . "' AND meta_type='media' LIMIT 0, 1";
		$metaExists = mysqli_query($connection, $metaCheckQuery);

		if ($metaExists && mysqli_num_rows($metaExists) != 0) {
			$metaData = mysqli_fetch_assoc($metaExists);
			$metaId = (int)$metaData['meta_id'];
			$updateMetaQuery = "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$title}', meta_description='{$description}', meta_keyword='{$alt}' WHERE meta_id='{$metaId}'";

			if (!mysqli_query($connection, $updateMetaQuery)) {
				echo json_encode(array('status' => 500, 'msg' => mysqli_error($connection)), true);
				exit;
			}
		} else {
			$insertMetaQuery = "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$mediaId}', '" . SITE_ID . "', 'media', '{$title}', '{$description}', '', '{$alt}', '', '', '', '', '', '', '', '', '', '')";

			if (!mysqli_query($connection, $insertMetaQuery)) {
				echo json_encode(array('status' => 500, 'msg' => mysqli_error($connection)), true);
				exit;
			}
		}

		echo json_encode(array('status' => 200, 'msg' => 'Media details updated successfully'), true);
		exit;
	}

	if ($action === 'delete') {
		$mediaId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

		if ($mediaId <= 0) {
			echo json_encode(array('status' => 400, 'msg' => 'Invalid media id'), true);
			exit;
		}

		if (!mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "media WHERE media_id='{$mediaId}' AND media_site_id='" . SITE_ID . "'")) {
			echo json_encode(array('status' => 500, 'msg' => mysqli_error($connection)), true);
			exit;
		}

		mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "meta WHERE meta_relation_id='{$mediaId}' AND meta_site_id='" . SITE_ID . "' AND meta_type='media'");

		echo json_encode(array('status' => 200, 'msg' => 'Media deleted successfully'), true);
		exit;
	}

	echo json_encode(array('status' => 400, 'msg' => 'Invalid action'), true);
	exit;
}

$mediaName = (isset($_GET['name']) && $_GET['name'] != '') ? 'AND media_title LIKE "%' . mysqli_real_escape_string($connection, $_GET['name']) . '%"' : '';
$mediaType = (isset($_GET['media']) && $_GET['media'] != '') ? ' AND media_type="' . mysqli_real_escape_string($connection, $_GET['media']) . '"' : '';

$query = "SELECT media_title, media_url FROM " . TABLE_PREFIX . "media WHERE media_status=1 AND media_site_id='" . SITE_ID . "' {$mediaType} {$mediaName} ORDER BY media_id DESC LIMIT 0, 100";

if ($checkImage = mysqli_query($connection, $query)) {
	if (mysqli_num_rows($checkImage) != 0) {
		$imageList = array();
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
