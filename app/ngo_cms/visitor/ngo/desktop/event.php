<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<!-- Detail Hero -->
<section
  class="detail-hero"
  style="
        background-image: url(<?php echo $event_image ?>);
      ">
  <div class="container detail-hero-content">
    <span
      class="event-badge"
      style="
            background: var(--secondary-color);
            color: var(--primary-color);
          "><?php echo ucfirst($category_title) ?></span>
    <h1 style="font-size: 3.5rem; margin-top: 1rem">
      <?php echo ucwords($event_title) ?>
    </h1>
  </div>
</section>

<!-- Event Meta Strip -->
<div class="event-meta-strip">
  <div class="container event-meta-grid">
    <div class="meta-item">
      <i class="fas fa-calendar-alt"></i>
      <div>
        <strong style="display: block">Date</strong>
        <span><?php echo date('F j, Y', strtotime($event_start_date)) ?></span>
      </div>
    </div>
    <div class="meta-item">
      <i class="fas fa-clock"></i>
      <div>
        <strong style="display: block">Time</strong>
        <span><?php echo date('h:i A', strtotime($event_start_time)) . '-' . date('h:i A', strtotime($event_end_time)) ?></span>
      </div>
    </div>
    <div class="meta-item">
      <i class="fas fa-map-marker-alt"></i>
      <div>
        <strong style="display: block">Location</strong>
        <span><?php echo ucwords($event_location) ?></span>
      </div>
    </div>
    <?php

    if ($tagName != '') {
      $tagName = explode(', ', $tagName);
      echo "<div class='meta-item'>
          <i class='fas fa-user-md'></i>
          <div>
            <strong style='display: block'>Services</strong><span>";
      for ($i = 0; $i < sizeof($tagName); $i++) {
        if ($i == 0) {
          echo ucwords($tagName[$i]);
        } else {
          echo ', ' . ucwords($tagName[$i]);
        }
      }
      echo "</span></div>
        </div>";
    }

    ?>
  </div>
</div>

<!-- Main Content Layout -->
<div class="container sidebar-layout">
  <!-- Main Content -->
  <div class="content-area">
    <?php echo $event_description ?>
    <?php
    $eventGallery = json_decode((string)$event_gallery, true);
    $eventGallery = is_array($eventGallery) ? $eventGallery : [];
    ?>

    <?php if (!empty($eventGallery)) { ?>
      <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Event Gallery</h3>
      <div class="content-gallery">
        <?php
        foreach ($eventGallery as $index => $galleryImage) {
          $imagePath = isset($galleryImage['path']) ? trim($galleryImage['path']) : '';
          if ($imagePath === '') {
            continue;
          }

          $imageTitle = isset($galleryImage['title']) ? trim($galleryImage['title']) : '';
          $imageCaption = $imageTitle !== '' ? $imageTitle : $event_title . ' - Image ' . ($index + 1);
          $safePath = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
          $safeCaption = htmlspecialchars($imageCaption, ENT_QUOTES, 'UTF-8');

          echo '<figure class="event-gallery-item">';
          echo '<img src="' . $safePath . '" alt="' . $safeCaption . '" data-caption="' . $safeCaption . '" class="event-gallery-image" loading="lazy" />';
          echo '<figcaption class="event-gallery-caption">' . $safeCaption . '</figcaption>';
          echo '</figure>';
        }
        ?>
      </div>
    <?php } ?>
  </div>

  <!-- Sidebar -->
  <?php

  require_once 'event-sidebar.php';

  ?>
</div>

<div id="footer-placeholder"></div>
<?php

require_once 'footer.php';

?>