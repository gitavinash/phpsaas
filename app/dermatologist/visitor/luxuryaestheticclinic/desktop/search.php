<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1 id="searchBannerHeader">CLINICAL SEARCH RESULTS</h1>
    <div class="search-subtitle" id="searchSubtitle">Search results for your query</div>
  </div>
</section>

<!-- SEARCH IN-PAGE BAR -->
<div class="search-bar-wrap" id="inPageSearchContainer">
  <div class="container">
    <form class="journal-search-form" onsubmit="event.preventDefault(); handleInPageSearch();">
      <input type="text" class="journal-search-input" id="inPageSearchInput" placeholder="Search another concern, treatment or article..." aria-label="Search portal">
      <button type="submit" class="journal-search-btn" aria-label="Search Submit">
        <i data-lucide="search" style="width: 24px; height: 24px;"></i>
      </button>
    </form>
  </div>
</div>

<!-- 3. MAIN PORTAL SEARCH RESULTS -->
<section class="search-results-section" id="searchResultsSection">
  <div class="container search-results-grid">

    <!-- Left: Matches List -->
    <div class="results-list-container">
      <div id="resultsContainer" style="display: flex; flex-direction: column; gap: 4rem;">
        <!-- Generated Dynamically -->
      </div>
      <!-- Pagination Container -->
      <div class="pagination-container" id="paginationContainer"></div>
    </div>

    <!-- Right: Concierge sidebar blocks -->
    <aside class="sidebar-sticky-container">
      <!-- Search Widget -->
      <div class="sidebar-widget">
        <h4 class="widget-title">Clinical Portal Search</h4>
        <form class="sidebar-search-form" onsubmit="event.preventDefault(); handleSidebarSearch();" style="position: relative;">
          <input type="text" id="sidebarSearchInput" placeholder="Search treatments, concerns..." style="width: 100%; padding: 1.5rem 5rem 1.5rem 2rem; font-size: 1.4rem; border: 1px solid var(--color-gold-light); background-color: var(--color-bg-ivory); color: var(--color-text-dark); transition: var(--transition-fast); outline: none;">
          <button type="submit" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-primary-gold); cursor: pointer;">
            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
          </button>
        </form>
      </div>

      <!-- Medical Categories Widget -->
      <div class="sidebar-widget">
        <h4 class="widget-title">Medical Categories</h4>
        <ul class="sidebar-categories-list" style="list-style: none; padding: 0; margin: 0; font-size: 1.45rem;">
          <li style="border-bottom: 1px solid var(--color-bg-cream); padding: 1.2rem 0; display: flex; justify-content: space-between; align-items: center;">
            <a href="category.html?type=skincare" style="color: var(--color-text-muted); font-weight: 500; transition: var(--transition-fast);">Skincare Science</a>
            <span style="font-size: 1.15rem; color: var(--color-primary-gold); background-color: var(--color-bg-cream); padding: 0.2rem 0.8rem; border-radius: 10px; font-weight: 600;">2 Articles</span>
          </li>
          <li style="border-bottom: 1px solid var(--color-bg-cream); padding: 1.2rem 0; display: flex; justify-content: space-between; align-items: center;">
            <a href="category.html?type=hair-growth" style="color: var(--color-text-muted); font-weight: 500; transition: var(--transition-fast);">Hair Restoration</a>
            <span style="font-size: 1.15rem; color: var(--color-primary-gold); background-color: var(--color-bg-cream); padding: 0.2rem 0.8rem; border-radius: 10px; font-weight: 600;">1 Article</span>
          </li>
          <li style="border-bottom: 1px solid var(--color-bg-cream); padding: 1.2rem 0; display: flex; justify-content: space-between; align-items: center;">
            <a href="category.html?type=lasers" style="color: var(--color-text-muted); font-weight: 500; transition: var(--transition-fast);">Laser Aesthetics</a>
            <span style="font-size: 1.15rem; color: var(--color-primary-gold); background-color: var(--color-bg-cream); padding: 0.2rem 0.8rem; border-radius: 10px; font-weight: 600;">1 Article</span>
          </li>
          <li style="padding: 1.2rem 0; display: flex; justify-content: space-between; align-items: center;">
            <a href="category.html?type=anti-aging" style="color: var(--color-text-muted); font-weight: 500; transition: var(--transition-fast);">Anti-Aging Care</a>
            <span style="font-size: 1.15rem; color: var(--color-primary-gold); background-color: var(--color-bg-cream); padding: 0.2rem 0.8rem; border-radius: 10px; font-weight: 600;">1 Article</span>
          </li>
        </ul>
      </div>

      <!-- Recent Journals Widget -->
      <div class="sidebar-widget">
        <h4 class="widget-title">Recent Journals</h4>
        <div class="sidebar-recent-articles" style="display: flex; flex-direction: column; gap: 2.5rem;">
          <a href="blog-single.html" class="related-post-item">
            <div class="related-post-thumb">
              <img src="assets/images/before_skin.png" alt="The Scientific Blueprint for Treating Deep Acne Scars">
            </div>
            <div class="related-post-text-col">
              <div class="related-post-meta">Skincare</div>
              <h5 class="related-post-title">The Scientific Blueprint for Treating Deep Acne Scars</h5>
            </div>
          </a>
          <a href="blog-single.html" class="related-post-item">
            <div class="related-post-thumb">
              <img src="assets/images/after_skin.png" alt="GFC vs PRP: Which Hair Therapy is Right for You?">
            </div>
            <div class="related-post-text-col">
              <div class="related-post-meta">Hair Growth</div>
              <h5 class="related-post-title">GFC vs PRP: Which Hair Therapy is Right for You?</h5>
            </div>
          </a>
        </div>
      </div>

      <!-- Concierge Lead Capture Widget -->
      <div class="sidebar-widget sidebar-concierge-card">
        <i data-lucide="award" style="width: 48px; height: 48px; color: var(--color-primary-gold); margin-bottom: 2rem; margin-left: auto; margin-right: auto;"></i>
        <h4>Expert Supervision</h4>
        <p>Not sure which treatment matches your skin goals? Request a private clinical evaluation with Dr. Elizabeth Reed.</p>
        <a href="book-consultation.html" class="btn btn-primary" style="display: block;">Book Consultation</a>
      </div>
    </aside>

  </div>
</section>
<?php

require_once 'footer.php';

?>