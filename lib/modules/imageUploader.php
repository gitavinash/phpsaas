<?php

require_once LOGIN_CHECK;

if (isset($_FILES)) {

	foreach ($_FILES as $imageName => $imageValue) {

		/** check file is properly uploaded or not */

		if ($imageName != 'gallery') {

			if ($imageValue['error'] == 0) {

				$validFileExtension = array('png', 'gif', 'jpg', 'jpeg');
				$validFileType = array(PNG_IMAGE, GIF_IMAGE, JPG_IMAGE, JPEG_IMAGE);

				$filename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $imageValue['name']))));
				$filetype = $imageValue['type'];
				$extension = explode('.', $filename);
				$fileExtension = end($extension);

				if (in_array($fileExtension, $validFileExtension)) {
					if (in_array($filetype, $validFileType)) {

						list($imageWidth, $imageHeight) = getimagesize($imageValue['tmp_name']);

						/* Default upload path directory of image */

						$uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/');
						$uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
						$cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $filename));
						$uploadFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
						$uploadMediaPath = str_replace(ROOT, '', $uploadFile);
						$uploadMediaLink = '/attachment' . str_replace('.' . $fileExtension, '', $uploadMediaPath) . '/';
						$uploadFileName = ucwords(preg_replace('/[_]+/', ' ', str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));

						/** Original Image variables */

						$originalUploadDir = $uploadPath . 'original/';
						$originalUploadFile = $originalUploadDir . str_replace($uploadPath, '', $uploadFile);

						/** Thumbnail Image variables */

						$thumbnailUploadDir = $uploadPath . 'thumbnail/';
						$thumbnailUploadFile = $thumbnailUploadDir . str_replace($uploadPath, '', $uploadFile);

						if ($imageName == "featureImage") {

							$thumbnailImage = str_replace(ROOT, '', $thumbnailUploadFile);
							$regenerateImageWidth =  (int)IMAGE_THUMBNAIL_WIDTH;
							$regenerateImageHeight = (int) IMAGE_THUMBNAIL_HEIGHT;
						} else {

							$regenerateImageWidth = (int) IMAGE_THUMBNAIL_WIDTH;
							if ($imageWidth > 0 && $imageHeight > 0) {
								$regenerateImageHeight = (int) round(
									($imageHeight / $imageWidth) * $regenerateImageWidth
								);
							} else {
								$regenerateImageHeight = (int) IMAGE_THUMBNAIL_HEIGHT;
							}
						}

						// Ensure base upload directory exists
						if (!is_dir($uploadPath)) {
							if (!mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
								die("Failed to create upload directory: $uploadPath");
							}
						}

						// Ensure original upload directory exists
						if (!is_dir($originalUploadDir)) {
							if (!mkdir($originalUploadDir, 0755, true) && !is_dir($originalUploadDir)) {
								die("Failed to create original upload directory: $originalUploadDir");
							}
						}

						// Ensure thumbnail upload directory exists
						if (!is_dir($thumbnailUploadDir)) {
							if (!mkdir($thumbnailUploadDir, 0755, true) && !is_dir($thumbnailUploadDir)) {
								die("Failed to create thumbnail upload directory: $thumbnailUploadDir");
							}
						}

						/** Compressing Images Process Start Here */

						switch ($filetype) {
							case PNG_IMAGE:
								// Load the PNG image
								$compressImage = imagecreatefrompng($imageValue['tmp_name']);
								if (!$compressImage) {
									echo "Error: Could not create image from PNG.";
									exit;
								}

								// Get original image dimensions
								list($imageWidth, $imageHeight) = getimagesize($imageValue['tmp_name']);

								// Check if height is greater than 500px
								if ($imageHeight > 500) {
									// Calculate new dimensions while maintaining aspect ratio
									$newHeight = 500;
									$newWidth = round(($imageWidth / $imageHeight) * 500);

									// Create a new transparent image with updated dimensions
									$imageSize = imagecreatetruecolor($newWidth, $newHeight);

									// Enable transparency
									imagesavealpha($imageSize, true);
									imagealphablending($imageSize, false);

									// Fill with full transparency
									$transparent = imagecolorallocatealpha($imageSize, 0, 0, 0, 127);
									imagefilledrectangle($imageSize, 0, 0, $newWidth, $newHeight, $transparent);

									// Copy and resize PNG while keeping transparency
									imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight);
								} else {
									// Keep original image if height <= 500px
									$imageSize = imagecreatetruecolor($imageWidth, $imageHeight);

									// Enable transparency
									imagesavealpha($imageSize, true);
									imagealphablending($imageSize, false);

									// Fill with full transparency
									$transparent = imagecolorallocatealpha($imageSize, 0, 0, 0, 127);
									imagefilledrectangle($imageSize, 0, 0, $imageWidth, $imageHeight, $transparent);

									// Copy original image
									imagecopy($imageSize, $compressImage, 0, 0, 0, 0, $imageWidth, $imageHeight);
								}

								// Save the PNG file
								imagepng($imageSize, $uploadFile, 9);
								imagepng($imageSize, $thumbnailUploadFile, 9);

								// Free memory
								imagedestroy($compressImage);
								imagedestroy($imageSize);
								break;


							case GIF_IMAGE:

								/** Compressing GIF/Animation Image and save it */

								$compressImage = imagecreatefromgif($imageValue['tmp_name']);
								imagegif($compressImage, $uploadFile, IMAGE_COMPRESSION_QUALITY);

								/** Creating image thumbnail */

								$imageSize = imagecreatetruecolor(IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight);
								imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight, $imageWidth, $imageHeight);
								imagegif($imageSize, $thumbnailUploadFile, 100);
								break;

							default:

								/** Compressing Other Images mainly JPG or JPEG and save it */

								$mime = mime_content_type($imageValue['tmp_name']);

								switch ($mime) {
									case 'image/jpeg':
										$compressImage = imagecreatefromjpeg($imageValue['tmp_name']);
										break;
									case 'image/png':
										$compressImage = imagecreatefrompng($imageValue['tmp_name']);
										break;
									case 'image/gif':
										$compressImage = imagecreatefromgif($imageValue['tmp_name']);
										break;
									default:
										echo "Unsupported file type: " . htmlspecialchars($mime);
										exit;
								}

								imagejpeg($compressImage, $uploadFile, IMAGE_COMPRESSION_QUALITY);

								/** Creating image thumbnail */

								$imageSize = imagecreatetruecolor(IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight);
								imagecopyresampled($imageSize, $compressImage, 0, 0, 0, 0, IMAGE_THUMBNAIL_WIDTH, $regenerateImageHeight, $imageWidth, $imageHeight);
								imagejpeg($imageSize, $thumbnailUploadFile, 100);
								break;
						}

						imagedestroy($imageSize);

						if (move_uploaded_file($imageValue['tmp_name'], $originalUploadFile)) {

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
					}
				} else {

					echo 'File not uploaded';
				}

				$image[$imageName] = $uploadMediaPath;
				$image[$imageName . '_url'] = $uploadMediaLink;

				// Extract the single upload image string to variables

				extract($image);
			}
		}
	}
}
