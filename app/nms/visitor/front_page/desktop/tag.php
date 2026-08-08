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

        if ($postList = mysqli_query($connection, "SELECT link_url, news_title, news_image, news_paid, meta_description, profile_display_name, link_published as publishedTime, news_published FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "news ON link_relation_id=news_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id INNER JOIN " . TABLE_PREFIX . "profile ON news_owner_id=profile_user_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND link_relation_id IN ({$resultTags}) ORDER BY news_published DESC LIMIT 0, 20")) {
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
                      $authorName = htmlspecialchars($postData['profile_display_name'] ?? '');
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
                        <span class="tag">#<?php echo $tagTitleFormatted; ?></span>
                        <h2><a href="<?php echo $linkUrl; ?>"><?php echo $newsTitle; ?></a></h2>
                        <p><?php echo $metaDesc; ?></p>
                        <div class="category-card-meta">
                          <span class="date"><?php echo $pubDate; ?><?php echo $authorName ? ' • ' . $authorName : ''; ?></span>
                          <div class="card-action-group">
                            <button class="trigger-bookmark-app bookmark-card-btn" title="Bookmark on Mobile App" aria-label="Bookmark Story"><i class="ph ph-bookmark-simple"></i></button>
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