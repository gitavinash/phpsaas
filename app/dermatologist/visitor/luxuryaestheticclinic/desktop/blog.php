<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1>SKINCARE JOURNALS</h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);">Skincare Journals</span>
    </div>
  </div>
</section>

<!-- 3. DYNAMIC SEARCH & FILTER SEGMENT -->
<section class="search-filter-section" id="searchFilters">
  <div class="container search-filter-grid">
    <!-- Search Input form -->
    <form class="journal-search-form" id="journalSearchForm" onsubmit="event.preventDefault(); handleSearchSubmit();">
      <input type="text" class="journal-search-input" id="searchInput" placeholder="Search clinical articles, concerns or treatments..." aria-label="Search articles">
      <button type="submit" class="journal-search-btn" aria-label="Search Submit">
        <i data-lucide="search" style="width: 24px; height: 24px;"></i>
      </button>
    </form>

    <!-- Filtering pills -->
    <ul class="filter-pills-list" id="filterPills">
      <li><button class="filter-pill active" data-category="all">All Journals</button></li>
      <li><button class="filter-pill" data-category="skincare">Skincare Science</button></li>
      <li><button class="filter-pill" data-category="hair-growth">Hair Restoration</button></li>
      <li><button class="filter-pill" data-category="lasers">Laser Aesthetics</button></li>
      <li><button class="filter-pill" data-category="anti-aging">Anti-Aging Care</button></li>
    </ul>
  </div>
</section>

<!-- 4. DYNAMIC BLOG GRID FEED -->
<section class="blog-feed-section">
  <div class="container">
    <!-- Featured Article Spotlight Container -->
    <div id="featuredArticleContainer"></div>

    <!-- Main Articles Grid -->
    <div class="blog-grid" id="blogGrid">
      <!-- Generated dynamically inside dynamic-rendering-script -->
    </div>

    <!-- Pagination Container -->
    <div class="pagination-container" id="paginationContainer"></div>
  </div>
</section>
<?php

require_once 'footer.php';

?>