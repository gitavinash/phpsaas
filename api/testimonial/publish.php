<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

	extract($data);

	if ($title != '') {

		if ($category != 0) {
			$category = explode('_', $category);
			$categoryParentID = $category[0];
		} else {
			$categoryParentID = 0;
		}

		/** Add category details to database */

		mysqli_query($connection, "START TRANSACTION");

		$addCategory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "category(category_parent_id, category_title, category_description, category_site_id, category_owner_id, category_type) VALUES('{$categoryParentID}', '{$title}', '{$description}', '" . SITE_ID . "', '" . USER_ID . "', '{$postType}')");
		$newCategoryID = mysqli_insert_id($connection);

		if ($addCategory) {

			mysqli_query($connection, "COMMIT");

			$status = 200;
			$msg = "New testimonial create successfully";
			$categoryID = $newCategoryID;
		} else {

			$msg = mysqli_error($connection);
			mysqli_query($connection, "ROLLBACK");
		}
	} else {

		$msg = 'Please enter category name';
	}

	$result = array(
		'status' => (isset($status)) ? $status : 'error',
		'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
		'categoryID' => ((isset($categoryID)) ? $categoryID : 0),
		'postType' => $postType,
		'redirect' => "/dashboard/{$postType}/?type=edit&editor=true&id=" . base64_encode($categoryID)
	);

	echo json_encode($result, TRUE);
}