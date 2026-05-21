<?php

require_once 'header.php';

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

function escape($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function articleImage($image)
{
    return !empty($image)
        ? $image
        : 'https://via.placeholder.com/800x600?text=No+Image';
}

function authorImage($image)
{
    return !empty($image)
        ? $image
        : 'https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg';
}

/*
|--------------------------------------------------------------------------
| Featured Article Query
|--------------------------------------------------------------------------
*/

$featuredQuery = "
SELECT
    profile_display_name,
    profile_image,
    post_image,
    post_title,
    link_url,
    LEFT(
            TRIM(
                REGEXP_REPLACE(
                    REPLACE(
                        REPLACE(post_description, '&nbsp;', ' '),
                        '\r\n',
                        ' '
                    ),
                    '<[^>]*>',
                    ''
                )
            ),
            100
        ) AS expert,
    category_title,
    link_published
FROM " . TABLE_PREFIX . "link
INNER JOIN " . TABLE_PREFIX . "post
    ON link_relation_id = post_id
    AND post_site_id = " . SITE_ID . "
INNER JOIN " . TABLE_PREFIX . "category
    ON category_id = post_category
INNER JOIN " . TABLE_PREFIX . "profile
    ON link_owner_id = profile_user_id
WHERE
    link_status = 200
    AND link_type = 'post'
    AND link_site_id = " . SITE_ID . "
ORDER BY
    link_is_featured DESC,
    link_published DESC
LIMIT 1
";

$featuredResult = mysqli_query($connection, $featuredQuery);

?>

<!-- Journal Hero -->
<section class="journal-hero light-teal-bg">
    <div class="container reveal-on-load">
        <span class="editorial-tag">Editorial</span>

        <h1 class="hero-premium-headline mb-20">
            The Hue <span class="hero-premium-hues">Journal</span>
        </h1>

        <p class="journal-desc reveal-on-load">
            Explorations in color psychology, interior styling, and personal
            alignment. Read the latest insights from our experts.
        </p>
    </div>
</section>

<?php

/*
|--------------------------------------------------------------------------
| Featured Article Section
|--------------------------------------------------------------------------
*/

if ($featuredResult && mysqli_num_rows($featuredResult) > 0) {

    $featuredArticle = mysqli_fetch_assoc($featuredResult);

?>

<section class="featured-post-section">
    <div class="container reveal">
        <div class="featured-post-card">

            <!-- Image Side -->
            <div class="featured-img-col">
                <a href="<?= escape($featuredArticle['link_url']); ?>" class="featured-img-link">
                    <img src="<?= articleImage($featuredArticle['post_image']); ?>"
                        alt="<?= escape($featuredArticle['post_title']); ?>" loading="eager" decoding="async">
                </a>
            </div>

            <!-- Text Side -->
            <div class="featured-text-col">

                <a href="javascript:void(0);" class="category-tag">
                    <?= escape($featuredArticle['category_title']); ?>
                </a>

                <a href="<?= escape($featuredArticle['link_url']); ?>" class="featured-title-link">
                    <h2 class="serif-normal">
                        <?= escape($featuredArticle['post_title']); ?>
                    </h2>
                </a>

                <p class="blog-excerpt">
                    <?= escape($featuredArticle['expert']); ?>
                </p>

                <div class="blog-meta">

                    <div class="author-info">
                        <img src="<?= authorImage($featuredArticle['profile_image']); ?>"
                            alt="<?= escape($featuredArticle['profile_display_name']); ?>" class="author-avatar"
                            loading="lazy" decoding="async">

                        <div class="author-text">
                            <p class="author-name">
                                <?= escape($featuredArticle['profile_display_name']); ?>
                            </p>

                            <p class="author-date">
                                <?= date('d M Y', strtotime($featuredArticle['link_published'])); ?>
                            </p>
                        </div>
                    </div>

                    <a href="<?= escape($featuredArticle['link_url']); ?>" class="btn-primary light-pink-bg">
                        Read Article
                    </a>

                </div>

            </div>

        </div>
    </div>
</section>

<?php
}

/*
|--------------------------------------------------------------------------
| Latest Articles Query
|--------------------------------------------------------------------------
*/

$latestQuery = "
SELECT
    post_image,
    post_title,
    link_url,
    LEFT(
            TRIM(
                REGEXP_REPLACE(
                    REPLACE(
                        REPLACE(post_description, '&nbsp;', ' '),
                        '\r\n',
                        ' '
                    ),
                    '<[^>]*>',
                    ''
                )
            ),
            100
        ) AS expert,
    category_title
FROM " . TABLE_PREFIX . "link
INNER JOIN " . TABLE_PREFIX . "post
    ON link_relation_id = post_id
    AND post_site_id = " . SITE_ID . "
INNER JOIN " . TABLE_PREFIX . "category
    ON category_id = post_category
WHERE
    link_status = 200
    AND link_type = 'post'
    AND link_site_id = " . SITE_ID . "
ORDER BY
    link_published DESC
LIMIT 3
";

$latestResult = mysqli_query($connection, $latestQuery);

/*
|--------------------------------------------------------------------------
| Latest Articles Section
|--------------------------------------------------------------------------
*/

if ($latestResult && mysqli_num_rows($latestResult) > 0) {

?>

<!-- Blog Grid Content -->
<section class="blog-list-section">

    <div class="container reveal">

        <!-- Search -->
        <div class="search-form-container">
            <form action="/search/" method="GET" class="search-form">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" name="q" placeholder="Search articles..." autocomplete="off">

                <button type="submit" class="btn-primary search-btn">
                    Search
                </button>

            </form>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid">

            <?php

                while ($article = mysqli_fetch_assoc($latestResult)) {

                ?>

            <div class="blog-card">

                <div class="blog-card-img-col">
                    <img src="<?= articleImage($article['post_image']); ?>" alt="<?= escape($article['post_title']); ?>"
                        loading="lazy" decoding="async">
                </div>

                <div class="blog-card-content-col">

                    <div class="blog-card-text">

                        <span class="category-tag-small">
                            <?= escape($article['category_title']); ?>
                        </span>

                        <h3 class="serif-normal">
                            <?= escape($article['post_title']); ?>
                        </h3>

                        <p class="blog-card-excerpt">
                            <?= escape($article['expert']); ?>
                        </p>

                    </div>

                    <a href="<?= escape($article['link_url']); ?>" class="read-more-link">
                        Read More
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>

            <?php
                }

                ?>

        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <a href="/blog-category/" class="btn-primary corumdum-blum-bg">
                Load More Articles
            </a>
        </div>

    </div>

</section>

<?php
}

require_once 'footer.php';

?>