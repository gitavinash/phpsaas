<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'warning';
$msg = 'Invalid Request';

if ($title != '') {

	mysqli_query($connection, 'START TRANSACTION');

	$categoryID = base64_decode($categoryID);

	$updateCategory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "category SET category_parent_id='{$categoryParentID}', category_title='{$title}', category_description='{$description}' WHERE category_site_id='" . SITE_ID . "' AND category_id='{$categoryID}'");

	/** Only update category */

	$modifyTime = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_modified=NOW() WHERE link_relation_id='{$categoryID}' AND link_status=200 AND link_type='category' AND link_site_id=" . SITE_ID);

	if ($updateCategory && $modifyTime) {

		$httpStatus = 201;
		$status = 'success';
		$msg = 'Category update successfully';
		mysqli_query($connection, 'COMMIT');
	} else {
		mysqli_query($connection, 'ROLLBACK');

		$msg = 'Something is wrong when try to update category';
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