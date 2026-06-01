<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<header class="page-header" style="background:linear-gradient(
            135deg,
            rgba(13, 59, 102, 0.92),
            rgba(13, 59, 102, 0.75)
          ),
           url(<?php echo $service_image ?>) center/cover;">
  <div class="container">
    <h1><?php echo $service_title ?></h1>
  </div>
</header>

<!-- ====== ABOUT INTRO ====== -->
<section class="section-padding">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="about-intro-content">
          <?php echo $service_description ?>
        </div>
      </div>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>
<?php

require_once 'footer.php';

?>