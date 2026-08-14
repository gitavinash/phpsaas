<?php

if (defined('DATA_FUNCTIONS')) {
  require_once DATA_FUNCTIONS;
}

$categoryIds = [201, 202, 204, 386, 399];
$category_title = "More Stories";

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : (isset($_GET['p']) ? (int)$_GET['p'] : 1);
if ($currentPage < 1) {
  $currentPage = 1;
}

$limit = 20;
$offset = ($currentPage - 1) * $limit;

$catIdsStr = implode(',', array_map('intval', $categoryIds));

$totalCount = 0;
if (isset($connection)) {
  $countQuery = "SELECT COUNT(DISTINCT news_id) AS total FROM " . TABLE_PREFIX . "news WHERE news_category IN ({$catIdsStr}) AND news_site_id=" . SITE_ID;
  if ($countRes = mysqli_query($connection, $countQuery)) {
    if ($row = mysqli_fetch_assoc($countRes)) {
      $totalCount = (int)$row['total'];
    }
  }
}

$totalPages = max(1, (int)ceil($totalCount / $limit));

$categorySinglePost = getNewsCategoryPost($connection, $categoryIds, 'news', 'new', 'all', $offset, $limit);

// If no stories found in specified categories, fallback to recent stories
if (empty($categorySinglePost) && isset($connection)) {
  $recentQuery = "SELECT n.news_image, n.news_title, LEFT(REGEXP_REPLACE(n.news_description, '<[^>]*>', ''), 500) AS news_description, n.news_paid, n.news_published, n.news_owner_id AS author_id, p.profile_display_name, c.category_title, cl.link_url AS category_url, l.link_url, m.meta_description FROM " . TABLE_PREFIX . "news n INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=n.news_id AND l.link_status=200 AND l.link_type='news' AND l.link_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id INNER JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id=n.news_owner_id INNER JOIN " . TABLE_PREFIX . "category c ON c.category_id=n.news_category AND c.category_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "link cl ON cl.link_relation_id=c.category_id AND cl.link_type='category' AND cl.link_status=200 AND cl.link_site_id=" . SITE_ID . " WHERE n.news_site_id=" . SITE_ID . " ORDER BY n.news_published DESC LIMIT {$offset}, {$limit}";
  if ($recentRes = mysqli_query($connection, $recentQuery)) {
    $categorySinglePost = [];
    while ($row = mysqli_fetch_assoc($recentRes)) {
      $categorySinglePost[] = $row;
    }
  }

  if ($totalCount === 0) {
    $recentCountQuery = "SELECT COUNT(DISTINCT news_id) AS total FROM " . TABLE_PREFIX . "news WHERE news_site_id=" . SITE_ID;
    if ($rCountRes = mysqli_query($connection, $recentCountQuery)) {
      if ($rRow = mysqli_fetch_assoc($rCountRes)) {
        $totalCount = (int)$rRow['total'];
        $totalPages = max(1, (int)ceil($totalCount / $limit));
      }
    }
  }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
  header("Content-Type: application/json; charset=utf-8");
  $items = [];
  if (!empty($categorySinglePost) && is_array($categorySinglePost)) {
    foreach ($categorySinglePost as $post) {
      $publishedTime = function_exists('publishedTimeAgo') ? publishedTimeAgo($post['news_published'] ?? '') : date('M d, Y', strtotime($post['news_published'] ?? 'now'));
      $postCatTitle = $post['category_title'] ?? $category_title ?? 'News';
      $postCatUrl = $post['category_url'] ?? '';
      if (!empty($postCatUrl) && $postCatUrl[0] !== '/') {
        $postCatUrl = '/' . ltrim($postCatUrl, '/');
      }
      $items[] = [
        'link_url' => $post['link_url'] ?? '',
        'news_title' => $post['news_title'] ?? '',
        'news_image' => $post['news_image'] ?? '',
        'news_published' => $publishedTime,
        'news_paid' => (int)($post['news_paid'] ?? 0),
        'category_title' => $postCatTitle,
        'category_url' => $postCatUrl
      ];
    }
  }
  echo json_encode([
    'status' => 200,
    'page' => $currentPage,
    'totalPages' => $totalPages,
    'items' => $items
  ]);
  exit();
}

$nextPageUrl = '';
if ($currentPage < $totalPages) {
  $queryParams = $_GET;
  $queryParams['page'] = $currentPage + 1;
  $nextPageUrl = '?' . http_build_query($queryParams);
}

require_once 'header.php';

?>
<?php if (!empty($categorySinglePost)): ?>
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
        $postCatTitle = htmlspecialchars($post['category_title'] ?? $category_title ?? 'News');
        $postCatUrl = $post['category_url'] ?? '';
        if (!empty($postCatUrl) && $postCatUrl[0] !== '/') {
          $postCatUrl = '/' . ltrim($postCatUrl, '/');
        }
        $postCatUrl = htmlspecialchars($postCatUrl);
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
              <?php if (!empty($postCatUrl)): ?>
                <a href="<?php echo $postCatUrl; ?>"><?php echo $postCatTitle; ?></a>
              <?php else: ?>
                <?php echo $postCatTitle; ?>
              <?php endif; ?>
            </span>
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
<?php endif; ?>

<?php require_once 'footer.php'; ?>