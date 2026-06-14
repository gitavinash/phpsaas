<?php

require_once 'header.php';

?>
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

<?php

require_once 'widgets/trust-bar.php';

?>

<!-- 9. WHY CHOOSE LUXURY AESTHETIC CLINIC -->
<section class="why-choose-section" id="why-us">
  <div class="container text-center reveal">
    <span class="section-tag">Distinctive Quality</span>
    <h2>Uncompromising Standards of Care</h2>
    <p class="max-width-600">
      We merge world-class clinical expertise with the comfort and prestige
      of high-end boutique hospitality. Experience clinical aesthetics
      redefined:
    </p>
  </div>

  <div class="container why-grid">
    <!-- Trait 1: Dermatologist Led -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="shield-check"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Expert Dermatologists</h3>
      <p class="why-desc">
        All treatments are designed, supervised, and conducted by certified
        medical dermatologists with rich training histories.
      </p>
    </div>

    <!-- Trait 2: FDA Tech -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="check-circle"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">FDA Approved Technologies</h3>
      <p class="why-desc">
        We invest exclusively in elite medical-grade devices cleared by the
        US FDA, guaranteeing maximum treatment safety.
      </p>
    </div>

    <!-- Trait 3: Bespoke Plans -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="heart"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Personalized Treatment</h3>
      <p class="why-desc">
        No pre-packaged blueprints. Your facial contours, skin health, and
        genetics dictate your custom enhancement plan.
      </p>
    </div>

    <!-- Trait 4: Luxury Ambience -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="coffee"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Boutique Luxury Experience</h3>
      <p class="why-desc">
        Indulge in high-end reception spaces, private treatment lounges, and
        warm hospitality designed to soothe anxiety.
      </p>
    </div>

    <!-- Trait 5: Proven Results -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="award"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Proven Natural Results</h3>
      <p class="why-desc">
        We advocate for subtle, structural enhancements that honor your
        natural anatomy rather than artificial distortions.
      </p>
    </div>

    <!-- Trait 6: Advanced Equipment -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="settings"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Advanced Diagnostics</h3>
      <p class="why-desc">
        We employ advanced 3D skin analysis hardware to scientifically map
        deep hydration, pigmentation, and UV damage.
      </p>
    </div>

    <!-- Trait 7: Minimal Downtime -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="clock"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Minimal Downtime Procedures</h3>
      <p class="why-desc">
        Modern cosmetic science allows us to target aging and scarring with
        advanced lasers that cause next to zero redness.
      </p>
    </div>

    <!-- Trait 8: Transparent Pricing -->
    <div class="why-card reveal reveal-stagger">
      <i
        data-lucide="dollar-sign"
        class="why-icon"
        style="width: 40px; height: 40px"></i>
      <h3 class="why-title">Transparent Integrity</h3>
      <p class="why-desc">
        Honest, straightforward consultations with zero hard upsells, hidden
        costs, or unnecessary procedural suggestions.
      </p>
    </div>
  </div>
</section>

<!-- 5. CONCERNS WE TREAT -->

<?php

$treatementList = getCategoriesWithPosts($connection, 'service', 'treatment', true, 3, 5);

$icons = [
  'sparkles',
  'scissors',
  'smile',
  'heart',
  'star',
  'gem',
  'shield',
  'flower'
];

$iconIndex = 0;

if (!empty($treatementList)) {
  echo "
<section class='concerns-section' id='concerns'>
  <div class='container text-center concerns-header reveal'>
    <span class='section-tag'>Targeted Clinical Solutions</span>
    <h2>Aesthetic Concerns We Treat</h2>
    <p class='max-width-600'>
      Every treatment starts with a deep clinical analysis of your skin,
      hair, or body goals. Select your primary concern area to discover
      clinical remedies:
    </p>
  </div>

  <div class='container concerns-grid'>";

  foreach ($treatementList as $category):

    $icon = $icons[$iconIndex % count($icons)];
    $iconIndex++;
?>

    <div class="concern-category-card reveal reveal-stagger">

      <!-- <div class="category-icon-wrapper">
        <i data-lucide="<?php echo $icon; ?>" style="width:32px;height:32px"></i>
      </div> -->

      <h3 class="category-title">
        <a href="<?php echo $category['category_url']; ?>">
          <?php echo ucwords($category['category_title']); ?>
        </a>
      </h3>

      <ul class="concern-links">

        <?php if (!empty($category['services'])): ?>

          <?php foreach ($category['services'] as $service): ?>

            <li>
              <a
                href="<?php echo $service['service_url']; ?>"
                class="concern-item-link" title="<?php echo ucwords($service['service_title']); ?>">

                <?php echo ucwords($service['service_title']); ?>

                <i data-lucide=" arrow-right"></i>
              </a>
            </li>

          <?php endforeach; ?>

        <?php else: ?>

          <li>
            <span class="concern-item-link disabled">
              Treatments Coming Soon
            </span>
          </li>

        <?php endif; ?>

      </ul>

    </div>

<?php endforeach;

  echo "</div>
</section>";
}
?>

<!-- 6. MOST POPULAR TREATMENTS -->

<?php
$treatements = getData($connection, 'service', 'treatment', 8, true); // Fetch 8 treatments
if (!empty($treatements)) {
  echo "<section class='treatments-section' id='treatments'>
  <div class='container text-center reveal'>
    <span class='section-tag'>Our Signature Enhancements</span>
    <h2>Most Popular Clinic Treatments</h2>
    <p class='max-width-600'>
      Clinical excellence paired with cutting-edge medical advances. Explore
      our most requested therapies for dramatic, lasting results:
    </p>
  </div>

  <div class='container treatments-grid'>";
  foreach ($treatements as $treatment) {
    extract($treatment);
    echo "<div class='treatment-card reveal reveal-stagger'>
      <div class='treatment-img-container'>
        <span class='treatment-badge'>{$category}</span>
        <img
          src='{$image}'
          alt='{$title}'
          class='treatment-img' />
      </div>
      <div class='treatment-card-content'>
        <h3 class='treatment-card-title'>{$title}</h3>
        <p class='treatment-card-desc'>
          {$description}
        </p>
        <a href='{$link_url}' class='btn-link'>Explore Treatment
          <i data-lucide='arrow-right' style='width: 16px'></i></a>
      </div>
    </div>";
  }
  echo "</div></section>";
}

$beforeAfterData = getGalleryWithCategories(
  $connection,
  'gallery',
  'gallery',
  true,
  5,
  1
);

if (!empty($beforeAfterData)) :
?>

  <section class="transformations-section" id="before-after">

    <div class="container text-center reveal active">
      <span class="section-tag">Clinical Proof & Results</span>
      <h2>Real Transformations, Beautiful Success</h2>
      <p class="max-width-600">
        Review real, untouched before and after treatment records.
        Slide to evaluate skin texture transformations achieved by our
        customized clinical programs.
      </p>
    </div>

    <div class="container transform-filter-container reveal active">

      <?php
      $isFirst = true;
      foreach ($beforeAfterData as $index => $category) :

        if (empty($category['gallerys'])) {
          continue;
        }
      ?>

        <button
          type="button"
          class="filter-btn <?= ($isFirst ? 'active' : ''); ?>"
          data-category="category_<?= $category['category_id']; ?>">

          <?= htmlspecialchars($category['category_title']); ?>

        </button>

      <?php
        $isFirst = false;
      endforeach; ?>

    </div>

    <?php
    $isFirst = true;
    foreach ($beforeAfterData as $index => $category) :

      if (empty($category['gallerys'])) {
        continue;
      }

      $item = $category['gallerys'][0];

      $galleryImageList = json_decode($item['gallery_image_list'], true);
      $beforeImage = $galleryImageList[0]['path'];
      $afterImage = $galleryImageList[1]['path'];

    ?>

      <div
        class="container transform-container-grid transformation-panel <?= ($isFirst ? 'active' : ''); ?>"
        data-category="category_<?= $category['category_id']; ?>">

        <div class="before-after-wrapper reveal active">

          <div class="slider-img slider-img-after">
            <img
              src="<?= $beforeImage ?>"
              alt="<?= htmlspecialchars($item['gallery_title']); ?> After">

            <span class="slider-badge badge-after">
              After
            </span>
          </div>

          <div class="slider-img slider-img-before">
            <img
              src="<?= $afterImage ?>"
              alt="<?= htmlspecialchars($item['gallery_title']); ?> Before">

            <span class="slider-badge badge-before">
              Before
            </span>
          </div>

          <div class="slider-handle">
            <button
              type="button"
              class="slider-handle-button">

              <i data-lucide="chevron-left"></i>
              <i data-lucide="chevron-right"></i>

            </button>
          </div>

        </div>

        <div class="transform-details-card reveal active">

          <?php if (!empty($item['gallery_description']) && strip_tags($item['gallery_description']) !== $item['gallery_description']) : ?>

            <?= $item['gallery_description']; ?>

          <?php else : ?>

            <?php if (!empty($item['gallery_description'])) : ?>
              <p class="transform-details-desc">
                <?= htmlspecialchars($item['gallery_description']); ?>
              </p>
            <?php endif; ?>

            <div class="transform-metrics">

              <?php if (!empty($item['gallery_duration'])) : ?>
                <div class="metric-item">
                  <h4 class="metric-item-num">
                    <?= htmlspecialchars($item['gallery_duration']); ?>
                  </h4>
                  <span class="metric-item-lbl">
                    Treatment Span
                  </span>
                </div>
              <?php endif; ?>

              <?php if (!empty($item['gallery_technology'])) : ?>
                <div class="metric-item">
                  <h4 class="metric-item-num">
                    <?= htmlspecialchars($item['gallery_technology']); ?>
                  </h4>
                  <span class="metric-item-lbl">
                    Clinical Technology
                  </span>
                </div>
              <?php endif; ?>

            </div>

            <div style="margin-top:4.5rem">
              <a
                href="<?= htmlspecialchars($item['gallery_url']); ?>"
                class="btn btn-primary">

                Achieve Similar Results

              </a>
            </div>

          <?php endif; ?>

        </div>

      </div>

    <?php
      $isFirst = false;
    endforeach; ?>

  </section>

<?php endif; ?>
<!-- 8. INSTAGRAM REELS & YOUTUBE SHORTS -->
<section class="social-proof-section" id="social-proof">
  <div class="container text-center reveal">
    <span class="section-tag">Watch Us In Action</span>
    <h2>Real Results & Expert Insights</h2>
    <p class="max-width-600">
      Step behind the scenes with live treatment journeys, patient diaries,
      and cosmetic advice directly from Dr. Elizabeth Reed:
    </p>
  </div>

  <div class="carousel-outer reveal">
    <!-- Carousel Slider Controls -->
    <button class="carousel-btn btn-prev" aria-label="Slide left">
      <i data-lucide="chevron-left" style="width: 24px; height: 24px"></i>
    </button>
    <button class="carousel-btn btn-next" aria-label="Slide right">
      <i data-lucide="chevron-right" style="width: 24px; height: 24px"></i>
    </button>

    <div class="carousel-container">
      <div class="carousel-track">
        <!-- Card 1 -->
        <div class="social-card">
          <div class="social-video-thumb">
            <img
              src="assets/images/hero_clinic.png"
              alt="Patient Skin Journey Video Thumbnail"
              class="social-video-img" />
            <div class="social-icon-badge instagram-icon">
              <i
                data-lucide="instagram"
                style="width: 18px; height: 18px"></i>
            </div>
            <button class="play-button-overlay" aria-label="Play video">
              <i
                data-lucide="play"
                style="width: 24px; height: 24px; fill: currentColor"></i>
            </button>
          </div>
          <div class="social-card-content">
            <span class="social-card-tag">Skin Transformation</span>
            <h4 class="social-card-title">
              Ananya's 3-Month Acne Scar Removal Journey
            </h4>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="social-card">
          <div class="social-video-thumb">
            <img
              src="assets/images/dr_founder.png"
              alt="Doctor Skincare Tips Video Thumbnail"
              class="social-video-img" />
            <div class="social-icon-badge youtube-icon">
              <i
                data-lucide="youtube"
                style="width: 18px; height: 18px"></i>
            </div>
            <button class="play-button-overlay" aria-label="Play video">
              <i
                data-lucide="play"
                style="width: 24px; height: 24px; fill: currentColor"></i>
            </button>
          </div>
          <div class="social-card-content">
            <span class="social-card-tag">Expert Advice</span>
            <h4 class="social-card-title">
              Top 3 Anti-Aging Active Ingredients - Dr. Reed
            </h4>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="social-card">
          <div class="social-video-thumb">
            <img
              src="assets/images/tech_showcase.png"
              alt="Laser Hair Removal Demo Thumbnail"
              class="social-video-img" />
            <div class="social-icon-badge instagram-icon">
              <i
                data-lucide="instagram"
                style="width: 18px; height: 18px"></i>
            </div>
            <button class="play-button-overlay" aria-label="Play video">
              <i
                data-lucide="play"
                style="width: 24px; height: 24px; fill: currentColor"></i>
            </button>
          </div>
          <div class="social-card-content">
            <span class="social-card-tag">Treatment Demo</span>
            <h4 class="social-card-title">
              Soprano Titanium Painless Laser Demonstration
            </h4>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="social-card">
          <div class="social-video-thumb">
            <img
              src="assets/images/after_skin.png"
              alt="Hydrafacial Session Walkthrough"
              class="social-video-img" />
            <div class="social-icon-badge instagram-icon">
              <i
                data-lucide="instagram"
                style="width: 18px; height: 18px"></i>
            </div>
            <button class="play-button-overlay" aria-label="Play video">
              <i
                data-lucide="play"
                style="width: 24px; height: 24px; fill: currentColor"></i>
            </button>
          </div>
          <div class="social-card-content">
            <span class="social-card-tag">Patient Vlogs</span>
            <h4 class="social-card-title">
              Celebrity Hydrating Facial Experience at Clinic
            </h4>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="social-card">
          <div class="social-video-thumb">
            <img
              src="assets/images/before_skin.png"
              alt="PRP Scalp Injections Demo"
              class="social-video-img" />
            <div class="social-icon-badge youtube-icon">
              <i
                data-lucide="youtube"
                style="width: 18px; height: 18px"></i>
            </div>
            <button class="play-button-overlay" aria-label="Play video">
              <i
                data-lucide="play"
                style="width: 24px; height: 24px; fill: currentColor"></i>
            </button>
          </div>
          <div class="social-card-content">
            <span class="social-card-tag">Hair Growth</span>
            <h4 class="social-card-title">
              Understanding Growth Factor Concentrate Therapy
            </h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA block -->
  <div class="container social-cta-box reveal">
    <a
      href="https://instagram.com"
      class="btn btn-secondary"
      target="_blank"
      rel="noopener">
      <i
        data-lucide="instagram"
        style="width: 16px; margin-right: 8px; vertical-align: middle"></i>
      Follow Instagram
    </a>
    <a
      href="https://youtube.com"
      class="btn btn-secondary"
      target="_blank"
      rel="noopener">
      <i
        data-lucide="youtube"
        style="width: 16px; margin-right: 8px; vertical-align: middle"></i>
      Subscribe YouTube
    </a>
  </div>
</section>

<!-- 10. TECHNOLOGY SHOWCASE -->
<section class="technology-section" id="technology">
  <div class="container tech-grid">
    <!-- Left Content Column -->
    <div class="tech-content-column reveal">
      <span class="section-tag">Pioneering Science</span>
      <h2>Advanced FDA-Approved Technologies</h2>
      <p>
        We source our medical lasers and diagnostic hardware exclusively
        from international industry leaders (USA, Germany, Israel). By
        utilizing advanced engineering, we achieve exceptional tissue
        targeting, absolute thermal safety, and superior long-term results.
      </p>

      <div class="tech-cards-grid">
        <div class="tech-mini-card">
          <h4 class="tech-mini-title">Soprano Titanium Laser</h4>
          <p class="tech-mini-desc">
            Triple wavelength technology providing painless, ultra-fast hair
            reduction across all skin tones.
          </p>
        </div>
        <div class="tech-mini-card">
          <h4 class="tech-mini-title">Ultherapy SMAS Lifting</h4>
          <p class="tech-mini-desc">
            High-Intensity ultrasound waves precision triggering collagen
            production at surgical deep tissue layers.
          </p>
        </div>
        <div class="tech-mini-card">
          <h4 class="tech-mini-title">Sylfirm X RadioFrequency</h4>
          <p class="tech-mini-desc">
            Advanced dual-wave micro-needling treating vascular lesions,
            redness, and deep acne scars.
          </p>
        </div>
        <div class="tech-mini-card">
          <h4 class="tech-mini-title">Vectra 3D Skin Scanner</h4>
          <p class="tech-mini-desc">
            Super high-definition camera mapping deep pigmentation, skin
            texture, and moisture reserves.
          </p>
        </div>
      </div>

      <div class="tech-badge-container">
        <div class="cert-badge">
          <i data-lucide="check-circle" style="width: 16px"></i> US FDA
          Approved
        </div>
        <div class="cert-badge">
          <i data-lucide="shield" style="width: 16px"></i> CE Certified
          Medical
        </div>
      </div>
    </div>

    <!-- Right Image Column -->
    <div class="tech-media-column reveal">
      <div class="tech-image-container">
        <img
          src="assets/images/tech_showcase.png"
          alt="Advanced FDA Approved Skincare Laser Machine"
          class="tech-img" />
      </div>
    </div>
  </div>
</section>

<?php

require_once 'widgets/recent-articles.php';

require_once 'footer.php';

?>