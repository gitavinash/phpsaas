<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner" style="background-image: url(<?php echo $treatment_image ?>);">
  <div class="container inner-banner-content">
    <h1><?php echo $treatment_title; ?></h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
      <span><i
          data-lucide="chevron-right"
          style="width: 14px; height: 14px"></i></span>
      <a href="<?php echo $category_url; ?>"><?php echo $category_title ?></a>
      <span><i
          data-lucide="chevron-right"
          style="width: 14px; height: 14px"></i></span>
      <span style="color: var(--color-primary-gold)"><?php echo $treatment_title; ?></span>
    </div>
  </div>
</section>

<?php

echo $treatment_description;

require_once 'widgets/recent-articles.php';

require_once 'footer.php';

?>