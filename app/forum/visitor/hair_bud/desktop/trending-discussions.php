<?php
require_once DATA_FUNCTIONS;
require_once 'header.php';

/* ----------------------------
   Query helpers / normalization
----------------------------- */
// Read category from ?category or legacy ?categoryName
$categoryParam = isset($_GET['category'])
    ? $_GET['category']
    : (isset($_GET['categoryName']) ? $_GET['categoryName'] : '');

// Fetch categories
$categoryList = getAllCategoryListFilter($connection, 'discussion');

/* ----------------------------
   Active Category label
----------------------------- */
$activeCategoryLabel = 'All Categories';
foreach ($categoryList as $cat) {
    // Assume link_url stores the canonical slug or route
    $linkSlug = strtolower(string: trim($cat['link_url']));
    if ($linkSlug == ACTIVE_URL_NON_QUERY) {
        $activeCategoryLabel = $cat['category_title'];
        break;
    }
}


/* ----------------------------
   Sort state
----------------------------- */
$sortOptions = [
    'latest'  => 'Latest',
    'popular' => 'Most Popular',
    'replies' => 'Most Replies',
    'likes'   => 'Most Liked',
    'oldest'  => 'Oldest',
];
// Normalize sort key
$rawSort    = $_GET['sort'] ?? 'latest';
$normSort   = strtolower(trim($rawSort));
$currentSort = array_key_exists($normSort, $sortOptions) ? $normSort : 'latest';
$activeSortLabel = $sortOptions[$currentSort];

/* ----------------------------
   Date/Time state (robust aliases)
----------------------------- */
$dateOptions = [
    'all'   => 'All Time',
    'today' => 'Today',
    'week'  => 'This Week',
    'month' => 'This Month',
    'year'  => 'This Year',
];
// Support ?date= and legacy ?time=
$rawDate  = $_GET['date'] ?? ($_GET['time'] ?? 'all');
$normDate = strtolower(trim($rawDate));
// Common aliases
$aliases = [
    ''          => 'all',
    'alltime'   => 'all',
    'any'       => 'all',
    'tod'       => 'today',
    'thisweek'  => 'week',
    'thismonth' => 'month',
    'thisyear'  => 'year',
];
if (isset($aliases[$normDate])) {
    $normDate = $aliases[$normDate];
}
$currentDate = array_key_exists($normDate, $dateOptions) ? $normDate : 'all';
$activeDateLabel = $dateOptions[$currentDate];
?>
<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-2.5 sm:py-3">
        <nav class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs sm:text-sm">
            <a href="/" class="text-gray-500 hover:text-primary">Home</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
            <span class="text-gray-900 font-medium">All Discussions</span>
        </nav>
    </div>
</div>

<!-- Page Header -->
<section class="bg-white py-6 sm:py-8">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1.5 sm:mb-2">All Discussions</h1>
                <p class="text-gray-600 text-sm sm:text-base">Browse through all community discussions and find answers
                    to your questions</p>
            </div>
            <a href="/start-a-discussion/" title="Start a discussion" target="_blank"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap">
                <i class="ri-add-line mr-2"></i>Start Discussion
            </a>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<section class="bg-white border-b border-gray-200 sticky top-14 sm:top-16 z-40">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-3 sm:py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4">
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">

                <!-- Category Filter -->
                <div class="filter-dropdown relative">
                    <button
                        class="flex items-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 !rounded-button whitespace-nowrap text-sm"
                        onclick="toggleDropdown('category')" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-folder-line mr-2"></i>
                        <span id="category-text"><?= htmlspecialchars($activeCategoryLabel) ?></span>
                        <i class="ri-arrow-down-s-line ml-2"></i>
                    </button>
                    <div id="category-dropdown"
                        class="dropdown-content absolute mt-2 w-56 sm:w-64 max-h-72 overflow-auto bg-white border border-gray-200 rounded-md shadow-lg hidden"
                        aria-hidden="true">
                        <div class="p-2 text-sm">
                            <?php
                            echo "<div class='py-2 px-3 rounded cursor-pointer'
                        onclick=\"selectCategory('', 'All Categories')\">All Categories</div>";
                            foreach ($categoryList as $category) {
                                $title  = $category['category_title'];
                                $slug   = strtolower(trim($category['link_url'])); // could be a route or a slug
                                $active = ($slug === ACTIVE_URL_NON_QUERY);
                                $classes = $active ? "bg-primary/10 text-primary font-medium" : "hover:bg-gray-50";
                                echo "<div class='py-2 px-3 rounded cursor-pointer {$classes}'
                            onclick=\"selectCategory('" . htmlspecialchars($slug, ENT_QUOTES) . "', '" . htmlspecialchars($title, ENT_QUOTES) . "')\"
                            role='menuitem' tabindex='0' aria-current='" . ($active ? "true" : "false") . "'>"
                                    . htmlspecialchars($title) .
                                    "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="filter-dropdown relative">
                    <button
                        class="flex items-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 !rounded-button whitespace-nowrap text-sm"
                        onclick="toggleDropdown('sort')" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-sort-desc mr-2"></i>
                        <span id="sort-text"><?= htmlspecialchars($activeSortLabel) ?></span>
                        <i class="ri-arrow-down-s-line ml-2"></i>
                    </button>
                    <div id="sort-dropdown"
                        class="dropdown-content absolute mt-2 w-48 max-h-72 overflow-auto bg-white border border-gray-200 rounded-md shadow-lg hidden"
                        aria-hidden="true">
                        <div class="p-2 text-sm">
                            <?php foreach ($sortOptions as $value => $label):
                                $active = ($value === $currentSort);
                                $classes = $active ? "bg-primary/10 text-primary font-medium" : "hover:bg-gray-50";
                            ?>
                            <div class="py-2 px-3 rounded cursor-pointer <?= $classes ?>"
                                onclick="selectSort('<?= htmlspecialchars($value, ENT_QUOTES) ?>','<?= htmlspecialchars($label, ENT_QUOTES) ?>')"
                                role="menuitem" tabindex="0" aria-current="<?= $active ? 'true' : 'false' ?>">
                                <?= htmlspecialchars($label) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="filter-dropdown relative">
                    <button
                        class="flex items-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 !rounded-button whitespace-nowrap text-sm"
                        onclick="toggleDropdown('date')" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-calendar-line mr-2"></i>
                        <span id="date-text"><?= htmlspecialchars($activeDateLabel) ?></span>
                        <i class="ri-arrow-down-s-line ml-2"></i>
                    </button>
                    <div id="date-dropdown"
                        class="dropdown-content absolute mt-2 w-44 max-h-72 overflow-auto bg-white border border-gray-200 rounded-md shadow-lg hidden"
                        aria-hidden="true">
                        <div class="p-2 text-sm">
                            <?php foreach ($dateOptions as $value => $label):
                                $active = ($value === $currentDate);
                                $classes = $active ? "bg-primary/10 text-primary font-medium" : "hover:bg-gray-50";
                            ?>
                            <div class="py-2 px-3 rounded cursor-pointer <?= $classes ?>"
                                onclick="selectDate('<?= htmlspecialchars($value, ENT_QUOTES) ?>','<?= htmlspecialchars($label, ENT_QUOTES) ?>')"
                                role="menuitem" tabindex="0" aria-current="<?= $active ? 'true' : 'false' ?>">
                                <?= htmlspecialchars($label) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-2 text-xs sm:text-sm">
                <span class="text-gray-500">8,721 discussions</span>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- Discussion List -->
        <main class="lg:col-span-8 xl:col-span-9">
            <div class="space-y-4" id="discussions-container">
                <!-- demo cards ... (your static/demo items remain unchanged) -->
                <!-- ... -->
            </div>

            <!-- Pagination (static demo) -->
            <div class="flex flex-wrap justify-center items-center gap-2 mt-6 sm:mt-8">
                <button class="px-3 py-2 text-gray-500 hover:text-gray-700 pagination-btn"><i
                        class="ri-arrow-left-s-line"></i></button>
                <button class="px-3 py-2 rounded-md pagination-btn active">1</button>
                <button class="px-3 py-2 rounded-md pagination-btn">2</button>
                <button class="px-3 py-2 rounded-md pagination-btn">3</button>
                <button class="px-3 py-2 rounded-md pagination-btn">4</button>
                <button class="px-3 py-2 rounded-md pagination-btn">5</button>
                <span class="px-3 py-2 text-gray-500">...</span>
                <button class="px-3 py-2 rounded-md pagination-btn">87</button>
                <button class="px-3 py-2 text-gray-500 hover:text-gray-700 pagination-btn"><i
                        class="ri-arrow-right-s-line"></i></button>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 xl:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 sm:p-6 mb-6">
                <div class="flex flex-wrap gap-2">
                    <?php
                    $tagList = popularTags($connection, 'discussion');
                    $currentTag = isset($_GET['tag']) ? $_GET['tag'] : '';

                    foreach ($tagList as $tags) {
                        $t = htmlspecialchars($tags['tag_title']);
                        $slug = strtolower(str_replace(' ', '_', $tags['tag_title']));
                        $active = ($currentTag === $slug) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700';
                        if ($t != '') {
                            echo "<span class='px-3 py-1 text-sm rounded-full cursor-pointer tag-filter {$active}'
                         data-slug='{$slug}'
                         onclick=\"handleTagClick(this)\">{$t}</span>";
                        }
                    }
                    ?>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php require_once 'footer.php'; ?>