<?php

    function publish($connection, $data) {

        extract($data);
        
        if($title!='') {

            if(isset($category)) {

                require_once MODULES.'imageUploader.php';

                if(isset($image['featureImage'])) {

                    // Extract the single upload image string to variables

                    extract($image);

                    $featurePost=((isset($featurePost)) ? 1 : 0 );
        
                    $facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
                    $twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

                    if(isset($_FILES['gallery'])) {
        
                        require_once MODULES.'galleryUploader.php';

                        /** Category */

                        $category=explode('_', $category);
                        $categoryURL=$category[1];
                        $category=$category[0];

                        /** product page url */

                        $url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                        
                        /* define facebook and twitter meta desciption */

                        $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                        $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                        if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

                            if(mysqli_num_rows($checkURL)==0) {

                                require_once EDITOR_CONTENT_FILTER;

                                /** Start uploading the product */

                                mysqli_query($connection, "START TRANSACTION");

                                /* Insert data into link table */

                                $addNewProduct=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."{$postType}({$postType}_image, {$postType}_thumbnail, {$postType}_title, {$postType}_description, {$postType}_category, {$postType}_owner_id, {$postType}_status, {$postType}_type) VALUES('{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', '{$website_user_id}', '{$post_status}', '{$postType}')");
                                $productID=mysqli_insert_id($connection);

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

                                if($tes) {

                                    $status=200;
                                    $msg='Product add successfully';
                                    $redirect='/dashboard/product/?type=add&editor=true&id='.$productID;
                                    mysqli_query($connection, "COMMIT");

                                } else {

                                    $msg='Something is wrong while adding new product';

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

                    $msg='Please select product fearure image.';

                }

            } else {

                $msg='Please select category';

            }

        } else {

            $msg='Please enter product name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'gallery' => ((isset($gallery)) ? $gallery : '' )
        );
    
        echo json_encode($result, TRUE);

    }