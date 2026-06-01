<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<header class="pageTemp-header">
  <div class="container">
    <h1><?php echo $page_title ?></h1>
  </div>
</header>

<section class="section-padding" style="padding-top: 0">
  <div class="container">
    <div class="pageTemp-content">
      <?php

      echo $page_description;

      ?>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>
<?php
require_once 'footer.php';
?>