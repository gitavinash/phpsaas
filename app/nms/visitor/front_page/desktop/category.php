<?php

require_once 'header.php';

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
        <span class="category-hero-kicker">EDITORIAL COVERAGE</span>
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
            $metaDesc = htmlspecialchars(!empty($post['meta_description']) ? $post['meta_description'] : ($post['news_description'] ?? ''));
            $publishedDate = date('M d, Y', strtotime($post['news_published'] ?? 'now'));
          ?>
          <article class="category-news-card">
            <div class="category-card-img">
              <?php if ($newsPaid): ?>
                <span class="paid-badge overlay">
                  <svg class="paid-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
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
              <p><?php echo $metaDesc; ?></p>
              <div class="category-card-meta">
                <span class="date"><?php echo $publishedDate; ?></span>
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('categoryInfiniteTrigger');
    const grid = document.getElementById('categoryNewsGrid');

    if (!trigger || !grid) return;

    let isFetching = false;

    const fetchNextPageStories = function(nextUrl) {
      if (isFetching || !nextUrl) return;
      isFetching = true;

      trigger.style.display = 'block';

      fetch(nextUrl)
        .then(response => response.text())
        .then(htmlText => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, 'text/html');
          const newCards = doc.querySelectorAll('#categoryNewsGrid .category-news-card');

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

            // Check if next page exists on fetched document
            const nextTrigger = doc.querySelector('#categoryInfiniteTrigger');
            const nextUrlAttribute = nextTrigger ? nextTrigger.getAttribute('data-next-page') : '';

            if (nextUrlAttribute && nextUrlAttribute !== nextUrl) {
              trigger.setAttribute('data-next-page', nextUrlAttribute);
            } else {
              trigger.style.display = 'none';
              if (observer) observer.unobserve(trigger);
            }
          } else {
            trigger.style.display = 'none';
            if (observer) observer.unobserve(trigger);
          }

          isFetching = false;
        })
        .catch(err => {
          console.error('Error fetching next page stories:', err);
          trigger.style.display = 'none';
          isFetching = false;
        });
    };

    let observer;
    if ('IntersectionObserver' in window) {
      // Trigger 600px early so next page loads seamlessly before user hits bottom
      observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !isFetching) {
            const nextUrl = trigger.getAttribute('data-next-page');
            if (nextUrl) {
              fetchNextPageStories(nextUrl);
            }
          }
        });
      }, { rootMargin: '600px' });

      observer.observe(trigger);
    }
  });
</script>

<?php require_once 'footer.php'; ?>