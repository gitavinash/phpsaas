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
            <a href="/trending-discussions/" class=" text-gray-500 hover:text-primary">All Discussions</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
            <span class="text-gray-900 font-medium"><?php echo $category_title ?></span>
        </nav>
    </div>
</div>

<!-- Page Header -->
<section class="bg-white py-6 sm:py-8">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1.5 sm:mb-2">
                    <?php echo ((isset($category_title)) ? $category_title : 'All') . ' Discussions' ?></h1>
                <p class="text-gray-600 text-sm sm:text-base">Browse through all
                    <?php echo ((isset($category_title)) ? $category_title : '') ?> discussions and
                    find answers
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
        <main class="lg:col-span-8 xl:col-span-9">
            <div class="space-y-4" id="discussions-container">


                <?php

                $categoryPost = getCategoryDiscussion(
                    $connection,
                    $category_id,
                    'discussion',
                    $currentSort,
                    $currentDate,
                    $pageNo
                );

                print_r($categoryPost);
                exit();

                ?>

                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 transition-all duration-300 discussion-card cursor-pointer">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                <span class="text-indigo-600 font-medium">EM</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">Emily Morgan</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-sm text-gray-500">2 hours ago</p>
                                </div>
                                <div class="flex items-center mt-1">
                                    <span
                                        class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Hair
                                        Loss</span>
                                    <div class="ml-3 flex items-center text-xs text-gray-500">
                                        <i class="ri-pushpin-line mr-1"></i>
                                        <span>Pinned</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="w-3 h-3 bg-primary rounded-full"></div>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 hover:text-primary">
                        My experience with finasteride after 6 months - detailed review
                    </h3>
                    <a href="https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/3e271e94-ec38-40af-bb38-d59c4857883b"
                        data-readdy="true" class="block">
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            I wanted to share my comprehensive journey with finasteride so
                            far. The first 2 months were challenging with some side
                            effects, but after adjusting the dosage with my doctor's
                            guidance, I'm seeing real improvements in hair density. I've
                            documented everything including photos, side effects, and
                            timeline...
                        </p>
                    </a>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                            <span class="flex items-center"><i class="ri-eye-line mr-2"></i> 1,247</span>
                            <span class="flex items-center"><i class="ri-message-2-line mr-2"></i> 48</span>
                            <span class="flex items-center"><i class="ri-heart-line mr-2"></i> 86</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center border-2 border-white">
                                    <span class="text-green-600 text-xs font-medium">DR</span>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center border-2 border-white">
                                    <span class="text-purple-600 text-xs font-medium">SJ</span>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center border-2 border-white">
                                    <span class="text-orange-600 text-xs font-medium">+5</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">Last reply 1h ago</span>
                        </div>
                    </div>
                </div>
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