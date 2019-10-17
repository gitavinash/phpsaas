<?php
	
	/* Update Post */

	function post($connection, $data) {

		$status=500;
		$msg='';

		global $table_prefix;

		require_once MODULES.'imageUploader.php';
		
		extract($data);

		if(isset($featurePost)) {
			$featurePost=1;
		} else {
			$featurePost=0;
		}

		if(isset($image)) {

			/** when image is upload by user */

			extract($image); // Extracting image array element into variable

			$featureImage=(((isset($featureImage)) ? $featureImage : $oldFeatureImage ));
			$facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
			$twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

		} else {

			/** When image is not upload by user */

			$featureImage=$oldFeatureImage;
			$facebookImage=(($oldFacebookImage=='') ? $oldFeatureImage : $oldFacebookImage);
			$twitterImage=(($oldTwitterImage=='') ? $oldFeatureImage : $oldTwitterImage);

		}
		
		/** encode special character into html codes */

		//$description=htmlentities($description);

		/** Fetch category url */

		$category=explode('_', $category);
		$categoryURL=str_replace('/category/', '', $category[1]);
		$category=$category[0];

		$url=strtolower($categoryURL.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

		if($oldURL!=$url) {

			if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}'")) {

				if(mysqli_num_rows($checkURL)==0) {

					/** Add 301 redirect */

					$addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}'");

					/** Add new active url of the post */

					$updateLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$post_id}', 200, '{$postType}', '{$website_user_id}', '{$website_id}', NOW(), NOW(), '{$featurePost}')");

				} else {

					$status=500;
					$msg="Please change {$postType} url";

				}

			}

		} else {

			$addRedirect=TRUE;

			if($postStatus==0) {
				$updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$post_id}' AND link_type='{$postType}' AND link_url='{$oldURL}'");
			} else {
				$updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_is_featured='{$featurePost}' WHERE link_relation_id='{$post_id}' AND link_type='{$postType}' AND link_url='{$oldURL}'");
			}

		}

		/** Update post table */

		$updatePost=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."post SET post_image='{$featureImage}', post_title='{$title}', post_description='{$description}', post_category='{$category}', post_status='1' WHERE post_id='{$post_id}'");

		/** Update meta table */

		$updateMeta=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$post_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$post_id}'");

		/** Manage Tags  */

		$deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$post_id}' AND tag_site_id='{$website_id}' AND tag_type='post'");
		$tags=explode(',', $tags);

		/* Insert tags into tag table */

		for($i=0; $i<sizeof($tags); $i++) {
			if($tags!='') {
				$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_name, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tags[$i]}', '{$post_id}', '{$website_user_id}', '{$website_id}', 'tag')");
			} else {
				$tagData='';
			}
		}

		if($addRedirect && $updateLink && $updatePost && $updateMeta && $tagData) {

			mysqli_query($connection, 'COMMIT');

			$status=200;
			$msg=$postType.' is update successfully';
			$redirect='/dashboard/'.$postType.'/?type=edit&editor=true&post_id='.$post_id;

		} else {

			echo $oldURL.' -> '.$url;

			mysqli_query($connection, 'ROLLBACK');
			$status=500;
			$msg='Something is wrong while updating '.$postType;

		}

		$result = array(
			'status' =>	$status,
			'msg' => $msg,
			'redirect' => ((isset($redirect)) ? $redirect : '' )
		);

		echo json_encode($result, TRUE);

	}

	/** Update Taxonomy */ 

	function taxonomy($connection, $data) {

		extract($data);
		global $table_prefix;

		$url='/category/'.$url.'/';

		$linkUpdate=TRUE;

		/** Upload image */

		require_once MODULES.'imageUploader.php';

		if(isset($image)) {

			/** when image is upload by user */

			extract($image); // Extracting image array element into variable

			$featureImage=(((isset($featureImage)) ? $featureImage : $oldFeatureImage ));
			$facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
			$twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

		} else {

			/** When image is not upload by user */

			$featureImage=$oldFeatureImage;
			$facebookImage=(($oldFacebookImage=='') ? $oldFeatureImage : $oldFacebookImage);
			$twitterImage=(($oldTwitterImage=='') ? $oldFeatureImage : $oldTwitterImage);

		}
		
		/** Change url if url is changed */

		mysqli_query($connection, 'START TRANSACTION');

		if($oldURL!=$url) {

			/** Updating taxonomy url */

			$linkUpdate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_url='{$url}' WHERE link_relation_id='{$category_id}' AND link_type='{$postType}'");
	
		}

		$categoryUpdate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."category SET category_image='{$featureImage}', category_title='{$title}', category_description='{$description}' WHERE category_id='{$category_id}'");

		$metaUpdate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_keyword='{$meta_key}', meta_robots='{$robots}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_type='{$postType}' AND meta_relation_id='{$category_id}'");

		/** Delete tags */

		$tagDelete=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_type='{$postType}' AND tag_relation_id='{$category_id}'");

		/* Insert tags into tag table */

		$tags=explode(',', $tags);

		if(sizeof($tags)!=0) {
			for($i=0; $i<sizeof($tags); $i++) {
				if($tags!='') {
					$tagUpdate=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_name, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tags[$i]}', '{$category_id}', '{$website_user_id}', '{$website_id}', '{$postType}')");
				} else {
					$tagUpdate=TRUE;
				}
			}
		}

		if($linkUpdate && $categoryUpdate && $metaUpdate && $tagDelete && $tagUpdate) {

			mysqli_query($connection, 'COMMIT');

			$status=200;
			$msg='Category update successfully';

			$redirect='/dashboard/post/?type=category';

		} else {

			mysqli_query($connection, "ROLLBACK");

			$msg='Unable to update category';

		}

		$result = array(
			'status' =>	((isset($status)) ? $status : 206 ),
			'msg' => ((isset($msg)) ? $msg : 'Unable to update '.$postType ),
			'redirect' => ((isset($redirect)) ? $redirect : '' )
		);

		echo json_encode($result, TRUE);

	}