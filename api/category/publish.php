<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

	extract($data);

	if ($name != '') {

		if ($category != 0) {
			$category = explode('_', $category);
			$categoryURL = $category[1];
			$categoryParentID = $category[0];
		} else {
			$categoryParentID = 0;
			$categoryURL = '/';
		}

		$url = strtolower('/' . 'category' . $categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url) . '/');

		if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . APP_SITE_ID . "' AND link_status=200")) {

			/* Check url is dublicate or not */

			if (mysqli_num_rows($checkURL) == 0) {

				/** Handle feature image upload */

				$featureImage = '';
				if (isset($_FILES['featureImage']) && $_FILES['featureImage']['error'] === 0) {
					require MODULES . 'imageUploader.php'; // require (not require_once) so it always runs
				}

				/** Add category details to database */

				mysqli_query($connection, "START TRANSACTION");

				$addCategory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "category(category_parent_id, category_title, category_description, category_image, category_site_id, category_owner_id, category_type) VALUES('{$categoryParentID}', '{$name}', '{$description}', '{$featureImage}', '" . SITE_ID . "', '" . USER_ID . "', '{$postType}')");
				$newCategoryID = mysqli_insert_id($connection);

				$addLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$newCategoryID}', '200', 'category', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())");
				$linkID = mysqli_insert_id($connection);
				$addMetaData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_type) VALUES('{$linkID}', " . APP_SITE_ID . ",'category')");

				if ($addCategory && $addMetaData && $addLink) {

					mysqli_query($connection, "COMMIT");

					$status = 200;
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

	echo json_encode($result, TRUE);
}