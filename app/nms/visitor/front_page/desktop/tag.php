<?php

require_once 'header.php';

$tagTitleFormatted = htmlspecialchars(ucwords($tag_title ?? 'Topic'));

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
  <!-- Full-Width Tag Hero Banner -->
  <div class="category-header-hero">
    <div class="container">
      <nav class="article-breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="sep">/</span>
        <a href="#">Topics</a>
        <span class="sep">/</span>
        <span class="current">#<?php echo $tagTitleFormatted; ?></span>
      </nav>

      <div class="category-hero-content">
        <span class="category-hero-kicker">TOPIC COVERAGE</span>
        <h1>#<?php echo $tagTitleFormatted; ?></h1>
        <p class="category-hero-desc">Explore the latest news, in-depth reports, and executive analysis tagged with #<?php echo $tagTitleFormatted; ?>.</p>
      </div>
    </div>
  </div>

  <div class="container">
    <?php
    $hasPosts = false;
    if (is_array($tagPost) && !empty($tagPost)) {
      $tagId = [];
      for ($i = 0; $i < sizeof($tagPost); $i++) {
        $tagId[] = $tagPost[$i]['tag_relation_id'];
      }
      $resultTags = implode(',', $tagId);

      if ($postList = mysqli_query($connection, "SELECT l.link_url, n.news_title, n.news_image, n.news_paid, n.news_owner_id, m.meta_description, p.profile_display_name, c.category_title, cl.link_url AS category_url, l.link_published as publishedTime, n.news_published FROM " . TABLE_PREFIX . "link l INNER JOIN " . TABLE_PREFIX . "news n ON l.link_relation_id=n.news_id INNER JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id INNER JOIN " . TABLE_PREFIX . "profile p ON n.news_owner_id=p.profile_user_id LEFT JOIN " . TABLE_PREFIX . "category c ON c.category_id=n.news_category AND c.category_type='news' AND c.category_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "link cl ON cl.link_relation_id=c.category_id AND cl.link_type='category' AND cl.link_status=200 AND cl.link_site_id=" . SITE_ID . " WHERE l.link_site_id=" . SITE_ID . " AND l.link_type='news' AND l.link_status=200 AND l.link_relation_id IN ({$resultTags}) ORDER BY n.news_published DESC LIMIT 0, 20")) {
        if (mysqli_num_rows($postList) > 0) {
          $hasPosts = true;
    ?>
          <div class="category-news-grid" id="tagNewsGrid">
            <?php while ($postData = mysqli_fetch_assoc($postList)): ?>
              <?php
              $linkUrl = htmlspecialchars($postData['link_url'] ?? '#');
              $newsTitle = htmlspecialchars($postData['news_title'] ?? '');
              $newsImg = htmlspecialchars($postData['news_image'] ?? '');
              $newsPaid = !empty($postData['news_paid']) && $postData['news_paid'] == 1;
              $metaDesc = htmlspecialchars($postData['meta_description'] ?? '');
              $pubDate = !empty($postData['news_published']) ? date('M d, Y', strtotime($postData['news_published'])) : '';
              $categoryTitle = $postData['category_title'] ?? 'News';
              $categoryUrl = $postData['category_url'] ?? '';
              if (!empty($categoryUrl) && $categoryUrl[0] !== '/') $categoryUrl = '/' . ltrim($categoryUrl, '/');
              $authorName = htmlspecialchars($postData['profile_display_name'] ?? '');
              $authorId = $postData['news_owner_id'] ?? '';
              $authorUrl = '';
              if (!empty($authorName) && !empty($authorId)) {
                $authorSlug = urlencode(strtolower(str_replace(' ', '-', trim($postData['profile_display_name'] ?? ''))));
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
                  <?php if (!empty($categoryTitle)): ?>
                    <span class="tag">
                      <?php if (!empty($categoryUrl)): ?>
                        <a href="<?php echo htmlspecialchars($categoryUrl); ?>"><?php echo htmlspecialchars($categoryTitle); ?></a>
                      <?php else: ?>
                        <?php echo htmlspecialchars($categoryTitle); ?>
                      <?php endif; ?>
                    </span>
                  <?php endif; ?>
                  <h2><a href="<?php echo $linkUrl; ?>"><?php echo $newsTitle; ?></a></h2>
                  <p><?php echo $metaDesc; ?></p>
                  <div class="category-card-meta">
                    <div class="meta-left">
                      <span class="date"><?php echo $pubDate; ?></span>
                      <?php if (!empty($authorName)): ?>
                        <span class="meta-sep">•</span>
                        <span class="author"> <?php if (!empty($authorUrl)): ?><a href="<?php echo $authorUrl; ?>"><?php echo $authorName; ?></a><?php else: ?><?php echo $authorName; ?><?php endif; ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="card-action-group">
                      <span class="read-more">Read Story <span class="arrow">→</span></span>
                    </div>
                  </div>
                </div>
              </article>
            <?php endwhile; ?>
          </div>
      <?php
        }
      }
    }

    if (!$hasPosts):
      ?>
      <div class="search-no-results search-no-results-custom">
        <div class="search-no-results-icon"><i class="ph ph-tag"></i></div>
        <h2>No Stories Tagged #<?php echo $tagTitleFormatted; ?></h2>
        <p>We couldn't find any active articles with this tag. Explore popular categories below.</p>
        <div class="popular-tags-bar popular-tags-center">
          <a href="/fintech-news/" class="popular-tag-item">Fintech News</a>
          <a href="/ai-and-emerging-tech/" class="popular-tag-item">AI &amp; Emerging Tech</a>
          <a href="/money-and-machines/" class="popular-tag-item">Startup Ecosystem</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Infinite Scroll Trigger Element -->
    <?php if (!empty($nextPageUrl)): ?>
      <div id="tagInfiniteTrigger" class="infinite-scroll-trigger" data-next-page="<?php echo htmlspecialchars($nextPageUrl); ?>">
        <div class="infinite-loader-content">
          <div class="infinite-spinner"></div>
          <span>Loading More Stories...</span>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>