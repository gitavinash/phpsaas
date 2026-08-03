<?php

require_once 'header.php';

$authorDetails = newsAuthorInfo(
    $connection,
    $news_id,
    [
        'id'    => $news_owner_id,
        'name'  => $profile_display_name,
        'image' => $profile_image,
    ]
);

$relatedNews = similarArticles($connection, $news_id, 'news', 5);

?>
<!-- SINGLE ARTICLE PAGE CONTENT -->
<div id="view-article" class="page-view active">
    <div class="article-body-container">
        <div
            class="article-hero-img" style="background-image: url(<?php echo $news_image ?>);">
            <a href="#" id="bookmarkNews" data-id="<?php echo $news_id ?>"
                class="icon-btn bookmark-floating-btn"
                aria-label="Bookmark">
                <i class="ph ph-bookmark-simple"></i>
            </a>
        </div>

        <div
            class="article-content"
            style="padding: 24px; position: relative">
            <div class="category-tag"><?php echo $category_title ?></div>
            <h1
                class="article-headline"
                style="
                  font-family: var(--font-serif);
                  font-size: 1.8rem;
                  line-height: 1.3;
                  margin-bottom: 16px;
                ">
                <?php echo $news_title ?>
            </h1>

            <div class="article-byline">

                <div class="author-avatar">
                    <?php if (!empty($authorDetails['primary']['image'])) { ?>
                        <img src="<?php echo $authorDetails['primary']['image']; ?>"
                            alt="<?php echo htmlspecialchars($authorDetails['primary']['name']); ?>">
                    <?php } else { ?>
                        <span><?php echo $authorDetails['primary']['character']; ?></span>
                    <?php } ?>
                </div>

                <div class="author-info">

                    <span class="author-name">

                        <?php foreach ($authorDetails['authors'] as $index => $author) { ?>

                            <?php if ($index > 0) { ?>
                                ,
                            <?php } ?>

                            <a href="<?php echo $author['link']; ?>">
                                <?php echo htmlspecialchars($author['name']); ?>
                            </a>

                        <?php } ?>

                    </span>

                    <span class="author-date">
                        <?php echo date('M d, Y', strtotime($link_published)); ?>
                        &bull;
                        <?php echo timeToRead($news_description); ?>
                    </span>

                </div>

            </div>

            <div class="editorial-body">
                <p class="drop-cap">

                    <?php


                    $showFullNews = false;
                    $showSubscriptionSection = false;
                    $showSignUpSection = false;

                    if (isset($_COOKIE['user_data'])) {
                        if ($activeSubscription == true) {
                            $showFullNews = true;
                        } else {
                            if ($news_paid == 1) {

                                // Check news is paid?

                                if ($checkPurchase = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id WHERE order_product_id={$link_relation_id} AND order_status=1 AND order_user_id=" . USER_ID . " AND order_site_id=" . SITE_ID . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($checkPurchase) != 0) {
                                        $showFullNews = true;
                                    } else {
                                        $showSubscriptionSection = true;
                                    }
                                }
                            } else {
                                $showFullNews = true;
                            }
                        }
                    } else {
                        if ($news_paid == 1) {
                            $showSignUpSection = true;
                        } else {
                            $showFullNews = false;
                        }
                    }

                    $userAccessType = base64_decode(((isset($_COOKIE['user_data'])) ? $_COOKIE['user_data'] : ''));

                    if ($userAccessType == 'author' || $userAccessType == 'administrator') {
                        $showFullNews = true;
                        $showSignUpSection = false;
                        $showSubscriptionSection = false;
                    }

                    if ($showFullNews == true) {
                        echo removeInlineStyles($news_description, true);
                    } else {
                        echo removeInlineStyles(trimParagraph($news_description, 300), true);
                    }

                    ?>
                </p>

                <?php

                if ($showSignUpSection == true) {
                    require_once 'subscription-now-section.php';
                }
                if ($showSubscriptionSection == true) {
                    require_once 'subscription-now-section.php';
                }

                ?>
            </div>

            <!-- RELATED NEWS -->
            <?php
            if (isset($relatedNews['articles'])) {
                if (sizeof($relatedNews['articles']) > 0) {
                    echo "
            <div class='related-news-section'>
                <h3
                    style='
                    font-family: var(--font-serif);
                    font-size: 1.3rem;
                    margin-bottom: 16px;
                  '>
                    Related News
                </h3>
                <div class='vertical-feed' style='padding: 0'>";
                    foreach ($relatedNews['articles'] as $news) {
                        echo "<a href='{$news['link_url']}' class='list-card native-list-item'>
                            <div class='list-content'>
                                <div class='list-meta'>
                                    <span class='tag-text'>{$news['category_title']}</span> &bull; " . date('M d, Y', strtotime($news['link_published'])) . "
                                </div>
                                <h4 class='list-title'>
                                    {$news['title']}
                                </h4>
                            </div>
                            <img
                                src='{$news['image']}'
                                alt='{$news['title']}'
                                class='list-img' />
                        </a>";
                    }
                    echo "
                </div>
            </div>
        </div>
    </div>";
                }
            }
            ?>

            <?php

            require_once 'footer.php';

            ?>