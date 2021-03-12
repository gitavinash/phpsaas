<?php

    require_once LOGIN_CHECK;

    function downloadImageFromURL($image) {

        for($i=0; $i<sizeof($image); $i++) {

            $fileExtension=end($image[$i]);
            $filetype='image/'.$fileExtension;

            echo $fileExtension;

            exit();

            $uploadPath=ROOT.'/upload/'.date('Y/m/');
            $uploadFile=$uploadPath.time().'_'.strtolower(preg_replace('~[^\p{M}\w.-]+~u', '', $filename)).'.'.$fileExtension;
            $uploadMediaPath=str_replace(ROOT, '', $uploadFile);
            $uploadMediaLink='/attachment'.str_replace('.'.$fileExtension, '', $uploadMediaPath).'/';
            $uploadFileName=ucwords(preg_replace('/[_]+/', ' ',str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));

            

            $response=file_put_contents($uploadFile, file_get_contents($galleryImages[$j]));

            if($response) {

                if($j==0) {

                    $productFeaturedImage=$uploadMediaPath;

                }

                $addNewMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadMediaPath}', '{$uploadFileName}', '{$filetype}', '{$fileExtension}', '".USER_ID."', 1, '".SITE_ID."')");
                $mediaID=mysqli_insert_id($connection);
                
                $mediaLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$uploadMediaLink}', '{$mediaID}', 200, 'media', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), NOW())");

            } else {

                $msg='Unable to download image from server ';

            }

        }

    }