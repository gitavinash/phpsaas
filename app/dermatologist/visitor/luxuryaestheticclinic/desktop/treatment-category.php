<?php

require_once 'header.php';

?>
<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner" style="background-image: url('<?php echo $category_image ?>');">
  <div class="container inner-banner-content">
    <h1><?php echo $category_title ?></h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
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

    $categoryTreatmentQuery = "SELECT treatment_title, treatment_image, link_url, meta_description FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "treatment ON treatment_id = link_relation_id AND treatment_category='{$category_id}' LEFT JOIN " . TABLE_PREFIX . "meta ON link_relation_id=meta_id WHERE link_status!=301 AND link_site_id=" . SITE_ID . " ORDER BY treatment_title ASC, link_published DESC";

    if ($treatmentData = mysqli_query($connection, $categoryTreatmentQuery)) {
      if (mysqli_num_rows($treatmentData) > 0) {
        while ($row = mysqli_fetch_assoc($treatmentData)) {
          extract($row);
          echo "<div class='treatment-row reveal'>
      <div class='treatment-img-col'>
        <div class='treatment-img-wrapper'>
          <img src='{$treatment_image}' alt='{$treatment_title}' />
        </div>
      </div>

      <div class='treatment-content-col'>
        <h3>{$treatment_title}</h3>
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

<?php

require_once 'footer.php';

?>