<?php

require_once DATA_FUNCTIONS;
require_once UTIL_FUNCTIONS;
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
            </div>" : ""); // Set author bio for the specific author if needed
    $authorHeading = "<a href='/author/{$authorProfileLink}/' target='_blank' alt='{$authorName} articles' title='{$authorName} articles' >{$authorName}</a>";
} else {
    $authorHeading = "";
    $authorLinks = [];
    foreach ($allAuthors as $author) {
        $profileLink = $author['authorId'] . '-' . urlencode(strtolower(str_replace(' ', '-', $author['authorName']))) . '-articles';
        $name = htmlspecialchars($author['authorName'], ENT_QUOTES, 'UTF-8');
        $authorLinks[] = "<a href='/author/{$profileLink}/' target='_blank' alt='{$name} articles' title='{$name} articles'>{$name}</a>";
    }
    $authorHeading .= implode(', ', $authorLinks);
    $authorBio = '';
}


$newsContent = strip_tags($news_description);
$newsContent =  substr($newsContent, 0, ((SITE_VERSION == 'mobile') ? 111 : 130));

?>
<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.css">
<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.css">
<section class="newsDetails">
    <div class="container container--box">
        <div class="row">
            <div class="col-lg-7">
                <div class="news_img">
                    <figure>
                        <img src="<?php echo $news_image ?>" class="img-fluid" alt="">
                    </figure>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="news_right">
                    <h3><?php echo $news_title ?></h3>
                    <span><?php echo date('d M Y, h:i A', strtotime($news_published)) ?></span>
                    <p><?php echo $meta_description ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news">
    <div class="container">
        <div class="news_box__container">
            <div class="news_box__heading">
                <div class="news_box__heading--left">
                    <h6><?php echo $authorHeading ?></h6>
                </div>
                <div class="social_link">
                    <p>Share the story on</p>
                    <a href="http://twitter.com/share?text=<?php echo urlencode($news_title) ?>&url=<?php echo SITE_URL . $link_url ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/twitter.png" class="share-icon"
                            alt=""></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo SITE_URL . $link_url ?>&title=<?php echo urlencode($news_title) ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/linkedin.png" class="share-icon"
                            alt=""></a>
                    <a href="https://wa.me/?text=<?php echo urlencode($news_title) . ' ' . SITE_URL . $link_url ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/whatsapp.png" class="share-icon"
                            alt=""></a>
                </div>

            </div>
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
                        $showFullNews = true;
                    }
                }

                $userAccessType = base64_decode($_COOKIE['user_type']);

                if ($userAccessType == 'author' || $userAccessType == 'administrator') {
                    $showFullNews = true;
                    $showSignUpSection = false;
                    $showSubscriptionSection = false;
                }

                echo "<div class='details_box__content {$news_paid} " . (($showFullNews == true) ? '' : 'text-gradient') . "'><p>" . (($showFullNews == true) ? $news_description : $newsContent) . "</p></div>";

                $newsAudioSource = ((isset($news_audio) && !empty($news_audio)) ? $news_audio : '');

                if (!empty($newsAudioSource)) {

                    $audioTitle   = htmlspecialchars($news_title, ENT_QUOTES, 'UTF-8');
                    $audioSource  = htmlspecialchars($newsAudioSource, ENT_QUOTES, 'UTF-8');
                    $audioArtwork = htmlspecialchars($news_image, ENT_QUOTES, 'UTF-8');
                    $audioSubscriptionCta = '/subscription/';

                    $audioAttributes = $showFullNews
                        ? 'preload="auto"'
                        : 'preload="metadata"';

                    $audioControlsHtml = $showFullNews
                        ? "<button type='button'
                            class='news-audio-player__button news-audio-player__button--primary news-audio-player__button--icon'
                            data-audio-toggle
                            aria-label='Play audio'>
                        <i class='bx bx-play news-audio-player__icon'
                           data-audio-toggle-icon
                           aria-hidden='true'></i>
                    </button>

                    <button type='button'
                            class='news-audio-player__button news-audio-player__button--icon'
                            data-audio-mute
                            aria-label='Mute audio'>
                        <i class='bx bx-volume-full news-audio-player__icon'
                           data-audio-mute-icon
                           aria-hidden='true'></i>
                    </button>

                    <span class='news-audio-player__time' data-audio-current>00:00</span>

                    <input type='range'
                           class='news-audio-player__seek'
                           min='0'
                           max='100'
                           step='0.1'
                           value='0'
                           data-audio-seek
                           aria-label='Seek audio playback'>

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

                ?>
                <style>
                    .news-gallery {
                        margin: 30px 0;
                    }

                    .news-gallery__slide {
                        margin: 0;
                        text-align: center;
                    }

                    .news-gallery__slide img {
                        max-width: 100%;
                        height: auto;
                        display: block;
                        margin-bottom: 10px;
                    }

                    .news-gallery__caption {
                        font-size: 14px;
                        color: #ee2a1a;
                        margin-top: 8px;
                        font-style: italic;
                        line-height: 24px;
                        padding: 10px 0px;
                    }

                    .news-audio-player {
                        position: fixed;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        z-index: 999;
                        width: 100%;
                        margin: 0;
                        padding: 16px 20px 14px;
                        border-radius: 18px 18px 0 0;
                        background: linear-gradient(180deg, rgba(255, 248, 246, 0.98) 0%, rgba(255, 255, 255, 0.96) 100%);
                        border: 1px solid rgba(238, 42, 26, 0.12);
                        border-bottom: 0;
                        box-shadow: 0 12px 30px rgba(28, 20, 18, 0.08);
                        backdrop-filter: blur(14px);
                        box-sizing: border-box;
                    }

                    .news-audio-player__main {
                        display: flex;
                        align-items: center;
                        gap: 16px;
                    }

                    .news-audio-player__artwork {
                        width: 68px;
                        height: 68px;
                        flex-shrink: 0;
                        border-radius: 16px;
                        overflow: hidden;
                        background: rgba(238, 42, 26, 0.08);
                        box-shadow: 0 8px 20px rgba(28, 20, 18, 0.12);
                    }

                    .news-audio-player__artwork img {
                        width: 100%;
                        height: 100%;
                        display: block;
                        object-fit: cover;
                    }

                    .news-audio-player__body {
                        min-width: 0;
                        flex: 1;
                    }

                    body.has-sticky-news-audio {
                        padding-bottom: 150px;
                    }

                    .news-audio-player__header {
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 12px;
                        margin-bottom: 12px;
                    }

                    .news-audio-player__title-wrap {
                        min-width: 0;
                        flex: 1;
                        overflow: hidden;
                    }

                    .news-audio-player__title {
                        margin: 0;
                        font-size: 18px;
                        line-height: 1.2;
                        color: #171717;
                        white-space: nowrap;
                        overflow: hidden;
                    }

                    .news-audio-player__title-text {
                        display: inline-block;
                        font-weight: 600;
                        color: #2c2c2c;
                    }

                    .news-audio-player__title.is-marquee .news-audio-player__title-text {
                        animation-name: news-audio-marquee;
                        animation-duration: var(--marquee-duration, 8s);
                        animation-timing-function: linear;
                        animation-iteration-count: infinite;
                        animation-fill-mode: both;
                        will-change: transform;
                    }

                    .news-audio-player__status {
                        flex-shrink: 0;
                        padding: 6px 10px;
                        border-radius: 999px;
                        background: rgba(238, 42, 26, 0.1);
                        color: #ee2a1a;
                        font-size: 12px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                    }

                    .news-audio-player__controls {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    }

                    .news-audio-player__subscribe {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 38px;
                        padding: 0 18px;
                        border-radius: 999px;
                        background: #ee2a1a;
                        color: #fff;
                        font-size: 13px;
                        font-weight: 700;
                        text-decoration: none;
                        white-space: nowrap;
                    }

                    .news-audio-player__subscribe:hover {
                        background: #d92616;
                        color: #fff;
                    }

                    .news-audio-player__button {
                        min-width: 64px;
                        height: 38px;
                        padding: 0 14px;
                        border: 0;
                        border-radius: 999px;
                        background: rgba(238, 42, 26, 0.1);
                        color: #8f1f14;
                        font-size: 13px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: background 0.2s ease, color 0.2s ease;
                    }

                    .news-audio-player__button--icon {
                        width: 42px;
                        min-width: 42px;
                        padding: 0;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .news-audio-player__icon {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 18px;
                        height: 18px;
                        font-size: 20px;
                        line-height: 1;
                    }

                    .news-audio-player__button:hover {
                        background: rgba(238, 42, 26, 0.16);
                    }

                    .news-audio-player__button--primary {
                        background: #ee2a1a;
                        color: #fff;
                    }

                    .news-audio-player__button--primary:hover {
                        background: #d92616;
                    }

                    .news-audio-player__time {
                        min-width: 42px;
                        color: #5c5c5c;
                        font-size: 12px;
                        font-weight: 700;
                        font-variant-numeric: tabular-nums;
                        text-align: center;
                    }

                    .news-audio-player__seek {
                        flex: 1;
                        min-width: 110px;
                        margin: 0;
                        appearance: none;
                        height: 6px;
                        border-radius: 999px;
                        outline: none;
                        background: linear-gradient(to right, #ee2a1a 0%, #ee2a1a var(--seek-progress, 0%), rgba(238, 42, 26, 0.18) var(--seek-progress, 0%), rgba(238, 42, 26, 0.18) 100%);
                    }

                    .news-audio-player__seek::-webkit-slider-thumb {
                        appearance: none;
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        background: #ee2a1a;
                        box-shadow: 0 0 0 3px rgba(238, 42, 26, 0.18);
                        cursor: pointer;
                    }

                    .news-audio-player__seek::-moz-range-thumb {
                        width: 16px;
                        height: 16px;
                        border: 0;
                        border-radius: 50%;
                        background: #ee2a1a;
                        box-shadow: 0 0 0 3px rgba(238, 42, 26, 0.18);
                        cursor: pointer;
                    }

                    .news-audio-player__seek::-moz-range-track {
                        height: 6px;
                        border-radius: 999px;
                        background: transparent;
                    }

                    .news-audio-player audio {
                        width: 100%;
                        display: none;
                    }

                    @keyframes news-audio-marquee {

                        0%,
                        12% {
                            transform: translateX(0);
                        }

                        88%,
                        100% {
                            transform: translateX(calc(-1 * var(--marquee-shift, 0px)));
                        }
                    }

                    .slick-prev,
                    .slick-next {
                        z-index: 1;
                        width: 50px;
                        height: 50px;
                        color: aliceblue;
                        background-color: rgba(205, 0, 0, 0.6);
                        color: white;
                        font-size: 20px;
                        border: none;
                        cursor: pointer;
                        opacity: 1 !important;
                        visibility: visible !important;
                        display: block !important;
                    }

                    .slick-prev:before,
                    .slick-next:before {
                        content: none !important;
                    }

                    .slick-prev:hover,
                    .slick-next:hover {
                        color: #fff;
                        background-color: rgba(205, 0, 0, 0.8);
                    }

                    /* Make news_box__heading--left and social_link full width on mobile */
                    @media (max-width: 767px) {

                        body.has-sticky-news-audio {
                            padding-bottom: 184px;
                        }

                        .news-audio-player {
                            padding: 14px 12px 12px;
                            border-radius: 16px 16px 0 0;
                        }

                        .news-audio-player__main {
                            gap: 12px;
                        }

                        .news-audio-player__artwork {
                            width: 56px;
                            height: 56px;
                            border-radius: 14px;
                        }

                        .news-audio-player__header {
                            align-items: flex-start;
                        }

                        .news-audio-player__status {
                            font-size: 11px;
                            padding: 5px 8px;
                        }

                        .news-audio-player__controls {
                            flex-wrap: wrap;
                            gap: 8px;
                        }

                        .news-audio-player__subscribe {
                            min-height: 34px;
                            padding: 0 14px;
                            font-size: 12px;
                        }

                        .news-audio-player__button {
                            height: 34px;
                            padding: 0 12px;
                            font-size: 12px;
                        }

                        .news-audio-player__button--icon {
                            width: 38px;
                            min-width: 38px;
                            padding: 0;
                        }

                        .news-audio-player__icon {
                            width: 16px;
                            height: 16px;
                            font-size: 18px;
                        }

                        .news-audio-player__time {
                            min-width: 38px;
                        }

                        .news-audio-player__seek {
                            order: 3;
                            width: 100%;
                            min-width: 100%;
                        }

                        .news_box__heading--left,
                        .social_link {
                            width: 100% !important;
                            display: block;
                            text-align: right;
                            margin-bottom: 10px;
                        }

                        .news_box__heading {
                            flex-direction: column;
                        }
                    }
                </style>
                <?php


                if ($showSignUpSection == true) {
                    require_once 'subscription-now-section.php';
                }
                if ($showSubscriptionSection == true) {
                    require_once 'subscription-now-section.php';
                }


                ?>

            </div>

            <?php

            // Show author card

            echo $authorBio;

            ?>
            <div class="tags">
                <ul>
                    <?php

                    if ($tagName != '') {
                        $tagName = explode(', ', $tagName);

                        for ($i = 0; $i < sizeof($tagName); $i++) {

                            echo "<li><a href='/tag/" . str_replace(' ', '-', $tagName[$i]) . "'>{$tagName[$i]}</a></li>";
                        }
                    }

                    ?>
                </ul>
            </div>

            <!-- Testimonial slider -->

            <?php

            $testimonials = testimonialsList($connection, 5);

            if (sizeof($testimonials) > 0) {
                echo "<div class='sliderBox'>
                <h2>Who Reads Us</h2>
                <div class=' testimonial-slider compact'>
                    <div class='testimonial-track'>";
                foreach ($testimonials as $row) {
                    $readerInfo = explode(",", $row["category_title"]);
                    $readerImage = (($row['category_image'] != '')  ? $row['category_image'] : IMG_LIBRARY . 'default-black-profile-pic-icon.png');
                    $readerName = $readerInfo[0];
                    $readerPosition = ucwords(trim(str_replace($readerName . ((sizeof($readerInfo) == 1) ? "" : ","), "", $row["category_title"])));
                    echo "<div class='testimonial-slide'>
                            <p class='testimonial-quote'>
                                “{$row['category_description']}”
                            </p>
                            <div class='testimonial-profile'>
                                <img src='{$readerImage}' alt='{$row['category_title']}'>
                                <div class='testimonial-meta'>
                                    <span class='testimonial-name'>{$readerName}</span>
                                    <span class='testimonial-role'>{$readerPosition}</span>
                                </div>
                            </div>
                        </div>";
                }
                echo "</div><div class='testimonial-dots' aria-label='Testimonials pagination'></div></div></div>";
            }


            ?>


            <!-- Related News -->

            <?php

            $newsList = similarArticles($connection, $news_id, 'news', 5);

            if (isset($newsList['articles'])) {
                if (sizeof($newsList['articles']) > 0) {
                    echo "<section class='related-wrapper' aria-labelledby='relatedHeading'>
                <div class='container'>
                    <h2 id='relatedHeading' class='related-title'>Related News</h2>

                    <div class='related-grid'>";
                    foreach ($newsList['articles'] as $news) {
                        echo "<article class='related-card'>
                <a class='related-link' href='{$news['link_url']}'>
                    <figure class='related-media'>
                        <img loading='lazy' src='{$news['image']}'
                            alt='{$news['title']}'>
                    </figure>
                    <div class='related-content'>
                        <span class='related-tag'>{$news['category_title']}</span>
                        <h3 class='related-head'>{$news['title']}</h3>
                        <time class='related-date' datetime='" . date("M d, Y", strtotime($news['link_published'])) . "'>" . date("M d, Y", strtotime($news['link_published'])) . "</time>
                    </div>
                </a>
            </article>";
                    }
                    echo "</div>
                <div class='related-footer'>
            <a class='related-more-link' href='/search/?q={$newsList['mostUserKeyword']}'>View all related news</a>
        </div>
                </div></section>";
                }
            }

            ?>

        </div>
    </div>
</section>
<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js"></script>
<script>
    (function($) {
        $(function() {
            const $gallery = $('.news-gallery');
            const stickyAudioPlayer = document.querySelector('[data-sticky-audio-player]');
            const audio = document.querySelector('[data-news-audio-player]');
            const audioTitle = document.querySelector('[data-audio-title]');
            const audioTitleText = audioTitle ? audioTitle.querySelector('.news-audio-player__title-text') : null;
            const audioToggle = document.querySelector('[data-audio-toggle]');
            const audioMute = document.querySelector('[data-audio-mute]');
            const audioSeek = document.querySelector('[data-audio-seek]');
            const audioCurrent = document.querySelector('[data-audio-current]');
            const audioTotal = document.querySelector('[data-audio-total]');
            const audioArtwork = document.querySelector('[data-audio-artwork]');
            const audioToggleIcon = document.querySelector('[data-audio-toggle-icon]');
            const audioMuteIcon = document.querySelector('[data-audio-mute-icon]');

            const syncMarqueeTitle = function() {
                if (!audioTitle || !audioTitleText) {
                    return;
                }

                audioTitle.classList.remove('is-marquee');
                audioTitle.style.removeProperty('--marquee-shift');

                const overflowWidth = Math.ceil(audioTitleText.scrollWidth - audioTitle.clientWidth);
                if (overflowWidth > 0) {
                    const travelSeconds = Math.max(4, overflowWidth / 40);
                    const totalSeconds = travelSeconds + 1;
                    audioTitle.style.setProperty('--marquee-shift', overflowWidth + 'px');
                    audioTitle.style.setProperty('--marquee-duration', totalSeconds + 's');
                    audioTitle.classList.add('is-marquee');
                }
            };

            if (stickyAudioPlayer) {
                document.body.classList.add('has-sticky-news-audio');
                window.addEventListener('load', syncMarqueeTitle);
                window.addEventListener('resize', syncMarqueeTitle);
                syncMarqueeTitle();
            }

            if (audio) {

                const formatDuration = function(totalSeconds) {
                    if (!Number.isFinite(totalSeconds) || totalSeconds < 0) {
                        return '00:00';
                    }

                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = Math.floor(totalSeconds % 60);

                    if (hours > 0) {
                        return [hours, minutes, seconds].map(function(unit, index) {
                            return index === 0 ? String(unit) : String(unit).padStart(2, '0');
                        }).join(':');
                    }

                    return [minutes, seconds].map(function(unit) {
                        return String(unit).padStart(2, '0');
                    }).join(':');
                };

                const applyFallbackSource = function() {
                    const defaultSource = audio.getAttribute('data-default-audio-source');
                    const sourceNode = audio.querySelector('source');

                    if (!defaultSource || !sourceNode || sourceNode.getAttribute('src') === defaultSource) {
                        return;
                    }

                    sourceNode.setAttribute('src', defaultSource);
                    audio.load();
                };

                const syncButtonState = function() {
                    if (audioToggle) {
                        audioToggle.setAttribute('aria-label', audio.paused ? 'Play audio' : 'Pause audio');
                    }

                    if (audioToggleIcon) {
                        audioToggleIcon.className = audio.paused ?
                            'bx bx-play news-audio-player__icon' :
                            'bx bx-pause news-audio-player__icon';
                    }

                    if (audioMute) {
                        audioMute.setAttribute('aria-label', audio.muted ? 'Unmute audio' : 'Mute audio');
                    }

                    if (audioMuteIcon) {
                        audioMuteIcon.className = audio.muted ?
                            'bx bx-volume-mute news-audio-player__icon' :
                            'bx bx-volume-full news-audio-player__icon';
                    }
                };

                const syncSeekState = function() {
                    if (!audioSeek) {
                        return;
                    }

                    const progress = audio.duration > 0 ?
                        (audio.currentTime / audio.duration) * 100 :
                        0;

                    audioSeek.value = progress;
                    audioSeek.style.setProperty('--seek-progress', progress + '%');
                };

                const syncTimeState = function() {
                    if (audioCurrent) {
                        audioCurrent.textContent = formatDuration(audio.currentTime);
                    }

                    if (audioTotal) {
                        audioTotal.textContent = formatDuration(audio.duration);
                    }

                    syncSeekState();
                };

                audio.addEventListener('error', applyFallbackSource);
                const sourceNode = audio.querySelector('source');
                if (sourceNode) {
                    sourceNode.addEventListener('error', applyFallbackSource);
                }

                if (audioArtwork) {
                    audioArtwork.addEventListener('error', function() {
                        const fallbackImage = audioArtwork.getAttribute('data-fallback-image');
                        if (fallbackImage && audioArtwork.getAttribute('src') !== fallbackImage) {
                            audioArtwork.setAttribute('src', fallbackImage);
                        }
                    });
                }

                if (audioToggle) {
                    audioToggle.addEventListener('click', function() {
                        if (audio.paused) {
                            audio.play();
                        } else {
                            audio.pause();
                        }
                    });
                }

                if (audioMute) {
                    audioMute.addEventListener('click', function() {
                        audio.muted = !audio.muted;
                        syncButtonState();
                    });
                }

                if (audioSeek) {
                    audioSeek.addEventListener('input', function() {
                        const progress = Number(audioSeek.value) || 0;
                        audioSeek.style.setProperty('--seek-progress', progress + '%');

                        if (audio.duration > 0) {
                            audio.currentTime = (progress / 100) * audio.duration;
                            syncTimeState();
                        }
                    });
                }

                audio.addEventListener('loadedmetadata', syncTimeState);
                audio.addEventListener('durationchange', syncTimeState);
                audio.addEventListener('timeupdate', syncTimeState);
                audio.addEventListener('play', syncButtonState);
                audio.addEventListener('pause', syncButtonState);
                audio.addEventListener('volumechange', syncButtonState);
                audio.addEventListener('ended', syncButtonState);

                syncButtonState();
                syncTimeState();

                if (audio.error) {
                    applyFallbackSource();
                }
            }

            if (!$gallery.length || typeof $.fn.slick !== 'function') {
                return;
            }

            const slideCount = $gallery.children().length;

            $gallery.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: true,
                adaptiveHeight: true,
                infinite: slideCount > 1,
                prevArrow: '<button type="button" class="slick-prev" aria-label="Previous slide">&#8249;</button>',
                nextArrow: '<button type="button" class="slick-next" aria-label="Next slide">&#8250;</button>'
            });
        });
    })(window.jQuery);
</script>
<?php

require_once 'footer.php';

?>