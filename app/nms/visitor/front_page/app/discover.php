<?php

require_once 'header.php';

?>


<!-- DISCOVER & CATEGORY PAGE CONTENT -->
<div id='view-discover' class='page-view active'>
    <div class='page-header'>
        <h1 class='greeting-title'>Discover</h1>
        <p class='page-subtitle'>Explore topics and trending insights.</p>
    </div>

    <!-- <section class='section-block'>
        <h3 class='section-title padded'>
            Trending Tags
        </h3>
        <div class='tags-container'>
            <span class='tag-pill'>#RBI</span>
            <span class='tag-pill'>#Fintech</span>
            <span class='tag-pill'>#VentureCapital</span>
            <span class='tag-pill'>#AI</span>
            <span class='tag-pill'>#Crypto</span>
        </div>
    </section> -->

    <section class='section-block'>
        <h3 class='section-title padded'>
            Explore Topics
        </h3>
        <div class='topics-grid'>
            <?php

            $categoryQuery = "SELECT link_url, category_title, category_image
FROM " . TABLE_PREFIX . "link
INNER JOIN " . TABLE_PREFIX . "category
    ON link_relation_id = category_id
    AND category_site_id = " . SITE_ID . "
    AND category_type='news'
WHERE link_type='category'
AND link_status=200
AND link_site_id=" . SITE_ID . "
ORDER BY RAND()";

            if ($categoryData = mysqli_query($connection, $categoryQuery)) {

                while ($categoryRow = mysqli_fetch_assoc($categoryData)) {

                    $title = trim($categoryRow['category_title']);

                    // First character
                    $character = strtoupper(mb_substr($title, 0, 1));

                    // Stable color based on category title
                    $backgrounds = [
                        'rgba(239, 68, 68, 0.18)',    // Soft Red
                        'rgba(249, 115, 22, 0.18)',   // Soft Orange
                        'rgba(245, 158, 11, 0.18)',   // Soft Amber
                        'rgba(234, 179, 8, 0.18)',    // Soft Yellow
                        'rgba(132, 204, 22, 0.18)',   // Soft Lime
                        'rgba(34, 197, 94, 0.18)',    // Soft Green
                        'rgba(16, 185, 129, 0.18)',   // Soft Emerald
                        'rgba(20, 184, 166, 0.18)',   // Soft Teal
                        'rgba(6, 182, 212, 0.18)',    // Soft Cyan
                        'rgba(14, 165, 233, 0.18)',   // Soft Sky
                        'rgba(59, 130, 246, 0.18)',   // Soft Blue
                        'rgba(99, 102, 241, 0.18)',   // Soft Indigo
                        'rgba(139, 92, 246, 0.18)',   // Soft Violet
                        'rgba(168, 85, 247, 0.18)',   // Soft Purple
                        'rgba(217, 70, 239, 0.18)',   // Soft Fuchsia
                        'rgba(236, 72, 153, 0.18)',   // Soft Pink
                        'rgba(244, 114, 182, 0.18)',  // Rose Pink
                        'rgba(120, 113, 108, 0.18)',  // Warm Stone
                        'rgba(107, 114, 128, 0.18)',  // Slate Gray
                        'rgba(75, 85, 99, 0.18)'      // Charcoal Gray
                    ];

                    $background = $backgrounds[crc32($title) % count($backgrounds)];

                    if (!empty($categoryRow['category_image'])) {

                        echo "
<a href='{$categoryRow['link_url']}'
   title='{$title}'
   class='topic-card'
   style=\"background-image:url('{$categoryRow['category_image']}');\">

    <div class='topic-overlay'>
        <h4>{$title}</h4>
    </div>

</a>";
                    } else {

                        echo "
<a href='{$categoryRow['link_url']}'
   title='{$title}'
   class='topic-card no-image'
   data-letter='{$character}' style='background: {$background};'>

    <div class='topic-overlay'>
        <h4>{$title}</h4>
    </div>

</a>";
                    }
                }
            }

            ?>
        </div>
    </section>

    <?php

    require_once 'footer.php';

    ?>