<?php

    $startLimit=mysqli_query($connection, (isset($_GET['from'])) ? $_GET['from'] : 0 );
    $endLimit=mysqli_query($connection, (isset($_GET['to'])) ? $_GET['to'] : PER_PAGE_LIMIT );

    echo $latestNewsQuery="SELECT link_url, news_image, news_title, category_title FROM isw_link INNER JOIN isw_news ON link_relation_id=news_id INNER JOIN isw_category ON news_category=category_id WHERE link_status=200 AND link_is_featured=1 AND link_type='news' AND link_site_id=1 AND news_status=1 ORDER BY link_modified DESC LIMIT {$startLimit}, {$endLimit}";

    //echo json_encode($msg, TRUE);