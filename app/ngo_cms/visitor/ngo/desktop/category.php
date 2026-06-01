<?php

require_once 'header.php';

?>

    <div id="header-placeholder"></div>

    <header class="blogPage-header">
      <div class="container">
        <h1><?php echo ucfirst($category_title) ?></h1>
        <div style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          <?php echo $category_description; ?>
        </div>
      </div>
    </header>

    <section class="section-padding">
      <div class="container blog-layout">
        <!-- Main Content -->
        <div class="blog-grid">
          <?php

                for ($i = 0; $i < 100; $i++) {
                    if (isset($categorySinglePost[$i])) {
                        extract($categorySinglePost[$i]);
                        echo "<div class='blog-card'><a href='{$link_url}' title='{$post_title}'>
            <img
              src='{$post_image}'
              alt='{$post_title}'
              class='blog-img'
            />
            <div class='blog-content'>
              <span class='blog-date'>March 10, 2026</span>
              <h3>{$post_title}</h3>
              <div class='blog-description'>
                " . strip_tags($post_description) . "
              </div>
              <a href='{$link_url}' class='read-more'>Read Full Story →</a>
            </div></a>
          </div>";
                    }
                }
                ?>
                <?php

                require_once 'pagination.php';

                ?>
        </div>

        <!-- Sidebar -->
         <?php require_once 'blog-sidebar.php'; ?>
      </div>
    </section>

    <div id="footer-placeholder"></div>
    <?php require_once 'footer.php'; ?>
