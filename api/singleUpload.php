<?php
	
	require_once LOGIN_CHECK;

	function singleUpload($connection) {

		global $table_prefix;

		if(isset($_FILES['upload'])) {

			$status=403;
			$msg='Please upload a valid file';

			if($_FILES['upload']['error']==0) {

				$validFileExtension = array( 'png', 'gif', 'jpg', 'jpeg', 'zip', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'csv' );
				$validFileType = array('image/png', 'image/gif', 'image/jpeg', 'application/zip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf', 'text/plain', 'text/csv');

				$filename=strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES['upload']['name']))));
				$filetype=$_FILES['upload']['type'];
				$extension=explode('.', $filename);
				$fileExtension=end($extension);

				if(in_array($fileExtension, $validFileExtension)) {
					if(in_array($filetype, $validFileType)) {
						$status=200;
						$uploadPath=ROOT.'/upload/'.date('Y/m/');

						/* Check upload path is available is not */

						if(!is_dir($uploadPath)) {
							mkdir($uploadPath, 0755, true);
						}


						/** Original File Upload Path */
						
						$originalUploadPath=$uploadPath.'original_'.time().'_'.str_replace(' ', '_', strtolower($filename));

						/** Compress File upload path */

						$uploadPath=$uploadPath.time().'_'.str_replace(' ', '_', strtolower($filename));

						/** Default Directory and Path setup */

						$uploadFile=str_replace('-', '_',str_replace('.', ' ',str_replace('_', ' ', str_replace('.'.$fileExtension, '', $filename))));
						$uploadURL=strtolower(str_replace(ROOT, '', $uploadPath));
						$link=strtolower(str_replace(' ', '_', '/attachment/'.time().'_'.$uploadFile).'/');

						/** Compressing Images */

						$imageInfo=getimagesize($_FILES['upload']['tmp_name']);

						if ($imageInfo['mime'] == 'image/jpeg' || $imageInfo['mime'] == 'image/jpg') {
						
							$compressImage=imagecreatefromjpeg($_FILES['upload']['tmp_name']);
							imagejpeg($compressImage, $uploadPath, 60);

						} else if ($imageInfo['mime'] == 'image/gif') {
							
							$compressImage = imagecreatefromgif($_FILES['upload']['tmp_name']);
							//imagegif($compressImage, $uploadPath, 60);
							$originalUploadPath=$uploadPath;

						} else if ($imageInfo['mime'] == 'image/png') {
							
							$compressImage = imagecreatefrompng($_FILES['upload']['tmp_name']);
							imagejpeg($compressImage, $uploadPath, 60);
							//$originalUploadPath=$uploadPath;

						}
						
						if(move_uploaded_file($_FILES['upload']['tmp_name'], $originalUploadPath)) {

							switch ($filetype) {

								case 'application/zip': $filetype='document'; break;
								case 'application/msword': $filetype='document'; break;
								case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': $filetype='document'; break;
								case 'application/vnd.ms-excel': $filetype='document'; break;
								case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': $filetype='document'; break;
								case 'application/pdf': $filetype='document'; break;
								case 'text/plain': $filetype='document'; break;
								case 'text/csv': $filetype='document'; break;
								
								default:
									$filetype='image';
									break;
							}

							if($checkLink=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."link WHERE link_site_id='".SITE_ID."' AND link_url='{$link}'")) {
								
								if(mysqli_num_rows($checkLink)!=0) {
									$link=strtolower(str_replace(' ', '_', '/attachment/'.time().'_'.$uploadFile).'/');
								}

							}

							mysqli_query($connection, 'START TRANSACTION');

							$insertPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadURL}', '{$uploadFile}', '{$filetype}', '{$fileExtension}', '".USER_ID."', 1, '".SITE_ID."')");
							$mediaID=mysqli_insert_id($connection);

							$createLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$link}', '{$mediaID}', 200, 'media', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), NOW())");

							if($insertPost && $createLink) {
								
								mysqli_query($connection, 'COMMIT');
								$msg=$uploadURL;

							} else {
								
								$status=500;
								$msg='Error while insert data';

								mysqli_query($connection, 'ROLLBACK');

							}

						} else {
							$status=403;
							$msg='file is not uploaded';
						}

					}
				}

			} else {

				$msg='file is not uploaded properly';

			}

		} else {

			$msg='Invalid request found';

		}

		$result = array (

			'status' 	=> 	$status,
			'msg'		=>	$msg

		);

		echo json_encode($result, true);

	}

?>