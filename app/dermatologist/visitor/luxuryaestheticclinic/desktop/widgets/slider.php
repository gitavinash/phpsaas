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
                      <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='currentColor' style='margin-right:8px;flex-shrink:0'><path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z'/></svg>
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