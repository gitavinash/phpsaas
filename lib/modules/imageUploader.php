<?php

	require_once MODULES.'isLogin.php';

	if(isset($_FILES)) {

		foreach($_FILES as $imageName => $imageValue) {

			/** check file is properly uploaded or not */

			if($imageValue['error']==0) {

				$validFileExtension = array( 'png', 'gif', 'jpg', 'jpeg' );
				$validFileType = array('image/png', 'image/gif', 'image/jpeg');

				$filename=strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $imageValue['name']))));
				$filetype=$imageValue['type'];
				$extension=explode('.', $filename);
				$fileExtension=end($extension);

				if(in_array($fileExtension, $validFileExtension)) {
					if(in_array($filetype, $validFileType)) {

						$uploadPath=ROOT.'/upload/'.date('Y/m/');

						if(!is_dir($uploadPath)) {
							mkdir($uploadPath, 0755, true);
						}

						/** Original File Upload Path */
						
						$originalUploadPath=$uploadPath.'original_'.time().'_'.str_replace(' ', '_', strtolower($filename));
						
						/** Compress File upload path */

						$uploadPath=$uploadPath.time().'_'.str_replace(' ', '_', strtolower($filename));

						/** Default Directory and Path setup */

						$uploadFile=str_replace('-', '_',str_replace('.', ' ',str_replace('_', ' ', str_replace('.'.$fileExtension, '', $filename))));
						$uploadURL=str_replace(ROOT, '', $uploadPath);
						$link=str_replace(' ', '_', '/attachment/'.time().'_'.strtolower($uploadFile).'/');

						/** Compressing Images */

						$imageInfo=getimagesize($imageValue['tmp_name']);

						if ($imageInfo['mime'] == 'image/jpeg' || $imageInfo['mime'] == 'image/jpg') {
						
							$compressImage=imagecreatefromjpeg($imageValue['tmp_name']);
							imagejpeg($compressImage, $uploadPath, 60);

						} else if ($imageInfo['mime'] == 'image/gif') {
							
							$compressImage = imagecreatefromgif($_FILES['upload']['tmp_name']);
							//imagegif($compressImage, $uploadPath, 60);
							$originalUploadPath=$uploadPath;

						} else if ($imageInfo['mime'] == 'image/png') {
							
							$compressImage = imagecreatefrompng($_FILES['upload']['tmp_name']);
							imagejpeg($compressImage, $uploadPath, 60);
							$originalUploadPath=$uploadPath;

						}

						if(move_uploaded_file($imageValue['tmp_name'], $originalUploadPath)) {

							mysqli_query($connection, 'START TRANSACTION');

							$insertPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadURL}', '{$uploadFile}', '{$filetype}', '{$fileExtension}', '".USER_ID."', 1, '".SITE_ID."')");
							$mediaID=mysqli_insert_id($connection);

							$createLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$link}', '{$mediaID}', 200, 'media', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), NOW())");
							if($insertPost && $createLink) {
								mysqli_query($connection, 'COMMIT');
							}

						}

					}

				}

				$image[$imageName]=$uploadURL;
				$image[$imageName.'_url']=$link;

			}

		}

	}