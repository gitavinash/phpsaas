<?php

require_once LOGIN_CHECK;

function update($connection, $data)
{

	extract($data);

	if (isset($_FILES)) {
		$featureImage = '';
		require_once MODULES . 'imageUploader.php';
	} else {
		$featureImage = '';
	}

	if ($title != '') {

		if (isset($id)) {

			/** Add category details to database */

			mysqli_query($connection, "START TRANSACTION");

			if ($checkCategory = mysqli_query($connection, "SELECT category_id FROM " . TABLE_PREFIX . "category WHERE category_id='{$id}' AND category_type='testimonial' AND category_site_id=" . SITE_ID)) {
				if (mysqli_num_rows($checkCategory) > 0) {
					extract(mysqli_fetch_assoc($checkCategory));
					$updateCategory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "category SET category_title='{$title}', category_description='{$description}', category_image='{$featureImage}' WHERE category_id='{$id}' AND category_type='testimonial' AND category_site_id=" . SITE_ID);
					if ($updateCategory) {
						$status = "success";
						$msg = "Testimonial update successfully";
						mysqli_query($connection, "COMMIT");
					} else {
						$msg = "Unabel to update testimonial";
						mysqli_query($connection, "ROLLBACK");
					}
				} else {
					$msg = "Invalid Testimonial info";
					echo "SELECT category_id FROM " . TABLE_PREFIX . "category WHERE category_id='{$id}' AND category_type='testimonial' AND category_site_id=" . SITE_ID;
				}
			}
		} else {
			$status = "warning";
			$msg = "Testimonial information is missing";
		}
	} else {

		$msg = 'Please enter category name';
	}

	$result = array(
		'status' => (isset($status)) ? $status : 'error',
		'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
	);

	echo json_encode($result, TRUE);
}