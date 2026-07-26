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
  foreach ($treatements as $idx => $treatment) {
    extract($treatment);
    $num = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
    echo "<a href='{$link_url}' class='treatment-card reveal reveal-stagger' aria-label='{$title}'>
      <!-- Full-bleed image -->
      <div class='treatment-img-container'>
        <img src='{$image}' alt='{$title}' class='treatment-img' loading='lazy' />
        <!-- Persistent bottom fade -->
        <div class='treatment-img-fade'></div>
        <!-- Hover overlay -->
        <div class='treatment-hover-overlay'></div>
      </div>

      <!-- Always-visible footer inside image -->
      <div class='treatment-card-footer'>
        <span class='treatment-card-num'>{$num}</span>
        <div class='treatment-card-info'>
          <span class='treatment-badge'>{$category}</span>
          <h3 class='treatment-card-title'>{$title}</h3>
        </div>
      </div>

      <!-- Hover CTA reveal -->
      <div class='treatment-card-cta'>
        <span>Explore Treatment</span>
        <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='5' y1='12' x2='19' y2='12'></line><polyline points='12 5 19 12 12 19'></polyline></svg>
      </div>
    </a>";
  }
  echo "</div></section>";
}

?>
