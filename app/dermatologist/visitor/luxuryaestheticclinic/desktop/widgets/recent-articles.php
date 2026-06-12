<?php

$articles = getLatestArticles($connection, 3); // Fetch the 3 most recent articles with a character limit of 250

if (!empty($articles)) {
    echo "<!-- 16. LATEST ARTICLES (Blog) -->
<section class='blog-section' id='blog'>
    <div class='container text-center reveal'>
        <span class='section-tag'>Dermatology Journals</span>
        <h2>Latest Editorial Articles</h2>
        <p class='max-width-600'>
            Stay informed with clinical skincare analyses, scientific treatment
            breakdowns, and clinical recommendations:
        </p>
    </div>

    <div class='container blog-grid'>";
    foreach ($articles as $article) {
        extract($article); // Extract article data into variables
        echo "<div class='blog-card reveal reveal-stagger'>
            <div class='blog-img-container'>
                <img    
                    src='{$image}'
                    alt='{$title}'
                    class='blog-img' />
            </div>
            <div class='blog-card-content'>
                <div class='blog-date-cat'>
                    <span>" . date('F j, Y', strtotime($link_published)) . "</span>
                    <span><a href='{$category_url}' title='{$category}'>{$category}</a></span>
                </div>
                <h3 class='blog-card-title'>
                    {$title}
                </h3>
                <a href='{$link_url}' class='btn-link'>Read Article <i data-lucide='arrow-right' style='width: 16px'></i></a>
            </div>
        </div>";
    }
    echo "</div></div></section>";
}
