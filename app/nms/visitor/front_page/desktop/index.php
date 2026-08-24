<?php

require_once 'header.php';

if ($featuredArticle = mysqli_query($connection, "SELECT n.news_image, n.news_title, n.news_description, n.news_published, n.news_paid, n.news_owner_id, p.profile_display_name, l.link_url, c.category_title, cl.link_url AS category_url, m.meta_description FROM " . TABLE_PREFIX . "link l INNER JOIN " . TABLE_PREFIX . "news n ON l.link_relation_id=n.news_id AND l.link_status=200 AND l.link_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id=n.news_owner_id LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id LEFT JOIN " . TABLE_PREFIX . "category c ON c.category_id=n.news_category AND c.category_type='news' AND c.category_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "link cl ON cl.link_relation_id=c.category_id AND cl.link_type='category' AND cl.link_status=200 AND cl.link_site_id=" . SITE_ID . " WHERE l.link_status=200 AND n.news_site_id=" . SITE_ID . " ORDER BY l.link_is_featured DESC, n.news_published DESC LIMIT 1")) {
  $featuredArticle = mysqli_fetch_assoc($featuredArticle);
} else {
  $featuredArticle = null;
}

// 2. Fetch Editor's Picks (Excluding featured article to avoid duplicate)
$editorsPicks = getNewsCategoryPost($connection, [201, 202, 204, 386, 399], 'news', 'new', 'all', 0, 10);
if ($featuredArticle && !empty($editorsPicks)) {
  $editorsPicks = array_filter($editorsPicks, function ($ep) use ($featuredArticle) {
    return ($ep['link_url'] ?? '') !== ($featuredArticle['link_url'] ?? '')
      && ($ep['news_title'] ?? '') !== ($featuredArticle['news_title'] ?? '');
  });
  $editorsPicks = array_values(array_slice($editorsPicks, 0, 6));
}

// If no Editor's Picks stories found, fallback to recent stories
if (empty($editorsPicks)) {
  $recentQuery = "SELECT n.news_image, n.news_title, LEFT(REGEXP_REPLACE(n.news_description, '<[^>]*>', ''), 500) AS news_description, n.news_paid, n.news_published, n.news_owner_id AS author_id, p.profile_display_name, c.category_title, cl.link_url AS category_url, l.link_url, m.meta_description FROM " . TABLE_PREFIX . "news n INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=n.news_id AND l.link_status=200 AND l.link_type='news' AND l.link_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id INNER JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id=n.news_owner_id INNER JOIN " . TABLE_PREFIX . "category c ON c.category_id=n.news_category AND c.category_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "link cl ON cl.link_relation_id=c.category_id AND cl.link_type='category' AND cl.link_status=200 AND cl.link_site_id=" . SITE_ID . " WHERE n.news_site_id=" . SITE_ID . " ORDER BY n.news_published DESC LIMIT 10";
  if ($recentRes = mysqli_query($connection, $recentQuery)) {
    $recentArticles = [];
    while ($row = mysqli_fetch_assoc($recentRes)) {
      $recentArticles[] = $row;
    }
    if ($featuredArticle && !empty($recentArticles)) {
      $recentArticles = array_filter($recentArticles, function ($ep) use ($featuredArticle) {
        return ($ep['link_url'] ?? '') !== ($featuredArticle['link_url'] ?? '')
          && ($ep['news_title'] ?? '') !== ($featuredArticle['news_title'] ?? '');
      });
    }
    $editorsPicks = array_values(array_slice($recentArticles, 0, 6));
  }
}

// 3. Fetch Fintech Chronicles
$fintechArticles = getNewsCategoryPost($connection, 281, 'news', 'new', 'all', 0, 4);

// 6. Fetch AI & Emerging Tech
$aiArticles = getNewsCategoryPost($connection, 282, 'news', 'new', 'all', 0, 3);

// 4. Fetch Money & Machines
$moneyMachinesArticles = getNewsCategoryPost($connection, 329, 'news', 'new', 'all', 0, 4);

// 5. Fetch IPO Tales
$ipoArticles = getNewsCategoryPost($connection, 386, 'news', 'new', 'all', 0, 4);

// 6. Video Section: Fetch YouTube Videos from @theheadandtale
$youtubeChannelUrl = 'https://www.youtube.com/@theheadandtale';
if (!function_exists('getYoutubeChannelVideos')) {
  function getYoutubeChannelVideos($channelId = 'UCEAwb_zHxUEYQBcz6ghpDMw')
  {
    $cacheFile = sys_get_temp_dir() . '/yt_feed_' . md5($channelId) . '.json';
    $cacheTime = 900; // 15-minute cache

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
      $cached = json_decode(file_get_contents($cacheFile), true);
      if (is_array($cached) && !empty($cached)) {
        return $cached;
      }
    }

    $feedUrl = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . urlencode($channelId);
    $context = stream_context_create([
      'http' => [
        'timeout' => 4,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
      ]
    ]);

    $videos = [];
    $rssContent = @file_get_contents($feedUrl, false, $context);
    if ($rssContent !== false && !empty($rssContent)) {
      $xml = @simplexml_load_string($rssContent);
      if ($xml && isset($xml->entry)) {
        foreach ($xml->entry as $entry) {
          $yt = $entry->children('yt', true);
          $videoId = (string)($yt->videoId ?? '');
          $title = (string)($entry->title ?? '');
          $published = (string)($entry->published ?? '');

          if (!empty($videoId)) {
            $thumb = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            $url = "https://www.youtube.com/watch?v={$videoId}";
            $videos[] = [
              'id' => $videoId,
              'title' => $title,
              'url' => $url,
              'thumb' => $thumb,
              'published' => $published ? date('M d, Y', strtotime($published)) : ''
            ];
          }
        }
      }
    }

    if (!empty($videos)) {
      file_put_contents($cacheFile, json_encode($videos));
      return $videos;
    }

    if (file_exists($cacheFile)) {
      $cached = json_decode(file_get_contents($cacheFile), true);
      if (is_array($cached) && !empty($cached)) {
        return $cached;
      }
    }

    return $videos;
  }
}

$videosList = array_slice(getYoutubeChannelVideos('UCEAwb_zHxUEYQBcz6ghpDMw'), 0, 3);

// 7. Fetch PE & VC
$peVcArticles = getNewsCategoryPost($connection, 399, 'news', 'new', 'all', 0, 10);

// 8. Fetch The monthly Crypto Map
$cryptoMapArticles = getNewsCategoryPost($connection, 411, 'news', 'new', 'all', 0, 10);

// 9. Fetch Money Trivia
$moneyTriviaArticles = getNewsCategoryPost($connection, 203, 'news', 'new', 'all', 0, 3);

// 10. Fetch Who Reads Us Testimonials (3 featured testimonials)
$featuredTestimonials = array_slice(testimonialsList($connection, 3), 0, 3);

// Helper function to clean quote content if not already declared
if (!function_exists('clean_quote')) {
  function clean_quote($s)
  {
    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
    $s = strip_tags($s);                                // remove inline styles/tags
    $s = preg_replace('/\xC2\xA0|&nbsp;?/i', ' ', $s); // remove nbsp
    $s = preg_replace('/\s{2,}/', ' ', $s);            // extra spaces
    return trim($s);
  }
}

?>
<?php if ($featuredArticle || !empty($editorsPicks)): ?>
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container hero-grid">
      <?php if ($featuredArticle):
        $featuredExcerpt = !empty($featuredArticle['meta_description'])
          ? $featuredArticle['meta_description']
          : (!empty($featuredArticle['news_description']) ? strip_tags($featuredArticle['news_description']) : '');
        if (mb_strlen($featuredExcerpt) > 160) {
          $featuredExcerpt = mb_substr($featuredExcerpt, 0, 157) . '...';
        }

        $authorName = $featuredArticle['profile_display_name'] ?? '';
        $authorId = $featuredArticle['news_owner_id'] ?? '';
        $authorUrl = '';
        if (!empty($authorName) && !empty($authorId)) {
          $authorSlug = urlencode(strtolower(str_replace(' ', '-', trim($authorName))));
          $authorUrl = "/author/{$authorId}-{$authorSlug}-articles/";
        }

        $categoryUrl = $featuredArticle['category_url'] ?? '';
        if (!empty($categoryUrl) && $categoryUrl[0] !== '/') {
          $categoryUrl = '/' . ltrim($categoryUrl, '/');
        }
      ?>
        <article class="featured-hero-card">
          <div class="featured-hero-img-wrapper">
            <a href="<?php echo htmlspecialchars($featuredArticle['link_url']); ?>" class="featured-hero-img-link" aria-label="<?php echo htmlspecialchars($featuredArticle['news_title']); ?>">
              <img
                src="<?php echo htmlspecialchars($featuredArticle['news_image']); ?>"
                alt="<?php echo htmlspecialchars($featuredArticle['news_title']); ?>"
                class="featured-hero-img"
                loading="eager"
                fetchpriority="high" />
            </a>

            <div class="featured-hero-badges">
              <div class="badges-left">
                <span class="badge-pill badge-featured">
                  <span class="pulse-dot"></span>
                  Featured
                </span>
              </div>
              <?php if (!empty($featuredArticle['news_paid']) && $featuredArticle['news_paid'] == 1): ?>
                <span class="badge-pill badge-premium">
                  <svg class="badge-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg>
                  Premium
                </span>
              <?php endif; ?>
            </div>
          </div>

          <div class="featured-hero-body">
            <div class="featured-hero-main-content">
              <h1 class="featured-hero-title">
                <a href="<?php echo htmlspecialchars($featuredArticle['link_url']); ?>">
                  <?php echo htmlspecialchars($featuredArticle['news_title']); ?>
                </a>
              </h1>
              <?php if (!empty($featuredExcerpt)): ?>
                <p class="featured-hero-excerpt">
                  <?php echo htmlspecialchars($featuredExcerpt); ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="featured-hero-meta">
              <div class="meta-author-time">
                <?php if (!empty($authorName)): ?>
                  <span class="meta-author">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                    <?php if (!empty($authorUrl)): ?>
                      <a href="<?php echo htmlspecialchars($authorUrl); ?>" class="author-link"><?php echo htmlspecialchars($authorName); ?></a>
                    <?php else: ?>
                      <?php echo htmlspecialchars($authorName); ?>
                    <?php endif; ?>
                  </span>
                  <span class="meta-dot">•</span>
                <?php endif; ?>
                <span class="meta-time">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg>
                  <?php echo publishedTimeAgo($featuredArticle['news_published']); ?>
                </span>
              </div>
              <a href="<?php echo htmlspecialchars($featuredArticle['link_url']); ?>" class="featured-cta">
                Read Story
                <svg class="cta-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </a>
            </div>
          </div>
        </article>
      <?php endif; ?>
      <?php if (!empty($editorsPicks)): ?>
        <aside class="hero-sidebar">
          <!-- Editor's Pick Module -->
          <div class="editors-pick-module">
            <div class="sidebar-title-row">
              <h3 class="sidebar-title">More stories</h3>
              <a href="/more-stories/" class="sidebar-view-all">View all stories</a>
            </div>
            <ul class="ep-list">
              <?php foreach ($editorsPicks as $ep): ?>
                <li class="ep-item">
                  <article class="ep-card">
                    <?php if (!empty($ep['news_image'])): ?>
                      <div class="ep-card-image">
                        <a href="<?php echo htmlspecialchars($ep['link_url']); ?>">
                          <img
                            src="<?php echo htmlspecialchars($ep['news_image']); ?>"
                            alt="<?php echo htmlspecialchars($ep['news_title']); ?>"
                            loading="lazy"
                            class="ep-img" />
                        </a>
                      </div>
                    <?php endif; ?>
                    <div class="ep-card-content">
                      <div class="ep-card-meta">
                        <?php if ($ep['news_paid'] == 1): ?>
                          <span class="paid-badge">
                            <svg class="paid-icon" viewBox="0 0 24 24">
                              <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                            </svg>
                            Premium
                          </span>
                        <?php endif; ?>
                        <?php if (!empty($ep['category_title'])): ?>
                          <span class="ep-category"><?php echo htmlspecialchars($ep['category_title']); ?></span>
                        <?php endif; ?>
                      </div>
                      <h4 class="ep-card-title">
                        <a href="<?php echo htmlspecialchars($ep['link_url']); ?>"><?php echo htmlspecialchars($ep['news_title']); ?></a>
                      </h4>
                    </div>
                  </article>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </aside>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($fintechArticles)): ?>
  <!-- Fintech News -->
  <section class="section trending-section">
    <div class="container">
      <div class="section-header">
        <h2>Fintech Highlights</h2>
        <div class="section-header-actions">
          <a href="/fintech-news/" class="view-all">View All <span class="arrow">→</span></a>
          <!-- <div class="slider-controls">
          <button class="slider-arrow prev-btn" id="trendingPrev" aria-label="Previous trending news">←</button>
          <button class="slider-arrow next-btn" id="trendingNext" aria-label="Next trending news">→</button>
        </div> -->
        </div>
      </div>
      <div class="trending-slider-container">
        <div class="trending-track" id="trendingTrack">
          <?php foreach ($fintechArticles as $tIndex => $tItem): ?>
            <a href="<?php echo htmlspecialchars($tItem['link_url']); ?>" class="trending-card-link" aria-label="Read article: <?php echo htmlspecialchars($tItem['news_title']); ?>">
              <article class="trending-card-slide" style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.88) 100%), url('<?php echo htmlspecialchars($tItem['news_image']); ?>');">
                <!-- <span class="trending-number"><?php echo sprintf('%02d', $tIndex + 1); ?></span> -->
                <div class="trending-card-content">
                  <?php if ($tItem['news_paid'] == 1): ?>
                    <span class="paid-badge"><svg class="paid-icon" viewBox="0 0 24 24">
                        <path
                          d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                      </svg>
                      Premium</span>
                  <?php endif; ?>
                  <h3><?php echo htmlspecialchars($tItem['news_title']); ?></h3>
                </div>
                <div class="trending-meta">
                  <span class="time"><?php echo date('d M, Y', strtotime($tItem['news_published'])); ?></span>
                </div>
              </article>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($aiArticles)): ?>
  <!-- Dark Mode Section: AI & Emerging Tech -->
  <section id="ai" class="section ai-section dark-mode">
    <div class="container">
      <div class="section-header">
        <div>
          <h2>AI & Emerging Tech</h2>
        </div>
        <a href="/ai-emerging-tech/" class="view-all dark">View All <span class="arrow">→</span></a>
      </div>
      <div class="card-grid">
        <?php foreach ($aiArticles as $ai): ?>
          <article class="tech-card">
            <div class="image-wrapper tech-image-wrapper">
              <?php if (!empty($ai['news_paid']) && $ai['news_paid'] == 1): ?>
                <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg> Premium</span>
              <?php endif; ?>
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>">
                <img
                  src="<?php echo htmlspecialchars($ai['news_image']); ?>"
                  alt="<?php echo htmlspecialchars($ai['news_title']); ?>"
                  class="tech-article-thumb" />
              </a>
            </div>
            <div class="tech-card-body">
              <h3>
                <a href="<?php echo htmlspecialchars($ai['link_url']); ?>"><?php echo htmlspecialchars($ai['news_title']); ?></a>
              </h3>
              <p>
                <?php echo htmlspecialchars(!empty($ai['meta_description']) ? $ai['meta_description'] : $ai['news_description']); ?>
              </p>
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>" class="read-more">Read more <span class="arrow">→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($moneyMachinesArticles)): ?>
  <!-- Money & Machines Section -->
  <section class="section mm-section">
    <div class="container">
      <div class="section-header">
        <h2>Money & Machines</h2>
        <a href="/money-and-machines/" class="view-all">View All <span class="arrow">→</span></a>
      </div>
      <div class="money-machines-grid">
        <?php foreach ($moneyMachinesArticles as $mm): ?>
          <article class="mm-card">
            <div class="image-wrapper">
              <?php if ($mm['news_paid'] == 1): ?>
                <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                    <path
                      d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg>
                  Premium</span>
              <?php endif; ?>
              <a href="<?php echo htmlspecialchars($mm['link_url']); ?>">
                <img
                  src="<?php echo htmlspecialchars($mm['news_image']); ?>"
                  alt="<?php echo htmlspecialchars($mm['news_title']); ?>"
                  class="article-thumb" />
              </a>
            </div>
            <h3>
              <a href="<?php echo htmlspecialchars($mm['link_url']); ?>"><?php echo htmlspecialchars($mm['news_title']); ?></a>
            </h3>
            <p><?php echo htmlspecialchars(!empty($mm['meta_description']) ? $mm['meta_description'] : $mm['news_description']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($ipoArticles)): ?>
  <!-- IPO Tales -->
  <section id="fintech" class="section fintech-section">
    <div class="container">
      <div class="section-header">
        <h2>IPO Tales</h2>
        <a href="/ipo-tales/" class="view-all">View All <span class="arrow">→</span></a>
      </div>
      <div class="fintech-news-grid">
        <!-- Main Lead Article (Left Column) -->
        <?php if (isset($ipoArticles[0])): $f0 = $ipoArticles[0]; ?>
          <article class="fintech-lead-card">
            <div class="image-wrapper fintech-lead-img">
              <?php if (!empty($f0['news_paid']) && $f0['news_paid'] == 1): ?>
                <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg> Premium</span>
              <?php endif; ?>
              <a href="<?php echo htmlspecialchars($f0['link_url']); ?>">
                <img
                  src="<?php echo htmlspecialchars($f0['news_image']); ?>"
                  alt="<?php echo htmlspecialchars($f0['news_title']); ?>" />
              </a>
            </div>
            <div class="fintech-lead-content">
              <h3>
                <a href="<?php echo htmlspecialchars($f0['link_url']); ?>"><?php echo htmlspecialchars($f0['news_title']); ?></a>
              </h3>
              <p>
                <?php echo htmlspecialchars(!empty($f0['meta_description']) ? $f0['meta_description'] : $f0['news_description']); ?>
              </p>
              <div class="meta-info">
                <span class="date"><?php echo date('M d, Y', strtotime($f0['news_published'])); ?></span>
              </div>
            </div>
          </article>
        <?php endif; ?>

        <!-- Right Column: Mini Cards & Feature Banner Card -->
        <div class="fintech-side-list">
          <?php if (isset($ipoArticles[1])): $f1 = $ipoArticles[1]; ?>
            <article class="fintech-mini-card">
              <div class="image-wrapper fintech-mini-img">
                <?php if (!empty($f1['news_paid']) && $f1['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($f1['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($f1['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($f1['news_title']); ?>" />
                </a>
              </div>
              <div class="fintech-mini-content">
                <h3><a href="<?php echo htmlspecialchars($f1['link_url']); ?>"><?php echo htmlspecialchars($f1['news_title']); ?></a></h3>
                <span class="time"><?php echo publishedTimeAgo($f1['news_published']); ?></span>
              </div>
            </article>
          <?php endif; ?>

          <?php if (isset($ipoArticles[2])): $f2 = $ipoArticles[2]; ?>
            <article class="fintech-mini-card">
              <div class="image-wrapper fintech-mini-img">
                <?php if (!empty($f2['news_paid']) && $f2['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($f2['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($f2['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($f2['news_title']); ?>" />
                </a>
              </div>
              <div class="fintech-mini-content">
                <h3><a href="<?php echo htmlspecialchars($f2['link_url']); ?>"><?php echo htmlspecialchars($f2['news_title']); ?></a></h3>
                <span class="time"><?php echo publishedTimeAgo($f2['news_published']); ?></span>
              </div>
            </article>
          <?php endif; ?>

          <?php if (isset($ipoArticles[3])): $f3 = $ipoArticles[3]; ?>
            <article class="fintech-banner-card" style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.88) 100%), url('<?php echo htmlspecialchars($f3['news_image']); ?>');">
              <?php if (!empty($f3['news_paid']) && $f3['news_paid'] == 1): ?>
                <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg> Premium</span>
              <?php endif; ?>
              <div class="fintech-banner-content">
                <h3>
                  <a href="<?php echo htmlspecialchars($f3['link_url']); ?>"><?php echo htmlspecialchars($f3['news_title']); ?></a>
                </h3>
                <span class="time light"><?php echo publishedTimeAgo($f3['news_published']); ?></span>
              </div>
            </article>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($videosList)): ?>
  <!-- Video Section (Matching AI & Emerging Tech Section Look) -->
  <section id="videos" class="section ai-section dark-mode video-section-grid">
    <div class="container">
      <div class="section-header">
        <div>
          <h2>Videos</h2>
        </div>
        <a href="<?php echo htmlspecialchars($youtubeChannelUrl); ?>" target="_blank" rel="noopener" class="view-all dark">Visit Channel <span class="arrow">→</span></a>
      </div>
      <div class="card-grid">
        <?php foreach ($videosList as $vItem): ?>
          <article class="tech-card video-tech-card">
            <div class="image-wrapper tech-image-wrapper">
              <a href="<?php echo htmlspecialchars($vItem['url']); ?>" target="_blank" rel="noopener" title="<?php echo htmlspecialchars($vItem['title']); ?>">
                <img
                  src="<?php echo htmlspecialchars($vItem['thumb']); ?>"
                  alt="<?php echo htmlspecialchars($vItem['title']); ?>"
                  class="tech-article-thumb"
                  loading="lazy" />
                <div class="video-play-overlay">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                </div>
                <?php if (!empty($vItem['published'])): ?>
                  <span class="video-duration-tag"><?php echo htmlspecialchars($vItem['published']); ?></span>
                <?php endif; ?>
              </a>
            </div>
            <div class="tech-card-body">
              <div>
                <span class="video-channel-badge">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.498 6.186a2.994 2.994 0 0 0-2.107-2.117C19.163 3.5 12 3.5 12 3.5s-7.163 0-9.391.569A2.994 2.994 0 0 0 .502 6.186C0 8.414 0 12 0 12s0 3.586.502 5.814a2.994 2.994 0 0 0 2.107 2.117C4.837 20.5 12 20.5 12 20.5s7.163 0 9.391-.569a2.994 2.994 0 0 0 2.107-2.117C24 15.586 24 12 24 12s0-3.586-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                  </svg>
                  The Head &amp; Tale
                </span>
                <h3>
                  <a href="<?php echo htmlspecialchars($vItem['url']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($vItem['title']); ?></a>
                </h3>
              </div>
              <a href="<?php echo htmlspecialchars($vItem['url']); ?>" target="_blank" rel="noopener" class="read-more">Watch on YouTube <span class="arrow">→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($peVcArticles)): ?>
  <!-- PE VC Section -->
  <section id="pevc" class="section ipo-section">
    <div class="container">
      <div class="section-header">
        <div>
          <h2>PE/VC Tales</h2>
        </div>
        <div class="section-header-actions">
          <a href="/pe-vc-tales/" class="view-all">View All <span class="arrow">→</span></a>
          <div class="slider-controls">
            <button class="slider-arrow prev-btn" id="pevcPrev" aria-label="Previous PE/VC story">←</button>
            <button class="slider-arrow next-btn" id="pevcNext" aria-label="Next PE/VC story">→</button>
          </div>
        </div>
      </div>
      <div class="ipo-slider-container">
        <div class="ipo-track" id="pevcTrack">
          <?php foreach ($peVcArticles as $pevc): ?>
            <article class="ipo-card">
              <div class="image-wrapper ipo-img-wrapper">
                <?php if (!empty($pevc['news_paid']) && $pevc['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($pevc['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($pevc['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($pevc['news_title']); ?>"
                    class="article-thumb" />
                </a>
              </div>
              <div class="ipo-content">
                <div class="ipo-date"><?php echo date('M d, Y', strtotime($pevc['news_published'])); ?></div>
                <h3>
                  <a href="<?php echo htmlspecialchars($pevc['link_url']); ?>"><?php echo htmlspecialchars($pevc['news_title']); ?></a>
                </h3>
                <p><?php echo htmlspecialchars(!empty($pevc['meta_description']) ? $pevc['meta_description'] : $pevc['news_description']); ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($cryptoMapArticles)): ?>
  <!-- The Monthly Crypto Map Section -->
  <section id="moneyCrypto" class="section ipo-section">
    <div class="container">
      <div class="section-header">
        <div>
          <h2>The Monthly Crypto Map</h2>
        </div>
        <div class="section-header-actions">
          <a href="/the-monthly-crypto-map/" class="view-all">View All <span class="arrow">→</span></a>
          <div class="slider-controls">
            <button class="slider-arrow prev-btn" id="moneyCryptoPrev" aria-label="Previous crypto story">←</button>
            <button class="slider-arrow next-btn" id="moneyCryptoNext" aria-label="Next crypto story">→</button>
          </div>
        </div>
      </div>
      <div class="ipo-slider-container">
        <div class="ipo-track" id="moneyCryptoTrack">
          <?php foreach ($cryptoMapArticles as $crypto): ?>
            <article class="ipo-card">
              <div class="image-wrapper ipo-img-wrapper">
                <?php if (!empty($crypto['news_paid']) && $crypto['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($crypto['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($crypto['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($crypto['news_title']); ?>"
                    class="article-thumb" />
                </a>
              </div>
              <div class="ipo-content">
                <div class="ipo-date"><?php echo date('M d, Y', strtotime($crypto['news_published'])); ?></div>
                <h3>
                  <a href="<?php echo htmlspecialchars($crypto['link_url']); ?>"><?php echo htmlspecialchars($crypto['news_title']); ?></a>
                </h3>
                <p><?php echo htmlspecialchars(!empty($crypto['meta_description']) ? $crypto['meta_description'] : $crypto['news_description']); ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($moneyTriviaArticles)): ?>
  <!-- Money Trivia -->
  <section id="money-trivia" class="section ai-section dark-mode trivia-section">
    <div class="container">
      <div class="section-header">
        <div>
          <h2>Money Trivia</h2>
        </div>
        <a href="/money-Trivia/" class="view-all dark">View All <span class="arrow">→</span></a>
      </div>
      <div class="card-grid">
        <?php foreach ($moneyTriviaArticles as $ai): ?>
          <article class="tech-card">
            <div class="image-wrapper tech-image-wrapper">
              <?php if (!empty($ai['news_paid']) && $ai['news_paid'] == 1): ?>
                <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg> Premium</span>
              <?php endif; ?>
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>">
                <img
                  src="<?php echo htmlspecialchars($ai['news_image']); ?>"
                  alt="<?php echo htmlspecialchars($ai['news_title']); ?>"
                  class="tech-article-thumb" />
              </a>
            </div>
            <div class="tech-card-body">
              <h3>
                <a href="<?php echo htmlspecialchars($ai['link_url']); ?>"><?php echo htmlspecialchars($ai['news_title']); ?></a>
              </h3>
              <p>
                <?php echo htmlspecialchars(!empty($ai['meta_description']) ? $ai['meta_description'] : $ai['news_description']); ?>
              </p>
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>" class="read-more">Read more <span class="arrow">→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($featuredTestimonials)): ?>
  <!-- Who Reads Us Section -->
  <style>
    .who-reads-us-section {
      padding: 56px 0;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      border-bottom: 1px solid #e2e8f0;
    }

    .who-reads-us-section .section-kicker {
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--accent-color, #d93025);
      text-transform: uppercase;
      margin-bottom: 4px;
      display: block;
    }

    .wru-index-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 24px;
      margin-top: 24px;
    }

    .wru-index-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .wru-index-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 14px 28px rgba(0, 0, 0, 0.07);
      border-color: #cbd5e1;
    }

    .wru-index-quote-mark {
      font-family: var(--font-heading, Georgia, serif);
      font-size: 2.5rem;
      line-height: 1;
      color: var(--accent-color, #d93025);
      opacity: 0.2;
      margin-bottom: -8px;
    }

    .wru-index-quote {
      font-family: var(--font-heading, Georgia, serif);
      font-size: 0.975rem;
      line-height: 1.6;
      color: #334155;
      margin-bottom: 20px;
      flex-grow: 1;
    }

    .wru-index-user {
      display: flex;
      align-items: center;
      gap: 12px;
      padding-top: 16px;
      border-top: 1px solid #f1f5f9;
    }

    .wru-index-avatar {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
    }

    .wru-index-meta {
      display: flex;
      flex-direction: column;
    }

    .wru-index-name {
      font-size: 0.925rem;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.3;
    }

    .wru-index-role {
      font-size: 0.8rem;
      color: #64748b;
      line-height: 1.3;
    }

    @media (max-width: 768px) {
      .wru-index-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <section id="who-reads-us" class="section who-reads-us-section">
    <div class="container">
      <div class="section-header">
        <div>
          <span class="section-kicker">EXECUTIVE READERSHIP</span>
          <h2>Who Reads Us</h2>
        </div>
        <a href="/who-reads-us/" class="view-all">Explore All Reviews <span class="arrow">→</span></a>
      </div>

      <div class="wru-index-grid">
        <?php foreach ($featuredTestimonials as $row): ?>
          <?php
          $readerInfo     = explode(",", $row["category_title"]);
          $readerName     = trim($readerInfo[0]);
          $readerPosition = ucwords(trim(str_replace($readerName . (sizeof($readerInfo) == 1 ? "" : ","), "", $row["category_title"])));

          $defaultAvatar = IMG_LIBRARY . 'default-black-profile-pic-icon.png';
          $img           = !empty($row['category_image']) ? $row['category_image'] : $defaultAvatar;
          $quote         = clean_quote($row['category_description']);

          $nameEsc  = htmlspecialchars($readerName, ENT_QUOTES, 'UTF-8');
          $roleEsc  = htmlspecialchars($readerPosition, ENT_QUOTES, 'UTF-8');
          $imgEsc   = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
          $quoteEsc = htmlspecialchars($quote, ENT_QUOTES, 'UTF-8');
          $defaultAvatarEsc = htmlspecialchars($defaultAvatar, ENT_QUOTES, 'UTF-8');
          ?>

          <article class="wru-index-card">
            <div class="wru-index-quote-mark">&ldquo;</div>
            <p class="wru-index-quote"><?php echo $quoteEsc; ?></p>

            <div class="wru-index-user">
              <img src="<?php echo $imgEsc; ?>" alt="<?php echo $nameEsc; ?>" class="wru-index-avatar" loading="lazy" onerror="this.onerror=null; this.src='<?php echo $defaultAvatarEsc; ?>';">
              <div class="wru-index-meta">
                <strong class="wru-index-name"><?php echo $nameEsc; ?></strong>
                <span class="wru-index-role"><?php echo $roleEsc; ?></span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Subscribe Section -->
<section id="subscribe" class="newsletter-section">
  <div class="container">
    <div class="newsletter-grid">
      <div class="newsletter-info">
        <span class="newsletter-badge">WEEKLY DEEPDIVE</span>
        <h2>Get sharper fintech and tech insights in your inbox</h2>
        <p>
          Sign up for concise summaries of powerful fintech and emerging tech insights delivered directly to your inbox every time we publish.
        </p>
      </div>
      <div class="newsletter-action">
        <form class="newsletter-form" action="/thankyou_newsletter/" method="GET">
          <div class=" input-wrapper">
            <input type="email" id="newsletterEmail" name="email" placeholder="Enter your email address..." required />
            <button type="submit" class="btn btn-newsletter" id="submitNewsletter">
              Join Now <span class="arrow">→</span>
            </button>
          </div>
          <div class="newsletter-trust">
            <svg class="shield-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Zero spam. Unsubscribe at any time with 1 click.</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php

require_once 'footer.php';

?>