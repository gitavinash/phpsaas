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
    <?php

    require_once 'blog_sidebar.php';

    ?>

  </div>
</section>
<?php

require_once 'footer.php';

?>