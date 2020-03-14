<?php

    $keyword=str_replace('-', ' ', mysqli_real_escape_string($connection, $_GET['q']));

    if($searchList=mysqli_query($connection, "SELECT DISTINCT(link_relation_id), link_status, link_published, link_url, news_image, news_title, meta_description FROM ".TABLE_PREFIX."news LEFT JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id LEFT JOIN ".TABLE_PREFIX."tag ON tag_relation_id=news_id LEFT JOIN ".TABLE_PREFIX."meta ON meta_relation_id=news_id WHERE meta_type='news' AND tag_type='news' AND link_type='news' AND link_status=200 AND news_title LIKE '%{$keyword}%' OR tag_title='{$keyword}' ORDER BY link_published DESC LIMIT 0, 30")) {

        if(mysqli_num_rows($searchList)!=0) {

            while($seachPostData=mysqli_fetch_assoc($searchList)) {

                $searchPost[]=$seachPostData;

            }

            $seachPath=USER_PATH.$keyword.'-search.php';

            /** Include Tag view template */

            if(file_exists($seachPath)) {

                require_once $seachPath;

            } else {

                require_once USER_PATH.'search.php';

            }

        } else {

            /** Search using post title */

            echo 'No Result Found';

        }

    }

	exit();