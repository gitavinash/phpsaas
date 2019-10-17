<?php

	/** Pagination */

	$currentPage=end($urlStructure);

	if(is_numeric($currentPage)) {

		/** Remove page no from url */
		$tag_name=str_replace('-', ' ', $urlStructure[1]);
		$currentPage=str_replace('-', ' ', $urlStructure[2]);

	} else {
		
        $tag_name=str_replace('-', ' ', $urlStructure[1]);
        $currentPage=0;

    }

    $postFrom=(($currentPage==0) ? 0 : ($currentPage-1)*PER_PAGE_LIMIT );

    //echo "SELECT DISTINCT(link_relation_id), link_status, link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."tag, ".TABLE_PREFIX."link, ".TABLE_PREFIX."post, ".TABLE_PREFIX."meta WHERE tag_name='{$tag_name}' AND link_relation_id=post_id AND link_relation_id=tag_relation_id AND tag_type='post' AND link_type='post' AND tag_relation_id=post_id AND post_id=meta_relation_id AND link_status=200 ORDER BY link_published DESC LIMIT {$postFrom}, ".PER_PAGE_LIMIT;

    if($tagList=mysqli_query($connection, "SELECT DISTINCT(link_relation_id), link_status, link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."tag, ".TABLE_PREFIX."link, ".TABLE_PREFIX."post, ".TABLE_PREFIX."meta WHERE tag_name='{$tag_name}' AND link_relation_id=post_id AND link_relation_id=tag_relation_id AND tag_type='post' AND link_type='post' AND tag_relation_id=post_id AND post_id=meta_relation_id AND link_status=200 ORDER BY link_published DESC LIMIT {$postFrom}, ".PER_PAGE_LIMIT)) {

        if(mysqli_num_rows($tagList)!=0) {

            while($tagPostData=mysqli_fetch_assoc($tagList)) {

                $tagPost[]=$tagPostData;

            }

            $tag_path=USER_VIEW_PATH.$urlStructure[1].'-category.php';

            /** Include Tag view template */

            if(file_exists($tag_path)) {

                require_once $tag_path;

            } else {

                $link_type='tag';
                $link_url=BASE_URL;

                require_once USER_VIEW_PATH.'tag.php';

            }

        } else {

            header("Location: /", TRUE, 301);

        }

    }

	exit();