<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <div class="categoryHeading">
                    <h1><?php echo ucwords($tag_title) ?></h1>
                </div>
                <?php

                if (is_array($tagPost)) {

                    for ($i = 0; $i < sizeof($tagPost); $i++) {

                        $tagId[] = $tagPost[$i]['tag_relation_id'];
                    }

                    $resultTags = implode(',', $tagId);

                    if ($postList = mysqli_query($connection, "SELECT link_url, news_title, news_image, news_paid, meta_description, profile_display_name, link_published as publishedTime FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "news ON link_relation_id=news_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id INNER JOIN " . TABLE_PREFIX . "profile ON news_owner_id=profile_user_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND link_relation_id IN ({$resultTags}) ORDER BY link_published DESC LIMIT 0, 20")) {

                        if (mysqli_num_rows($postList) != 0) {

                            while ($postData = mysqli_fetch_assoc($postList)) {

                                extract($postData);

                                echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, H:i A', strtotime($publishedTime)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                            }
                        }
                    } else {
                        echo mysqli_error($connection);
                    }
                }
                ?>
                <?php

                require_once 'pagination.php';

                ?>
            </div>

            <?php

            require_once 'sidebar.php';

            ?>
        </div>
    </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>