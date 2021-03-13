<?php

    function update($connection, $data) {

        extract($data);
        
        if($title!='') {

            if(isset($category)) {

                if($checkGallery=mysqli_query($connection, "SELECT link_id, meta_relation_id as oldLinkID, gallery_image_list FROM ".TABLE_PREFIX."gallery INNER JOIN ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_url='{$oldURL}' AND meta_type='gallery' AND gallery_id='{$id}'")) {

                    if(mysqli_num_rows($checkGallery)!=0) {

                        extract(mysqli_fetch_assoc($checkGallery));

                        require_once MODULES.'imageUploader.php';

                        if(isset($image)) {
                            
                            // Extract the single upload image string to variables

                            $thumbnailImage=(((isset($thumbnailImage)) ? $featureImage : $featureImage ));
                            $facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
                            $twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

                        } else {

                            $featureImage=$oldFeatureImage;
                            $thumbnailImage=(((isset($thumbnailImage)) ? $featureImage : $oldFeatureImage ));
                            $facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
                            $twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

                        }

                        $featurePost=((isset($featurePost)) ? 1 : 0 );
                        $in_stock=((isset($in_stock)) ? 1 : 0 );
                        $backorder=((isset($backorder)) ? 1 : 0 );
                        $postType=((isset($postType)) ? $postType : $type );
                        $currentStock=((isset($currentStock)) ? $currentStock : 0 );

                        if(isset($_FILES['gallery'])) {
                
                            require_once MODULES.'galleryUploader.php';
                            
                            if($gallery!='') {

                                $isGalleryUpdate=TRUE;

                            }
                    
                        }

                        $oldGallery=json_decode($gallery_image_list, TRUE);
                        
                        // Match old gallery db from from value
                        $newGallery=array();

                        if(isset($_POST['oldGallery'])) {

                            for($i=0; $i<sizeof($_POST['oldGallery']); $i++){
   
                                $j=$_POST['oldGallery'][$i]-1;
                                
                                $newGallery[]=$oldGallery[$j];
    
                            }

                        }

                        /** Update old gallery */

                        if(isset($isGalleryUpdate)) {

                            $newGallery=array_merge($newGallery, $images);

                        }

                        $newGallery=json_encode($newGallery, TRUE);
                            
                        /** Category */

                        $category=explode('_', $category);
                        $categoryURL=$category[1];
                        $category=$category[0];

                        /** gallery page url */

                        $url=strtolower('/gallery'.$categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                                
                        /* define facebook and twitter meta desciption */

                        $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                        $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                        /** Start updating in gallerys */

                        mysqli_query($connection, "START TRANSACTION");

                        if($oldURL!=$url) {
                        
                            if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_type='gallery' AND link_site_id='".SITE_ID."'")) {
                    
                                if(mysqli_num_rows($checkURL)==0) {
                    
                                    /** Add 301 redirect */
                    
                                    $addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_type='gallery' AND link_site_id='".SITE_ID."'");
                    
                                    /** Add new active url of the post */
                    
                                    $updateLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$postType}', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), '{$featurePost}')");
                                    $link_id=mysqli_insert_id($connection);
                    
                                } else {
                    
                                    $addRedirect=FALSE;
                                    $status='warning';
                                    $msg="Please change post url. Because its already in used";
                    
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

                        $updateGallery=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."gallery SET gallery_image='{$featureImage}', gallery_thumbnail='{$thumbnailImage}', gallery_title='{$title}', gallery_description='{$description}', gallery_category='{$category}', gallery_owner_id='".USER_ID."', gallery_status='{$postStatus}', gallery_type='{$postType}', gallery_image_list='{$newGallery}' WHERE gallery_id='{$id}'");

                        $updateMeta=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$link_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$oldLinkID}'");
                            
                        $deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$id}' AND tag_site_id=".SITE_ID." AND tag_type='{$postType}'");

                        /* Insert tags into tag table */

                        if($tags!='') {

                            $tags=explode(',', $tags);

                            for($i=0; $i<sizeof($tags); $i++) {

                                $tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                $updateTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$id}', '".USER_ID."', '".SITE_ID."', '{$postType}')");
                
                            }

                        } else {

                            $updateTags=TRUE;

                        }

                        if($updateGallery && $addRedirect && $updateMeta && $updateTags) {

                            $status=200;
                            $msg='Gallery upload successfully';
                            $redirect="/dashboard/gallery/?type=edit&editor=true&id=".base64_encode($id);
                            mysqli_query($connection, "COMMIT");

                        } else {

                            $msg='Something is wrong while updateing gallery';
                            mysqli_query($connection, "ROLLBACK");

                        }

                    } else {

                        $msg='Invalid value received';

                    }

                }

            } else {

                $msg='Please select category';

            }

        } else {

            $msg='Please enter gallery name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);

    }