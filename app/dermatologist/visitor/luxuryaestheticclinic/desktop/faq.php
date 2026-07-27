<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1><?php echo $page_title ?></h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);"><?php echo $page_title ?></span>
    </div>
  </div>
</section>

<section class="faq-categories-section">

  <?php

  $currentCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

  $categoryInfo = array();
  if ($checkCategory = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_type='question' AND category_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkCategory) > 0) {
      while ($category = mysqli_fetch_assoc($checkCategory)) {
        $categoryInfo[] = $category;
      }
    }
  }

  // 1. Find FAQ category
  foreach ($categoryInfo as $cat) {
    if (stripos($cat['category_title'], 'faq') !== false) {
      $currentCategoryId = $cat['category_id'];
      break;
    }
  }

  // 2. If no FAQ found, find Homepage category
  if ($currentCategoryId == 0) {
    foreach ($categoryInfo as $cat) {
      if (
        stripos($cat['category_title'], 'home') !== false ||
        stripos($cat['category_title'], 'homepage') !== false
      ) {
        $currentCategoryId = $cat['category_id'];
        break;
      }
    }
  }

  // 3. If neither found, use the first category
  if ($currentCategoryId == 0 && !empty($categoryInfo)) {
    $currentCategoryId = $categoryInfo[0]['category_id'];
  }

  if ($checkFAQ = mysqli_query($connection, "SELECT post_image, post_title, post_description FROM " . TABLE_PREFIX . "post WHERE post_category=" . $currentCategoryId . " AND post_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkFAQ) > 0) {
      echo "<div class='faq-category-block' id='skinFaqs'>
            <div class='container'>
            <div class='faq-wrapper reveal'>";
      while ($faq = mysqli_fetch_assoc($checkFAQ)) {
        extract($faq);
        echo "<div class='faq-item'>
                <div class='faq-header'>
                  <span class='faq-question'>{$post_title}</span>
                  <div class='faq-icon-box'><i data-lucide='chevron-down'></i></div>
                </div>
                <div class='faq-body'>
                  <div class='faq-content'>
                    {$post_description}
                  </div>
                </div>
              </div>";
      }
      echo "</div>
          </div>
        </div>";
    }
  }

  ?>
</section>

<?php

require_once 'footer.php';

?>