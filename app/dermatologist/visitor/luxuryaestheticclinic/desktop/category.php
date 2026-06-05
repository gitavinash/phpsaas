<?php

require_once 'header.php';

?>
<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1 id="categoryTitle">CLINICAL JOURNALS</h1>
    <div class="breadcrumb-path">
      <a href="index.html">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <a href="blog.html">Journals</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);" id="breadcrumbCategory">Clinical Category</span>
    </div>
  </div>
</section>

<!-- 3. DYNAMIC BLOG GRID FEED -->
<section class="blog-feed-section">
  <div class="container">
    <div class="blog-grid" id="blogGrid">
      <!-- Static Template Blog Card 1 -->
      <div class="blog-card reveal reveal-stagger">
        <div class="blog-img-container">
          <img src="assets/images/before_skin.png" alt="The Scientific Blueprint for Treating Deep Acne Scars" class="blog-img">
        </div>
        <div class="blog-card-content">
          <div class="blog-date-cat">
            <span>May 20, 2026</span>
            <span style="color: var(--color-primary-gold); font-weight: 600;">Skincare</span>
          </div>
          <h3 class="blog-card-title">The Scientific Blueprint for Treating Deep Acne Scars</h3>
          <p class="treatment-card-desc" style="font-size: 1.4rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 2.5rem;">
            Deep acne scars require a multi-modal dermatological approach. Discover why combining micro-needling, subcision, and customized chemical peeling yields superior skin remodeling outcomes.
          </p>
          <a href="blog-single.html" class="btn-link">Read Article <i data-lucide="arrow-right" style="width: 16px;"></i></a>
        </div>
      </div>

      <!-- Static Template Blog Card 2 -->
      <div class="blog-card reveal reveal-stagger">
        <div class="blog-img-container">
          <img src="assets/images/after_skin.png" alt="GFC vs PRP: Which Hair Therapy is Right for You?" class="blog-img">
        </div>
        <div class="blog-card-content">
          <div class="blog-date-cat">
            <span>May 15, 2026</span>
            <span style="color: var(--color-primary-gold); font-weight: 600;">Hair Growth</span>
          </div>
          <h3 class="blog-card-title">GFC vs PRP: Which Hair Therapy is Right for You?</h3>
          <p class="treatment-card-desc" style="font-size: 1.4rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 2.5rem;">
            Autologous blood therapies represent a massive leap in clinical trichology. Compare standard Platelet-Rich Plasma (PRP) with next-generation Growth Factor Concentrate (GFC) to see which offers faster density improvements.
          </p>
          <a href="blog-single.html" class="btn-link">Read Article <i data-lucide="arrow-right" style="width: 16px;"></i></a>
        </div>
      </div>

      <!-- Static Template Blog Card 3 -->
      <div class="blog-card reveal reveal-stagger">
        <div class="blog-img-container">
          <img src="assets/images/tech_showcase.png" alt="Permanent Laser Hair Removal: Expectations & Care" class="blog-img">
        </div>
        <div class="blog-card-content">
          <div class="blog-date-cat">
            <span>May 10, 2026</span>
            <span style="color: var(--color-primary-gold); font-weight: 600;">Lasers</span>
          </div>
          <h3 class="blog-card-title">Permanent Laser Hair Removal: Expectations & Care</h3>
          <p class="treatment-card-desc" style="font-size: 1.4rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 2.5rem;">
            Unveil the truth about permanent laser hair reduction. Learn about Fitzpatrick skin typing, why cooling technology is critical, and standard pre/post session safety guidelines.
          </p>
          <a href="blog-single.html" class="btn-link">Read Article <i data-lucide="arrow-right" style="width: 16px;"></i></a>
        </div>
      </div>
    </div>
    <!-- Pagination Container -->
    <div class="pagination-container" id="paginationContainer">
      <button class="pagination-btn" disabled aria-label="Previous Page">PREV</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn" aria-label="Next Page">NEXT</button>
    </div>
  </div>
</section>

<!-- 4. DYNAMIC CATEGORY DIAGNOSTIC CTA -->
<section class="category-cta-section">
  <div class="container">
    <div class="category-cta-box reveal" id="categoryCtaBox">
      <div class="category-cta-content">
        <h3>Customized Scalp Diagnostics</h3>
        <p>Experiencing hair fall or crown thinning? Learn about our medical autologous growth concentrate therapies administered by board-certified specialists.</p>
      </div>
      <a href="services-hair.html" class="btn btn-primary">Explore Hair Solutions</a>
    </div>
  </div>
</section>
<?php

require_once 'footer.php';

?>