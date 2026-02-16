<?php

require_once DATA_FUNCTIONS;
require_once UTIL_FUNCTIONS;
require_once 'header.php';

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
                    <h6><?php echo $profile_display_name ?></h6>
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

                echo "<div class='details_box__content {$news_paid} " . (($showFullNews == true) ? '' : 'text-gradient') . "'><p>" . (($showFullNews == true) ? $news_description : $newsContent) . "</p></div>";

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

            if ($news_owner_id == 9423) { ?>
            <div class="user_content">
                <p>The author is Founder and Editor of The Head and Tale. She can be reached at
                    <a href="mailto:arti.singh@theheadandtale.com">arti.singh@theheadandtale.com</a><br />
                    Tweets <a href='https://twitter.com/artijourno' target="_blank"
                        style="font-style: italic">@artijourno</a>
                </p>
            </div>
            <?php
            }

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
                        <h3 class='related-head line-2'>{$news['title']}</h3>
                        <p class='related-excerpt line-2'>" . truncateText($news['description'], 100) . "</p>
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