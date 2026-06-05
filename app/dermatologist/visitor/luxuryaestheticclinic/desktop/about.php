<?php

require_once 'header.php';

?>

<!-- 2. ABOUT US HERO SECTION -->
<section class="about-hero" id="aboutHero">
  <div class="container about-hero-grid">
    <!-- Left: Headlines -->
    <div class="hero-content">
      <span class="section-tag">Elite Clinical Sanctuary</span>
      <h1>Discover Comfort, <br /><span>Care & Flawless Results</span></h1>
      <p class="hero-subheadline">
        Luxury Aesthetics Centre is a leading cosmetic and skincare practice
        in Delhi, offering advanced skin and hair treatments delivered by
        highly trained medical experts with decades of clinical experience.
      </p>

      <!-- CTAs -->
      <div class="hero-actions">
        <a
          href="#consultation"
          class="btn btn-primary"
          id="aboutHeroBookBtn">Book Consultation</a>
        <a
          href="index.html#treatments"
          class="btn btn-secondary"
          id="aboutHeroTreatmentsBtn">Explore Treatments</a>
      </div>
    </div>

    <!-- Right: Premium Gold Placeholder -->
    <div class="about-hero-img-wrapper reveal">
      <div class="about-hero-img-container">
        <img
          src="https://placehold.co/1200x800/FAF7F2/C5A880?text=Luxury+Aesthetic+Oasis"
          alt="Luxury Aesthetics Premium Lounge interior"
          class="about-hero-img" />
      </div>
    </div>
  </div>
</section>

<!-- 3. CLINICAL PHILOSOPHY / PILLARS -->
<section class="pillars-section" id="philosophy">
  <div class="container text-center reveal">
    <span class="section-tag">Our Core Philosophy</span>
    <h2>Uncompromising Standards of Care</h2>
    <p class="max-width-600">
      Thousands trust our safe, ethical, and professional approach. We focus
      on personalised care, creating a relaxing experience supported by the
      latest FDA-approved technologies and scientifically proven methods.
    </p>
  </div>

  <div class="container pillars-grid">
    <!-- Pillar 1: Safe & Ethical -->
    <div class="pillar-card reveal reveal-stagger">
      <div class="pillar-icon-wrapper">
        <i data-lucide="shield-check" style="width: 28px; height: 28px"></i>
      </div>
      <h3>Safe & Ethical Care</h3>
      <p>
        We advocate for transparent clinical diagnostics with absolute
        integrity. Sourcing only US FDA-approved technologies, we guarantee
        the highest tissue safety standards with zero hard-sell upsells.
      </p>
    </div>

    <!-- Pillar 2: Personalised Journey -->
    <div class="pillar-card reveal reveal-stagger">
      <div class="pillar-icon-wrapper">
        <i data-lucide="heart" style="width: 28px; height: 28px"></i>
      </div>
      <h3>Personalised Rejuvenation</h3>
      <p>
        No cookie-cutter solutions. Every skin, hair, and body contour goal
        is mapped out via advanced 3D diagnostics, creating a bespoke
        treatment schedule tailored for long-lasting, naturally radiant
        results.
      </p>
    </div>

    <!-- Pillar 3: Expert Medical Leadership -->
    <div class="pillar-card reveal reveal-stagger">
      <div class="pillar-icon-wrapper">
        <i data-lucide="award" style="width: 28px; height: 28px"></i>
      </div>
      <h3>Dermatologist Led</h3>
      <p>
        All advanced cosmetic treatments, injectables, and medical laser
        operations are administered directly by or under the close, active
        supervision of certified, chief medical dermatologists.
      </p>
    </div>

    <!-- Pillar 4: Luxury Sanctuary -->
    <div class="pillar-card reveal reveal-stagger">
      <div class="pillar-icon-wrapper">
        <i data-lucide="coffee" style="width: 28px; height: 28px"></i>
      </div>
      <h3>Boutique Hospitality</h3>
      <p>
        Experience a relaxing medical sanctuary. Our reception spaces and
        private treatment rooms are designed as clean, calming architectural
        suites to soothe anxious clinical feelings.
      </p>
    </div>
  </div>
</section>

<!-- 4. SIGNATURE FOCUS CATEGORIES (FACE, HAIR, BODY) -->
<section class="categories-section" id="categories">
  <div class="container text-center reveal">
    <span class="section-tag">Clinical Rejuvenation</span>
    <h2>Our Signature Treatment Portfolios</h2>
    <p class="max-width-600">
      Discover targeted clinical enhancements designed by expert
      dermatologists to elevate your structural symmetry, restore hair
      growth, and shape custom silhouettes:
    </p>
  </div>

  <div class="container categories-grid">
    <!-- Category 1: Face -->
    <div class="category-card reveal reveal-stagger">
      <div class="category-img-container">
        <span class="category-badge">Radiance</span>
        <img
          src="https://placehold.co/1024x683/F4EFE6/C5A880?text=Face+Aesthetics"
          alt="Premium Face Aesthetics"
          class="category-img" />
      </div>
      <div class="category-content">
        <h3>Face Care Portfolio</h3>
        <p>
          From botanical medi-peels and vortex HydraFacials to structural
          Allergan Botox and Juvederm fillers. Rejuvenate, contour, and
          restore naturally youthful skin symmetry.
        </p>
        <a href="index.html#treatments" class="btn-link">Explore Face <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Category 2: Hair -->
    <div class="category-card reveal reveal-stagger">
      <div class="category-img-container">
        <span class="category-badge">Restoration</span>
        <img
          src="https://placehold.co/1024x683/FAF7F2/C5A880?text=Hair+Restoration"
          alt="Clinical Hair Restoration"
          class="category-img" />
      </div>
      <div class="category-content">
        <h3>Hair Solutions Portfolio</h3>
        <p>
          Advanced Platelet Rich Plasma (PRP), Next-Gen Growth Factor
          Concentrate (GFC), and scientific QR678 scalp therapy to stop
          severe hair fall and trigger follicular density growth.
        </p>
        <a href="services-hair.html" class="btn-link">Explore Hair <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>

    <!-- Category 3: Body -->
    <div class="category-card reveal reveal-stagger">
      <div class="category-img-container">
        <span class="category-badge">Contouring</span>
        <img
          src="https://placehold.co/1024x683/F4EFE6/C5A880?text=Body+Contouring"
          alt="Advanced Body Aesthetics"
          class="category-img" />
      </div>
      <div class="category-content">
        <h3>Body Aesthetics Portfolio</h3>
        <p>
          Precision fat layer freezing with FDA-approved CoolSculpting and
          tissue tightening with Exilis 360 to smoothly contour and lift
          your natural body silhouette painlessly.
        </p>
        <a href="index.html#treatments" class="btn-link">Explore Body <i data-lucide="arrow-right" style="width: 16px"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- 6. CLINICAL METRICS (Stats Bar) -->
<section class="trust-bar" id="stats">
  <div class="container trust-bar-grid">
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="15" data-suffix="+">0</span>
      <span class="counter-label">Expert Dermatologists</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="2000" data-suffix="+">0</span>
      <span class="counter-label">Happy Patients</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="20" data-suffix="">0</span>
      <span class="counter-label">Private Treatment Rooms</span>
    </div>
    <div class="counter-item reveal reveal-stagger">
      <span class="counter-number" data-target="10" data-suffix="+">0</span>
      <span class="counter-label">Years of Excellence</span>
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
<?php

require_once 'widgets/recent-articles.php';

require_once 'footer.php';

?>