<?php
if (empty($event_id)) {
    http_response_code(404);
    if (defined('ERROR_404') && file_exists(ERROR_404)) {
        include_once ERROR_404;
    } else {
        include_once __DIR__ . '/404.php';
    }
    return;
}

$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'event') ? urlencode('Hello Veloura Atelier, I would like to enquire about the workshop: ' . $event_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

$tagTitle = '';
if ($tagResult = mysqli_query($connection, "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id = {$event_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'event' LIMIT 1")) {
    if (mysqli_num_rows($tagResult) > 0) {
        $tagRow = mysqli_fetch_assoc($tagResult);
        $tagTitle = $tagRow['tag_title'];
    }
}

$tagList = '';
if (!empty($tagTitle)) {
    $escapedTagTitle = mysqli_real_escape_string($connection, $tagTitle);
    if ($getTagIds = mysqli_query($connection, "SELECT * 
    FROM " . TABLE_PREFIX . "tag 
    WHERE tag_title LIKE '%{$escapedTagTitle}%'
    AND tag_relation_id != {$event_id}
    AND tag_site_id = " . SITE_ID . "
    AND tag_type = 'event'
    AND tag_time >= NOW() - INTERVAL 1 YEAR
    GROUP BY tag_relation_id
    ORDER BY RAND()
    LIMIT 0, 4")) {
        if (mysqli_num_rows($getTagIds) > 0) {
            $tagIds = array();
            while ($tagData = mysqli_fetch_assoc($getTagIds)) {
                $tagIds[] = $tagData['tag_relation_id'];
            }
            $tagList = implode(',', $tagIds);
        }
    }
}

$eventDate = !empty($event_start_date) ? date('d M Y', strtotime($event_start_date)) : 'Upcoming Weekend';
$eventTime = (($event_start_time != '00:00:00') ? date('h:i A', strtotime($event_start_time)) : '11:00 AM');
$eventEndTime = (($event_end_time != '00:00:00') ? date('h:i A', strtotime($event_end_time)) : '02:00 PM');

$thumbnails = array();
if (!empty($event_gallery)) {
    $galleryImages = json_decode($event_gallery, true);
    if (!empty($galleryImages) && is_array($galleryImages)) {
        $thumbnails[] = array('path' => $event_image, 'title' => $event_title);
        foreach ($galleryImages as $gallery) {
            if (!empty($gallery['path'])) {
                $thumbnails[] = array('path' => $gallery['path'], 'title' => $event_title);
            }
        }
    }
}
if (empty($thumbnails)) {
    $thumbnails[] = array('path' => $event_image, 'title' => $event_title);
}

// Scoped Event Inventory Resolution
$invQuery = "SELECT inventory_price, inventory_mrp, inventory_currency_symbol FROM " . TABLE_PREFIX . "inventory WHERE inventory_relation_id = {$event_id} AND inventory_type = 'event' AND inventory_site_id = " . SITE_ID . " LIMIT 1";
$invRes = mysqli_query($connection, $invQuery);
$invRow = ($invRes && mysqli_num_rows($invRes) > 0) ? mysqli_fetch_assoc($invRes) : [];

$effectivePrice = (float)($invRow['inventory_price'] ?? ($inventory_price ?? 4500));
if ($effectivePrice <= 0) {
    $effectivePrice = (float)($invRow['inventory_mrp'] ?? 4500);
}
$currencySymbol = !empty($invRow['inventory_currency_symbol']) ? $invRow['inventory_currency_symbol'] : '₹';

require_once 'header.php';
$galleryJson = safe_html(json_encode($thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 md:py-12 pb-28 md:pb-24">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-[#635E59] mb-4 sm:mb-8 overflow-x-auto scrollbar-none" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1.5 sm:gap-2.5">
            <li><a href="/" class="hover:text-[#C47C68] transition-colors">Home</a></li>
            <li class="text-[#D5C4B7]" aria-hidden="true">/</li>
            <li><a href="/events/" class="hover:text-[#C47C68] transition-colors">Floral Workshops</a></li>
            <li class="text-[#D5C4B7]" aria-hidden="true">/</li>
            <li class="text-[#242220] font-medium truncate" aria-current="page"><?php echo safe_html(ucwords((string) $event_title)); ?></li>
        </ol>
    </nav>

    <!-- Workshop Hero Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 mb-12 sm:mb-16 items-start">
        <div class="lg:col-span-6 md:sticky md:top-28 md:self-start">
            <div class="rounded-2xl overflow-hidden bg-white border border-[#E8DDD5] shadow-sm aspect-square relative select-none">
                <img src="<?php echo safe_html((string) $event_image); ?>"
                    id="mainProductImage"
                    alt="<?php echo safe_html(ucwords((string) $event_title)); ?>"
                    fetchpriority="high"
                    loading="eager"
                    decoding="async"
                    class="w-full h-full object-cover">
            </div>
        </div>

        <div class="lg:col-span-6 space-y-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="vl-eyebrow">Botanical Masterclass</span>
                    <span class="vl-badge vl-badge-sage text-[10px]">Limited to 12 Seats</span>
                </div>

                <h1 class="font-serif text-3xl sm:text-4xl text-[#242220] font-medium leading-tight">
                    <?php echo safe_html(ucwords((string) $event_title)); ?>
                </h1>

                <!-- Price -->
                <div class="flex items-baseline gap-3 pt-2">
                    <span class="text-3xl font-serif font-bold text-[#C47C68]">
                        <?php echo $currencySymbol . number_format($effectivePrice, 0); ?>
                    </span>
                    <span class="text-xs text-[#635E59] font-medium">per participant • includes all blooms, ceramic vessel, tools &amp; champagne brunch</span>
                </div>
            </div>

            <!-- Workshop Logistics Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-[#FAF7F5] border border-[#E8DDD5] text-xs text-[#242220]">
                <div class="flex items-center gap-2.5">
                    <i class="ri-calendar-line text-lg text-[#C47C68]" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-[#635E59]">Date</p>
                        <p class="font-medium text-sm"><?php echo safe_html($eventDate); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <i class="ri-time-line text-lg text-[#3D5447]" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-[#635E59]">Timing</p>
                        <p class="font-medium text-sm"><?php echo safe_html($eventTime . ' – ' . $eventEndTime); ?></p>
                    </div>
                </div>
                <div class="sm:col-span-2 pt-2 border-t border-[#E8DDD5] flex items-center gap-2.5">
                    <i class="ri-map-pin-2-line text-lg text-[#C47C68]" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-[#635E59]">Venue</p>
                        <p class="font-medium text-sm">Chhatarpur Floral Atelier, Main Mandir Road, New Delhi</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($event_description)): ?>
                <div class="text-sm text-[#635E59] leading-relaxed font-light border-t border-[#E8DDD5] pt-4">
                    <?php echo nl2br(safe_html((string) $event_description)); ?>
                </div>
            <?php endif; ?>

            <!-- Action Area: Seats & Reserve Button -->
            <div class="space-y-4 pt-4 border-t border-[#E8DDD5]">
                <div class="flex items-center gap-4">
                    <div class="vl-qty-control">
                        <button type="button" id="decrease-quantity" class="vl-qty-btn" aria-label="Decrease tickets">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="10" class="vl-qty-input" aria-label="Number of seats">
                        <button type="button" id="increase-quantity" class="vl-qty-btn" aria-label="Increase tickets">+</button>
                    </div>

                    <!-- Hidden Event Meta (Crucial for API to branch to 'event' and set ₹4,500!) -->
                    <div id="productInfo"
                        value="<?php echo (int) $event_id . '_' . (float) $effectivePrice . '_1'; ?>"
                        data-product-id="<?php echo (int) $event_id; ?>"
                        data-unit-price="<?php echo (float) $effectivePrice; ?>"
                        data-currency="<?php echo safe_html($currencySymbol); ?>"
                        data-item-type="event"
                        class="hidden"></div>

                    <!-- Reserve Seat Button -->
                    <button type="button" id="addToCart"
                        class="vl-btn-primary flex-1 py-3.5 text-sm font-semibold justify-center">
                        <i class="ri-ticket-line text-lg" aria-hidden="true"></i>
                        <span id="addToCartText">Reserve Your Seat</span>
                    </button>
                </div>

                <!-- WhatsApp Concierge Support -->
                <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                    class="w-full vl-btn-outline justify-center py-3 text-xs gap-2">
                    <i class="ri-whatsapp-line text-base text-[#25D366]" aria-hidden="true"></i>
                    <span>Enquire About Private Group Bookings on WhatsApp</span>
                </a>
            </div>

            <!-- Inclusions Accordion -->
            <div class="pt-6 border-t border-[#E8DDD5] space-y-3">
                <details class="group rounded-xl bg-[#FAF7F5] border border-[#E8DDD5] p-4 cursor-pointer" open>
                    <summary class="font-serif font-medium text-base text-[#242220] flex items-center justify-between list-none">
                        <span>Masterclass Inclusions</span>
                        <i class="ri-arrow-down-s-line text-lg group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <ul class="pt-3 text-xs text-[#635E59] leading-relaxed space-y-1.5 list-disc pl-4">
                        <li>Premium Dutch garden roses, hydrangeas, orchids, and seasonal foliage.</li>
                        <li>Artisanal ceramic vase to design and take home.</li>
                        <li>Professional Japanese florist shears provided for the session.</li>
                        <li>Champagne brunch and light refreshments served at our studio terrace.</li>
                    </ul>
                </details>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
