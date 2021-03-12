<?php

    require_once LOGIN_CHECK;

    $openFile=fopen($_FILES['importFile']['tmp_name'], 'r');

        while(($dataLines=fgetcsv($openFile)) !== false) {

            if($i!=0) {

                $post_title=mysqli_real_escape_string($connection, $dataLines[0]);
                $images['post_images']=mysqli_real_escape_string($connection, $dataLines[1]);
                $post_description=mysqli_real_escape_string($connection, $dataLines[2]);
                $link_url=mysqli_real_escape_string($connection, $dataLines[3]);
                $link_creation=mysqli_real_escape_string($connection, $dataLines[4]);
                $link_published=mysqli_real_escape_string($connection, $dataLines[5]);

                mysqli_query($connection, "START TRANSACTION");

                $categoryID=explode('_', $categoryID);
                $categoryURL=str_replace('category/', '', $categoryID[1]);
                $categoryID=$categoryID[0];

                if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$link_url}' AND link_site_id='".SITE_ID."'")) {
                        
                    if(mysqli_num_rows($checkURL)!=0) {

                        // $url=ltrim($url, '/').'-'.time().'/';
                        $link_url=strtolower($categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $post_title)).'/';

                    }

                }

                require_once MODULES.'downloadImageFromURL.php';
                downloadImageFromURL($images);

                // downloadImageFromURL($post_image);
                exit();

                // $addNewPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_site_id, post_image, post_thumbnail, post_title, post_description, post_category, post_owner_id, post_status) VALUES('{$featureImage}', '{$featureImage}', '{$post_title}', '{$post_description}', '{$categoryID}', ".USER_ID.", 1)");

            }

            $i++;

    }

    $responseData = array(
        'success' => $success,
        'failed' => $failed,
        'dublicate' => $dublicate
    );