<?php
require_once 'header.php';

/**
 * Map normalized $searchData payload
 */
$searchKeyword = $searchData['q']        ?? ($searchKeyword ?? '');
$queryPost     = $searchData['results']  ?? [];
$source        = $searchData['source']   ?? 'post';

// Helper for escaping
if (!function_exists('h')) {
    function h($s)
    {
        $clean = strip_tags((string)$s);
        return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
    }
}

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : (isset($_GET['p']) ? (int)$_GET['p'] : 1);
$totalPages = defined('TOTAL_PAGE') ? (int)TOTAL_PAGE : 1;

$nextPageUrl = '';
if ($currentPage < $totalPages) {
    $queryParams = $_GET;
    $queryParams['page'] = $currentPage + 1;
    $nextPageUrl = '?' . http_build_query($queryParams);
}

// Client-side batching if many results passed on single page
$totalPosts = is_array($queryPost) ? count($queryPost) : 0;
$initialBatchSize = 9;
$initialPosts = is_array($queryPost) ? array_slice($queryPost, 0, $initialBatchSize) : [];
$remainingPosts = is_array($queryPost) ? array_slice($queryPost, $initialBatchSize) : [];

?>

<section class="search-page">
  <!-- Full-Width Search Header Banner -->
  <div class="search-header-hero">
    <div class="container">
      <nav class="article-breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="sep">/</span>
        <a href="#">Search</a>
        <span class="sep">/</span>
        <span class="current">Results</span>
      </nav>

      <div class="category-hero-content">
        <span class="category-hero-kicker">SEARCH RESULTS</span>
        <h1>Results for "<mark><?php echo h(ucwords($searchKeyword)); ?></mark>"</h1>
        <p class="category-hero-desc">
          <?php if (!empty($queryPost)): ?>
            Showing <?php echo $totalPosts; ?> stories matching your search query.
          <?php else: ?>
            No exact matches found for your query. Try refining your keywords or explore popular topics below.
          <?php endif; ?>
        </p>
      </div>
    </div>
  </div>

  <div class="container">
    <?php if (!empty($initialPosts)): ?>
      <div class="search-results-grid" id="searchResultsGrid">
        <?php foreach ($initialPosts as $row): ?>
          <?php
            $link_url = $row['url'] ?? '#';
            if ($link_url !== '' && $link_url[0] !== '/') $link_url = '/' . ltrim($link_url, '/');

            $news_title = $row['title'] ?? '';
            $news_image = $row['image'] ?? '';
            $news_paid = $row['news_paid'] ?? 0;
            $news_published = $row['published_at'] ?? '';
            $meta_description = $row['meta_description'] ?? '';
            $news_description = $row['description'] ?? '';

            $shortDescription = trim($meta_description) ?: trim($news_description);
            if (strlen($shortDescription) > 160) {
              $shortDescription = substr($shortDescription, 0, 157) . '...';
            }
          ?>
          <article class="search-result-card">
            <div class="search-card-img">
              <?php if ($news_paid == 1): ?>
                <span class="paid-badge overlay">
                  <svg class="paid-icon" viewBox="0 0 24 24">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z" />
                  </svg>
                  Premium
                </span>
              <?php endif; ?>
              <a href="<?php echo h($link_url); ?>">
                <img src="<?php echo h($news_image); ?>" alt="<?php echo h($news_title); ?>" loading="lazy" />
              </a>
            </div>
            <div class="search-card-content">
              <span class="tag">Search Match</span>
              <h2><a href="<?php echo h($link_url); ?>"><?php echo h($news_title); ?></a></h2>
              <p><?php echo h($shortDescription); ?></p>
              <div class="category-card-meta">
                <span class="date"><?php echo ($news_published ? date('M d, Y', strtotime($news_published)) : ''); ?></span>
                <span class="read-more">Read Story <span class="arrow">→</span></span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="search-no-results">
        <div class="search-no-results-icon"><i class="ph ph-magnifying-glass"></i></div>
        <h2>No Stories Found</h2>
        <p>We couldn't find any articles matching "<strong><?php echo h($searchKeyword); ?></strong>". Try searching for related industry terms or explore popular sections below.</p>
        <div class="popular-tags-bar popular-tags-center">
          <a href="/fintech-news/" class="popular-tag-item">Fintech News</a>
          <a href="/ai-and-emerging-tech/" class="popular-tag-item">AI & Emerging Tech</a>
          <a href="/ipo-tales/" class="popular-tag-item">IPO Tales</a>
          <a href="/money-and-machines/" class="popular-tag-item">Money & Machines</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Infinite Scroll Trigger Element -->
    <?php if (!empty($remainingPosts) || !empty($nextPageUrl)): ?>
      <div id="searchInfiniteTrigger" class="infinite-scroll-trigger" data-next-page="<?php echo htmlspecialchars($nextPageUrl); ?>" data-remaining='<?php echo htmlspecialchars(json_encode($remainingPosts), ENT_QUOTES, 'UTF-8'); ?>'>
        <div class="infinite-loader-content">
          <div class="infinite-spinner"></div>
          <span>Loading More Results...</span>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>