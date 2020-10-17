<?php
		
    /* Update Post */
    
    require_once LOGIN_CHECK;

	function update($connection, $data) {

		extract($data);
		
        if($title!='') {
            
            if(isset($category)) {
                
                require_once MODULES.'imageUploader.php';

                if(isset($featurePost)) {
                    $featurePost=1;
                } else {
                    $featurePost=0;
                }
        
                if(isset($image)) {
        
                    /** when image is upload by user */
        
                    extract($image);
        
                    $featureImage=(((isset($featureImage)) ? $featureImage : $oldFeatureImage ));
                    $thumbnailImage=(((isset($thumbnailImage)) ? $featureImage : $oldThumbnailImage ));
                    $facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
                    $twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));
        
                } else {
        
                    /** When image is not upload by user */
        
                    $featureImage=$oldFeatureImage;
                    $thumbnailImage=$featureImage;
                    $facebookImage=(($oldFacebookImage=='') ? $oldFeatureImage : $oldFacebookImage);
                    $twitterImage=(($oldTwitterImage=='') ? $oldFeatureImage : $oldTwitterImage);
        
                }
        
                /** Fetch category url */
        
                $category=explode('_', $category);
                $categoryURL=str_replace('/category/', '', $category[1]);
                $category=$category[0];
        
                $url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
        
                if($oldURL!=$url) {
        
                    if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {
        
                        if(mysqli_num_rows($checkURL)==0) {
        
                            /** Add 301 redirect */
        
                            $addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
        
                            /** Add new active url of the post */
        
                            $updateLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$postType}', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), '{$featurePost}')");
        
                        } else {
        
                            $addRedirect=FALSE;
                            $msg='Please change url';
        
                        }
        
                    }
        
                } else {
        
                    $addRedirect=TRUE;
        
                    if($postStatus==0) {
        
                        $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
        
                    } else {
        
                        $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
        
                    }
        
                }
        
                /** Update post table */
        
                $updatePost=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."post SET post_image='{$featureImage}', post_thumbnail='{$thumbnailImage}', post_title='{$title}', post_description='{$description}', post_category='{$category}', post_status='1' WHERE post_id='{$id}'");
        
                /** Update meta table */
        
                $updateMeta=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$id}'");
        
                /** Manage Tags  */
        
                $deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$id}' AND tag_site_id=".SITE_ID." AND tag_type='{$postType}'");
                $tags=explode(',', $tags);
        
                /* Insert tags into tag table */
        
                for($i=0; $i<sizeof($tags); $i++) {
        
                    if($tags!='') {
                        
                        $tagName=strtolower($tags[$i]);
                        $tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$id}', '".USER_ID."', '".SITE_ID."', '{$postType}')");
        
                    } else {
                    
                        $tagData='';
        
                    }
        
                }
        
                if($addRedirect && $updateLink && $updatePost && $updateMeta && $tagData) {
        
                    mysqli_query($connection, 'COMMIT');
        
                    $status=200;
                    $msg=$postType.' is update successfully';
                    $redirect='/dashboard/'.$postType.'/?type=edit&editor=true&id='.$id;
        
                } else {
                    
                    echo $addRedirect.' && '.$updateLink.' && '.$updatePost.' && '.$updateMeta.' && '.$tagData;

                    $msg="Somthing is wrong while updating {$postType}";
                    mysqli_query($connection, 'ROLLBACK');

        
                }

            } else {

                $msg='Please select category';

            }

        } else {

            $msg='Please enter post title';

        }

		$result = array(
			'status' =>	((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating '.$postType ),
			'redirect' => ((isset($redirect)) ? $redirect : '' )
		);

		echo json_encode($result, TRUE);

	}