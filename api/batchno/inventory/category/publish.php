<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'warning';
$msg = 'Invalid Request';

if ($title != '') {

	$url = strtolower('/' . 'category' . preg_replace('~[^\p{M}\w-]+~u', '-', $title) . '-' . time() . '/');

	if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . APP_SITE_ID . "'")) {

		/* Check url is dublicate or not */

		if (mysqli_num_rows($checkURL) == 0) {

			/** Add category details to database */

			mysqli_query($connection, "START TRANSACTION");

			$addCategory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "category(category_parent_id, category_title, category_description, category_site_id, category_owner_id, category_type) VALUES('{$categoryParentID}', '{$title}', '{$description}', '" . SITE_ID . "', '" . USER_ID . "', '{$postType}')");
			$newCategoryID = mysqli_insert_id($connection);

			$addLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$newCategoryID}', '200', 'category', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())");
			$linkID = mysqli_insert_id($connection);
			$addMetaData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_type) VALUES('{$linkID}', " . APP_SITE_ID . ",'category')");

			if ($addCategory && $addMetaData && $addLink) {

				mysqli_query($connection, "COMMIT");

				$httpStatus = 201;
				$status = 'success';
				$msg = 'New category create successfully';
				$categoryID = $newCategoryID;
			} else {

				$msg = mysqli_error($connection);
				mysqli_query($connection, "ROLLBACK");
			}
		} else {

			/** If url is found in database */

			$msg = 'Please change the category url';
		}
	}
} else {

	$msg = 'Please enter category name';
}

$result = array(
	'status' => (isset($status)) ? $status : 'error',
	'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
	'categoryID' => ((isset($categoryID)) ? $categoryID : 0),
	'postType' => ((isset($postType)) ? $postType : 'post')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);