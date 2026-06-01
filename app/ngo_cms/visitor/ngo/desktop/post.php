<?php

require_once 'header.php'; ?>
<div id="header-placeholder"></div>

<!-- Detail Hero -->
<section
  class="detail-hero"
  style="
        background-image: url(&quot;assets/images/gallery/education/1000071271.jpg&quot;);
      ">
  <div class="container detail-hero-content">
    <span
      class="event-badge"
      style="
            background: var(--secondary-color);
            color: var(--primary-color);
            margin-bottom: 1rem;
          "><?php echo ucwords($category_title); ?></span>
    <h1 style="font-size: 3rem; line-height: 1.2">
      <?php echo ucfirst($post_title); ?>
    </h1>
  </div>
</section>

<!-- Main Content Layout -->
<div class="container sidebar-layout">
  <!-- Main Article -->
  <article class="content-area">
    <div class="blog-meta">
      <div style="display: flex; align-items: center; gap: 0.5rem">
        <img src="assets/images/achievements/woman.jpeg" alt="Author" />
        <span>By <strong><?php echo ucwords($profile_display_name); ?></strong></span>
      </div>
      <span><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($post_time)); ?></span>
    </div>

    <div class="article-content">
      <?php echo $post_description; ?>
    </div>

    <div class="share-buttons">
      <strong style="margin-right: 1rem">Share this story:</strong>
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo SITE_URL .
          $link_url; ?>" class="share-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
      <a href="http://twitter.com/share?text=<?php echo urlencode($post_title); ?>&url=<?php echo SITE_URL .
    $link_url; ?>" class="share-btn" target="_blank"><i class="fab fa-x"></i></a>
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo SITE_URL .
          $link_url; ?>&title=<?php echo urlencode(
    $post_title,
); ?>" class="share-btn" target="_blank"><i class="fab fa-linkedin-in"></i></a>
      <a href="https://wa.me/?text=<?php echo urlencode($post_title) .
          ' ' .
          SITE_URL .
          $link_url; ?>" class="share-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
    </div>

    <div class="author-box">
      <img
        src="<?php echo !empty($profile_image)
            ? $profile_image
            : 'https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg'; ?>"
        alt="<?php echo ucwords($profile_display_name); ?>"
        style="width: 80px; height: 80px; border-radius: 50%" />
      <div>
        <h4 style="margin-bottom: 0.5rem"><?php echo ucwords($profile_display_name); ?></h4>
        <p style="font-size: 0.9rem; color: #666">
          <!-- <?php echo $profile_bio; ?> -->
        </p>
      </div>
    </div>
  </article>

  <!-- Sidebar -->
  
  <?php require_once 'blog-sidebar.php'; ?>

</div>

<!-- Related Articles -->
<?php
$articleList = similarArticles($connection, $post_id, 'post', 2);

if (count($articleList) > 0) {
    echo "
<section class='section-padding' style='background: #f4f4f4'>
  <div class='container'>
    <h2 style='margin-bottom: 2rem'>You Might Also Like</h2>
    <div
      class='home-events-grid'
      style='grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))'>";

    foreach ($articleList['articles'] as $article) {
        echo "
        <div
          style='
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
              '>
          <img
            src='{$article['image']}'
            alt='{$article['title']}'
            style='width: 100%; height: 200px; object-fit: cover' />
          <div style='padding: 1.5rem'>
            <span
              style='
                    color: var(--secondary-color);
                    font-size: 0.8rem;
                    font-weight: 700;
                    text-transform: uppercase;
                  '>{$article['category_title']}</span>
            <h3 style='font-size: 1.2rem; margin: 0.5rem 0'>
              {$article['title']}
            </h3>
            <a
              href='{$article['link_url']}'
              style='
                    color: var(--primary-color);
                    font-weight: 600;
                    text-decoration: none;
                  '>Read More &rarr;</a>
          </div>
        </div>
      ";
    }

    echo "</div>
  </div>
</section>";
}
?>

<div id="footer-placeholder"></div>
<?php require_once 'footer.php';
?>
