<?php

require_once 'header.php';

?>
<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1><?php echo $category_title ?></h1>
    <div class="breadcrumb-path">
      <a href="index.html">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <a>Treatments</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);"><?php echo $category_title ?></span>
    </div>
  </div>
</section>

<!-- 4. ALTERNATING CLINICAL TREATMENT ROWS -->
<section class="treatments-section" id="treatments">
  <?php echo $category_description ?>

  <div class="container treatment-row-container">

    <?php

    if ($serviceData = mysqli_query($connection, "SELECT service_title, service_image, link_url, meta_description FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "service ON service_id = service_id AND service_type='treatment' INNER JOIN " . TABLE_PREFIX . "meta ON link_relation_id=meta_id WHERE link_site_id=" . SITE_ID . " ORDER BY service_title ASC, link_published DESC")) {
      if (mysqli_num_rows($serviceData) > 0) {
        while ($row = mysqli_fetch_assoc($serviceData)) {
          extract($row);
          echo "<div class='treatment-row reveal'>
      <div class='treatment-img-col'>
        <div class='treatment-img-wrapper'>
          <img src='{$service_image}' alt='{$service_title}' />
        </div>
      </div>

      <div class='treatment-content-col'>
        <span class='section-tag'>Non-Surgical Skin Lift</span>
        <h3>{$service_title}</h3>
        <p class='treatment-desc-p'>
          {$meta_description}
        </p>

        <div class='row-actions'>
          <a href='{$link_url}' class='btn btn-primary'>Know more...</a>
        </div>
      </div>
    </div>";
        }
      }
    }

    ?>

  </div>
</section>

<!-- 5. INTERACTIVE BEFORE & AFTER SLIDER -->
<section class="before-after-section" id="results">
  <div class="container text-center reveal">
    <span class="section-tag">Clinical Transformation</span>
    <h2>Real Contouring Results</h2>
    <p class="max-width-600">
      Slide to evaluate the untouched, verified results of our CoolSculpting and fat reduction therapies:
    </p>
  </div>

  <div class="container transform-container-grid">
    <!-- Left side: Drag Comparison Slider -->
    <div class="before-after-wrapper reveal">
      <!-- After Image -->
      <div class="slider-img slider-img-after">
        <img src="https://placehold.co/1024x683/FAF7F2/C5A880?text=After:+CoolSculpting+Contours" alt="CoolSculpting After Treatment">
        <span class="slider-badge badge-after">After</span>
      </div>

      <!-- Before Image -->
      <div class="slider-img slider-img-before">
        <img src="https://placehold.co/1024x683/FAF7F2/C5A880?text=Before:+Stubborn+Fat+Pockets" alt="Stubborn Fat Pockets Before Treatment">
        <span class="slider-badge badge-before">Before</span>
      </div>

      <!-- Drag Handle Line & Slider button -->
      <div class="slider-handle">
        <button class="slider-handle-button" aria-label="Drag slider to compare before and after">
          <i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i>
          <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i>
        </button>
      </div>
    </div>

    <!-- Right side: Transformation details card -->
    <div class="transform-details-card reveal">
      <span class="section-tag gold-accent">Featured Treatment Journey</span>
      <h3 class="transform-title">CoolSculpting & Fat Reduction</h3>
      <p class="transform-details-desc">
        Cryolipolysis technology precision freezing targeted fat cells in stubborn body regions. Safely processed out by the immune system for structural contouring.
      </p>

      <div class="transform-metrics">
        <div class="metric-item">
          <h4 class="metric-item-num">2 Sessions</h4>
          <span class="metric-item-lbl">Treatment Span</span>
        </div>
        <div class="metric-item">
          <h4 class="metric-item-num">24% Reduction</h4>
          <span class="metric-item-lbl">Fat Layer</span>
        </div>
      </div>

      <div style="margin-top: 4.5rem;">
        <a href="#consultation" class="btn btn-primary">Achieve Similar Results</a>
      </div>
    </div>
  </div>
</section>
<?php

require_once 'footer.php';

?>