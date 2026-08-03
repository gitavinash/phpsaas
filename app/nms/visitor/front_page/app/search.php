<?php

if (defined('DATA_FUNCTIONS')) {
    require_once DATA_FUNCTIONS;
}

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

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header("Content-Type: application/json; charset=utf-8");
    $items = [];
    if (is_array($queryPost)) {
        foreach ($queryPost as $row) {
            $publishedTime = function_exists('publishedTimeAgo') ? publishedTimeAgo($row['published_at'] ?? '') : '';
            $items[] = [
                'link_url' => $row['url'] ?? '',
                'news_title' => $row['title'] ?? '',
                'news_image' => $row['image'] ?? '',
                'published_at' => $publishedTime,
                'category_title' => !empty($row['category_title']) ? $row['category_title'] : 'News',
                'news_paid' => (int)($row['news_paid'] ?? 0)
            ];
        }
    }
    echo json_encode([
        'status' => 200,
        'page' => (int)($page ?? 1),
        'totalPages' => (int)($pages ?? 1),
        'items' => $items
    ]);
    exit();
}

require_once 'header.php';
$searchTotalPages = (int)($pages ?? 1);

?>

<div id='view-search' class='page-view active'>
    <div class='page-header'>
        <h1 class='greeting-title'>Search</h1>
    </div>
    <div class='search-page-container'>
        <form class='search-form' action='/search/' method='get'>
            <div class='search-input-box'>
                <i class='ph ph-magnifying-glass search-icon'></i>
                <input
                    type='text'
                    id='main-search-input'
                    class='search-input'
                    name="q"
                    value="<?php echo h($searchKeyword); ?>"
                    placeholder='Search news, companies, topics...' />
                <i class='ph ph-x-circle clear-search-icon' id='clear-search-input' style='<?php echo !empty($searchKeyword) ? "display: block;" : "display: none;"; ?>'></i>
            </div>
        </form>

        <!-- Autocomplete Suggestions (Shown when typing) -->
        <div class='search-suggestions' id='search-suggestions' style='display: none;'>
            <h4 class='recent-title'>Suggestions</h4>
            <div class='suggestions-list' id='suggestions-list'>
                <!-- Dynamically populated via JS -->
            </div>
        </div>

        <!-- Dynamic Search Results -->
        <div
            class='search-results'
            id='search-results'>
            <h4 class='recent-title'>
                Search Results for '<span id='search-query-display'><?php echo h($searchKeyword); ?></span>'
            </h4>
            <div class='vertical-feed no-padding'>
                <?php
                if (is_array($queryPost) && count($queryPost) > 0) {

                    foreach ($queryPost as $row) {
                        $paidBadge = ((isset($row['news_paid']) && $row['news_paid'] == 1) ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");
                        $categoryName = !empty($row['category_title']) ? h($row['category_title']) : 'News';
                        echo "<a href='{$row['url']}' class='list-card native-list-item'>
            <div class='list-content'>
                <div class='list-meta'>
                    <span class='tag-text'>{$categoryName}</span> &bull; " . publishedTimeAgo($row['published_at']) . "{$paidBadge}
                </div>
                <h4 class='list-title'>
                    " . h($row['title']) . "
                </h4>
            </div>
            <img
                src='" . h($row['image']) . "'
                alt='" . h($row['title']) . "'
                class='list-img' />
        </a>";
                    }
                } else { ?>

                    <div
                        class="no-results-container"
                        id="search-no-results">
                        <div class="no-results-icon">
                            <i class="ph ph-magnifying-glass-slash"></i>
                        </div>
                        <h3 class="no-results-title">No Results Found</h3>
                        <p class="no-results-desc">
                            We couldn't find any articles matching "<span id="no-results-query"></span>". Try checking for typos or searching for broader terms.
                        </p>

                        <div class="no-results-suggestions">
                            <span class="suggestion-title">Suggested Keywords:</span>
                            <div class="suggestion-tags">
                                <a href="/search/?q=paytm" title="Paytm" class="suggestion-chip">Paytm</a>
                                <a href="/search/?q=fintech" title="Fintech" class="suggestion-chip">Fintech</a>
                                <a href="/search/?q=rbi" title="RBI" class="suggestion-chip">RBI</a>
                                <a href="/search/?q=deepdive" title="Deepdive" class="suggestion-chip">Deepdive</a>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
            <?php if (is_array($queryPost) && count($queryPost) > 0) { ?>
                <div id="infinite-scroll-sentinel" data-feed-type="search" data-query="<?php echo h($searchKeyword); ?>" data-page="1" data-total-pages="<?php echo $searchTotalPages; ?>"></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>