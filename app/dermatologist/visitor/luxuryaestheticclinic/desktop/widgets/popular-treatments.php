<!-- 6. MOST POPULAR TREATMENTS -->

<?php
$treatements = getData($connection, 'treatment', 'treatment', 8, true);
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
        <a href='{$link_url}' class='btn-link'>Explore Treatment
          <i data-lucide='arrow-right' style='width: 16px'></i></a>
      </div>
    </div>";
  }
  echo "</div></section>";
}
