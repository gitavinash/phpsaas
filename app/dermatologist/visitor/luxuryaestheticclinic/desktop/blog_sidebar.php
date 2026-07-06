<aside class="sidebar-sticky-container">
    <?php

    if (BASE_URL != '/search/') { ?>
        <!-- Search Widget -->
        <div class="sidebar-widget">
            <h4 class="widget-title">Clinical Portal Search</h4>
            <form class="sidebar-search-form" action="/search/" method="GET" style="position: relative;">
                <input type="text" name="q" placeholder="Search treatments, concerns..." style="width: 100%; padding: 1.5rem 5rem 1.5rem 2rem; font-size: 1.4rem; border: 1px solid var(--color-gold-light); background-color: var(--color-bg-ivory); color: var(--color-text-dark); transition: var(--transition-fast); outline: none;">
                <button type="submit" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-primary-gold); cursor: pointer;">
                    <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                </button>
            </form>
        </div>
    <?php
    }

    ?>

    <!-- Medical Categories Widget -->

    <?php

    $categoryData = getCategories($connection);

    if (sizeof($categoryData) > 1) {
        echo "<div class='sidebar-widget'>
            <h4 class='widget-title'>Categories</h4>
            <ul class='sidebar-categories-list' style='list-style: none; padding: 0; margin: 0; font-size: 1.45rem;'>";
        foreach ($categoryData as $categoryItem) {
            echo "<li style='border-bottom: 1px solid var(--color-bg-cream); padding: 1.2rem 0; display: flex; justify-content: space-between; align-items: center;'>
                <a href='{$categoryItem['link_url']}' style='color: var(--color-text-muted); font-weight: 500; transition: var(--transition-fast);'>{$categoryItem['title']}</a>
                <span style='font-size: 1.15rem; color: var(--color-primary-gold); background-color: var(--color-bg-cream); padding: 0.2rem 0.8rem; border-radius: 10px; font-weight: 600;'>{$categoryItem['article_count']} Articles</span>
            </li>";
        }
        echo "</ul>
            </div>";
    }

    ?>



    <!-- Related Journals Widget -->
    <div class="sidebar-widget">
        <h4 class="widget-title">Related Journals</h4>
        <div id="relatedPostsList">
            <a class="related-post-item" href="blog-single/">
                <div class="related-post-thumb">
                    <img src="assets/images/after_skin.png" alt="GFC vs PRP: Which Hair Therapy is Right for You?">
                </div>
                <div class="related-post-text-col">
                    <div class="related-post-meta">Hair Growth</div>
                    <h5 class="related-post-title">GFC vs PRP: Which Hair Therapy is Right for You?</h5>
                </div>
            </a>
            <a class="related-post-item" href="blog-single/">
                <div class="related-post-thumb">
                    <img src="assets/images/tech_showcase.png" alt="Permanent Laser Hair Removal: Expectations & Care">
                </div>
                <div class="related-post-text-col">
                    <div class="related-post-meta">Lasers</div>
                    <h5 class="related-post-title">Permanent Laser Hair Removal: Expectations & Care</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Concierge Lead Capture card -->
    <div class="sidebar-widget concierge-widget-card">
        <i
            data-lucide="award"
            style="
                width: 48px;
                height: 48px;
                color: var(--color-primary-gold);
                margin-bottom: 2rem;
              "></i>
        <h4>Derm-Directed Care</h4>
        <p>
            Begin your customized corrective path with Dr. Reed. Request a
            private skin or scalp diagnostics mapping session.
        </p>
        <a
            href="book-consultation/"
            class="btn btn-primary"
            style="display: block">Schedule Evaluation</a>
    </div>
</aside>