<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<!-- Detail Hero -->
<section
  class="detail-hero"
  style="
        height: 40vh;
        background-image: url(<?php echo $gallery_image ?>);
      ">
  <div class="container detail-hero-content">
    <a
      href="/gallery/"
      style="
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
            display: inline-block;
          "><i class="fas fa-arrow-left"></i> Back to Gallery</a>
    <h1 style="font-size: 3rem"><?php echo $gallery_title ?></h1>
  </div>
</section>

<!-- Album Description -->
<div
  class="container"
  style="margin-top: 3rem; max-width: 800px; text-align: center">
  <?php echo $gallery_description ?>
</div>

<!-- Gallery Grid -->
<section class="section-padding">
  <div class="container">
    <div class="gallery-strip">
      <?php

      $gallery_images = json_decode($gallery_image_list, true);

      foreach ($gallery_images as $image) {
        echo "<img
        src='" . $image['path'] . "'
        alt='" . $image['name'] . "'
        data-caption='" . $image['name'] . "' />";
      }

      ?>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>
<?php

require_once 'footer.php';

?>