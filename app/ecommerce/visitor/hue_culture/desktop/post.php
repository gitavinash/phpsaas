<?php

require_once DATA_FUNCTIONS;
require_once UTIL_FUNCTIONS;
require_once 'header.php';

?>

<!-- Article Hero Section -->
<section class="article-hero">
    <div class="container reveal-on-load">
        <a href="blogs.html" class="article-category-badge"><?php echo $category_title; ?></a>

        <h1 class="hero-premium-headline">
            <?php echo $post_title ?>
        </h1>

        <div class="blog-meta">
            <img src="<?php echo ($profile_image != '') ? $profile_image : 'https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg'; ?>"
                alt="<?php echo $profile_display_name ?>" class="author-avatar" />
            <div class="article-meta-text">
                <p class="article-meta-author"><?php echo $profile_display_name ?></p>
                <p class="article-meta-date"><?php echo date('d M Y', strtotime($link_published)); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Article Cover Image -->
<section class="article-cover-section">
    <div class="container reveal-on-load">
        <img src="<?php echo $post_image ?>" alt="<?php echo $post_title ?>" class="article-cover-img" />
    </div>
</section>

<!-- Article Body -->
<section class="article-body-section">
    <div class="container reveal article-content">

        <?php echo $post_description ?>

        <?php

        if ($tagName != '') {
            $tagName = explode(', ', $tagName);

            if (sizeof($tagName) > 0) {
                echo "
        <!-- Article Footer / Tags -->
        <div class='article-footer-tags'>
            <div class='article-tags'>
                <span class='tags-label'>Tags:</span>";
                for ($i = 0; $i < sizeof($tagName); $i++) {
                    echo "<a href='/tag/" . str_replace(' ', '-', $tagName[$i]) . "/' class='tag-link'>{$tagName[$i]}</a>";
                }
                echo "
            </div>
        </div>";
            }
        }

        ?>

        <?php

        $articleList = similarArticles($connection, $post_id, 'post', 2);

        if (isset($articleList['articles'])) {
            if (sizeof($articleList['articles']) > 0) {
                echo "<div class='related-posts' style='margin-top: 40px;'>
            <h2 class='serif-normal related-posts-title'>Related Articles</h2>
            <div class='related-blog-grid'>";
                foreach ($articleList['articles'] as $article) {
                    echo "<div class='blog-card'>
                    <div class='blog-card-img-col'>
                        <a href='{$article['link_url']}'>
                            <img src='{$article['image']}'
                                alt='{$article['title']}' />
                        </a>
                    </div>
                    <div class='blog-card-content-col'>
                        <div class='blog-card-text'>
                            <span class='category-tag-small'>{$article['category_title']}</span>
                            <a href='{$article['link_url']}' style='text-decoration: none;'>
                                <h3 class='serif-normal'>{$article['title']}</h3>
                            </a>
                            <p class='blog-card-excerpt'>
                                {$article['expert']}
                            </p>
                        </div>

                        <div class='blog-card-meta-footer'>
                            <img src='" . (($article['profile_image'] != '') ? $article['profile_image'] : 'https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg') . "'
                                alt='{$article['profile_display_name']}' class='author-avatar' />
                            <div class='author-text'>
                                <p class='author-name'>{$article['profile_display_name']}</p>
                                <p class='author-date'>" . date('d M Y', strtotime($article['link_published'])) . "</p>
                            </div>
                        </div>
                    </div>
                </div>";
                }
                echo "</div>
        </div>";
            }
        }

        ?>


    </div>
</section>
<?php

require_once 'footer.php';

?>