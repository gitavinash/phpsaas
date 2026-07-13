<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner" style="background-image: url(<?php echo $page_image ?>);">
  <div class="container inner-banner-content">
    <h1><?php echo $page_title ?></h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <a href="/about-us/">About Us</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);"><?php echo $page_title ?></span>
    </div>
  </div>
</section>

<!-- 3. SPECIALISTS GRID SECTION -->
<section class="specialists-section" id="teamGrid">
  <div class="container text-center reveal">
    <?php echo $page_description ?>
  </div>

  <?php

  if ($checkTeam = mysqli_query($connection, "SELECT link_url, post_image, post_title, post_description FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "post ON link_relation_id=post_id AND post_type='team' WHERE link_type='post' AND link_status!=301 AND link_site_id='$site_id'")) {
    if (mysqli_num_rows($checkTeam) > 0) {
      echo "<div class='container specialists-grid'>";
      while ($teamData = mysqli_fetch_assoc($checkTeam)) {
        extract($teamData);
        $teamInfo = explode(',', $post_title);
        $teamName = $teamInfo[0];
        $teamRole = ((isset($teamInfo[1]) && !empty($teamInfo[1])) ? ', ' . $teamInfo[1] : '');
        $teamPosition = ((isset($teamInfo[2]) && !empty($teamInfo[2])) ? $teamInfo[2] : '');
        $drDetails = explode(",", $post_description);
        echo "<div class='specialist-card reveal reveal-stagger'>
      <div class='specialist-img-wrapper'>
        <img src='{$post_image}' alt='{$post_title}'>
      </div>
      <div class='specialist-info'>
        <h3>{$teamName} {$teamRole}</h3>
        <span class='specialist-role'>{$teamPosition}</span>
        <div class='specialist-description'>
          <p>{$post_description}</p>
        </div>
        <div class='card-footer-cta'>
          <a href='/contact-us/?source=" . urlencode(strtolower($teamName)) . "' class='read-profile-link'>Book Consultation <i data-lucide='chevron-right' style='width: 16px; height: 16px;'></i></a>
        </div>
      </div>
    </div>";
      }
      echo "</div>";
    }
  }

  ?>
</section>

<?php

require_once 'widgets/governance.php';

require_once 'footer.php';

?>