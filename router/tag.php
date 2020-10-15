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

    if($tagList=mysqli_query($connection, "SELECT link_relation_id, tag_title, link_type, link_status, link_url, news_image, news_title, meta_description FROM ".TABLE_PREFIX."tag INNER JOIN ".TABLE_PREFIX."news ON news_id=tag_relation_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id INNER JOIN ".TABLE_PREFIX."link FORCE INDEX (link_filter) ON link_relation_id=news_id WHERE tag_title LIKE '%{$searchTag}%' AND link_type='news' AND link_status=200 AND tag_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, ".PER_PAGE_LIMIT)) {

        if(mysqli_num_rows($tagList)!=0) {

            while($tagPostData=mysqli_fetch_assoc($tagList)) {

                $tagPost[]=$tagPostData;

            }

            $tag_path=USER_PATH.$urlStructure[1].'-tag.php';

            /** Include Tag view template */

            if(file_exists($tag_path)) {

                require_once $tag_path;

            } else {

                $link_type='tag';
                $link_url=BASE_URL;

                if(file_exists(USER_PATH.'tag.php')) {

                    require_once USER_PATH.'tag.php';

                } else {

                    echo '<h1>Unable to find the template path</h1>';

                }

            }

        } else {

            if(file_exists(ERROR_404)) {

                require_once ERROR_404;

            } else {

                echo $tag_title;
                exit();

                echo '<h1>Unable to find the Error page template path</h1>';

            }

        }

    } else {

        echo 'i m here';

    }