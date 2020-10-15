<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        require_once MODULES.'imageUploader.php';
		
		extract($data);

        $featurePost=((isset($featurePost)) ? 1 : 0 );

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
			$thumbnailImage=$oldFeatureImage;
			$facebookImage=(($oldFacebookImage=='') ? $oldFeatureImage : $oldFacebookImage);
			$twitterImage=(($oldTwitterImage=='') ? $oldFeatureImage : $oldTwitterImage);

		}

		$url='/'.$taxonomyType.'/'.strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';

        /** Check valid information of taxanomy */
        
        if($pageDetails=mysqli_query($connection, "SELECT {$taxonomyType}_id, link_id FROM ".TABLE_PREFIX."{$taxonomyType}, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta WHERE link_type='{$taxonomyType}' AND link_relation_id={$taxonomyType}_id AND meta_relation_id=link_id AND {$taxonomyType}_id='{$id}' AND link_status!=301 AND meta_type='{$taxonomyType}' AND link_type='{$taxonomyType}' LIMIT 1")) {

            if(mysqli_num_rows($pageDetails)!=0) {

                extract(mysqli_fetch_assoc($pageDetails));
                $oldLinkID=$link_id;

                // Update taxonomy code start

                mysqli_query($connection, "START TRANSACTION");

                if($oldURL!=$url) {

                    if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_type='{$taxonomyType}' AND link_site_id='".SITE_ID."'")) {
                    
                        if(mysqli_num_rows($checkURL)==0) {
                    
                            /** Add 301 redirect */
                    
                            $addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_type='{$taxonomyType}' AND link_site_id='".SITE_ID."'");
                    
                            /** Add new active url of the post */
                    
                            $updateLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$taxonomyType}', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), '{$featurePost}')");
                            $link_id=mysqli_insert_id($connection);
                    
                        } else {
                    
                            $addRedirect=FALSE;
                            $status='warning';
                            $msg="Please change {$taxonomyType} url. Because its already in used";
                    
                        }
                    
                    }
                    
                } else {
                    
                    $addRedirect=TRUE;
                    
                    if($postStatus==0) {

                        $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$taxonomyType}' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
                    
                    } else {
                    
                        $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_is_featured='{$featurePost}', link_modified=NOW() WHERE link_relation_id='{$id}' AND link_type='{$taxonomyType}' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
                    
                    }
                    
                }
                    
                /** Update post table */

                $updatePost=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."{$taxonomyType} SET {$taxonomyType}_image='{$featureImage}', {$taxonomyType}_thumbnail='{$thumbnailImage}', {$taxonomyType}_title='{$title}', {$taxonomyType}_description='{$description}', {$taxonomyType}_status='1' WHERE {$taxonomyType}_id='{$id}' AND {$taxonomyType}_type='{$postType}'");
                    
                /** Update meta table */
                
                $updateMeta=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$link_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_type='{$taxonomyType}' AND meta_relation_id='{$oldLinkID}'");
                    
                /** Manage Tags  */
                    
                $deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$id}' AND tag_site_id=".SITE_ID." AND tag_type='{$taxonomyType}'");
                $tags=explode(',', $tags);
                
                /* Insert tags into tag table */
                    
                for($i=0; $i<sizeof($tags); $i++) {
                    
                    if($tags!='') {
                            
                        $tagName=strtolower($tags[$i]);
                        $tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$id}', '".USER_ID."', '".SITE_ID."', '{$taxonomyType}')");
                    
                    } else {
                        
                        $tagData='';
                        
                    }
                    
                }
                    
                if($addRedirect && $updateLink && $updatePost && $updateMeta && $tagData) {
                    
                    mysqli_query($connection, 'COMMIT');
                        
                    $status=200;
                    $msg=$taxonomyType.' is update successfully';
                    $redirect="/dashboard/{$postType}/?type=taxonomy&editor=true&id={$id}&taxonomy={$taxonomyType}";
                    
                } else {

                    // echo $addRedirect.' && '.$updateLink.' && '.$updatePost.' && '.$updateMeta.' && '.$tagData;
                    mysqli_query($connection, 'ROLLBACK');
                    
                }

            } else {

                $msg='Invalid parameters received';

            }

        }

		$result = array(
			'status' =>	((isset($status)) ? $status : 'error' ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating '.$taxonomyType ),
			'redirect' => ((isset($redirect)) ? $redirect : '' )
		);

		echo json_encode($result, TRUE);

    }