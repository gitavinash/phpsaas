<?php

require_once 'header.php';

$authorDetails = newsAuthorInfo(
    $connection,
    $page_id,
    [
        'id'    => $page_owner_id,
        'name'  => $profile_display_name,
        'image' => $profile_image,
    ]
);

$relatedNews = similarArticles($connection, $page_id, 'news', 5);

?>
<!-- SINGLE ARTICLE PAGE CONTENT -->
<div id="view-article" class="page-view active">
    <div class="article-body-container">

        <div
            class="article-content"
            style="padding: 24px; position: relative">
            <h1
                class="article-headline"
                style="
                  font-family: var(--font-serif);
                  font-size: 1.8rem;
                  line-height: 1.3;
                  margin-bottom: 16px;
                ">
                <?php echo $page_title ?>
            </h1>

            <div class="editorial-body">
                <p class="drop-cap">
                    <?php
                    echo removeInlineStyles($page_description);
                    ?>
                </p>
            </div>

            <?php

            require_once 'footer.php';

            ?>