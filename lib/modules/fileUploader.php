<?php

require_once LOGIN_CHECK;

if (isset($_FILES["files"])) {

	/** Supported file extensions */
	$validFileExtension = array('png', 'gif', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx');
	/** Supported image types for compression */
	$validFileType = array(PNG_IMAGE, GIF_IMAGE, JPG_IMAGE, JPEG_IMAGE);

	/** Multiple file upload manager */
	for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {

		if ($_FILES["files"]["error"][$i] == 0) {

			$filename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES["files"]['name'][$i]))));
			$filetype = $_FILES["files"]['type'][$i];
			$extension = explode('.', $filename);
			$fileExtension = end($extension);

			if (in_array($fileExtension, $validFileExtension)) {

				$uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/');
				$uploadFile = $uploadPath . time() . '_' . strtolower(preg_replace('~[^\p{M}\w.-]+~u', '', $filename));
				$uploadMediaPath = str_replace(ROOT, '', $uploadFile);
				$uploadMediaLink = '/attachment' . str_replace('.' . $fileExtension, '', $uploadMediaPath) . '/';
				$uploadFileName = ucwords(preg_replace('/[_]+/', ' ', str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));

				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0755, true);
				}
				
				/** For non-image files (PDF, DOC, etc.) */
				$originalUploadDir = $uploadPath;
				if (!is_dir($originalUploadDir)) mkdir($originalUploadDir, 0755, true);
				$originalUploadFile = $originalUploadDir . str_replace($uploadPath, '', $uploadFile);

				if (!move_uploaded_file($_FILES["files"]['tmp_name'][$i], $originalUploadFile)) {
					$msg = 'Unable to move file';
				}
				
				/** Insert the file details into the database */
				mysqli_query($connection, 'START TRANSACTION');

				$insertPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadMediaPath}', '{$uploadFileName}', '{$filetype}', '{$fileExtension}', '" . USER_ID . "', 1, '" . SITE_ID . "')");
				$mediaID = mysqli_insert_id($connection);

				$createLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$uploadMediaLink}', '{$mediaID}', 200, 'media', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), NOW())");

				if ($insertPost && $createLink) {
					mysqli_query($connection, 'COMMIT');
				} else {
					mysqli_error($connection);
					mysqli_query($connection, 'ROLLBACK');
				}

				$files[] = array(
					'name' => $uploadFileName,
					'path' => $uploadMediaPath,
					'type' => $filetype
				);
			} else {
				echo 'Invalid file extension';
			}
		}
	}

	$files = ((isset($files)) ? json_encode($files, TRUE) : '');
}
