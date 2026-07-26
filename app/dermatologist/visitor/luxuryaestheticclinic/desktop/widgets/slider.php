<!-- 2. HERO SECTION — Full-Screen Background Slider -->
<section class="hero-slider-section" id="hero">

  <!-- Swiper Container -->
  <div class="swiper hero-swiper">
    <div class="swiper-wrapper">

      <?php
      $sliderList = getSlider($connection);

      if (!empty($sliderList)) {
        $firstSlide = true;
        foreach ($sliderList as $index => $slide) {
          extract($slide);

          $cta1BgColor = isset($cta1_bg_color) ? $cta1_bg_color : '#c5a880';
          $cta1TxtColor = isset($cta1_text_color) ? $cta1_text_color : '#ffffff';
          $cta2BgColor = isset($cta2_bg_color) ? $cta2_bg_color : '#25d366';
          $cta2TxtColor = isset($cta2_text_color) ? $cta2_text_color : '#ffffff';
          $cta1IsSolid = isset($cta1_style) && $cta1_style == 'solid';
          $cta2IsSolid = isset($cta2_style) && $cta2_style == 'solid';

          echo "
          <div class='swiper-slide hero-slide' style='background-image: url(\"{$slide_image}\");' role='group' aria-label='Slide " . ($index + 1) . "'>
            <!-- Dark gradient overlay -->
            <div class='hero-slide-overlay'></div>

            <!-- Slide Content -->
            <div class='hero-slide-content'>
              <div class='container'>
                <div class='hero-inner'>
                  <span class='hero-eyebrow reveal'>{$slide_subtitle}</span>
                  <h1 class='hero-headline reveal'>{$slide_title}</h1>
                  <div class='hero-desc reveal'>
                    {$slide_description}
                  </div>
                  <div class='hero-ctas reveal'>
                    <a href='{$slide_link}' class='btn hero-btn-primary' id='heroBookBtn{$index}' style='background-color: " . ($cta1IsSolid ? $cta1BgColor : 'transparent') . "; color: {$cta1TxtColor}; border: 1.5px solid {$cta1BgColor};'>{$slider_cta}</a>
                    <a href='{$slide_link2}' class='btn hero-btn-secondary' id='heroWhatsAppBtn{$index}' target='_blank' rel='noopener' style='background-color: " . ($cta2IsSolid ? $cta2BgColor : 'transparent') . "; color: {$cta2TxtColor}; border: 1.5px solid {$cta2BgColor};'>
                      <i data-lucide='message-square' style='width:16px;margin-right:8px;flex-shrink:0;'></i>
                      {$slider_cta2}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>";

          $firstSlide = false;
        }
      }
      ?>

    </div><!-- /.swiper-wrapper -->

    <!-- Navigation Arrows -->
    <button class="hero-swiper-prev" aria-label="Previous slide">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button class="hero-swiper-next" aria-label="Next slide">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>

    <!-- Pagination Dots -->
    <div class="hero-swiper-pagination"></div>

    <!-- Scroll Indicator -->
    <div class="hero-scroll-hint" aria-hidden="true">
      <span>Scroll</span>
      <div class="hero-scroll-line"></div>
    </div>

  </div><!-- /.swiper -->

</section>