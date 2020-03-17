<?php
	
	/* Add New News */

	function news($connection, $data) {

		require_once MODULES.'imageUploader.php';
		
		extract($data);

		if(isset($featurePost)) {
			$featurePost=1;
		} else {
			$featurePost=0;
		}

		if(isset($image)) {

			extract($image);

			if(isset($featureImage)) {

				$facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
				$twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

			}

		} else {

			$featureImage = $twitterImage = $facebookImage = '';

		}

		$category=explode('_', $category);
		$categoryURL=$category[1];
		$category=$category[0];

		$url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';

		/* define facebook and twitter meta desciption */

		$fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
		$tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

		if($title!='') {

			if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

				if(mysqli_num_rows($checkURL)==0) {

					mysqli_query($connection, "START TRANSACTION");

					$post_status=0;

					if(USER_TYPE=='reporter') {
						$link_status=202;
					} else if(USER_TYPE=='reporter') {
						$link_status=201;
					} else {
						$post_status=1;
						$link_status=200;
					}


					$bodyContent=str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", html_entity_decode($description)));
					$bodyContent=str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent)));

					/* Insert data into link table */

					$addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."{$postType}({$postType}_image, {$postType}_title, {$postType}_description, {$postType}_category, {$postType}_owner_id, {$postType}_status, {$postType}_type) VALUES('{$featureImage}', '{$title}', '{$description}', '{$category}', '{$website_user_id}', '{$post_status}', '{$postType}')");
					$postID=mysqli_insert_id($connection);

					$linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '{$link_status}', '{$postType}', '{$featurePost}', '{$website_user_id}', '".SITE_ID."', NOW() ,NOW())");
					$metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$postID}', '{$meta_title}', '{$postType}', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

					/* Insert tags into tag table */

					if($tags!='') {

						$tags=explode(',', $tags);

						for($i=0; $i<sizeof($tags); $i++) {

							$tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
							$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$postID}', '".USER_ID."', '".SITE_ID."', '{$postType}')");
						}

					} else {

						$tagData=TRUE;

					}

					if($addNewPost && $linkData && $metaData && $tagData) {
						mysqli_query($connection, "COMMIT");

						$status=200;
						$msg=$postType.' is publish successfully';
						$redirect='/dashboard/'.$postType.'/?type=edit&editor=true&id='.$postID;

					} else {

						mysqli_query($connection, "ROLLBACK");

					}

				} else {

					$msg='Please change URL. Because its already used';

				}

			}

		} else {

			$msg='Please enter '.$postType.' title';

		}
		
		$result = array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong while creating new post' ),
			'redirect' => ((isset($redirect)) ? $redirect : '#' )
		);

		echo json_encode($result, TRUE);

	}