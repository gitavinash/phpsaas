<?php

    require_once LOGIN_CHECK;

    function publish($connection, $data) {

        extract($data);
        
        if($title!='') {

            if(isset($category)) {

                require_once MODULES.'imageUploader.php';

                if(isset($image['featureImage'])) {

                    // Extract the single upload image string to variables

                    $featurePost=((isset($featurePost)) ? 1 : 0 );
        
                    $facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
                    $twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

                    /** Category */

                    $category=explode('_', $category);
                    $categoryURL=$category[1];
                    $category=$category[0];

                    /** post page url */

                    $url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                    
                    /* define facebook and twitter meta desciption */

                    $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                    $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                    if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

                        if(mysqli_num_rows($checkURL)==0) {

                            require_once EDITOR_CONTENT_FILTER;

                            $link_status=200;

                            /** Start uploading the post */

                            mysqli_query($connection, "START TRANSACTION");

                            /* Insert data into link table */

                            $addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_image, post_thumbnail, post_title, post_description, post_category, post_owner_id, post_status, post_type) VALUES('{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', '".USER_ID."', '1', 'post')");
                            $postID=mysqli_insert_id($connection);

                            $addNewLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '{$link_status}', 'post', '{$featurePost}', ".USER_ID.", '".SITE_ID."', NOW() ,NOW())");
                            $linkID=mysqli_insert_id($connection);

                            $addNewMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', ".APP_SITE_ID.", '{$meta_title}', 'post', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                            /* Insert tags into tag table */

                            if($tags!='') {

                                $tags=explode(',', $tags);

                                for($i=0; $i<sizeof($tags); $i++) {

                                    $tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                    $addNewTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$postID}', '".USER_ID."', '".SITE_ID."', 'post')");
                                }

                            } else {

                                $addNewTags=TRUE;

                            }

                            if($addNewPost && $addNewLink && $addNewMeta && $addNewTags) {

                                $status=200;
                                $msg='post add successfully';
                                $redirect='/dashboard/post/?type=edit&editor=true&id='.base64_encode($postID);
                                mysqli_query($connection, "COMMIT");

                            } else {

                                $msg='Something is wrong while adding new post';

                                mysqli_query($connection, "ROLLBACK");

                            }

                        } else {

                            $msg='Please change url';

                        }

                    } else {

                        $msg='Technical errro';

                    }

                } else {

                    $msg='Please select post fearure image.';

                }

            } else {

                $msg='Please select category';

            }

        } else {

            $msg='Please enter post name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);

    }