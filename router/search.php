<?php

    $keyword=str_replace('-', ' ', mysqli_real_escape_string($connection, $_GET['q']));

    if($searchList=mysqli_query($connection, "SELECT DISTINCT(link_relation_id), link_status, link_published, link_url, post_image, post_title, meta_description FROM isw_post LEFT JOIN isw_link ON link_relation_id=post_id LEFT JOIN isw_tag ON tag_relation_id=post_id LEFT JOIN isw_meta ON meta_relation_id=post_id WHERE meta_type='post' AND tag_type='post' AND link_type='post' AND link_status=200 AND post_title LIKE '%{$keyword}%' OR tag_name='{$keyword}' ORDER BY link_published DESC LIMIT 0, 30")) {

        if(mysqli_num_rows($searchList)!=0) {

            while($seachPostData=mysqli_fetch_assoc($searchList)) {

                $searchPost[]=$seachPostData;

            }

            $seachPath=USER_VIEW_PATH.$keyword.'-search.php';

            /** Include Tag view template */

            if(file_exists($seachPath)) {

                require_once $seachPath;

            } else {

                require_once USER_VIEW_PATH.'search.php';

            }

        } else {

            /** Search using post title */

            echo 'No Result Found';

        }

    }

	exit();