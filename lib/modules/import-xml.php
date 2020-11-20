<?php

    $xmlFileData = simplexml_load_string($fileData, 'SimpleXMLElement', LIBXML_NOCDATA);;

    $json_convert=json_encode($xmlFileData);

    $jsonData=json_decode($json_convert, TRUE);

    $totalArticles=$jsonData['channel']['item'];

    for($i=0; $i<sizeof($totalArticles); $i++) {

        $title=$totalArticles[$i]['title'];
        $link=$totalArticles[$i]['link'];
        $featureImage=$totalArticles[$i]['fullimage'];
        $description=$totalArticles[$i]['description'];
        $metaDescription=$totalArticles[$i]['excerpt'];
        $tags=$totalArticles[$i]['tags'];
        $publishDate=date('Y-m-d H:i:s', strtotime($totalArticles[$i]['pubDate']));
        $modifiedDate=date('Y-m-d H:i:s', strtotime($totalArticles[$i]['modifiedDate']));

        /** Update article to current website's link */

        $link=parse_url($link);
        $link=$categoryUrl.ltrim($link['path'].((isset($link['query'])) ? $link['query'] : '' ), '/');

        /** Check if featureimage variable is empty */

        if(!is_array($featureImage)) {

            /** Create Current Month Directory  */

            $uploadPath=ROOT.'/upload/'.date('Y/m/');

            if(!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
                                
            }

            $featureImageName=explode('/', $featureImage);

            $featureImageName=str_replace(' ', '_', end($featureImageName));
            
            /** Image File Name Without Extension */

            $featureImageFileName=explode('.', $featureImageName);
            $featureImageFileExtension=end($featureImageFileName);
            $featureImageFileName=str_replace($featureImageFileExtension, '', preg_replace('/[^a-zA-Z0-9\']/', ' ', $featureImageName));
            
            $localFeatureImage=$uploadPath.time().'_'.$featureImageName;

            /** Compress Image */

            if(!is_dir($localFeatureImage)) {

                $info = getimagesize($featureImage);

                if ($info['mime'] == 'image/jpeg') 
                    $image = imagecreatefromjpeg($featureImage);

                elseif ($info['mime'] == 'image/gif') 
                    $image = imagecreatefromgif($featureImage);

                elseif ($info['mime'] == 'image/png') 
                    $image = imagecreatefrompng($featureImage);

                if(imagejpeg($image, $localFeatureImage, 60)) {

                    $featureImage=str_replace(ROOT, '', $localFeatureImage);

                    /** Insert media data into database */

                    mysqli_query($connection, 'START TRANSACTION');

                    $insertMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_status, media_type, media_ext, media_owner_id, media_site_id) VALUES('{$featureImage}', '{$featureImageFileName}', '1', 'image', '{$featureImageFileExtension}', '{$website_user_id}', '{$website_id}')");
                    $mediaID=mysqli_insert_id($connection);

                    /** Create media public link */

                    $mediaPublicURL=str_replace('.'.$featureImageFileExtension, '', '/attachment/'.time().'_'.$featureImageName.'/');
                    $insertMediaLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$mediaPublicURL}', '{$mediaID}', '200', 'media', '{$website_user_id}', '{$website_id}', NOW(), NOW())");

                    if($insertMedia && $insertMediaLink) {

                        mysqli_query($connection, 'COMMIT');

                    } else {

                        echo 'Error while inserting image';

                        mysqli_query($connection, 'ROLLBACK');

                        exit();

                    }

                }


            }

        }

        /** Start import data into database */

        /** Check url is avaiable or not */

        if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$link}' AND link_site_id=".SITE_ID."")) {

            if(mysqli_num_rows($checkURL)!=0) {

                $link=rtrim($link, '/').time().'/';

            }

        }

        mysqli_query($connection, 'START TRANSACTION');

        /** Insert Data into post table */        

        $insertPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_image, post_title, post_description, post_category, post_owner_id, post_status) VALUES('{$featureImage}', '{$title}', '{$description}', '{$categoryID}', '{$website_user_id}', 1)");
        $postID=mysqli_insert_id($connection);

        /** Insert data into link table and create post */

        $insertLink=mysqli_query($connection , "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$link}', '{$postID}', '200', 'post', '{$website_user_id}', '{$website_id}', '{$publishDate}', '{$publishDate}', '{$modifiedDate}')");
        $linkID=mysqli_insert_id($connection);

        /** Insert Data into meta table */

        $insertMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_site_id,  meta_type, meta_title, meta_description, meta_keyword) VALUES('{$linkID}', 'post', '{$title}', '{$metaDescription}', '{$tags}')");

        /** Insert Tags into tag table */

        $tags=explode(',', $tags);

        if(sizeof($tags)!=0) {

            for($j=0; $j<sizeof($tags); $j++) {

                $insertTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tags[$j]}', '{$postID}', '{$website_user_id}', '{$website_id}', 'post')");

            }

        } else {

            $insertTags=TRUE;

        }

        $increseCategory=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."category SET category_total_count=category_total_count+1 WHERE category_id='{$categoryID}' ");

        if($insertPost && $insertLink && $insertMeta && $insertTags && $increseCategory) {

            $status=200;
            $msg='Data import successfully';

            mysqli_query($connection, 'COMMIT');

        } else {

            mysqli_query($connection, 'ROLLBACK');
            $msg='Error while impoting data';

        }

    }