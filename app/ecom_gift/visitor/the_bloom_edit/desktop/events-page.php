<?php include_once 'header.php'; ?>

<?php
// Page Metadata defaults
$page_title = !empty($page_title) ? $page_title : 'Events & Workshops';
$page_description = !empty($page_description) ? $page_description : 'Discover & join our exclusive floral workshops, masterclasses, and community events.';
$bannerImage = $page_image ?? '';
$bannerFromProduct = false;

// Sort & filter parameters
$sort = $_GET['sort'] ?? 'newest';
$allowedSorts = ['newest', 'oldest', 'price_low', 'price_high', 'title_asc'];
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'newest';
}

$searchQuery = trim((string) ($_GET['q'] ?? ''));

$sortQuery = "ORDER BY e.event_start_date ASC, e.event_id DESC";
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
    if (!$hasDate || $startDate >= $today) {
        $upcomingEvents[] = $ev;
    } else {
        $previousEvents[] = $ev;
    }
}

// On default chronological sort, show soonest upcoming first (TBA at end) and latest previous events first
if ($sort === 'newest') {
    usort($upcomingEvents, function ($a, $b) {
        $dateA = $a['event_start_date'] ?? '';
        $dateB = $b['event_start_date'] ?? '';
        $hasA = (!empty($dateA) && $dateA !== '0000-00-00');
        $hasB = (!empty($dateB) && $dateB !== '0000-00-00');
        if ($hasA !== $hasB) {
            return $hasA ? -1 : 1;
        }
        return strcmp((string)$dateA, (string)$dateB);
    });

    usort($previousEvents, function ($a, $b) {
        $dateCmp = strcmp((string)($b['event_start_date'] ?? ''), (string)($a['event_start_date'] ?? ''));
        if ($dateCmp !== 0) {
            return $dateCmp;
        }
        return ((int)($b['event_id'] ?? 0)) <=> ((int)($a['event_id'] ?? 0));
    });
}

// Active tab selection (upcoming, previous, all)
$activeTab = $_GET['tab'] ?? '';
if (!in_array($activeTab, ['upcoming', 'previous', 'all'], true)) {
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

// Compact Event Card Renderer (4 direct card regions: media, header, meta, footer)
if (!function_exists('renderEventCardItem')) {
    function renderEventCardItem($event, $today, $cardIndex = 0)
    {
        $eventId = $event['event_id'] ?? 0;
        $eventTitle = ucwords((string) ($event['event_title'] ?? ''));
        $categoryName = !empty($event['category_title']) ? ucwords((string) $event['category_title']) : 'Workshop';
        $eventImg = !empty($event['event_image']) ? (string) $event['event_image'] : (!empty($event['event_thumbnail']) ? (string) $event['event_thumbnail'] : '');
        $eventLink = !empty($event['link_url']) ? (string) $event['link_url'] : ('event.php?id=' . $eventId);

        // Date formatting
        $startDate = $event['event_start_date'] ?? '';
        $hasDate = (!empty($startDate) && $startDate !== '0000-00-00');
        $dateDay = $hasDate ? date('d', strtotime($startDate)) : '--';
        $dateMonth = $hasDate ? date('M', strtotime($startDate)) : 'TBA';
        $fullDateStr = $hasDate ? date('D, d M Y', strtotime($startDate)) : 'Date TBA';

        // Past event check
        $isPast = ($hasDate && $startDate < $today);

        // Time formatting
        $startTime = $event['event_start_time'] ?? '';
        $endTime = $event['event_end_time'] ?? '';
        $hasTime = (!empty($startTime) && $startTime !== '00:00:00');
        $timeStart = $hasTime ? date('h:i A', strtotime($startTime)) : '';
        $hasEndTime = (!empty($endTime) && $endTime !== '00:00:00');
        $timeEnd = $hasEndTime ? date('h:i A', strtotime($endTime)) : '';
        $timeStr = $hasTime ? ($timeStart . ($hasEndTime ? ' - ' . $timeEnd : '')) : '';

        // Pricing
        $invPrice = $event['inventory_price'] ?? 0;
        $invMrp = $event['inventory_mrp'] ?? 0;
        $price = (isset($invPrice) && $invPrice != 0 ? $invPrice : ($invMrp ?? 0));
        $mrp = $invMrp ?? 0;
        $currencySymbol = !empty($event['inventory_currency_symbol']) ? (string) $event['inventory_currency_symbol'] : '₹';
        $discount = (isset($invPrice) && $invPrice != 0 && isset($invMrp) && $invPrice < $invMrp)
            ? (int) round((($invMrp - $invPrice) / $invMrp) * 100)
            : 0;

        $locationRaw = !empty($event['event_location']) ? (string) $event['event_location'] : 'Venue TBA';
        $locationStr = htmlspecialchars($locationRaw, ENT_QUOTES, 'UTF-8');
        $seatsAvailable = isset($event['inventory_current_stock']) ? (int) $event['inventory_current_stock'] : 0;

        $searchIndex = function_exists('mb_strtolower')
            ? mb_strtolower($eventTitle . ' ' . $categoryName . ' ' . $locationRaw . ' ' . $fullDateStr, 'UTF-8')
            : strtolower($eventTitle . ' ' . $categoryName . ' ' . $locationRaw . ' ' . $fullDateStr);
?>
        <article class="event-card<?= $isPast ? ' is-past' : '' ?>"
                 data-search="<?= htmlspecialchars($searchIndex, ENT_QUOTES, 'UTF-8') ?>">
            <?php if ($eventImg !== ''): ?>
                <img src="<?= htmlspecialchars($eventImg, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>"
                     class="event-card-media"
                     loading="<?= $cardIndex < 3 ? 'eager' : 'lazy' ?>"
                     decoding="async">
            <?php endif; ?>

            <header class="event-card-header">
                <div class="event-card-badges">
                    <time class="event-date-pill"<?= $hasDate ? ' datetime="' . htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                        <span><?= htmlspecialchars($dateDay, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="event-date-month"><?= htmlspecialchars($dateMonth, ENT_QUOTES, 'UTF-8') ?></span>
                    </time>
                    <span class="event-category-pill"><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($isPast): ?>
                        <span class="event-status-pill is-past">Concluded</span>
                    <?php elseif ($discount > 0): ?>
                        <span class="event-status-pill is-discount"><?= $discount ?>% OFF</span>
                    <?php elseif ($seatsAvailable > 0): ?>
                        <span class="event-status-pill is-seats"><?= $seatsAvailable ?> left</span>
                    <?php endif; ?>
                </div>
                <h3 class="event-card-title">
                    <a href="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>" class="event-card-link">
                        <?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h3>
            </header>

            <dl class="event-card-meta">
                <div class="event-meta-row">
                    <dt class="sr-only">Date &amp; Time</dt>
                    <dd>
                        <i class="ri-calendar-line" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($fullDateStr . ($timeStr !== '' ? ' · ' . $timeStr : ''), ENT_QUOTES, 'UTF-8') ?></span>
                    </dd>
                </div>
                <div class="event-meta-row">
                    <dt class="sr-only">Location</dt>
                    <dd>
                        <i class="ri-map-pin-line" aria-hidden="true"></i>
                        <span><?= $locationStr ?></span>
                    </dd>
                </div>
            </dl>

            <footer class="event-card-footer">
                <div class="event-price">
                    <?php if ($price > 0): ?>
                        <span class="event-price-current"><?= htmlspecialchars($currencySymbol, ENT_QUOTES, 'UTF-8') . number_format((float) $price) ?></span>
                        <?php if ($discount > 0 && !$isPast): ?>
                            <span class="event-price-mrp"><?= htmlspecialchars($currencySymbol, ENT_QUOTES, 'UTF-8') . number_format((float) $mrp) ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="event-price-free">Free</span>
                    <?php endif; ?>
                </div>
                <span class="event-card-cta" aria-hidden="true">
                    <?= $isPast ? 'Details' : 'Book Seat' ?> <i class="ri-arrow-right-line"></i>
                </span>
            </footer>
        </article>
<?php
    }
}
?>

<link rel="stylesheet" href="<?= USER_STYLESHEET ?>events.css?v=<?= assetVersion('css/events.css') ?>">

<div class="events-page">
    <!-- Hero Banner -->
    <div class="relative min-h-[220px] md:min-h-[360px] w-full mb-6 sm:mb-10 overflow-hidden flex items-center justify-center bg-[#183028] border-b border-[#E6DDD3]">
        <?php if (!empty($bannerImage)) { ?>
            <img src="<?= htmlspecialchars((string) $bannerImage, ENT_QUOTES, 'UTF-8') ?>"
                 alt=""
                 aria-hidden="true"
                 loading="eager"
                 decoding="async"
                 class="events-hero-bg<?= $bannerFromProduct ? ' is-blurred' : '' ?>">
        <?php } else { ?>
            <div class="absolute inset-0 bg-gradient-to-br from-[#183028] via-[#214035] to-[#2B4B3F]"></div>
        <?php } ?>

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#1C1A18]/80 via-[#183028]/55 to-[#183028]/35"></div>

        <div class="container mx-auto px-4 py-8 sm:py-12 h-full flex flex-col justify-center items-center text-center relative z-10">
            <!-- Breadcrumb -->
            <nav class="flex items-center text-xs sm:text-sm text-white/80 mb-4 sm:mb-6 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/20" aria-label="Breadcrumb">
                <a href="/" class="hover:text-white transition-colors">Home</a>
                <span class="mx-2 text-white/50">/</span>
                <span class="text-white font-medium" aria-current="page"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></span>
            </nav>

            <h1 class="font-serif text-3xl sm:text-4xl md:text-6xl text-white font-bold mb-3 sm:mb-4 tracking-tight drop-shadow-md">
                <?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-white/90 text-base sm:text-lg md:text-xl max-w-2xl font-light leading-relaxed drop-shadow-sm">
                <?= htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <!-- Main Listing Content -->
    <div class="events-shell">
        <!-- Single-Row Filter, Search, Sort & View Toolbar -->
        <div class="events-toolbar">
            <div class="events-tabs" aria-label="Event schedule filter">
                <a href="?tab=upcoming<?= $sort !== 'newest' ? '&amp;sort=' . urlencode($sort) : '' ?><?= $searchQuery !== '' ? '&amp;q=' . urlencode($searchQuery) : '' ?>"
                    data-tab="upcoming"
                    class="event-tab-btn<?= $activeTab === 'upcoming' ? ' is-active' : '' ?>"<?= $activeTab === 'upcoming' ? ' aria-current="page"' : '' ?>>
                    <i class="ri-calendar-event-line" aria-hidden="true"></i>
                    <span>Upcoming</span>
                    <span class="tab-count-badge"><?= count($upcomingEvents) ?></span>
                </a>

                <a href="?tab=previous<?= $sort !== 'newest' ? '&amp;sort=' . urlencode($sort) : '' ?><?= $searchQuery !== '' ? '&amp;q=' . urlencode($searchQuery) : '' ?>"
                    data-tab="previous"
                    class="event-tab-btn<?= $activeTab === 'previous' ? ' is-active' : '' ?>"<?= $activeTab === 'previous' ? ' aria-current="page"' : '' ?>>
                    <i class="ri-history-line" aria-hidden="true"></i>
                    <span>Previous</span>
                    <span class="tab-count-badge"><?= count($previousEvents) ?></span>
                </a>

                <a href="?tab=all<?= $sort !== 'newest' ? '&amp;sort=' . urlencode($sort) : '' ?><?= $searchQuery !== '' ? '&amp;q=' . urlencode($searchQuery) : '' ?>"
                    data-tab="all"
                    class="event-tab-btn<?= $activeTab === 'all' ? ' is-active' : '' ?>"<?= $activeTab === 'all' ? ' aria-current="page"' : '' ?>>
                    <i class="ri-apps-2-line" aria-hidden="true"></i>
                    <span>All</span>
                    <span class="tab-count-badge"><?= $totalEventsCount ?></span>
                </a>
            </div>

            <div class="events-controls">
                <div class="events-search-box">
                    <i class="ri-search-line events-search-icon" aria-hidden="true"></i>
                    <input type="search" id="eventSearchInput" class="events-search-input"
                        value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Filter events..." aria-label="Filter events by title, category, or venue">
                    <button type="button" id="eventSearchClear" class="events-search-clear"
                        aria-label="Clear filter"<?= $searchQuery === '' ? ' hidden' : '' ?>>
                        <i class="ri-close-line" aria-hidden="true"></i>
                    </button>
                </div>

                <form method="GET" class="events-sort-form" id="eventSortForm">
                    <input type="hidden" name="tab" id="eventSortTabInput" value="<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="q" id="eventSortSearchInput" value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="eventSortSelect" class="sr-only">Sort events by</label>
                    <select id="eventSortSelect" name="sort" class="events-sort-select" aria-label="Sort events by">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Sort: Latest / Soonest</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Sort: Oldest First</option>
                        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title: A to Z</option>
                    </select>
                </form>

                <div class="events-view-switch" role="group" aria-label="Layout view">
                    <button type="button" class="events-view-btn" data-events-view="grid" aria-pressed="true" aria-label="Grid view" title="Grid view">
                        <i class="ri-layout-grid-line" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="events-view-btn" data-events-view="list" aria-pressed="false" aria-label="Compact list view" title="Compact list view">
                        <i class="ri-list-check-2" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Filter Status -->
        <div id="eventsFilterStatus" class="events-filter-status" aria-live="polite" hidden>
            <span id="eventsFilterStatusText"></span>
            <button type="button" id="eventsResetSearch" class="events-reset-btn">
                <i class="ri-close-line" aria-hidden="true"></i> Clear filter
            </button>
        </div>

        <!-- TAB PANEL: Upcoming Events -->
        <div id="tab-panel-upcoming"<?= $activeTab !== 'upcoming' ? ' hidden' : '' ?>>
            <?php if (!empty($upcomingEvents)) { ?>
                <div class="events-grid events-view-grid">
                    <?php foreach ($upcomingEvents as $idx => $event) {
                        renderEventCardItem($event, $today, $idx);
                    } ?>
                </div>
            <?php } else { ?>
                <div class="events-empty-card">
                    <div class="events-empty-icon"><i class="ri-calendar-check-line" aria-hidden="true"></i></div>
                    <h3>No Upcoming Events Right Now</h3>
                    <p>We are curating new floral workshops and masterclasses. Explore our past sessions in the meantime.</p>
                    <?php if (!empty($previousEvents)) { ?>
                        <button type="button" data-switch-tab="previous" class="event-switch-tab-btn events-empty-btn">
                            <i class="ri-history-line" aria-hidden="true"></i> View Previous Events (<?= count($previousEvents) ?>)
                        </button>
                    <?php } else { ?>
                        <a href="/" class="events-empty-btn">
                            <i class="ri-home-4-line" aria-hidden="true"></i> Back to Homepage
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- TAB PANEL: Previous Events -->
        <div id="tab-panel-previous"<?= $activeTab !== 'previous' ? ' hidden' : '' ?>>
            <?php if (!empty($previousEvents)) { ?>
                <div class="events-grid events-view-grid">
                    <?php foreach ($previousEvents as $idx => $event) {
                        renderEventCardItem($event, $today, $idx);
                    } ?>
                </div>
            <?php } else { ?>
                <div class="events-empty-card">
                    <div class="events-empty-icon"><i class="ri-history-line" aria-hidden="true"></i></div>
                    <h3>No Previous Events</h3>
                    <p>Completed workshop highlights and past event archives will appear here.</p>
                    <?php if (!empty($upcomingEvents)) { ?>
                        <button type="button" data-switch-tab="upcoming" class="event-switch-tab-btn events-empty-btn">
                            <i class="ri-calendar-event-line" aria-hidden="true"></i> View Upcoming Events (<?= count($upcomingEvents) ?>)
                        </button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- TAB PANEL: All Events -->
        <div id="tab-panel-all"<?= $activeTab !== 'all' ? ' hidden' : '' ?>>
            <?php if (!empty($siteEvents)) { ?>
                <?php if (!empty($upcomingEvents)) { ?>
                    <section class="events-group" aria-label="Upcoming events">
                        <div class="events-group-header">
                            <span class="events-group-dot" aria-hidden="true"></span>
                            <h2 class="events-group-title">Upcoming Events</h2>
                            <span class="events-group-count"><?= count($upcomingEvents) ?></span>
                        </div>
                        <div class="events-grid events-view-grid">
                            <?php foreach ($upcomingEvents as $idx => $event) {
                                renderEventCardItem($event, $today, $idx);
                            } ?>
                        </div>
                    </section>
                <?php } ?>

                <?php if (!empty($previousEvents)) { ?>
                    <section class="events-group" aria-label="Previous events">
                        <div class="events-group-header">
                            <span class="events-group-dot is-past" aria-hidden="true"></span>
                            <h2 class="events-group-title">Previous Events</h2>
                            <span class="events-group-count is-past"><?= count($previousEvents) ?></span>
                        </div>
                        <div class="events-grid events-view-grid">
                            <?php foreach ($previousEvents as $idx => $event) {
                                renderEventCardItem($event, $today, $idx);
                            } ?>
                        </div>
                    </section>
                <?php } ?>
            <?php } else { ?>
                <div class="events-empty-card">
                    <div class="events-empty-icon"><i class="ri-calendar-event-line" aria-hidden="true"></i></div>
                    <h3>No Active Events Found</h3>
                    <p>There are currently no active workshops or events listed. Please check back soon.</p>
                    <a href="/" class="events-empty-btn">
                        <i class="ri-home-4-line" aria-hidden="true"></i> Back to Homepage
                    </a>
                </div>
            <?php } ?>
        </div>

        <!-- No Filter Matches Fallback -->
        <div id="eventsNoFilterMatch" class="events-empty-card" hidden>
            <div class="events-empty-icon"><i class="ri-search-line" aria-hidden="true"></i></div>
            <h3>No Matching Events</h3>
            <p>Try a different keyword, clear your search filter, or browse all scheduled workshops.</p>
            <div class="events-empty-actions">
                <button type="button" id="eventsNoMatchClearBtn" class="events-empty-btn">
                    <i class="ri-close-line" aria-hidden="true"></i> Clear Filter
                </button>
                <button type="button" id="eventsNoMatchAllBtn" data-switch-tab="all" class="event-switch-tab-btn events-empty-btn is-secondary">
                    <i class="ri-apps-2-line" aria-hidden="true"></i> Show All Events (<?= $totalEventsCount ?>)
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= USER_JS ?>events.js?v=<?= assetVersion('js/events.js') ?>"></script>
<?php include_once 'footer.php'; ?>
