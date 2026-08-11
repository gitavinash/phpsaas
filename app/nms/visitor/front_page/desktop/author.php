<?php

require_once 'header.php';

$authorName = htmlspecialchars($authorInfo['profile_display_name'] ?? $authorInfo['user_username'] ?? 'Author');
$authorImage = !empty($authorInfo['profile_image']) ? htmlspecialchars($authorInfo['profile_image']) : '';

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
  <!-- Full-Width Author Hero Banner -->
  <div class="author-header-hero">
    <div class="container">
      <nav class="article-breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="sep">/</span>
        <a href="#">Authors</a>
        <span class="sep">/</span>
        <span class="current"><?php echo $authorName; ?></span>
      </nav>

      <div class="author-hero-card">
        <?php if (!empty($authorImage)): ?>
          <img src="<?php echo $authorImage; ?>" alt="<?php echo $authorName; ?>" class="author-avatar-lg" />
        <?php else: ?>
          <div class="author-avatar-lg author-avatar-fallback">
            <span><?php echo strtoupper(substr($authorName, 0, 1)); ?></span>
          </div>
        <?php endif; ?>

        <div class="author-meta-info">
          <span class="author-role-tag">JOURNALIST &amp; CONTRIBUTOR</span>
          <h1><?php echo $authorName; ?></h1>

          <?php
          $socials = [
            'facebook' => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0"/></svg>',
            'twitter' => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557a9.93 9.93 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.92 4.92 0 0 0-8.384 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.247a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.513 14.009-14.009 0-.213-.005-.425-.014-.636A10.012 10.012 0 0 0 24 4.557z"/></svg>',
            'linkedin' => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.327-.027-3.037-1.849-3.037-1.851 0-2.132 1.445-2.132 2.939v5.667H9.358V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.368 4.267 5.455v6.285zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM7.119 20.452H3.554V9h3.565v11.452zM22.225 0H1.771C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.771 24h20.451C23.2 24 24 23.229 24 22.271V1.723C24 .771 23.2 0 22.225 0z"/></svg>',
            'youtube' => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a2.994 2.994 0 0 0-2.107-2.117C19.163 3.5 12 3.5 12 3.5s-7.163 0-9.391.569A2.994 2.994 0 0 0 .502 6.186C0 8.414 0 12 0 12s0 3.586.502 5.814a2.994 2.994 0 0 0 2.107 2.117C4.837 20.5 12 20.5 12 20.5s7.163 0 9.391-.569a2.994 2.994 0 0 0 2.107-2.117C24 15.586 24 12 24 12s0-3.586-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
          ];
          $socialLinks = [];
          if (!empty($authorInfo['social_links'])) {
            if (is_array($authorInfo['social_links'])) {
              $socialLinks = $authorInfo['social_links'];
            } else {
              $socialLinks = json_decode($authorInfo['social_links'], true);
              if (!is_array($socialLinks)) $socialLinks = [];
            }
          }
          $hasAny = false;
          foreach ($socials as $platform => $icon) {
            if (!empty($socialLinks[$platform])) {
              $hasAny = true;
              break;
            }
          }
          if ($hasAny) {
            echo '<div class="author-social-row">';
            foreach ($socials as $platform => $icon) {
              $url = $socialLinks[$platform] ?? '';
              if (!empty($url)) {
                echo '<a href="' . htmlspecialchars($url) . '" target="_blank" title="' . ucfirst($platform) . '">' . $icon . '</a>';
              }
            }
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="section-header author-articles-header">
      <h2>Articles by <?php echo $authorName; ?></h2>
    </div>

    <?php if (!empty($authorArticles)): ?>
      <div class="category-news-grid" id="authorNewsGrid">
        <?php foreach ($authorArticles as $article): ?>
          <?php
          $linkUrl = htmlspecialchars($article['link_url'] ?? '#');
          $newsTitle = htmlspecialchars($article['news_title'] ?? '');
          $newsImg = htmlspecialchars($article['news_image'] ?? '');
          $newsPaid = !empty($article['news_paid']) && $article['news_paid'] == 1;
          $metaDesc = htmlspecialchars(!empty($article['meta_description']) ? $article['meta_description'] : (trimParagraph($article['news_description'] ?? '', 150) ?? ''));
          $publishedDate = !empty($article['news_published']) ? date('M d, Y', strtotime($article['news_published'])) : '';
          $catTitle = $article['category_title'] ?? '';
          $catUrl = $article['category_url'] ?? '';
          if (!empty($catUrl) && $catUrl[0] !== '/') $catUrl = '/' . ltrim($catUrl, '/');
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
              <span class="tag">
                <?php if (!empty($catTitle)): ?>
                  <?php if (!empty($catUrl)): ?>
                    <a href="<?php echo htmlspecialchars($catUrl); ?>"><?php echo htmlspecialchars($catTitle); ?></a>
                  <?php else: ?>
                    <?php echo htmlspecialchars($catTitle); ?>
                  <?php endif; ?>
                <?php endif; ?>
              </span>
              <h2><a href="<?php echo $linkUrl; ?>"><?php echo $newsTitle; ?></a></h2>
              <p><?php echo removeInlineStyles($metaDesc, true); ?></p>
              <div class="category-card-meta">
                <span class="date"><?php echo $publishedDate; ?></span>
                <span class="read-more">Read Story <span class="arrow">→</span></span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>No articles found for this author.</p>
    <?php endif; ?>

    <!-- Infinite Scroll Trigger Element -->
    <?php if (!empty($nextPageUrl)): ?>
      <div id="authorInfiniteTrigger" class="infinite-scroll-trigger" data-next-page="<?php echo htmlspecialchars($nextPageUrl); ?>">
        <div class="infinite-loader-content">
          <div class="infinite-spinner"></div>
          <span>Loading More Stories...</span>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>