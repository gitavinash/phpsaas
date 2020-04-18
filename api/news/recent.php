<?php

    $type=((isset($secure['type']) ? mysqli_real_escape_string($connection, $secure['type']) : SITE_APP ));

    if($recentArticle=mysqli_query($connection, "SELECT link_url, {$type}_image, {$type}_title, meta_description FROM ".TABLE_PREFIX."link FORCE INDEX (link_filter) INNER JOIN ".TABLE_PREFIX."{$type} ON link_relation_id={$type}_id INNER JOIN ".TABLE_PREFIX."meta ON {$type}_id=meta_relation_id WHERE link_status=200 AND link_is_featured=1 AND link_type='{$type}' AND link_site_id=".SITE_ID." AND {$type}_status=1 AND meta_type='{$type}' ORDER BY link_published DESC LIMIT 0, 35")) {
        
        if(mysqli_num_rows($recentArticle)) {

            $status=200;
            $msg='Data is successfully received by server';
            $recentArticleData=array();

            while($recentArticleData=mysqli_fetch_assoc($recentArticle)) {

                $recentArticleList[]=$recentArticleData;

            }

        } else {
            $msg='Not Found';
        }

    } else {
        $msg="SELECT link_url, {$type}_image, {$type}_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."{$type} ON link_relation_id={$type}_id INNER JOIN ".TABLE_PREFIX."meta ON {$type}_id=meta_relation_id WHERE link_status=200 AND link_is_featured=1 AND link_type='{$type}' AND link_site_id=".SITE_ID." AND {$type}_status=1 AND meta_type='{$type}' ORDER BY link_modified DESC LIMIT 0, 35";
    }

    $result=array(
        'status' => ((isset($status)) ? $status : 206 ),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while fetching data from server'),
        'articles' => $recentArticleList
    );

    echo json_encode($result, TRUE);