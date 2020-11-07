<?php

    /** Pagination */

	$currentPage=end($urlStructure);

	if(is_numeric($currentPage)) {

		/** Remove page no from url */
		$tag_title=str_replace('-', ' ', $urlStructure[1]);
		$currentPage=str_replace('-', ' ', $urlStructure[2]);

	} else {
		
        $tag_title=str_replace('-', ' ', $urlStructure[1]);
        $currentPage=0;

    }

    $searchTag=mysqli_real_escape_string($connection, str_replace('-', ' ', $urlStructure[1]));

    if($tagList=mysqli_query($connection, "SELECT tag_title, tag_image, tag_description, tag_type FROM ".TABLE_PREFIX."tag WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=".SITE_ID." LIMIT 0, 1")) {

        if(mysqli_num_rows($tagList)!=0) {

            extract(mysqli_fetch_assoc($tagList));

            while($tagPostData=mysqli_fetch_assoc($tagList)) {

                $tagPost[]=$tagPostData;

            }

            $tag_path=USER_PATH."{$tag_type}-tag.php";

            /** Include Tag view template */

            if(file_exists($tag_path)) {

                require_once $tag_path;

            } else {
                
                require_once USER_PATH.'tag.php';

            }

        } else {

            require_once ERROR_404;

        }

    } else {

        require_once ERROR_404;

    }