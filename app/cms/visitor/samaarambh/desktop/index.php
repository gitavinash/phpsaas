<?php

require_once 'header.php';

?>
<!--Main Slider Start-->
<section class="main-slider clearfix">
    <div class="swiper-container thm-swiper__slider" data-swiper-options='{"slidesPerView": 1, "loop": true,
                "effect": "fade",
                "pagination": {
                "el": "#main-slider-pagination",
                "type": "bullets",
                "clickable": true
                },
                "navigation": {
                "nextEl": "#main-slider__swiper-button-next",
                "prevEl": "#main-slider__swiper-button-prev"
                },
                "autoplay": {
                "delay": 5000
                }}'>
        <div class="swiper-wrapper">

            <?php

            if ($desktopSlider = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slider_device='" . SITE_VERSION . "' AND slide_site_id=" . SITE_ID)) {
                $numberOfSlider = mysqli_num_rows($desktopSlider);
                if ($numberOfSlider != 0) {
                    while ($desktopSliderData = mysqli_fetch_assoc($desktopSlider)) {
                        extract($desktopSliderData);
                        echo "
                                        <div class='swiper-slide'>
                                        <div class='image-layer' style='background-image: url(" . SITE_URL . "{$slide_image});'>
        </div>
        <div class='main-slider-shape-1'
            style='background-image: url(" . SITE_URL . "{$slide_image});'>
        </div>

        <div class='container'>
            <div class='row'>
                <div class='col-xl-6 col-lg-8'>
                    <div class='main-slider__content'>
                        <p class='main-slider__sub-title'>{$slide_title}</p>
                        <h2 class='main-slider__title'>{$slide_description}</h2>
                        <div class='main-slider__btn-box'>
                            <a href='{$slide_link}' class='thm-btn main-slider__btn'> {$slider_cta}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ";
                    }
                }
            }

            ?>

        </div>

        <!-- If we need navigation buttons -->
        <div class="main-slider__nav">
            <div class="swiper-button-prev" id="main-slider__swiper-button-next">
                <i class="icon-left-arrow"></i>
            </div>
            <div class="swiper-button-next" id="main-slider__swiper-button-prev">
                <i class="icon-right-arrow"></i>
            </div>
        </div>

    </div>
</section>

<?php

echo $page_description;

?>

<?php

require_once 'footer.php';

?>