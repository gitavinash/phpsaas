<?php

	/* Save Draft */

	function add($connection, $data) {

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

			mysqli_query($connection, "START TRANSACTION");

			/* Insert data into link table */
			$draftPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."{$postType}({$postType}_image, {$postType}_title, {$postType}_description, {$postType}_category, {$postType}_owner_id, {$postType}_status, {$postType}_type) VALUES('{$featureImage}', '{$title}', '{$description}', '{$category}', '{$website_user_id}', 0, '{$postType}')");
			$newDraftID=mysqli_insert_id($connection);

			$linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation) VALUES('{$url}', '{$newDraftID}', 206, '{$postType}', '{$featurePost}', '{$website_user_id}', '".SITE_ID."', NOW())");
			$metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, meta_canonical, meta_keyword, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$newDraftID}', '{$meta_title}', '{$postType}', '{$meta_desc}', '{$canonical}', '{$meta_key}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

		
			/* Insert tags into tag table */
				
			$tags=explode(',', $tags);

			$tagData=TRUE;

			for($i=0; $i<sizeof($tags); $i++) {
				if($tags[$i]!='') {
					$tagName=rtrim(ltrim($tags[$i], ' '), ' ');
					$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$newDraftID}', '{$website_user_id}', '".SITE_ID."', '{$postType}')");
				}
			}

			if($draftPost && $linkData && $metaData && $tagData) {
				
				mysqli_query($connection, "COMMIT");
				header("Location: /dashboard/{$postType}/?type=edit&editor=true&id=$newDraftID");

			} else {
				
				echo mysqli_error($connection);
				mysqli_query($connection, "ROLLBACK");
				
			}

		} else { ?>
				<script type='text/javascript'>
					alert('<?php echo $postType ?> Drafted');
				</script>
			<?php
			header("Location: /dashboard/{$postType}/?type=add&editor=true");

		}

		exit();

	}

	function update($connection, $data) {

		require_once MODULES.'imageUploader.php';
		
		extract($data);

		if(isset($featurePost)) {
			$featurePost=1;
		} else {
			$featurePost=0;
		}

		if(isset($image)) {

			extract($image);

			$featureImage=(((isset($featureImage)) ? $featureImage : $oldFeatureImage ));
			$facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
			$twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

		} else {

			$featureImage=$oldFeatureImage;
			$facebookImage=(($oldFacebookImage=='') ? $oldFeatureImage : $oldFacebookImage);
			$twitterImage=(($oldTwitterImage=='') ? $oldFeatureImage : $oldTwitterImage);

		}

		/** encode special character into html codes */

		$description=htmlentities($description);

		$category=explode('_', $category);
		$categoryURL=str_replace('/category/', '', $category[1]);
		$category=$category[0];

		$url=strtolower($categoryURL.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

		$updatePostDraft=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."news SET news_image='{$featureImage}', news_title='{$title}', news_description='{$description}', news_category='{$category}', news_owner_id='{$website_user_id}', news_status='0' WHERE news_id='{$post_id}'");
		
		if($oldURL==$url) {

			$updateLinkDraft=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_url='{$url}', link_status='206', link_modified='now()', link_is_featured='{$featurePost}' WHERE link_relation_id='{$post_id}' AND link_type='{$postType}' AND link_site_id='".SITE_ID."'");

		} else {
		
			if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

				if(mysqli_num_rows($checkURL)==0) {

					/** Add 301 redirect */

					$addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");

					/** Add new active url of the post */

					$updateLinkDraft=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$post_id}', 206, '{$postType}', '{$website_user_id}', '".SITE_ID."', NOW(), NOW(), '{$featurePost}')");

				} else {

					$msg="Please change {$postType} url";

				}

			} else {

				echo mysqli_error($connection);

			}

		}

		$updateMetaDraft=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_keyword='{$meta_key}', meta_relation_id='{$post_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$post_id}'");

		$deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$post_id}' AND tag_site_id='".SITE_ID."' AND tag_type='{$postType}' AND tag_site_id='".SITE_ID."'");

		$tags=explode(',', $tags);

		/* Insert tags into tag table */

		for($i=0; $i<sizeof($tags); $i++) {
			if($tags!='') {
				$tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tags[$i]}', '{$post_id}', '{$website_user_id}', '".SITE_ID."', '{$postType}')");
			} else {
				$tagData='';
			}
		}

		$meta_key=explode(',', $meta_key);
		
		for($i=0; $i<sizeof($meta_key); $i++) {
			if($meta_key!='') {
				$metaKeyData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$meta_key[$i]}', '{$post_id}', '{$website_user_id}', '".SITE_ID."', 'keyword')");
			} else {
				$metaKeyData='';
			}
		}

		if($updatePostDraft && $updateLinkDraft && $updateMetaDraft && $deleteTags && $tagData && $metaKeyData) { ?>
			<script type='text/javascript'>
				alert('Draft update successfull');
			</script>
			<?php
			mysqli_query($connection, "COMMIT");
			header("Location: /dashboard/{$postType}/?type=edit&editor=true&id=$post_id");

		} else { ?>
			<script type='text/javascript'>
				alert('Error while trying to update draft');
			</script>
			<?php
			mysqli_query($connection, "ROLLBACK");
			header("Location: /dashboard/{$postType}/?type=edit&editor=true&id=$post_id");

		}

	}