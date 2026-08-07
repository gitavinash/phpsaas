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
        <div class="popular-tags-bar" style="justify-content: center; margin-top: 20px;">
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('searchInfiniteTrigger');
    const grid = document.getElementById('searchResultsGrid');

    if (!trigger || !grid) return;

    let remainingPosts = [];
    try {
      remainingPosts = JSON.parse(trigger.getAttribute('data-remaining') || '[]');
    } catch (e) {
      console.error('Error parsing remaining search posts:', e);
    }

    let isFetching = false;

    const appendBatch = function() {
      if (isFetching) return;
      isFetching = true;

      if (remainingPosts.length > 0) {
        const batchSize = 6;
        const nextBatch = remainingPosts.splice(0, batchSize);

        nextBatch.forEach((row, index) => {
          const card = document.createElement('article');
          card.className = 'search-result-card';
          card.style.opacity = '0';
          card.style.transform = 'translateY(15px)';
          card.style.transition = `opacity 0.4s ease ${index * 0.05}s, transform 0.4s ease ${index * 0.05}s`;

          const linkUrl = row.url ? (row.url.startsWith('/') ? row.url : '/' + row.url) : '#';
          const newsTitle = row.title || '';
          const newsImg = row.image || '';
          const newsPaid = row.news_paid == 1;
          const pubDate = row.published_at ? new Date(row.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
          let shortDesc = (row.meta_description || row.description || '').trim();
          if (shortDesc.length > 160) shortDesc = shortDesc.substring(0, 157) + '...';

          const paidBadge = newsPaid ? `
            <span class="paid-badge overlay">
              <svg class="paid-icon" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v-1h14v1z"/></svg>
              Premium
            </span>` : '';

          card.innerHTML = `
            <div class="search-card-img">
              ${paidBadge}
              <a href="${linkUrl}">
                <img src="${newsImg}" alt="${newsTitle.replace(/"/g, '')}" loading="lazy" />
              </a>
            </div>
            <div class="search-card-content">
              <span class="tag">Search Match</span>
              <h2><a href="${linkUrl}">${newsTitle}</a></h2>
              <p>${shortDesc}</p>
              <div class="category-card-meta">
                <span class="date">${pubDate}</span>
                <span class="read-more">Read Story <span class="arrow">→</span></span>
              </div>
            </div>
          `;

          grid.appendChild(card);
          requestAnimationFrame(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          });
        });

        if (remainingPosts.length === 0 && !trigger.getAttribute('data-next-page')) {
          trigger.style.display = 'none';
        }
        isFetching = false;
      } else {
        // Fetch next page via AJAX
        const nextPageUrl = trigger.getAttribute('data-next-page');
        if (!nextPageUrl) {
          trigger.style.display = 'none';
          isFetching = false;
          return;
        }

        fetch(nextPageUrl)
          .then(res => res.text())
          .then(htmlText => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            const newCards = doc.querySelectorAll('#searchResultsGrid .search-result-card');

            if (newCards && newCards.length > 0) {
              newCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                card.style.transition = `opacity 0.4s ease ${index * 0.05}s, transform 0.4s ease ${index * 0.05}s`;
                grid.appendChild(card);

                requestAnimationFrame(() => {
                  card.style.opacity = '1';
                  card.style.transform = 'translateY(0)';
                });
              });

              const nextTrigger = doc.querySelector('#searchInfiniteTrigger');
              const nextUrlAttr = nextTrigger ? nextTrigger.getAttribute('data-next-page') : '';

              if (nextUrlAttr && nextUrlAttr !== nextPageUrl) {
                trigger.setAttribute('data-next-page', nextUrlAttr);
              } else {
                trigger.style.display = 'none';
              }
            } else {
              trigger.style.display = 'none';
            }
            isFetching = false;
          })
          .catch(err => {
            console.error('Error fetching search results:', err);
            trigger.style.display = 'none';
            isFetching = false;
          });
      }
    };

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !isFetching) {
            appendBatch();
          }
        });
      }, { rootMargin: '600px' });

      observer.observe(trigger);
    }
  });
</script>

<?php require_once 'footer.php'; ?>