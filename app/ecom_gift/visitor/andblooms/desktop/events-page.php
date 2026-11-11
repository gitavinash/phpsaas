<?php include_once 'header.php'; ?>

<?php
// Page Metadata defaults
$page_title = !empty($page_title) ? $page_title : 'Events & Workshops';
$page_description = !empty($page_description) ? $page_description : 'Discover & join our exclusive floral workshops, masterclasses, and community events.';
$bannerImage = $page_image ?? '';
$bannerFromProduct = false;

// Sort parameter
$sort = $_GET['sort'] ?? 'newest';
$sortQuery = "ORDER BY e.event_start_date ASC";

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
                   AND inv.inventory_type = 'event'
                   AND inv.inventory_site_id = " . SITE_ID . "
                WHERE e.event_status = 1 
                  AND e.event_site_id = " . SITE_ID . " 
                {$sortQuery}";

$eventsResult = mysqli_query($connection, $eventsQuery);
$seenEventIds = [];
if ($eventsResult && mysqli_num_rows($eventsResult) > 0) {
    while ($row = mysqli_fetch_assoc($eventsResult)) {
        $eventId = isset($row['event_id']) ? (int)$row['event_id'] : 0;
        if ($eventId > 0) {
            if (isset($seenEventIds[$eventId])) {
                continue;
            }
            $seenEventIds[$eventId] = true;
        }
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

// Categorize events into Upcoming and Previous based on event_start_date
$today = date('Y-m-d');
$upcomingEvents = [];
$previousEvents = [];

foreach ($siteEvents as $ev) {
    $startDate = $ev['event_start_date'] ?? '';
    $hasDate = (!empty($startDate) && $startDate !== '0000-00-00');
    // If no date specified or start date is today or in the future -> upcoming
    if (!$hasDate || $startDate >= $today) {
        $upcomingEvents[] = $ev;
    } else {
        $previousEvents[] = $ev;
    }
}

// Active tab selection (upcoming, previous, all)
$activeTab = $_GET['tab'] ?? '';
if (!in_array($activeTab, ['upcoming', 'previous', 'all'])) {
    $activeTab = (!empty($upcomingEvents) || empty($previousEvents)) ? 'upcoming' : 'previous';
}

// Banner Image Fallback
if (empty($bannerImage)) {
    $firstUpcoming = !empty($upcomingEvents) ? reset($upcomingEvents) : null;
    if ($firstUpcoming && !empty($firstUpcoming['event_image'])) {
        $bannerImage = $firstUpcoming['event_image'];
        $bannerFromProduct = true;
    } elseif (!empty($siteEvents)) {
        $firstEvent = reset($siteEvents);
        if (is_array($firstEvent) && !empty($firstEvent['event_image'])) {
            $bannerImage = $firstEvent['event_image'];
            $bannerFromProduct = true;
        }
    }
}

// Reusable Event Card Renderer
if (!function_exists('renderEventCardItem')) {
    function renderEventCardItem($event, $today)
    {
        $eventId = $event['event_id'] ?? 0;
        $eventTitle = ucwords($event['event_title'] ?? '');
        $categoryName = !empty($event['category_title']) ? ucwords($event['category_title']) : 'Workshop';
        $eventImg = !empty($event['event_image']) ? $event['event_image'] : (!empty($event['event_thumbnail']) ? $event['event_thumbnail'] : 'assets/images/placeholder.jpg');
        $eventLink = !empty($event['link_url']) ? $event['link_url'] : ('event.php?id=' . $eventId);

        // Date formatting
        $startDate = $event['event_start_date'] ?? '';
        $hasDate = (!empty($startDate) && $startDate !== '0000-00-00');
        $dateDay = $hasDate ? date('d', strtotime($startDate)) : '--';
        $dateMonth = $hasDate ? date('M', strtotime($startDate)) : 'TBA';
        $fullDateStr = $hasDate ? date('D, d M Y', strtotime($startDate)) : 'Date to be announced';

        // Past event check
        $isPast = ($hasDate && $startDate < $today);

        // Time formatting
        $startTime = $event['event_start_time'] ?? '';
        $endTime = $event['event_end_time'] ?? '';
        $hasTime = (!empty($startTime) && $startTime !== '00:00:00');
        $timeStart = $hasTime ? date('h:i A', strtotime($startTime)) : '';
        $hasEndTime = (!empty($endTime) && $endTime !== '00:00:00');
        $timeEnd = $hasEndTime ? date('h:i A', strtotime($endTime)) : '';
        $timeStr = $hasTime ? ($timeStart . ($hasEndTime ? ' - ' . $timeEnd : '')) : 'All Day';

        // Pricing
        $invPrice = $event['inventory_price'] ?? 0;
        $invMrp = $event['inventory_mrp'] ?? 0;
        $price = (isset($invPrice) && $invPrice != 0 ? $invPrice : ($invMrp ?? 0));
        $mrp = $invMrp ?? 0;
        $currencySymbol = !empty($event['inventory_currency_symbol']) ? $event['inventory_currency_symbol'] : '₹';
        $discount = (isset($invPrice) && $invPrice != 0 && isset($invMrp) && $invPrice < $invMrp)
            ? round((($invMrp - $invPrice) / $invMrp) * 100)
            : 0;

        // Location & description
        $locationStr = !empty($event['event_location']) ? htmlspecialchars($event['event_location'], ENT_QUOTES, 'UTF-8') : 'Venue to be announced';
        $descSnippet = !empty($event['event_description']) ? htmlspecialchars(mb_strimwidth(strip_tags($event['event_description']), 0, 160, '...'), ENT_QUOTES, 'UTF-8') : '';
        $seatsAvailable = isset($event['inventory_current_stock']) ? (int)$event['inventory_current_stock'] : 0;
?>
        <article class="bg-white rounded-2xl border <?= $isPast ? 'border-gray-200/80' : 'border-gray-100' ?> shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col md:flex-row group">

            <!-- Left: Event Image & Date Badge Overlay -->
            <div class="relative md:w-2/5 lg:w-1/3 min-h-[220px] md:min-h-full overflow-hidden bg-gray-100">
                <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>" class="block h-full">
                    <img src="<?= htmlspecialchars($eventImg, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 <?= $isPast ? 'filter saturate-90' : '' ?>">
                </a>

                <!-- Calendar Badge Overlay -->
                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md text-gray-900 rounded-xl px-3 py-2 text-center shadow-lg border border-white/40 min-w-[60px]">
                    <span class="block text-xl font-extrabold leading-none <?= $isPast ? 'text-gray-700' : 'text-primary' ?>"><?= $dateDay ?></span>
                    <span class="block text-xs font-bold uppercase tracking-wider text-gray-600 mt-1"><?= $dateMonth ?></span>
                </div>

                <?php if ($isPast): ?>
                    <!-- Past Event Overlay Badge -->
                    <div class="absolute top-4 right-4 bg-gray-900/85 backdrop-blur-md text-white font-semibold text-xs px-3 py-1 rounded-full shadow-md flex items-center gap-1.5">
                        <i class="ri-history-line"></i> Past Event
                    </div>
                <?php elseif ($discount > 0): ?>
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $isPast ? 'bg-gray-100 text-gray-700' : 'bg-primary/10 text-primary' ?>">
                            <i class="ri-price-tag-3-line mr-1.5"></i> <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <?php if ($isPast): ?>
                            <span class="inline-flex items-center text-xs font-medium text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">
                                <i class="ri-checkbox-circle-line text-emerald-600 mr-1"></i> Concluded
                            </span>
                        <?php elseif ($seatsAvailable > 0): ?>
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
                            <i class="ri-calendar-line <?= $isPast ? 'text-gray-400' : 'text-primary' ?> text-base"></i>
                            <span><?= htmlspecialchars($fullDateStr, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ri-time-line <?= $isPast ? 'text-gray-400' : 'text-primary' ?> text-base"></i>
                            <span><?= htmlspecialchars($timeStr, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex items-center gap-2 sm:col-span-2">
                            <i class="ri-map-pin-line <?= $isPast ? 'text-gray-400' : 'text-primary' ?> text-base"></i>
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
                                <?php if ($discount > 0 && !$isPast): ?>
                                    <span class="text-sm text-gray-400 line-through">
                                        <?= $currencySymbol . number_format($mrp) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-lg font-bold text-emerald-600">Free Event</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($isPast): ?>
                        <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-200 transition-all duration-200 shadow-sm group-hover:shadow group-hover:translate-x-0.5">
                            View Event Details <i class="ri-arrow-right-line ml-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm bg-primary text-white hover:bg-opacity-90 transition-all duration-200 shadow-sm group-hover:shadow group-hover:translate-x-0.5">
                            Explore & Book Seat <i class="ri-arrow-right-line ml-2"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
<?php
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

    <!-- Filter & Tabs Toolbar Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 mb-8 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">

        <!-- Tabs Switcher -->
        <div class="flex flex-wrap items-center gap-2 p-1.5 bg-gray-100/80 rounded-xl border border-gray-200/60 w-full lg:w-auto">
            <!-- Upcoming Events Tab -->
            <a href="?tab=upcoming<?= !empty($sort) && $sort !== 'newest' ? '&sort=' . urlencode($sort) : '' ?>"
                data-tab="upcoming"
                class="event-tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all <?= $activeTab === 'upcoming' ? 'bg-white text-gray-900 shadow-sm border border-gray-200/80' : 'text-gray-600 hover:text-gray-900 hover:bg-white/60' ?>">
                <i class="ri-calendar-event-line <?= $activeTab === 'upcoming' ? 'text-primary' : 'text-gray-400' ?>"></i>
                <span>Upcoming</span>
                <span class="tab-count-badge px-2 py-0.5 text-xs rounded-full <?= $activeTab === 'upcoming' ? 'bg-primary/10 text-primary font-bold' : 'bg-gray-200/80 text-gray-600' ?>">
                    <?= count($upcomingEvents) ?>
                </span>
            </a>

            <!-- Previous Events Tab -->
            <a href="?tab=previous<?= !empty($sort) && $sort !== 'newest' ? '&sort=' . urlencode($sort) : '' ?>"
                data-tab="previous"
                class="event-tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all <?= $activeTab === 'previous' ? 'bg-white text-gray-900 shadow-sm border border-gray-200/80' : 'text-gray-600 hover:text-gray-900 hover:bg-white/60' ?>">
                <i class="ri-history-line <?= $activeTab === 'previous' ? 'text-primary' : 'text-gray-400' ?>"></i>
                <span>Previous</span>
                <span class="tab-count-badge px-2 py-0.5 text-xs rounded-full <?= $activeTab === 'previous' ? 'bg-primary/10 text-primary font-bold' : 'bg-gray-200/80 text-gray-600' ?>">
                    <?= count($previousEvents) ?>
                </span>
            </a>

            <!-- All Events Tab -->
            <a href="?tab=all<?= !empty($sort) && $sort !== 'newest' ? '&sort=' . urlencode($sort) : '' ?>"
                data-tab="all"
                class="event-tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all <?= $activeTab === 'all' ? 'bg-white text-gray-900 shadow-sm border border-gray-200/80' : 'text-gray-600 hover:text-gray-900 hover:bg-white/60' ?>">
                <i class="ri-apps-2-line <?= $activeTab === 'all' ? 'text-primary' : 'text-gray-400' ?>"></i>
                <span>All Events</span>
                <span class="tab-count-badge px-2 py-0.5 text-xs rounded-full <?= $activeTab === 'all' ? 'bg-primary/10 text-primary font-bold' : 'bg-gray-200/80 text-gray-600' ?>">
                    <?= count($siteEvents) ?>
                </span>
            </a>
        </div>

        <!-- Sort Control -->
        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <label for="eventSortSelect" class="text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:inline-block">Sort By:</label>
            <form method="GET" class="w-full sm:w-auto" id="eventSortForm">
                <input type="hidden" name="tab" id="eventSortTabInput" value="<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>">
                <select id="eventSortSelect" name="sort"
                    class="w-full sm:w-auto bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all cursor-pointer">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Date: Newest First</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Date: Oldest First</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title: A to Z</option>
                </select>
            </form>
        </div>
    </div>

    <!-- TAB PANEL: Upcoming Events -->
    <div id="tab-panel-upcoming" class="<?= $activeTab === 'upcoming' ? 'block' : 'hidden' ?> space-y-6">
        <?php if (!empty($upcomingEvents)) { ?>
            <div class="space-y-6">
                <?php foreach ($upcomingEvents as $event) {
                    renderEventCardItem($event, $today);
                } ?>
            </div>
        <?php } else { ?>
            <!-- Upcoming Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm max-w-xl mx-auto px-6">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 text-primary text-2xl">
                    <i class="ri-calendar-check-line"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Upcoming Events Right Now</h3>
                <p class="text-gray-500 mb-6 text-sm leading-relaxed">
                    We are currently curating new workshops, masterclasses, and floral experiences. Stay tuned for upcoming dates, or explore our previous sessions!
                </p>
                <?php if (!empty($previousEvents)) { ?>
                    <button type="button" data-switch-tab="previous" class="event-switch-tab-btn inline-flex items-center px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-opacity-90 transition shadow-sm">
                        <i class="ri-history-line mr-2"></i> Explore Previous Events (<?= count($previousEvents) ?>)
                    </button>
                <?php } else { ?>
                    <a href="/" class="inline-flex items-center px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-opacity-90 transition shadow-sm">
                        <i class="ri-home-4-line mr-2"></i> Back to Homepage
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <!-- TAB PANEL: Previous Events -->
    <div id="tab-panel-previous" class="<?= $activeTab === 'previous' ? 'block' : 'hidden' ?> space-y-6">
        <?php if (!empty($previousEvents)) { ?>
            <div class="space-y-6">
                <?php foreach ($previousEvents as $event) {
                    renderEventCardItem($event, $today);
                } ?>
            </div>
        <?php } else { ?>
            <!-- Previous Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm max-w-xl mx-auto px-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-500 text-2xl">
                    <i class="ri-history-line"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Previous Events</h3>
                <p class="text-gray-500 mb-6 text-sm leading-relaxed">
                    Past event archives and completed workshop highlights will appear here.
                </p>
                <?php if (!empty($upcomingEvents)) { ?>
                    <button type="button" data-switch-tab="upcoming" class="event-switch-tab-btn inline-flex items-center px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-opacity-90 transition shadow-sm">
                        <i class="ri-calendar-event-line mr-2"></i> View Upcoming Events (<?= count($upcomingEvents) ?>)
                    </button>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <!-- TAB PANEL: All Events -->
    <div id="tab-panel-all" class="<?= $activeTab === 'all' ? 'block' : 'hidden' ?> space-y-8">
        <?php if (!empty($siteEvents)) { ?>
            <?php if (!empty($upcomingEvents)) { ?>
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200">
                        <span class="w-3 h-3 rounded-full bg-primary"></span>
                        <h3 class="text-lg font-bold text-gray-900">Upcoming Events</h3>
                        <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-primary/10 text-primary">
                            <?= count($upcomingEvents) ?>
                        </span>
                    </div>
                    <div class="space-y-6">
                        <?php foreach ($upcomingEvents as $event) {
                            renderEventCardItem($event, $today);
                        } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (!empty($previousEvents)) { ?>
                <div class="<?= !empty($upcomingEvents) ? 'mt-10' : '' ?>">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200">
                        <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                        <h3 class="text-lg font-bold text-gray-900">Previous Events</h3>
                        <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            <?= count($previousEvents) ?>
                        </span>
                    </div>
                    <div class="space-y-6">
                        <?php foreach ($previousEvents as $event) {
                            renderEventCardItem($event, $today);
                        } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <!-- Global No Events Found Message -->
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl mx-auto px-6">
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

</div>

<script src="<?= USER_JS ?>events.js" defer></script>
<?php include_once 'footer.php'; ?>