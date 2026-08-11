<?php

require_once 'header.php';

$oldAuthorInfo = false;
if ($authorInfo = mysqli_query($connection, "SELECT authors.profile_user_id as authorId, profile.profile_display_name as authorName FROM " . TABLE_PREFIX . "authors as authors INNER JOIN " . TABLE_PREFIX . "profile as profile ON profile.profile_user_id=authors.profile_user_id WHERE authors.rel_id={$link_relation_id} AND authors.rel_type='news' ORDER BY authors.seq_no ASC")) {
    if (mysqli_num_rows($authorInfo) != 0) {
        $allAuthors = [];
        while ($row = mysqli_fetch_assoc($authorInfo)) {
            $allAuthors[] = $row;
        }
    } else {
        $oldAuthorInfo = true;
    }
}

if ($oldAuthorInfo == true) {
    $authorProfileLink = $news_owner_id . '-' . urlencode(strtolower(str_replace(' ', '-', $profile_display_name))) . '-articles';
    $authorName = $profile_display_name;
    $authorBio = (($news_owner_id == 9423) ? "<div class='user_content'>
                <p>The author is Founder and Editor of The Head and Tale. She can be reached at
                    <a href='mailto:arti.singh@theheadandtale.com'>arti.singh@theheadandtale.com</a><br />
                    Tweets <a href='https://twitter.com/artijourno' target='_blank'
                        style='font-style: italic'>@artijourno</a>
                </p>
            </div>" : "");
    $authorHeading = "<a href='/author/{$authorProfileLink}/' target='_blank' title='{$authorName} articles'>{$authorName}</a>";
} else {
    $authorHeading = "";
    $authorLinks = [];
    foreach ($allAuthors as $author) {
        $profileLink = $author['authorId'] . '-' . urlencode(strtolower(str_replace(' ', '-', $author['authorName']))) . '-articles';
        $name = htmlspecialchars($author['authorName'], ENT_QUOTES, 'UTF-8');
        $authorLinks[] = "<a href='/author/{$profileLink}/' target='_blank' title='{$name} articles'>{$name}</a>";
    }
    $authorHeading .= implode(', ', $authorLinks);
    $authorBio = '';
}

$newsContent = strip_tags($news_description);
$newsContent = substr($newsContent, 0, ((SITE_VERSION == 'mobile') ? 111 : 130));

// Calculate Reading Time
$wordCount = str_word_count(strip_tags($news_description));
$readTime = max(1, ceil($wordCount / 200));

// Fetch Related Articles for Next Infinite Story
$newsList = similarArticles($connection, $news_id, 'news', 6);
$nextArticleUrl = '';

// Track already loaded stories to avoid duplicate infinite-scroll loops.
$seenIdsParam = isset($_GET['seen']) ? (string)$_GET['seen'] : '';
$seenIds = [];
if ($seenIdsParam !== '') {
    foreach (explode(',', $seenIdsParam) as $rawSeenId) {
        $id = (int)trim($rawSeenId);
        if ($id > 0) {
            $seenIds[$id] = true;
        }
    }
}
$seenIds[(int)$news_id] = true;

if (isset($newsList['articles']) && sizeof($newsList['articles']) > 0) {
    foreach ($newsList['articles'] as $candidate) {
        $candidateId = (int)($candidate['id'] ?? 0);
        $candidateUrl = (string)($candidate['link_url'] ?? '');

        if ($candidateId <= 0 || $candidateUrl === '') {
            continue;
        }
        if (isset($seenIds[$candidateId])) {
            continue;
        }

        $glue = (strpos($candidateUrl, '?') !== false) ? '&' : '?';
        $nextArticleUrl = $candidateUrl . $glue . 'seen=' . implode(',', array_keys($seenIds));
        break;
    }
}

?>
<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.css">
<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.css">

<div id="storyFeedContainer">
    <article class="article-detail-page" data-article-url="<?php echo SITE_URL . $link_url; ?>" data-article-title="<?php echo htmlspecialchars($news_title); ?>">
        <div class="container article-reading-container">

            <!-- Article Header -->
            <header class="article-header-section">
                <nav class="article-breadcrumb" aria-label="Breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <a href="/news/">News</a>
                    <span>/</span>
                    <span>Story</span>
                </nav>

                <span class="article-category-badge">Editorial Deepdive</span>

                <h1><?php echo htmlspecialchars($news_title); ?></h1>

                <?php if (!empty($meta_description)): ?>
                    <p class="article-lead-excerpt"><?php echo htmlspecialchars($meta_description); ?></p>
                <?php endif; ?>

                <div class="article-meta-bar">
                    <div class="article-meta-left">
                        <span class="article-meta-author">By <?php echo $authorHeading; ?></span>
                        <span class="separator">•</span>
                        <span class="article-meta-date"><?php echo date('M d, Y • h:i A', strtotime($news_published)); ?></span>
                    </div>
                    <div class="article-meta-right">
                        <span class="read-time"><i class="ph ph-book-open"></i> <?php echo $readTime; ?> min read</span>
                    </div>
                </div>
            </header>

            <!-- Hero Featured Media -->
            <?php if (!empty($news_image)): ?>
                <div class="article-hero-media">
                    <img src="<?php echo htmlspecialchars($news_image); ?>" alt="<?php echo htmlspecialchars($news_title); ?>" fetchpriority="high" decoding="async" />
                </div>
            <?php endif; ?>

            <!-- Social Share Bar -->
            <div class="social-share-container">
                <span class="social-share-label">Share Story</span>
                <div class="social-share-icons">
                    <a href="http://twitter.com/share?text=<?php echo urlencode($news_title); ?>&url=<?php echo SITE_URL . $link_url; ?>" target="_blank" class="social-share-btn" aria-label="Share on X">
                        <svg viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                        X (Twitter)
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo SITE_URL . $link_url; ?>&title=<?php echo urlencode($news_title); ?>" target="_blank" class="social-share-btn" aria-label="Share on LinkedIn">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                        </svg>
                        LinkedIn
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($news_title) . ' ' . SITE_URL . $link_url; ?>" target="_blank" class="social-share-btn" aria-label="Share on WhatsApp">
                        <svg viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>

            <!-- Article Description / Content -->
            <div class="details_box">
                <?php
                $showFullNews = false;
                $showSubscriptionSection = false;
                $showSignUpSection = false;

                if (isset($_COOKIE['user_data'])) {
                    if ($activeSubscription == true) {
                        $showFullNews = true;
                    } else {
                        if ($news_paid == 1) {
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
                        $showFullNews = true;
                    }
                }

                $userAccessType = base64_decode(((isset($_COOKIE['user_data'])) ? $_COOKIE['user_data'] : ''));

                if ($userAccessType == 'author' || $userAccessType == 'administrator') {
                    $showFullNews = true;
                    $showSignUpSection = false;
                    $showSubscriptionSection = false;
                }

                echo "<div class='details_box__content {$news_paid} " . (($showFullNews == true) ? '' : 'text-gradient') . "'>" . (($showFullNews == true) ? removeInlineStyles($news_description) : "<p>" . removeInlineStyles($newsContent) . "</p>") . "</div>";

                // Audio Player Component
                $newsAudioSource = ((isset($news_audio) && !empty($news_audio)) ? $news_audio : '');

                if (!empty($newsAudioSource)) {
                    $audioTitle   = htmlspecialchars($news_title, ENT_QUOTES, 'UTF-8');
                    $audioSource  = htmlspecialchars($newsAudioSource, ENT_QUOTES, 'UTF-8');
                    $audioArtwork = htmlspecialchars($news_image, ENT_QUOTES, 'UTF-8');
                    $audioSubscriptionCta = '/subscription/';

                    $audioAttributes = $showFullNews ? 'preload="auto"' : 'preload="metadata"';

                    $audioControlsHtml = $showFullNews
                        ? "<button type='button' class='news-audio-player__button news-audio-player__button--primary news-audio-player__button--icon' data-audio-toggle aria-label='Play audio'>
                    <i class='bx bx-play news-audio-player__icon' data-audio-toggle-icon aria-hidden='true'></i>
                </button>
                <button type='button' class='news-audio-player__button news-audio-player__button--icon' data-audio-mute aria-label='Mute audio'>
                    <i class='bx bx-volume-full news-audio-player__icon' data-audio-mute-icon aria-hidden='true'></i>
                </button>
                <span class='news-audio-player__time' data-audio-current>00:00</span>
                <input type='range' class='news-audio-player__seek' min='0' max='100' step='0.1' value='0' data-audio-seek aria-label='Seek audio playback'>
                <span class='news-audio-player__time' data-audio-total>00:00</span>"
                        : "<a href='{$audioSubscriptionCta}' class='news-audio-player__subscribe' aria-label='Subscribe now to play this audio'>Subscribe now to play this audio</a>";

                    $audioElementHtml = $showFullNews
                        ? "<audio {$audioAttributes} data-news-audio-player data-default-audio-source='{$newsAudioSource}'>
                    <source src='{$audioSource}' type='audio/mpeg'>
                    Your browser does not support the audio element.
                </audio>"
                        : '';

                    echo "
            <div class='news-audio-player' data-sticky-audio-player>
                <div class='news-audio-player__main'>
                    <div class='news-audio-player__artwork'>
                        <img src='{$audioArtwork}' alt='{$audioTitle}' data-audio-artwork>
                    </div>
                    <div class='news-audio-player__body'>
                        <div class='news-audio-player__header'>
                            <h4 class='news-audio-player__title' data-audio-title>
                                <span class='news-audio-player__title-text'>{$audioTitle}</span>
                            </h4>
                            <span class='news-audio-player__status'>
                                " . ($showFullNews ? 'Full Access' : 'Preview') . "
                            </span>
                        </div>
                        <div class='news-audio-player__controls'>
                            {$audioControlsHtml}
                        </div>
                    </div>
                </div>
                {$audioElementHtml}
            </div>";
                }

                // Image Gallery Component
                if ($showFullNews == true && !empty($news_gallery)) {
                    $galleryImages = json_decode($news_gallery, true);
                    if (is_array($galleryImages) && count($galleryImages) > 0) {
                        echo "<div class='news-gallery'>";
                        foreach ($galleryImages as $image) {
                            $imagePath = htmlspecialchars($image['path'] ?? '', ENT_QUOTES, 'UTF-8');
                            $imageName = htmlspecialchars($image['name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $imageAlt = !empty($imageName) ? $imageName : 'Gallery image';
                            echo "<figure class='news-gallery__slide'>
                            <img src='{$imagePath}' alt='{$imageAlt}'>
                            " . (!empty($imageName) ? "<figcaption class='news-gallery__caption'>{$imageName}</figcaption>" : "") . "
                          </figure>";
                        }
                        echo "</div>";
                    }
                }

                if ($showSignUpSection == true || $showSubscriptionSection == true) {
                    require_once 'subscription-now-section.php';
                }
                ?>
            </div>

            <!-- Author Bio Card -->
            <?php if (!empty($authorBio)): ?>
                <div class="author-bio-card">
                    <div>
                        <h4>About the Author</h4>
                        <?php echo $authorBio; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tags List -->
            <?php if (!empty($tagName)): ?>
                <div class="article-tags-wrapper">
                    <div class="article-tags-header">
                        <svg class="tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <span>Related Topics & Keywords</span>
                    </div>
                    <div class="tags-pill-list">
                        <?php
                        $tagArray = explode(', ', $tagName);
                        foreach ($tagArray as $tName) {
                            $tClean = htmlspecialchars(ucwords($tName));
                            $tSlug = str_replace(' ', '-', $tClean);
                            echo "<a href='/tag/{$tSlug}' class='tag-pill'><span class='hash'>#</span>{$tClean}</a>";
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </article>
</div>

<!-- Infinite Scroll Trigger Element -->
<?php if (!empty($nextArticleUrl)): ?>
    <div id="infiniteScrollTrigger" class="infinite-scroll-trigger" data-next-url="<?php echo htmlspecialchars($nextArticleUrl); ?>">
        <div class="infinite-loader-content">
            <div class="infinite-spinner"></div>
            <span>Loading Next Story...</span>
        </div>
    </div>
<?php endif; ?>

<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js"></script>


<?php require_once 'footer.php'; ?>