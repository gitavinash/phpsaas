<?php

require_once 'header.php';

// Helper function to format relative time (e.g., "2 hrs ago", "1 day ago")
if (!function_exists('timeAgoFrontPage')) {
  function timeAgoFrontPage($datetime)
  {
    $time = strtotime($datetime);
    if (!$time) return '';
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hrs ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $time);
  }
}

// 1. Fetch Latest/Featured News
$latestNewsData = [];
$latestNewsQuery = "
    SELECT 
        link_is_featured, 
        link_modified, 
        news_title, 
        news_image, 
        news_description, 
        news_paid, 
        link_url, 
        link_published,
        news_published,
        meta_description 
    FROM " . TABLE_PREFIX . "news 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
    INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
    WHERE 
        link_type = 'news' 
        AND link_status = 200 
        AND news_category IN (201, 202, 285, 386, 399) 
        AND link_site_id = " . SITE_ID . "
    ORDER BY 
        link_is_featured DESC, 
        news_published DESC
    LIMIT 100";

if ($result = mysqli_query($connection, $latestNewsQuery)) {
  while ($row = mysqli_fetch_assoc($result)) {
    $latestNewsData[] = $row;
  }
}

// Featured article is index 0
$featuredArticle = $latestNewsData[0] ?? null;

// 2. Fetch Editor's Picks (tag containing 'editor')
$editorsPicks = [];
$epQuery = "
    SELECT news_title, news_image, link_url, news_paid, category_title 
    FROM " . TABLE_PREFIX . "tag 
    INNER JOIN " . TABLE_PREFIX . "news ON news_id=tag_relation_id 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id 
    LEFT JOIN " . TABLE_PREFIX . "category ON news_category = category_id AND category_site_id = " . SITE_ID . "
    WHERE link_site_id=" . SITE_ID . " 
      AND link_type='news' 
      AND link_status=200 
      AND tag_title LIKE '%editor%' 
      AND tag_site_id=" . SITE_ID . " 
    ORDER BY news_published DESC 
    LIMIT 5";

if ($epResult = mysqli_query($connection, $epQuery)) {
  while ($row = mysqli_fetch_assoc($epResult)) {
    $editorsPicks[] = $row;
  }
}

// Helper to fetch section posts using getCategoryPost with fallback JOIN query
if (!function_exists('fetchSectionPostsViaGetCategoryPost')) {
  function fetchSectionPostsViaGetCategoryPost($conn, $categoryId, $fallbackQuery)
  {
    $articles = [];
    try {
      $raw = getCategoryPost($conn, $categoryId, 'news', 'new', 'all', 0, 10);
      if (!empty($raw)) {
        if (isset($raw[0]) && is_array($raw[0])) {
          $articles = $raw;
        } else {
          $articles = [$raw];
        }
      }
    } catch (\Throwable $e) {
      $articles = [];
    }
    if (empty($articles) || empty($articles[0]['link_url'])) {
      if ($res = mysqli_query($conn, $fallbackQuery)) {
        $articles = [];
        while ($row = mysqli_fetch_assoc($res)) {
          $articles[] = $row;
        }
      }
    }
    return $articles;
  }
}

// 3. Fetch Fintech Chronicles (news_category = 281)
$fintechQuery = "
    SELECT link_url, news_image, news_title, news_description, news_paid, news_published, meta_description 
    FROM " . TABLE_PREFIX . "news 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id 
    LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
    WHERE news_category=281 
      AND link_type='news' 
      AND link_status=200 
      AND link_site_id=" . SITE_ID . " 
    ORDER BY news_published DESC 
    LIMIT 4";
$fintechArticles = fetchSectionPostsViaGetCategoryPost($connection, 281, $fintechQuery);

// 4. Fetch Money & Machines (news_category = 329)
$mmQuery = "
    SELECT link_url, news_image, news_title, news_description, news_paid, news_published, meta_description 
    FROM " . TABLE_PREFIX . "news 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id 
    LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
    WHERE news_category=329 
      AND link_type='news' 
      AND link_status=200 
      AND link_site_id=" . SITE_ID . " 
    ORDER BY news_published DESC 
    LIMIT 4";
$moneyMachinesArticles = fetchSectionPostsViaGetCategoryPost($connection, 329, $mmQuery);

// 5. Fetch IPO Tales (news_category = 386)
$ipoQuery = "
    SELECT link_url, news_image, news_title, news_description, news_paid, news_published, meta_description 
    FROM " . TABLE_PREFIX . "news 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id 
    LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
    WHERE news_category=386 
      AND link_type='news' 
      AND link_status=200 
      AND link_site_id=" . SITE_ID . " 
    ORDER BY news_published DESC 
    LIMIT 5";
$ipoArticles = fetchSectionPostsViaGetCategoryPost($connection, 386, $ipoQuery);

// 6. Fetch AI & Emerging Tech (news_category = 282)
$aiQuery = "
    SELECT link_url, news_image, news_title, news_description, news_paid, news_published, meta_description 
    FROM " . TABLE_PREFIX . "news 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id 
    LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
    WHERE news_category=282 
      AND link_type='news' 
      AND link_status=200 
      AND link_site_id=" . SITE_ID . " 
    ORDER BY news_published DESC 
    LIMIT 3";
$aiArticles = fetchSectionPostsViaGetCategoryPost($connection, 282, $aiQuery);

// 7. YouTube Channel Videos (https://www.youtube.com/@theheadandtale - max 5 videos)
$youtubeChannelUrl = "https://www.youtube.com/@theheadandtale";
$youtubeVideos = [
  [
    'title' => 'Inside India\'s Fintech Revolution: The Rise of UPI & Digital Lending',
    'url' => 'https://www.youtube.com/@theheadandtale',
    'thumb' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=800&q=80',
    'duration' => '12:45'
  ],
  [
    'title' => 'Why RBI is Cracking Down on Unregulated WealthTech Platforms',
    'url' => 'https://www.youtube.com/@theheadandtale',
    'thumb' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
    'duration' => '08:20'
  ],
  [
    'title' => 'Startup Founders Breakdown: How to Raise Seed Capital in 2026',
    'url' => 'https://www.youtube.com/@theheadandtale',
    'thumb' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=800&q=80',
    'duration' => '15:10'
  ],
  [
    'title' => 'The IPO Dilemma: Tech Unicorns Navigating Public Markets',
    'url' => 'https://www.youtube.com/@theheadandtale',
    'thumb' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=800&q=80',
    'duration' => '10:30'
  ],
  [
    'title' => 'AI Agents in Banking: Real Hype vs Real Implementation',
    'url' => 'https://www.youtube.com/@theheadandtale',
    'thumb' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80',
    'duration' => '14:05'
  ]
];

?>
<!-- Hero Section -->
<section class="hero-section">
  <div class="container hero-grid">
    <article class="hero-featured">
      <span class="category-tag">Featured</span>
      <?php if ($featuredArticle): ?>
        <a href="<?php echo htmlspecialchars($featuredArticle['link_url']); ?>" class="hero-featured-link">
          <img
            src="<?php echo htmlspecialchars($featuredArticle['news_image']); ?>"
            alt="<?php echo htmlspecialchars($featuredArticle['news_title']); ?>"
            fetchpriority="high"
            decoding="async"
            class="article-thumb" />
          <h1>
            <?php echo htmlspecialchars($featuredArticle['news_title']); ?>
          </h1>
        </a>
        <div class="meta-info">
          <span class="author">By The Head & Tale</span>
          <span class="separator">•</span>
          <span class="date"><?php echo date('M d, Y', strtotime($featuredArticle['news_published'])); ?></span>
        </div>
      <?php endif; ?>
    </article>
    <aside class="hero-sidebar">
      <!-- Editor's Pick Module -->
      <div class="editors-pick-module">
        <h3 class="sidebar-title">More stories</h3>
        <ul class="ep-list">
          <?php if (!empty($editorsPicks)): ?>
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
          <?php else: ?>
            <li class="ep-item"><a>No article found</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </aside>
  </div>
</section>

<!-- Trending Section (Below Hero - Left-to-Right Slider) -->
<section class="section trending-section">
  <div class="container">
    <div class="section-header">
      <h2>Trending Now</h2>
      <div class="slider-controls">
        <button class="slider-arrow prev-btn" id="trendingPrev" aria-label="Previous trending news">←</button>
        <button class="slider-arrow next-btn" id="trendingNext" aria-label="Next trending news">→</button>
      </div>
    </div>
    <div class="trending-slider-container">
      <div class="trending-track" id="trendingTrack">
        <?php
        // Fetch up to 8 trending items for the slider
        $trendingArticles = array_slice($latestNewsData, 1, 8);
        if (!empty($trendingArticles)):
          foreach ($trendingArticles as $tIndex => $tItem):
        ?>
            <article class="trending-card-slide" style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.88) 100%), url('<?php echo htmlspecialchars($tItem['news_image']); ?>');">
              <span class="trending-number"><?php echo sprintf('%02d', $tIndex + 1); ?></span>
              <div class="trending-card-content">
                <?php if ($tItem['news_paid'] == 1): ?>
                  <span class="paid-badge"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path
                        d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg>
                    Premium</span>
                <?php endif; ?>
                <h3>
                  <a href="<?php echo htmlspecialchars($tItem['link_url']); ?>"><?php echo htmlspecialchars($tItem['news_title']); ?></a>
                </h3>
              </div>
              <div class="trending-meta">
                <span class="time"><?php echo timeAgoFrontPage($tItem['news_published']); ?></span>
              </div>
            </article>
          <?php
          endforeach;
        else:
          ?>
          <p>No trending articles found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>


<!-- Dark Mode Section: AI & Emerging Tech -->
<section id="ai" class="section ai-section dark-mode">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>AI & Emerging Tech</h2>
        <p class="subtitle">The algorithms shaping our future.</p>
      </div>
      <a href="/ai-emerging-tech/" class="view-all dark">View All <span class="arrow">→</span></a>
    </div>
    <div class="card-grid">
      <?php if (!empty($aiArticles)): ?>
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
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>" class="read-more">Read Analysis <span class="arrow">→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- Money & Machines Section -->
<section class="section mm-section">
  <div class="container">
    <div class="section-header">
      <h2>Money & Machines</h2>
      <a href="/money-and-machines/" class="view-all">View All <span class="arrow">→</span></a>
    </div>
    <div class="money-machines-grid">
      <?php if (!empty($moneyMachinesArticles)): ?>
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
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Fintech Section (Professional Editorial News Layout) -->
<section id="fintech" class="section fintech-section">
  <div class="container">
    <div class="section-header">
      <h2>Fintech Chronicles</h2>
      <a href="/fintech-news/" class="view-all">View All <span class="arrow">→</span></a>
    </div>
    <div class="fintech-news-grid">
      <!-- Main Lead Article (Left Column) -->
      <?php if (isset($fintechArticles[0])): $f0 = $fintechArticles[0]; ?>
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
            <span class="tag">Payments</span>
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
        <?php if (isset($fintechArticles[1])): $f1 = $fintechArticles[1]; ?>
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
              <span class="tag">Lending</span>
              <h3><a href="<?php echo htmlspecialchars($f1['link_url']); ?>"><?php echo htmlspecialchars($f1['news_title']); ?></a></h3>
              <span class="time"><?php echo timeAgoFrontPage($f1['news_published']); ?></span>
            </div>
          </article>
        <?php endif; ?>

        <?php if (isset($fintechArticles[2])): $f2 = $fintechArticles[2]; ?>
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
              <span class="tag">Aggregators</span>
              <h3><a href="<?php echo htmlspecialchars($f2['link_url']); ?>"><?php echo htmlspecialchars($f2['news_title']); ?></a></h3>
              <span class="time"><?php echo timeAgoFrontPage($f2['news_published']); ?></span>
            </div>
          </article>
        <?php endif; ?>

        <?php if (isset($fintechArticles[3])): $f3 = $fintechArticles[3]; ?>
          <article class="fintech-banner-card" style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.88) 100%), url('<?php echo htmlspecialchars($f3['news_image']); ?>');">
            <?php if (!empty($f3['news_paid']) && $f3['news_paid'] == 1): ?>
              <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                  <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                </svg> Premium</span>
            <?php endif; ?>
            <div class="fintech-banner-content">
              <span class="tag light">Strategy</span>
              <h3>
                <a href="<?php echo htmlspecialchars($f3['link_url']); ?>"><?php echo htmlspecialchars($f3['news_title']); ?></a>
              </h3>
              <span class="time light"><?php echo timeAgoFrontPage($f3['news_published']); ?></span>
            </div>
          </article>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- IPO Tales Section -->
<section id="ipo" class="section ipo-section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>IPO Tales</h2>
      </div>
      <div class="section-header-actions">
        <a href="/ipo-tales/" class="view-all">View All <span class="arrow">→</span></a>
        <div class="slider-controls">
          <button class="slider-arrow prev-btn" id="ipoPrev" aria-label="Previous IPO story">←</button>
          <button class="slider-arrow next-btn" id="ipoNext" aria-label="Next IPO story">→</button>
        </div>
      </div>
    </div>
    <div class="ipo-slider-container">
      <div class="ipo-track" id="ipoTrack">
        <?php if (!empty($ipoArticles)): ?>
          <?php foreach ($ipoArticles as $ipo): ?>
            <article class="ipo-card">
              <div class="image-wrapper ipo-img-wrapper">
                <?php if (!empty($ipo['news_paid']) && $ipo['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($ipo['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($ipo['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($ipo['news_title']); ?>"
                    class="article-thumb" />
                </a>
              </div>
              <div class="ipo-content">
                <div class="ipo-date"><?php echo date('M d, Y', strtotime($ipo['news_published'])); ?></div>
                <h3>
                  <a href="<?php echo htmlspecialchars($ipo['link_url']); ?>"><?php echo htmlspecialchars($ipo['news_title']); ?></a>
                </h3>
                <p><?php echo htmlspecialchars(!empty($ipo['meta_description']) ? $ipo['meta_description'] : $ipo['news_description']); ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Video Section -->
<section id="videos" class="section video-section" style="background-color: #0e0e11; color: #ffffff; padding: 48px 0;">
  <div class="container">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 12px;">
      <div>
        <h2 style="color: #ffffff; margin: 0; font-size: 2rem;">Videos</h2>
      </div>
      <div class="section-header-actions" style="display: flex; align-items: center; gap: 16px;">
        <a href="<?php echo htmlspecialchars($youtubeChannelUrl); ?>" target="_blank" rel="noopener" class="view-all" style="color: #ef4444; text-decoration: none; font-weight: 600;">Visit Channel <span class="arrow">→</span></a>
        <div class="slider-controls" style="display: flex; gap: 8px;">
          <button class="slider-arrow prev-btn" id="videoPrev" aria-label="Previous video" style="background: rgba(255,255,255,0.08); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">←</button>
          <button class="slider-arrow next-btn" id="videoNext" aria-label="Next video" style="background: rgba(255,255,255,0.08); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">→</button>
        </div>
      </div>
    </div>
    <div class="video-slider-container" style="position: relative; overflow: hidden; margin: 0 -4px; padding: 4px;">
      <div class="video-track" id="videoTrack" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; scrollbar-width: none; padding-bottom: 8px;">
        <?php foreach (array_slice($youtubeVideos, 0, 5) as $vItem): ?>
          <article class="video-card-slide" style="flex: 0 0 calc((100% - 40px) / 3); width: calc((100% - 40px) / 3); max-width: calc((100% - 40px) / 3); min-width: 280px; box-sizing: border-box; scroll-snap-align: start; background: #18181b; border: 1px solid #27272a; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
            <div class="video-thumb-wrapper" style="position: relative; width: 100%; aspect-ratio: 16/9; overflow: hidden; background: linear-gradient(135deg, #1f1f23 0%, #09090b 100%);">
              <a href="<?php echo htmlspecialchars($vItem['url']); ?>" target="_blank" rel="noopener" title="<?php echo htmlspecialchars($vItem['title']); ?>" style="display: block; width: 100%; height: 100%; position: relative; text-decoration: none;">
                <img src="<?php echo htmlspecialchars($vItem['thumb']); ?>" alt="<?php echo htmlspecialchars($vItem['title']); ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block; opacity: 0.9;" />
                <div class="video-play-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 52px; height: 52px; background: rgba(220,38,38,0.92); color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(0,0,0,0.5); z-index: 2;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                </div>
                <span class="video-duration-tag" style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.85); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 3px 7px; border-radius: 4px; z-index: 2;"><?php echo htmlspecialchars($vItem['duration']); ?></span>
              </a>
            </div>
            <div class="video-card-info" style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
              <span class="video-channel-badge" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #ef4444; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M23.498 6.186a2.994 2.994 0 0 0-2.107-2.117C19.163 3.5 12 3.5 12 3.5s-7.163 0-9.391.569A2.994 2.994 0 0 0 .502 6.186C0 8.414 0 12 0 12s0 3.586.502 5.814a2.994 2.994 0 0 0 2.107 2.117C4.837 20.5 12 20.5 12 20.5s7.163 0 9.391-.569a2.994 2.994 0 0 0 2.107-2.117C24 15.586 24 12 24 12s0-3.586-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                </svg>
                The Head &amp; Tale
              </span>
              <h3 style="font-size: 0.98rem; font-weight: 600; line-height: 1.4; margin: 0;">
                <a href="<?php echo htmlspecialchars($vItem['url']); ?>" target="_blank" rel="noopener" style="color: #f4f4f5; text-decoration: none;"><?php echo htmlspecialchars($vItem['title']); ?></a>
              </h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>


<!-- IPO Tales Section -->
<section id="ipo" class="section ipo-section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>IPO Tales</h2>
      </div>
      <div class="section-header-actions">
        <a href="/ipo-tales/" class="view-all">View All <span class="arrow">→</span></a>
        <div class="slider-controls">
          <button class="slider-arrow prev-btn" id="ipoPrev" aria-label="Previous IPO story">←</button>
          <button class="slider-arrow next-btn" id="ipoNext" aria-label="Next IPO story">→</button>
        </div>
      </div>
    </div>
    <div class="ipo-slider-container">
      <div class="ipo-track" id="ipoTrack">
        <?php if (!empty($ipoArticles)): ?>
          <?php foreach ($ipoArticles as $ipo): ?>
            <article class="ipo-card">
              <div class="image-wrapper ipo-img-wrapper">
                <?php if (!empty($ipo['news_paid']) && $ipo['news_paid'] == 1): ?>
                  <span class="paid-badge overlay"><svg class="paid-icon" viewBox="0 0 24 24">
                      <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                    </svg> Premium</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($ipo['link_url']); ?>">
                  <img
                    src="<?php echo htmlspecialchars($ipo['news_image']); ?>"
                    alt="<?php echo htmlspecialchars($ipo['news_title']); ?>"
                    class="article-thumb" />
                </a>
              </div>
              <div class="ipo-content">
                <div class="ipo-date"><?php echo date('M d, Y', strtotime($ipo['news_published'])); ?></div>
                <h3>
                  <a href="<?php echo htmlspecialchars($ipo['link_url']); ?>"><?php echo htmlspecialchars($ipo['news_title']); ?></a>
                </h3>
                <p><?php echo htmlspecialchars(!empty($ipo['meta_description']) ? $ipo['meta_description'] : $ipo['news_description']); ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>


<!-- Dark Mode Section: AI & Emerging Tech -->
<section id="ai" class="section ai-section dark-mode">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>AI & Emerging Tech</h2>
        <p class="subtitle">The algorithms shaping our future.</p>
      </div>
      <a href="/ai-emerging-tech/" class="view-all dark">View All <span class="arrow">→</span></a>
    </div>
    <div class="card-grid">
      <?php if (!empty($aiArticles)): ?>
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
              <a href="<?php echo htmlspecialchars($ai['link_url']); ?>" class="read-more">Read Analysis <span class="arrow">→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>



<!-- Money & Machines Section -->
<section class="section mm-section">
  <div class="container">
    <div class="section-header">
      <h2>Money & Machines</h2>
      <a href="/money-and-machines/" class="view-all">View All <span class="arrow">→</span></a>
    </div>
    <div class="money-machines-grid">
      <?php if (!empty($moneyMachinesArticles)): ?>
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
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- Subscribe Section (Full Width Edge-to-Edge) -->
<section id="subscribe" class="newsletter-section">
  <div class="container">
    <div class="newsletter-grid">
      <div class="newsletter-info">
        <span class="newsletter-badge">WEEKLY DEEPDIVE</span>
        <h2>Join 15,000+ Smart Readers</h2>
        <p>
          Get the deepest insights on Indian Tech and Finance, delivered straight to your inbox every Sunday morning.
        </p>
        <ul class="newsletter-benefits">
          <li>
            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Exclusive PE/VC & Fintech deepdives
          </li>
          <li>
            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Analytical breakdowns with zero fluff
          </li>
        </ul>
      </div>
      <div class="newsletter-action">
        <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
          <div class="input-wrapper">
            <input type="email" placeholder="Enter your email address..." required />
            <button type="submit" class="btn btn-newsletter">
              Subscribe Free <span class="arrow">→</span>
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