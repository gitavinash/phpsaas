<?php

    function publish($connection, $data) {

        require_once MODULES.'imageUploader.php';
        
        extract($data);

        if(isset($featurePost)) {
			$featurePost=1;
		} else {
			$featurePost=0;
		}

		if(isset($image)) {

			

			if(isset($featureImage)) {

				$facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
				$twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

			}

		} else {

			$featureImage = $twitterImage = $facebookImage = $thumbnailImage = '';

		}

		$category=0;
		$tags='';

		// $category=explode('_', $category);
		// $categoryURL=$category[1];
		// $category=$category[0];

        // $url='/video/'.strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
        $url='/video/'.strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';

		/* define facebook and twitter meta desciption */

		$fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
		$tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

		if(isset($live)) {

			$postType='live';

		} else {

			preg_match('/src="([^"]+)"/', $description, $match);
			print_r($match);

		}

		exit();

		if($title!='') {

			if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".APP_SITE_ID."'")) {

				if(mysqli_num_rows($checkURL)==0) {

					mysqli_query($connection, "START TRANSACTION");

					$video_status=1;
					$link_status=200;

					$bodyContent=str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", html_entity_decode($description)));
					$bodyContent=str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent)));

					/* Insert data into link table */

					$addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."{$postType}({$postType}_image, {$postType}_thumbnail, {$postType}_title, {$postType}_description, {$postType}_category, {$postType}_owner_id, {$postType}_status, {$postType}_type) VALUES('{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', '".USER_ID."', '{$video_status}', '{$postType}')");
					$postID=mysqli_insert_id($connection);

					$linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '{$link_status}', '{$postType}', '{$featurePost}', '".USER_ID."', '".SITE_ID."', NOW() ,NOW())");
					$linkID=mysqli_insert_id($connection);

					$metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', ".APP_SITE_ID.", '{$meta_title}', '{$postType}', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

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
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => '/dashboard/youtube.php?type=list&status=all',
        );

        echo json_encode($result, TRUE);

    }