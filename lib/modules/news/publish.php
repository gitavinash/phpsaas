<?php
	
	/* Add New Post */

	function post($connection, $data) {

		global $table_prefix;

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

		/** encode special character into html codes */

		//$description=htmlentities($description);

		$category=explode('_', $category);
		$categoryURL=$category[1];
		$category=$category[0];

		$url=strtolower($categoryURL.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

		/* define facebook and twitter meta desciption */

		$fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
		$tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

		if($title!='') {

			if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

				if(mysqli_num_rows($checkURL)==0) {

					mysqli_query($connection, "START TRANSACTION");

					if(USER_TYPE=='reporter') {
						$post_status=0;
						$link_status=206;
					} else {
						$post_status=1;
						$link_status=200;
					}


					$bodyContent=str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", html_entity_decode($description)));
					$bodyContent=str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent)));

					/* Insert data into link table */
					$addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_image, post_title, post_description, post_category, post_owner_id, post_status, post_type) VALUES('{$featureImage}', '{$title}', '{$description}', '{$category}', '{$website_user_id}', '{$post_status}', '{$postType}')");
					$postID=mysqli_insert_id($connection);

					$linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '{$link_status}', '{$postType}', '{$featurePost}', '{$website_user_id}', '{$website_id}', NOW() ,NOW())");
					$metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$postID}', '{$meta_title}', '{$postType}', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

					/* Insert tags into tag table */
					if($tags!='') {
						$tags=explode(',', $tags);

						for($i=0; $i<sizeof($tags); $i++) {
							$tagsName=rtrim(ltrim($tags[$i], ' '), ' ');
							$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_name, tag_relation_id, tag_owner_id, tag_site_id) VALUES('{$tagsName}', '{$postID}', '{$website_user_id}', '{$website_id}')");
						}

					} else {

						$tagData=TRUE;

					}

					if($addNewPost && $linkData && $metaData && $tagData) {
						mysqli_query($connection, "COMMIT");
						$result = array(
							'status' => 200,
							'msg' => $postType.' is publish successfully',
							'redirect' => '/dashboard/'.$postType.'/?type=edit&editor=true&post_id='.$postID
						);
					} else {
						$result = array(
							'status' => 500,
							'msg' => 'Error while publish this '.$postType
						);
						mysqli_query($connection, "ROLLBACK");
					}

				} else {

					$result = array(
						'status' => 206,
						'msg' => 'Please change URL. Because its already used'
					);

				}

			}

		} else {

			$result = array(
				'status' => 206,
				'msg' => 'Please enter '.$postType.' title'
			);

		}

		echo json_encode($result, TRUE);

	}
	
	/* Add New Page */

	function page($connection, $data) {

		global $table_prefix;

		require_once MODULES.'imageUploader.php';
		
		extract($data);

		if(isset($image)) {

			extract($image);

			if(isset($featureImage)) {

				$facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
				$twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

			}

		} else {

			$featureImage = $twitterImage = $facebookImage = '';

		}

		/** encode special character into html codes */

		//$description=htmlentities($description);

		$url=strtolower('/'.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

		/* define facebook and twitter meta desciption */

		$fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
		$tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

		if($title!='') {

			mysqli_query($connection, "START TRANSACTION");

			if(USER_TYPE=='reporter') {
				$post_status=0;
				$link_status=206;
			} else {
				$post_status=1;
				$link_status=200;
			}

			/* Insert data into link table */
			$addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_image, post_title, post_description, post_owner_id, post_status, post_type) VALUES('{$featureImage}', '{$title}', '{$description}', '{$website_user_id}', '{$post_status}', '{$postType}')");
			$postID=mysqli_insert_id($connection);

			$linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '{$link_status}', '{$postType}', '{$website_user_id}', '{$website_id}', NOW() ,NOW())");
			$metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$postID}', '{$meta_title}', '{$postType}', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

		
			/* Insert tags into tag table */
			$tags=explode(',', $tags);

			for($i=0; $i<sizeof($tags); $i++) {
				$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_name, tag_relation_id, tag_owner_id, tag_site_id) VALUES('{$tags[$i]}', '{$postID}', '{$website_user_id}', '{$website_id}')");
			}

			if($addNewPost && $linkData && $metaData && $tagData) {
				mysqli_query($connection, "COMMIT");
				$result = array(
					'status' => 200,
					'msg' => $postType.' is publish successfully',
					'redirect' => '/dashboard/'.$postType.'/?type=edit&editor=true&post_id='.$postID
				);
			} else {
				mysqli_query($connection, "ROLLBACK");
				$result = array(
					'status' => 500,
					'msg' => 'Error while publish this '.$postType
				);
			}

		} else {

			$result = array(
				'status' => 206,
				'msg' => 'Please enter '.$postType.' title'
			);

		}

		echo json_encode($result, TRUE);

	}