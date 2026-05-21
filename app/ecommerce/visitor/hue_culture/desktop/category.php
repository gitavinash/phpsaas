<?php

require_once 'header.php';

?>

<!-- Category Hero -->
<section class="category-hero-section">
    <div class="container reveal-on-load">
        <span class="editorial-tag">Category</span>

        <h1 class="hero-premium-headline">
            Color <span class="hero-premium-hues">Theory</span>
        </h1>

        <p class="category-hero-subtitle reveal-on-load">
            Explore in-depth articles that demystify color interaction, shade behaviors, and scientific color frameworks
            to elevate your environment.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="category-main-content">
    <div class="container category-layout-grid">

        <!-- Sidebar -->
        <aside class="editorial-sidebar reveal-on-load">

            <div class="sidebar-widget">
                <h3 class="sidebar-title">Search</h3>
                <form action="#" class="sidebar-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search keywords..." />
                </form>
            </div>

            <div class="sidebar-widget">
                <h3 class="sidebar-title">Categories</h3>
                <ul class="sidebar-links">
                    <li><a href="#">All Articles <span class="post-count">24</span></a></li>
                    <li><a href="#" class="active">Color Theory <span class="post-count">8</span></a></li>
                    <li><a href="#">Personal Styling <span class="post-count">6</span></a></li>
                    <li><a href="#">Interior Spaces <span class="post-count">7</span></a></li>
                    <li><a href="#">News <span class="post-count">3</span></a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3 class="sidebar-title">Popular Tags</h3>
                <div class="article-tags" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <a href="#" class="tag-link">#Undertones</a>
                    <a href="#" class="tag-link">#Lighting</a>
                    <a href="#" class="tag-link">#Contrast</a>
                    <a href="#" class="tag-link">#Sass</a>
                </div>
            </div>

        </aside>

        <!-- List View Feed -->
        <div class="category-feed">
            <div class="related-posts-list" style="margin: 0;">

                <!-- Blog Card 1 -->
                <div class="featured-post-card related-post-card">
                    <div class="featured-img-col">
                        <a href="blog-details.html" class="featured-img-link related-img-link">
                            <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=2070&auto=format&fit=crop"
                                alt="Personal Styling" />
                        </a>
                    </div>
                    <div class="featured-text-col related-text-col">
                        <a href="#" class="category-tag">Personal Styling</a>
                        <a href="blog-details.html" class="featured-title-link related-title-link">
                            <h2 class="serif-normal">Determining Your True Season</h2>
                        </a>
                        <p class="blog-excerpt related-blog-excerpt">
                            Stop guessing your undertones online. A scientific breakdown of the Hue Personal Color
                            Analysis framework.
                        </p>
                        <a href="blog-details.html" class="read-more-link">Read More <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Blog Card 2 -->
                <div class="featured-post-card related-post-card">
                    <div class="featured-img-col">
                        <a href="blog-details.html" class="featured-img-link related-img-link">
                            <img src="https://images.unsplash.com/photo-1544457070-4cd773b4d71e?q=80&w=2130&auto=format&fit=crop"
                                alt="Color Theory" />
                        </a>
                    </div>
                    <div class="featured-text-col related-text-col">
                        <a href="#" class="category-tag">Color Theory</a>
                        <a href="blog-details.html" class="featured-title-link related-title-link">
                            <h2 class="serif-normal">The Myth of Universal Khaki</h2>
                        </a>
                        <p class="blog-excerpt related-blog-excerpt">
                            Why standard neutrals often fail across different settings and how to select dynamic
                            supporting shades instead.
                        </p>
                        <a href="blog-details.html" class="read-more-link">Read More <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Blog Card 3 -->
                <div class="featured-post-card related-post-card">
                    <div class="featured-img-col">
                        <a href="blog-details.html" class="featured-img-link related-img-link">
                            <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?q=80&w=1974&auto=format&fit=crop"
                                alt="Spaces" />
                        </a>
                    </div>
                    <div class="featured-text-col related-text-col">
                        <a href="#" class="category-tag">Spaces</a>
                        <a href="blog-details.html" class="featured-title-link related-title-link">
                            <h2 class="serif-normal">Anchoring Large Open Concepts</h2>
                        </a>
                        <p class="blog-excerpt related-blog-excerpt">
                            Techniques for creating distinct zones in massive rooms without resorting to physical
                            dividers or harsh breaks.
                        </p>
                        <a href="blog-details.html" class="read-more-link">Read More <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <div class="number-pagination-container">
                <a href="#" class="page-link page-link-icon"><i class="fa-solid fa-chevron-left"></i></a>
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link page-link-icon"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
        </div>

    </div>
</section>
<?php

require_once 'footer.php';

?>