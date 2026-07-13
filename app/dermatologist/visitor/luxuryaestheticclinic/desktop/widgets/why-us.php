<?php

if ($checkConsultationWidget = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id WHERE link_url ='/why-us---widget/' AND link_status=200 AND link_site_id=" . SITE_ID)) {

  if (mysqli_num_rows($checkConsultationWidget) != 0) {
    extract(mysqli_fetch_assoc($checkConsultationWidget));
    echo $page_description;
  } else {
    echo "
<section class='why-choose-section' id='why-us'>
  <div class='container text-center reveal'>
    <span class='section-tag'>Distinctive Quality</span>
    <h2>Uncompromising Standards of Care</h2>
    <p class='max-width-600'>
      We merge world-class clinical expertise with the comfort and prestige
      of high-end boutique hospitality. Experience clinical aesthetics
      redefined:
    </p>
  </div>

  <div class='container why-grid'>
    <!-- Trait 1: Dermatologist Led -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='shield-check'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Expert Dermatologists</h3>
      <p class='why-desc'>
        All treatments are designed, supervised, and conducted by certified
        medical dermatologists with rich training histories.
      </p>
    </div>

    <!-- Trait 2: FDA Tech -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='check-circle'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>FDA Approved Technologies</h3>
      <p class='why-desc'>
        We invest exclusively in elite medical-grade devices cleared by the
        US FDA, guaranteeing maximum treatment safety.
      </p>
    </div>

    <!-- Trait 3: Bespoke Plans -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='heart'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Personalized Treatment</h3>
      <p class='why-desc'>
        No pre-packaged blueprints. Your facial contours, skin health, and
        genetics dictate your custom enhancement plan.
      </p>
    </div>

    <!-- Trait 4: Luxury Ambience -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='coffee'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Boutique Luxury Experience</h3>
      <p class='why-desc'>
        Indulge in high-end reception spaces, private treatment lounges, and
        warm hospitality designed to soothe anxiety.
      </p>
    </div>

    <!-- Trait 5: Proven Results -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='award'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Proven Natural Results</h3>
      <p class='why-desc'>
        We advocate for subtle, structural enhancements that honor your
        natural anatomy rather than artificial distortions.
      </p>
    </div>

    <!-- Trait 6: Advanced Equipment -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='settings'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Advanced Diagnostics</h3>
      <p class='why-desc'>
        We employ advanced 3D skin analysis hardware to scientifically map
        deep hydration, pigmentation, and UV damage.
      </p>
    </div>

    <!-- Trait 7: Minimal Downtime -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='clock'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Minimal Downtime Procedures</h3>
      <p class='why-desc'>
        Modern cosmetic science allows us to target aging and scarring with
        advanced lasers that cause next to zero redness.
      </p>
    </div>

    <!-- Trait 8: Transparent Pricing -->
    <div class='why-card reveal reveal-stagger'>
      <i
        data-lucide='dollar-sign'
        class='why-icon'
        style='width: 40px; height: 40px'></i>
      <h3 class='why-title'>Transparent Integrity</h3>
      <p class='why-desc'>
        Honest, straightforward consultations with zero hard upsells, hidden
        costs, or unnecessary procedural suggestions.
      </p>
    </div>
  </div>
</section>";
  }
}
