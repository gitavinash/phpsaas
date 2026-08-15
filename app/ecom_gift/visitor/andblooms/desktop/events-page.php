<?php include_once 'header.php'; ?>

<?php
// Page Metadata defaults
$page_title = !empty($page_title) ? $page_title : 'Events & Workshops';
$page_description = !empty($page_description) ? $page_description : 'Discover & join our exclusive floral workshops, masterclasses, and community events.';
$bannerImage = $page_image ?? '';
$bannerFromProduct = false;

// Sort parameter
$sort = $_GET['sort'] ?? 'newest';
$sortQuery = "ORDER BY e.event_start_date DESC, e.event_id DESC";

if ($sort === 'price_low') {
    $sortQuery = "ORDER BY inv.inventory_price ASC, e.event_id DESC";
} elseif ($sort === 'price_high') {
    $sortQuery = "ORDER BY inv.inventory_price DESC, e.event_id DESC";
} elseif ($sort === 'oldest') {
    $sortQuery = "ORDER BY e.event_start_date ASC, e.event_id ASC";
} elseif ($sort === 'title_asc') {
    $sortQuery = "ORDER BY e.event_title ASC";
}

// Fetch all site events where event_status = 1
$siteEvents = [];
$eventsQuery = "SELECT e.*, l.link_url, c.category_title,
                       inv.inventory_price, inv.inventory_mrp, inv.inventory_currency_symbol, inv.inventory_in_stock, inv.inventory_current_stock
                FROM " . TABLE_PREFIX . "event e
                LEFT JOIN " . TABLE_PREFIX . "link l 
                    ON l.link_relation_id = e.event_id 
                   AND l.link_type = 'event' 
                   AND l.link_status = 200 
                   AND l.link_site_id = " . SITE_ID . " 
                LEFT JOIN " . TABLE_PREFIX . "category c 
                    ON c.category_id = e.event_category 
                   AND c.category_site_id = " . SITE_ID . " 
                LEFT JOIN " . TABLE_PREFIX . "inventory inv
                    ON inv.inventory_relation_id = e.event_id
                   AND inv.inventory_site_id = " . SITE_ID . "
                WHERE e.event_status = 1 
                  AND e.event_site_id = " . SITE_ID . " 
                {$sortQuery}";

$eventsResult = mysqli_query($connection, $eventsQuery);
if ($eventsResult && mysqli_num_rows($eventsResult) > 0) {
    while ($row = mysqli_fetch_assoc($eventsResult)) {
        $siteEvents[] = $row;
    }
}

// Pagination Setup & Constants Definition
$perPage = defined('PER_PAGE_LIMIT') ? (int)PER_PAGE_LIMIT : 10;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalEventsCount = count($siteEvents);
$totalPages = max(1, (int)ceil($totalEventsCount / $perPage));

if (!defined('PAGE_NO')) {
    define('PAGE_NO', $currentPage);
}
if (!defined('TOTAL_PAGE')) {
    define('TOTAL_PAGE', $totalPages);
}
if (!defined('NEXT_PAGE')) {
    define('NEXT_PAGE', min($totalPages, $currentPage + 1));
}
if (!defined('PREVIOUS_PAGE')) {
    define('PREVIOUS_PAGE', max(1, $currentPage - 1));
}

// Get sliced events for current page
$offset = ($currentPage - 1) * $perPage;
$pagedEvents = array_slice($siteEvents, $offset, $perPage);

if (empty($bannerImage) && !empty($siteEvents) && is_array($siteEvents)) {
    $firstEvent = reset($siteEvents);
    if (is_array($firstEvent) && !empty($firstEvent['event_image'])) {
        $bannerImage = $firstEvent['event_image'];
        $bannerFromProduct = true;
    }
}
?>

<!-- Hero Banner -->
<div class="relative min-h-[320px] md:min-h-[380px] w-full mb-12 mt-16 overflow-hidden flex items-center justify-center bg-gray-900">
    <?php if (!empty($bannerImage)) { ?>
        <?php
        $bannerStyle = "background-image: url('{$bannerImage}');";
        if ($bannerFromProduct) {
            $bannerStyle .= ' filter: blur(4px); transform: scale(1.08);';
        }
        ?>
        <div style="<?= $bannerStyle ?>" class="absolute inset-0 bg-cover bg-center transition-all duration-700"></div>
    <?php } else { ?>
        <div class="absolute inset-0 bg-gradient-to-r from-[#D4946D] via-[#c68259] to-[#8d4f2b]"></div>
    <?php } ?>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/30"></div>

    <div class="container mx-auto px-4 py-12 h-full flex flex-col justify-center items-center text-center relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-white/80 mb-6 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/20">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span class="mx-2 text-white/50">/</span>
            <span class="text-white font-medium"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <h1 class="font-serif text-4xl md:text-6xl text-white font-bold mb-4 tracking-tight drop-shadow-md">
            <?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="text-white/90 text-lg md:text-xl max-w-2xl font-light leading-relaxed drop-shadow-sm">
            <?= htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
</div>

<!-- Main Content Area -->
<div class="container mx-auto px-4 pb-16">

    <!-- Filter & Toolbar Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                <i class="ri-calendar-event-line"></i>
            </div>
            <div>
                <h2 class="text-lg md:text-xl font-bold text-gray-900">All Upcoming Events</h2>
                <p class="text-xs md:text-sm text-gray-500">Showing <span class="font-semibold text-primary"><?= count($siteEvents) ?></span> active events</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <label for="eventSortSelect" class="text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:inline-block">Sort By:</label>
            <form method="GET" class="w-full sm:w-auto">
                <select id="eventSortSelect" name="sort" onchange="this.form.submit()"
                    class="w-full sm:w-auto bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Date: Newest First</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Date: Oldest First</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title: A to Z</option>
                </select>
            </form>
        </div>
    </div>

    <?php if (!empty($pagedEvents)) { ?>
        <!-- Events List (Landscape Card Layout) -->
        <div class="space-y-6">
            <?php foreach ($pagedEvents as $event) {
                extract($event);
                $eventTitle = ucwords($event_title);
                $categoryName = !empty($category_title) ? ucwords($category_title) : 'Workshop';
                $eventImg = !empty($event_image) ? $event_image : (!empty($event_thumbnail) ? $event_thumbnail : 'assets/images/placeholder.jpg');
                $eventLink = !empty($link_url) ? $link_url : ('event.php?id=' . $event_id);
                
                // Date formatting
                $hasDate = (!empty($event_start_date) && $event_start_date !== '0000-00-00');
                $dateDay = $hasDate ? date('d', strtotime($event_start_date)) : '--';
                $dateMonth = $hasDate ? date('M', strtotime($event_start_date)) : 'TBA';
                $dateYear = $hasDate ? date('Y', strtotime($event_start_date)) : '';
                $fullDateStr = $hasDate ? date('D, d M Y', strtotime($event_start_date)) : 'Date to be announced';
                
                // Time formatting
                $hasTime = (!empty($event_start_time) && $event_start_time !== '00:00:00');
                $timeStart = $hasTime ? date('h:i A', strtotime($event_start_time)) : '';
                $hasEndTime = (!empty($event_end_time) && $event_end_time !== '00:00:00');
                $timeEnd = $hasEndTime ? date('h:i A', strtotime($event_end_time)) : '';
                $timeStr = $hasTime ? ($timeStart . ($hasEndTime ? ' - ' . $timeEnd : '')) : 'All Day';

                // Pricing
                $price = (isset($inventory_price) && $inventory_price != 0 ? $inventory_price : ($inventory_mrp ?? 0));
                $mrp = $inventory_mrp ?? 0;
                $currencySymbol = !empty($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';
                $discount = (isset($inventory_price) && $inventory_price != 0 && isset($inventory_mrp) && $inventory_price < $inventory_mrp)
                    ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
                    : 0;

                // Location & description
                $locationStr = !empty($event_location) ? htmlspecialchars($event_location, ENT_QUOTES, 'UTF-8') : 'Venue to be announced';
                $descSnippet = !empty($event_description) ? htmlspecialchars(mb_strimwidth(strip_tags($event_description), 0, 160, '...'), ENT_QUOTES, 'UTF-8') : '';
                $seatsAvailable = isset($inventory_current_stock) ? (int)$inventory_current_stock : 0;
            ?>
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col md:flex-row group">
                    
                    <!-- Left: Event Image & Date Badge Overlay -->
                    <div class="relative md:w-2/5 lg:w-1/3 min-h-[220px] md:min-h-full overflow-hidden bg-gray-100">
                        <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>" class="block h-full">
                            <img src="<?= htmlspecialchars($eventImg, ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        </a>

                        <!-- Calendar Badge Overlay -->
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md text-gray-900 rounded-xl px-3 py-2 text-center shadow-lg border border-white/40 min-w-[60px]">
                            <span class="block text-xl font-extrabold leading-none text-primary"><?= $dateDay ?></span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-gray-600 mt-1"><?= $dateMonth ?></span>
                        </div>

                        <?php if ($discount > 0): ?>
                            <div class="absolute top-4 right-4 bg-red-600 text-white font-semibold text-xs px-3 py-1 rounded-full shadow-md">
                                <?= $discount ?>% OFF
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right: Event Content & Actions -->
                    <div class="p-6 md:p-8 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Meta -->
                            <div class="flex items-center gap-3 mb-3 flex-wrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                    <i class="ri-price-tag-3-line mr-1.5"></i> <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <?php if ($seatsAvailable > 0): ?>
                                    <span class="inline-flex items-center text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                                        <i class="ri-user-line mr-1"></i> <?= $seatsAvailable ?> seats left
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Event Title -->
                            <h3 class="text-xl md:text-2xl font-bold font-serif text-gray-900 group-hover:text-primary transition-colors mb-3 leading-snug">
                                <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h3>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line text-primary text-base"></i>
                                    <span><?= htmlspecialchars($fullDateStr, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ri-time-line text-primary text-base"></i>
                                    <span><?= htmlspecialchars($timeStr, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex items-center gap-2 sm:col-span-2">
                                    <i class="ri-map-pin-line text-primary text-base"></i>
                                    <span class="truncate"><?= $locationStr ?></span>
                                </div>
                            </div>

                            <?php if (!empty($descSnippet)): ?>
                                <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed mb-6">
                                    <?= $descSnippet ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Card Footer / CTA -->
                        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <?php if ($price > 0): ?>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-2xl font-bold text-gray-900">
                                            <?= $currencySymbol . number_format($price) ?>
                                        </span>
                                        <?php if ($discount > 0): ?>
                                            <span class="text-sm text-gray-400 line-through">
                                                <?= $currencySymbol . number_format($mrp) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-lg font-bold text-emerald-600">Free Event</span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>"
                               class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm bg-primary text-white hover:bg-opacity-90 transition-all duration-200 shadow-sm group-hover:shadow group-hover:translate-x-0.5">
                                Explore & Book Seat <i class="ri-arrow-right-line ml-2"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php } ?>
        </div>

        <?php
        if (TOTAL_PAGE > 1) {
            require_once 'pagination.php';
        }
        ?>

    <?php } else { ?>
        <!-- No Event Found Message -->
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary text-3xl">
                <i class="ri-calendar-event-line"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">No Active Events Found</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">There are currently no events listed with active status. Please check back soon for upcoming workshops and masterclasses.</p>
            <a href="/" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-opacity-90 transition shadow-sm">
                <i class="ri-home-4-line mr-2"></i> Back to Homepage
            </a>
        </div>
    <?php } ?>

</div>

<?php include_once 'footer.php'; ?>