<?php

    function publish($connection, $data) {

        extract($data);
        
        if($title!='') {

            if(isset($category)) {

                require_once MODULES.'imageUploader.php';

                if(isset($image['featureImage'])) {

                    $featurePost=((isset($featurePost)) ? 1 : 0 );
                    $post_status=((isset($post_status)) ? $post_status : 0 );
                    $link_status=((isset($link_status)) ? $link_status : 200 );
                    $postType=((isset($postType)) ? $postType : 'gallery' );
        
                    $facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
                    $twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

                    if(isset($_FILES['gallery'])) {
        
                        require_once MODULES.'galleryUploader.php';

                        /** Category */

                        $category=explode('_', $category);
                        $categoryURL=$category[1];
                        $category=$category[0];

                        /** gallery page url */

                        $url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                        
                        /* define facebook and twitter meta desciption */

                        $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                        $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                        if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

                            if(mysqli_num_rows($checkURL)==0) {

                                require_once EDITOR_CONTENT_FILTER;

                                /** Start uploading the gallery */

                                mysqli_query($connection, "START TRANSACTION");

                                /* Insert data into link table */

                                $addNewGallery=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."gallery(gallery_image, gallery_thumbnail, gallery_title, gallery_description, gallery_image_list, gallery_category, gallery_owner_id, gallery_status, gallery_type) VALUES('{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$gallery}', '{$category}', '".USER_ID."', '1', 'gallery')");
                                $galleryID=mysqli_insert_id($connection);

                                $addNewLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$galleryID}', '{$link_status}', '{$postType}', '{$featurePost}', '".USER_ID."', '".SITE_ID."', NOW() ,NOW())");
                                $linkID=mysqli_insert_id($connection);
                                $addNewMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', '{$meta_title}', '{$postType}', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

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

                                if($addNewGallery && $addNewLink && $addNewMeta) {

                                    $status=200;
                                    $msg='Gallery add successfully';
                                    $redirect='/dashboard/gallery/?type=edit&editor=true&id='.$galleryID;
                                    mysqli_query($connection, "COMMIT");

                                } else {

                                    $msg='Something is wrong while adding new gallery';

                                    mysqli_query($connection, "ROLLBACK");

                                }

                            } else {

                                $msg='Invalid activity';

                            }

                        } else {

                            $msg='Technical errro';

                        }
            
                    }

                } else {

                    $msg='Please select gallery feature image.';

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