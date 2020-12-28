<?php

    $tag_type='';
    $tag_description='';

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