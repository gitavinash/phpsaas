<?php

    $tag_type='';
    $tag_description='';

    /** Pagination */

    $searchTag=str_replace('-', ' ', mysqli_real_escape_string($connection, $_GET['q']));

    if($tagList=mysqli_query($connection, "SELECT tag_title, tag_relation_id, tag_image, tag_description, tag_type FROM ".TABLE_PREFIX."tag WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=".SITE_ID." ORDER BY tag_time DESC")) {

        if(mysqli_num_rows($tagList)!=0) {

            while($tagPostData=mysqli_fetch_assoc($tagList)) {

                $tagPost[]=$tagPostData;

            }

            extract($tagPost[0]); // Extract first result for page meta's

        } else {

            $tagPost='';

        }

        /** Include dedicated tag theme template */

        $tag_path=USER_PATH."{$tag_type}-search.php";

        /** Include Tag view template */

        if(file_exists($tag_path)) {

            require_once $tag_path;

        } else {
            
            require_once USER_PATH.'search.php';

        }

    } else {

        require_once ERROR_404;

    }