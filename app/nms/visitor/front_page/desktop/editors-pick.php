<?php

require_once 'header.php';

$categoryIds = [201, 202, 204, 386, 399];
$category_title = "Editors' Picks";
$categorySinglePost = getNewsCategoryPost($connection, $categoryIds, 'news', 'new', 'all', 0, 20);

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : (isset($_GET['p']) ? (int)$_GET['p'] : 1);
$totalPages = defined('TOTAL_PAGE') ? (int)TOTAL_PAGE : 1;

$nextPageUrl = '';
if ($currentPage < $totalPages) {
  $queryParams = $_GET;
  $queryParams['page'] = $currentPage + 1;
  $nextPageUrl = '?' . http_build_query($queryParams);
}

?>
<section class="category-page">
  <!-- Full-Width Category Hero Banner -->
  <div class="category-header-hero">
    <div class="container">
      <nav class="article-breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="sep">/</span>
        <a href="#">Categories</a>
        <span class="sep">/</span>
        <span class="current"><?php echo htmlspecialchars($category_title ?? 'News'); ?></span>
      </nav>

      <div class="category-hero-content">
        <!-- <span class="category-hero-kicker">EDITORIAL COVERAGE</span> -->
        <h1><?php echo htmlspecialchars($category_title ?? 'Category News'); ?></h1>
        <p class="category-hero-desc">In-depth reporting, market intelligence, and analytical commentary on <?php echo htmlspecialchars(strtolower($category_title ?? 'this sector')); ?>.</p>
      </div>
    </div>
  </div>

  <div class="container">
    <!-- Category News Grid -->
    <div class="category-news-grid" id="categoryNewsGrid">
      <?php if (!empty($categorySinglePost)): ?>
        <?php foreach ($categorySinglePost as $post): ?>
          <?php
          $linkUrl = htmlspecialchars($post['link_url'] ?? '#');
          $newsTitle = htmlspecialchars($post['news_title'] ?? '');
          $newsImg = htmlspecialchars($post['news_image'] ?? '');
          $newsPaid = !empty($post['news_paid']) && $post['news_paid'] == 1;
          $metaDesc = htmlspecialchars(!empty($post['meta_description']) ? $post['meta_description'] : (trimParagraph($post['news_description'] ?? '', 150) ?? ''));
          $authorName = htmlspecialchars($post['profile_display_name'] ?? '');
          $authorId = $post['author_id'] ?? ($post['news_owner_id'] ?? '');
          $authorUrl = '';
          if (!empty($authorName) && !empty($authorId)) {
            $authorSlug = urlencode(strtolower(str_replace(' ', '-', trim($post['profile_display_name'] ?? ''))));
            $authorUrl = "/author/{$authorId}-{$authorSlug}-articles/";
          }
          ?>
          <article class="category-news-card">
            <div class="category-card-img">
              <?php if ($newsPaid): ?>
                <span class="paid-badge overlay">
                  <svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg>
                  Premium
                </span>
              <?php endif; ?>
              <a href="<?php echo $linkUrl; ?>">
                <img src="<?php echo $newsImg; ?>" alt="<?php echo $newsTitle; ?>" loading="lazy" />
              </a>
            </div>
            <div class="category-card-content">
              <span class="tag"><?php echo htmlspecialchars($category_title ?? 'News'); ?></span>
              <h2><a href="<?php echo $linkUrl; ?>"><?php echo $newsTitle; ?></a></h2>
              <p><?php echo removeInlineStyles($metaDesc, true); ?></p>
              <div class="category-card-meta">
                <div class="meta-left">
                  <span class="date"><?php echo date('M d, Y', strtotime($post['news_published'])); ?></span>
                  <?php if (!empty($authorName)): ?>
                    <span class="meta-sep">•</span>
                    <span class="author"> <?php if (!empty($authorUrl)): ?><a href="<?php echo $authorUrl; ?>"><?php echo $authorName; ?></a><?php else: ?><?php echo $authorName; ?><?php endif; ?></span>
                  <?php endif; ?>
                </div>
                <span class="read-more">Read Story <span class="arrow">→</span></span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No stories found in this category.</p>
      <?php endif; ?>
    </div>

    <!-- Infinite Scroll Trigger Element for Server Pages -->
    <?php if (!empty($nextPageUrl)): ?>
      <div id="categoryInfiniteTrigger" class="infinite-scroll-trigger" data-next-page="<?php echo htmlspecialchars($nextPageUrl); ?>">
        <div class="infinite-loader-content">
          <div class="infinite-spinner"></div>
          <span>Loading More Stories...</span>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>