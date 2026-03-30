<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <div class="categoryHeading">
                    <h1>
                        <?php
                        if (isset($authorArticles[0]['news_owner_name'])) {
                            echo htmlspecialchars($authorArticles[0]['news_owner_name']);
                        } else {
                            echo 'Author';
                        }
                        ?>
                    </h1>
                </div>
                <?php
                if (!empty($authorArticles)) {
                    foreach ($authorArticles as $article) {
                        $news_title = $article['news_title'] ?? '';
                        $news_image = $article['news_image'] ?? '';
                        $news_paid = $article['news_paid'] ?? 0;
                        $news_published = $article['news_published'] ?? '';
                        $meta_description = $article['meta_description'] ?? '';
                        $link_url = $article['link_url'] ?? '#';
                        echo "<div class='news_box'><a href='{$link_url}'>
                                <div class='feature_image'>
                                    <img src='{$news_image}' alt='" . str_replace("'", "", str_replace('"', '', $news_title)) . "'>
                                    " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
                                </div>
                                <div class='news_intro'>
                                    <h2>{$news_title}</h2>
                                    <p><span>" . (!empty($news_published) ? date('d M Y, H:i A', strtotime($news_published)) : '') . "</span></p>
                                    <p>{$meta_description}</p>
                                </div>
                            </a></div>";
                    }
                } else {
                    echo '<p>No articles found for this author.</p>';
                }
                ?>
                <?php require_once 'pagination.php'; ?>
            </div>
            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>