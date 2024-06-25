<?php

require_once LOGIN_CHECK;

if (isset($_FILES['gallery'])) {

	/** Multple image upload manager */

	for ($i = 0; $i < count($_FILES["gallery"]["name"]); $i++) {

		if ($_FILES["gallery"]["error"][$i] == 0) {

			$validFileExtension = array('png', 'gif', 'jpg', 'jpeg');
			$validFileType = array(PNG_IMAGE, GIF_IMAGE, JPG_IMAGE, JPEG_IMAGE);

			$filename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES["gallery"]['name'][$i]))));
			$filetype = $_FILES["gallery"]['type'][$i];
			$extension = explode('.', $filename);
			$fileExtension = end($extension);

			if (in_array($fileExtension, $validFileExtension)) {

				if (in_array($filetype, $validFileType)) {

					list($imageWidth, $imageHeight) = getimagesize($_FILES["gallery"]['tmp_name'][$i]);

					/* Default upload path directory of image */

					$uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/');
					$uploadFile = $uploadPath . time() . '_' . strtolower(preg_replace('~[^\p{M}\w.-]+~u', '', $filename));
					$uploadMediaPath = str_replace(ROOT, '', $uploadFile);
					$uploadMediaLink = '/attachment' . str_replace('.' . $fileExtension, '', $uploadMediaPath) . '/';
					$uploadFileName = ucwords(preg_replace('/[_]+/', ' ', str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));
					/** Original Image variables */

					$originalUploadDir = $uploadPath . 'original/';
					$originalUploadFile = $originalUploadDir . str_replace($uploadPath, '', $uploadFile);

					/** Thumbnail Image variables */

					$thumbnailUploadDir = $uploadPath . 'thumbnail/';
					$thumbnailUploadFile = $thumbnailUploadDir . str_replace($uploadPath, '', $uploadFile);

					$regenerateImageWidth = IMAGE_THUMBNAIL_WIDTH;
					$regenerateImageHeight = ($imageWidth / $imageHeight) * IMAGE_THUMBNAIL_HEIGHT;


					if (!is_dir($uploadPath)) {

						mkdir($uploadPath, 0755, true);

						/* Creating original image directory inside default upload directory */

						mkdir($originalUploadDir, 0755, true);

						/* Creating thumbnail image directory inside default upload directory */

						mkdir($thumbnailUploadDir, 0755, true);
					}

					/** Compressing Images Process Start Here */

					switch ($filetype) {

						case PNG_IMAGE:

							/** Compressing PNG Image and save it */

							$compressImage = imagecreatefrompng($_FILES["gallery"]['tmp_name'][$i]);
							imagepng($compressImage, $uploadFile, 9);

							/** Creating image thumbnail */

							$imageSize = imagecreatetruecolor(IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight);
							imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight, $imageWidth, $imageHeight);
							imagepng($imageSize, $thumbnailUploadFile, 9);
							break;

						case GIF_IMAGE:

							/** Compressing GIF/Animation Image and save it */

							$compressImage = imagecreatefromgif($_FILES["gallery"]['tmp_name'][$i]);
							imagegif($compressImage, $uploadFile, IMAGE_COMPRESSION_QUALITY);

							/** Creating image thumbnail */

							$imageSize = imagecreatetruecolor(IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight);
							imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight, $imageWidth, $imageHeight);
							imagegif($imageSize, $thumbnailUploadFile, 100);
							break;

						default:

							/** Compressing Other Images mainly JPG or JPEG and save it */

							$compressImage = imagecreatefromjpeg($_FILES["gallery"]['tmp_name'][$i]);
							imagejpeg($compressImage, $uploadFile, IMAGE_COMPRESSION_QUALITY);

							/** Creating image thumbnail */

							$imageSize = imagecreatetruecolor(IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight);
							imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight, $imageWidth, $imageHeight);
							imagejpeg($imageSize, $thumbnailUploadFile, 100);
							break;
					}

					imagedestroy($imageSize);

					if (move_uploaded_file($_FILES["gallery"]['tmp_name'][$i], $originalUploadFile)) {

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
					} else {

						$msg = 'Unable to move file';
					}
				} else {

					echo 'invalid file type -> ' . $i . ' | ';
				}
			} else {

				echo 'not uploaded';
			}

			$images[] = array(
				'name' => $uploadFileName,
				'path' => $uploadMediaPath,
				'type' => $filetype
			);
		}
	}

	$gallery = ((isset($images)) ? json_encode($images, TRUE) : '');
}
