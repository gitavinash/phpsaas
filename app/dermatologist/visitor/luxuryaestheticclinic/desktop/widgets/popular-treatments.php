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
