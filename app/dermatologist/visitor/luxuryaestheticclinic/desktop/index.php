<?php

require_once 'header.php';

?>
<!-- 2. HERO SECTION -->
<section class="hero" id="hero">
  <?php
  $sliderList = getSlider($connection);
  print_r($sliderList);
  exit();
  ?>
</section>

<!-- 3. TRUST & AUTHORITY BAR -->
<section class="trust-bar" id="trust-bar">
  <div class="container trust-bar-grid">
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="1500" data-suffix="+">0</span>
      <span class="counter-label">Happy Patients</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="10" data-suffix="+">0</span>
      <span class="counter-label">Years Experience</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="20" data-suffix="+">0</span>
      <span class="counter-label">Advanced Treatments</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="500" data-suffix="+">0</span>
      <span class="counter-label">Verified Reviews</span>
    </div>
  </div>
</section>

<!-- 5. CONCERNS WE TREAT -->
<section class="concerns-section" id="concerns">
  <div class="container text-center concerns-header reveal">
    <span class="section-tag">Targeted Clinical Solutions</span>
    <h2>Aesthetic Concerns We Treat</h2>
    <p class="max-width-600">
      Every treatment starts with a deep clinical analysis of your skin,
      hair, or body goals. Select your primary concern area to discover
      clinical remedies:
    </p>
  </div>

  <div class="container concerns-grid">
    <!-- Category 1: Skin -->
    <div class="concern-category-card reveal reveal-stagger">
      <div class="category-icon-wrapper">
        <i data-lucide="sparkles" style="width: 32px; height: 32px"></i>
      </div>
      <h3 class="category-title">Skin Rejuvenation</h3>
      <ul class="concern-links">
        <li>
          <a href="services-face/" class="concern-item-link">Active Acne & Breakouts <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services-face/" class="concern-item-link">Pigmentation & Melasma <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services-face/" class="concern-item-link">Acne Scars & Uneven Texture <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services-face/" class="concern-item-link">Fine Lines, Wrinkles & Sagging <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services-face/" class="concern-item-link">Open Pores & Dull Complexion <i data-lucide="arrow-right"></i></a>
        </li>
      </ul>
    </div>

    <!-- Category 2: Hair -->
    <div class="concern-category-card reveal reveal-stagger">
      <div class="category-icon-wrapper">
        <i data-lucide="scissors" style="width: 32px; height: 32px"></i>
      </div>
      <h3 class="category-title">Hair Restoration</h3>
      <ul class="concern-links">
        <li>
          <a href="services/" class="concern-item-link">Severe Hair Fall & Shedding <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services/" class="concern-item-link">Pattern Baldness (Alopecia) <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services/" class="concern-item-link">Thinning Crowns & Recedings <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services/" class="concern-item-link">Chronic Dandruff & Scalp Health
            <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="services/" class="concern-item-link">Post-Transplant Recovery care <i data-lucide="arrow-right"></i></a>
        </li>
      </ul>
    </div>

    <!-- Category 3: Body -->
    <div class="concern-category-card reveal reveal-stagger">
      <div class="category-icon-wrapper">
        <i data-lucide="smile" style="width: 32px; height: 32px"></i>
      </div>
      <h3 class="category-title">Body Contouring</h3>
      <ul class="concern-links">
        <li>
          <a href="#treatments" class="concern-item-link">Stubborn Localized Fat pockets <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="#treatments" class="concern-item-link">Stretch Marks & Scarring <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="#treatments" class="concern-item-link">Skin Laxity & Sagging Areas <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="#treatments" class="concern-item-link">Cellulite Reduction & Smoothness
            <i data-lucide="arrow-right"></i></a>
        </li>
        <li>
          <a href="#treatments" class="concern-item-link">Full Body Sculpting & Contours <i data-lucide="arrow-right"></i></a>
        </li>
      </ul>
    </div>
  </div>
</section>

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

<!-- 6. MOST POPULAR TREATMENTS -->
<section class="treatments-section" id="treatments">
  <div class="container text-center reveal">
    <span class="section-tag">Our Signature Enhancements</span>
    <h2>Most Popular Clinic Treatments</h2>
    <p class="max-width-600">
      Clinical excellence paired with cutting-edge medical advances. Explore
      our most requested therapies for dramatic, lasting results:
    </p>
  </div>

  <div class="container treatments-grid">
    <!-- Treatment 1: HydraFacial -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Skin Glow</span>
        <img
          src="assets/images/hero_clinic.png"
          alt="HydraFacial Treatment Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">Synergistic HydraFacial</h3>
        <p class="treatment-card-desc">
          Bespoke 6-step vortex infusion cleaning, extracting impurities and
          saturating skin with rich hyaluronic peptides.
        </p>
        <a href="treatment/" class="btn-link">Explore Treatment
          <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 2: PRP Hair Therapy -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Hair Care</span>
        <img
          src="assets/images/before_skin.png"
          alt="PRP Hair Therapy Treatment Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">PRP Platelet Rich Therapy</h3>
        <p class="treatment-card-desc">
          Utilizes refined plasma growth factor concentrations injected
          directly to stimulate passive hair follicles.
        </p>
        <a href="services/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 3: GFC Hair Treatment -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Advanced Hair</span>
        <img
          src="assets/images/after_skin.png"
          alt="GFC Hair Treatment Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">GFC Growth Factor Concentrate</h3>
        <p class="treatment-card-desc">
          The next generation of scalp restoration using highly stable
          growth factors extracted directly from platelets.
        </p>
        <a href="services/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 4: Botox -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Anti-Aging</span>
        <img
          src="assets/images/dr_founder.png"
          alt="Botox Injections Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">FDA-Approved Allergan Botox</h3>
        <p class="treatment-card-desc">
          Precision micro-injections gently relaxing target hyperactive
          facial muscles to soften wrinkles and dynamic lines.
        </p>
        <a href="services-face/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 5: Dermal Fillers -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Contours</span>
        <img
          src="assets/images/dr_founder.png"
          alt="Juvederm Dermal Fillers Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">Juvederm Dermal Fillers</h3>
        <p class="treatment-card-desc">
          Restores structural volume and contours cheekbones, jawlines, and
          lips using medical-grade hyaluronic acid gels.
        </p>
        <a href="services-face/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 6: HIFU Ultra Therapy -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Non-Surgical Lift</span>
        <img
          src="assets/images/tech_showcase.png"
          alt="HIFU Treatment Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">
          High-Intensity Focused Ultrasound
        </h3>
        <p class="treatment-card-desc">
          Precision thermal ultrasound energy contracting deep SMAS tissue
          layers to lift and tighten cheeks and jowls.
        </p>
        <a href="services-face/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 7: Laser Hair Removal -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Smoothness</span>
        <img
          src="assets/images/tech_showcase.png"
          alt="Triple Wavelength Laser Hair Removal"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">Soprano Titanium Laser Care</h3>
        <p class="treatment-card-desc">
          Advanced triple-wavelength cooling laser system destroying deep
          follicles painlessly for permanent hair reductions.
        </p>
        <a href="treatment/" class="btn-link">Explore Details
          <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Treatment 8: Chemical Peels -->
    <div class="treatment-card reveal reveal-stagger">
      <div class="treatment-img-container">
        <span class="treatment-badge">Resurfacing</span>
        <img
          src="assets/images/hero_clinic.png"
          alt="Chemical Peel Session"
          class="treatment-img" />
      </div>
      <div class="treatment-card-content">
        <h3 class="treatment-card-title">Premium Medi-Peels</h3>
        <p class="treatment-card-desc">
          Expertly chosen botanical and clinical acid blends resurfacing top
          dead cell layers for massive radiance increases.
        </p>
        <a href="services-face/" class="btn-link">Explore Hub <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- 7. BEFORE & AFTER TRANSFORMATIONS -->
<section class="transformations-section" id="before-after">
  <div class="container text-center reveal">
    <span class="section-tag">Clinical Proof & Results</span>
    <h2>Real Transformations, Beautiful Success</h2>
    <p class="max-width-600">
      Review real, untouched before and after treatment records. Slide to
      evaluate skin texture transformations achieved by our customized
      clinical programs:
    </p>
  </div>

  <!-- Filter tabs -->
  <div class="container transform-filter-container reveal">
    <button class="filter-btn active" data-category="skin">
      Skin Pigmentation
    </button>
    <button class="filter-btn" data-category="hair">
      Hair Restoration
    </button>
    <button class="filter-btn" data-category="body">Body Contouring</button>
  </div>

  <div class="container transform-container-grid">
    <!-- Left side: The Drag Slider -->
    <div class="before-after-wrapper reveal">
      <!-- After Image (Base Background) -->
      <div class="slider-img slider-img-after">
        <img
          src="assets/images/after_skin.png"
          alt="Flawless Glowing Skin - After Treatment" />
        <span class="slider-badge badge-after">After</span>
      </div>

      <!-- Before Image (Resize Wrapper) -->
      <div class="slider-img slider-img-before">
        <img
          src="assets/images/before_skin.png"
          alt="Hyperpigmentation & Textures - Before Treatment" />
        <span class="slider-badge badge-before">Before</span>
      </div>

      <!-- Drag Handle Line & Slider button -->
      <div class="slider-handle">
        <button
          class="slider-handle-button"
          aria-label="Drag slider to compare before and after">
          <i
            data-lucide="chevron-left"
            style="width: 16px; height: 16px"></i>
          <i
            data-lucide="chevron-right"
            style="width: 16px; height: 16px"></i>
        </button>
      </div>
    </div>

    <!-- Right side: Transformation details card -->
    <div class="transform-details-card reveal">
      <span class="section-tag gold-accent">Featured Treatment Journey</span>
      <h3 class="transform-title">
        Advanced Pigmentation & Melasma Therapy
      </h3>
      <p class="transform-details-desc">
        Targeted medical-grade laser treatment specifically breaking down
        deep dermal melanin. Yields smooth, even-toned skin with zero
        scarring and minimal down-time.
      </p>

      <div class="transform-metrics">
        <div class="metric-item">
          <h4 class="metric-item-num">3 Sessions</h4>
          <span class="metric-item-lbl">Treatment Span</span>
        </div>
        <div class="metric-item">
          <h4 class="metric-item-num">FDA Approved Laser</h4>
          <span class="metric-item-lbl">Clinical Technology</span>
        </div>
      </div>

      <div style="margin-top: 4.5rem">
        <a href="#consultation" class="btn btn-primary">Achieve Similar Results</a>
      </div>
    </div>
  </div>
</section>

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