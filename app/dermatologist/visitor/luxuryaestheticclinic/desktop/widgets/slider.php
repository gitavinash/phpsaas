<!-- 2. HERO SECTION -->
<section class="hero" id="hero">
  <?php
  $sliderList = getSlider($connection);

  if (!empty($sliderList)) {
    foreach ($sliderList as $slide) {
      extract($slide);
      echo "<div class='container hero-grid'>
    <!-- Hero Left: Headlines & Trust -->
    <div class='hero-content'>
      <span class='section-tag'>{$slide_subtitle}</span>
      <h1>{$slide_title}</h1>
      <div class='hero-subheadline'>
        {$slide_description}
      </div>
      <!-- CTAs -->
      <div class='hero-actions'>
        <a href='{$slide_link}' class='btn btn-primary' id='heroBookBtn' style='background-color: " . (($cta1_style == 'solid') ? $cta1_bg_color : 'transparent') . " !important; color: {$cta1_text_color} !important;  display: inline-flex; border: 2px solid {$cta1_bg_color};'>{$slider_cta}</a>
        <a
          href='{$slide_link2}'
          class='btn btn-whatsapp'
          id='heroWhatsAppBtn'
          target='_blank'
          rel='noopener'
          style='background-color: " . (($cta2_style == 'solid') ? $cta2_bg_color : 'transparent') . " !important; color: {$cta2_text_color} !important;  display: inline-flex; border: 2px solid {$cta2_bg_color};'
          >
          <i
            data-lucide='message-square'
            style='width: 16px; margin-right: 8px'></i>
          {$slider_cta2}
        </a>
      </div>
    </div>

    <!-- Hero Right: Images & Floating Stats -->
    <div class='hero-media-wrapper'>
      <div class='hero-image-container'>
        <img
          src='{$slide_image}'
          alt='{$slide_title}'
          class='hero-img' />
      </div>
    </div>
  </div>";
    }
  }
  ?>
</section>