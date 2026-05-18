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
                        $align = isset($slide_alignment) ? $slide_alignment : 'center';
                        $overlay = isset($slide_overlay) && $slide_overlay == 1 ? "<div style='position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);'></div>" : "";
                        $cta1_bg = !empty($cta1_bg_color) ? $cta1_bg_color : '#F5D154';
                        $cta1_text = !empty($cta1_text_color) ? $cta1_text_color : '#1E293B';
                        $cta2_style = isset($cta2_style) ? $cta2_style : 'outline';
                        $cta2_bg = !empty($cta2_bg_color) ? $cta2_bg_color : '#FFFFFF';
                        $cta2_text = !empty($cta2_text_color) ? $cta2_text_color : '#000000';
                        $slide_subtitle = !empty($slide_subtitle) ? $slide_subtitle : '';

                        $alignClass = $align === 'center' ? 'text-center' : ($align === 'right' ? 'text-right' : 'text-left');
                        $colClass = $align === 'center' ? 'col-xl-8 col-lg-10 offset-xl-2 offset-lg-1' : ($align === 'right' ? 'col-xl-6 col-lg-8 offset-xl-6 offset-lg-4' : 'col-xl-6 col-lg-8');
                        $btnJustify = $align === 'center' ? 'justify-content-center' : ($align === 'right' ? 'justify-content-end' : 'justify-content-start');

                        echo "
    <div class='swiper-slide'>
        <div class='image-layer' style='background-image: url(" . SITE_URL . "{$slide_image});'></div>
        {$overlay}
        <div class='main-slider-shape-1' style='background-image: url(" . SITE_URL . "{$slide_image});'></div>

        <div class='container' style='position:relative; z-index:2;'>
            <div class='row'>
                <div class='{$colClass}'>
                    <div class='main-slider__content {$alignClass}'>
                        " . (!empty($slide_subtitle) ? "<p class='main-slider__sub-title'>{$slide_subtitle}</p>" : "") . "
                        <h2 class='main-slider__title'>{$slide_description}</h2>
                        <div class='main-slider__btn-box d-flex {$btnJustify}' style='gap:15px;'>
                            <a href='{$slide_link}' class='thm-btn main-slider__btn' style='background-color: {$cta1_bg}; color: {$cta1_text}; border-color: {$cta1_bg};'> {$slider_cta}</a>
                            " . (!empty($slide_link2) && !empty($slider_cta2) ? "<a href='{$slide_link2}' class='thm-btn main-slider__btn' style='" . ($cta2_style === 'outline' ? "background:transparent; border:2px solid {$cta2_text}; color:{$cta2_text};" : "background:{$cta2_bg}; color:{$cta2_text}; border:2px solid {$cta2_bg};") . "'> {$slider_cta2}</a>" : "") . "
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