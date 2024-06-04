<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <?php

            if ($newsArticles = mysqli_query($connection, "SELECT link_is_featured, link_modified, news_title, news_image, news_description, news_paid, link_url, link_published, meta_description 
FROM " . TABLE_PREFIX . "news 
INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
WHERE link_type = 'news' 
AND link_status = 200 
AND link_site_id = " . SITE_ID . " 
ORDER BY link_is_featured DESC, link_published DESC
LIMIT 0, 10")) {

                if (mysqli_num_rows($newsArticles) != 0) {

                    while ($newsData = mysqli_fetch_assoc($newsArticles)) {

                        extract($newsData);

                        echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('M d, Y', strtotime($link_published)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                } else {

                    echo "<p>No news found</p>";
                }
            } else {

                echo mysqli_error($connection);
                exit();
            }

            ?>
        </div>
    </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>