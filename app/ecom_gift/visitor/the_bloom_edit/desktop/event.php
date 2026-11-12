<?php

$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'event') ? urlencode('i want to know about ' . $event_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

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

$productTags = array();
if ($getProductTags = mysqli_query($connection, "SELECT DISTINCT tag_title FROM " . TABLE_PREFIX . "tag WHERE
tag_relation_id = {$event_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'event' ORDER BY tag_title")) {
    while ($tagRow = mysqli_fetch_assoc($getProductTags)) {
        if (isset($tagRow['tag_title']) && $tagRow['tag_title'] !== '') {
            $productTags[] = $tagRow['tag_title'];
        }
    }
}

$eventDate = !empty($event_start_date) ? date('d M Y', strtotime($event_start_date)) : '';
$eventTime = (($event_start_time != '00:00:00') ? date('h:i A', strtotime($event_start_time)) : '');
$eventEndTime = (($event_end_time != '00:00:00') ? date('h:i A', strtotime($event_end_time)) : '');

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
$effectivePrice = ($inventory_price == 0) ? $inventory_mrp : $inventory_price;
$currencySymbol = !empty($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';

require_once 'header.php';
$galleryJson = safe_html(json_encode($thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 md:py-12 pb-28 md:pb-24">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-[#5E5650] mb-4 sm:mb-8 overflow-x-auto scrollbar-none" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1.5 sm:gap-2.5">
            <li><a href="/" class="hover:text-[#183028] transition-colors">Home</a></li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li><a href="/events/" class="hover:text-[#183028] transition-colors truncate"><?php echo safe_html(ucwords((string) ($category_title ?? 'Workshops'))); ?></a></li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li class="text-[#1C1A18] font-medium truncate" aria-current="page"><?php echo safe_html(ucwords((string) $event_title)); ?></li>
        </ol>
    </nav>

    <!-- Workshop Hero Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-14 mb-10 sm:mb-16 items-start">
        <div class="lg:col-span-6 product-media-sticky md:sticky md:top-28 md:self-start">
            <div class="product-gallery-wrapper">
                <div class="product-main-view relative mb-3">
                    <div class="product-image-container relative aspect-square rounded-xl overflow-hidden bg-white border border-[#E6DDD3] shadow-sm cursor-crosshair group select-none"
                        id="productMainContainer"
                        data-gallery-images="<?php echo $galleryJson; ?>">
                        <div id="productAmbientBg" class="product-ambient-bg"></div>
                        <img src="<?php echo safe_html((string) $event_image); ?>"
                            id="mainProductImage"
                            alt="<?php echo safe_html(ucwords((string) $event_title)); ?>"
                            fetchpriority="high"
                            loading="eager"
                            decoding="async"
                            class="w-full h-full object-cover object-center select-none pointer-events-none relative z-10">
                        <div id="zoomLens" class="zoom-lens hidden"></div>
                        <div class="absolute top-3 left-3 bg-[#FAF7F2]/92 backdrop-blur-sm text-[#1C1A18] border border-[#E6DDD3] text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 pointer-events-none transition-opacity duration-200 group-hover:opacity-0 z-10">
                            <i class="ri-zoom-in-line text-[#9B5637] text-sm" aria-hidden="true"></i>
                            <span class="hidden md:inline font-medium">Hover to inspect</span>
                            <span class="md:hidden font-medium">Tap to expand</span>
                        </div>
                        <span id="galleryCounter" class="absolute bottom-3 right-3 bg-[#183028]/80 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full font-medium z-10 pointer-events-none md:hidden">
                            1 / <?php echo count($thumbnails); ?>
                        </span>
                        <?php if (count($thumbnails) > 1): ?>
                            <button type="button" id="galleryPrevBtn" class="gallery-nav-btn prev absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] flex items-center justify-center transition z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100" aria-label="Previous image">
                                <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                            </button>
                            <button type="button" id="galleryNextBtn" class="gallery-nav-btn next absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] flex items-center justify-center transition z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100" aria-label="Next image">
                                <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div id="zoomFlyout" class="zoom-flyout hidden">
                        <img src="<?php echo safe_html((string) $event_image); ?>" id="zoomFlyoutImg" alt="" class="zoom-flyout-img">
                        <div class="zoom-flyout-badge"><i class="ri-zoom-in-line" aria-hidden="true"></i> Click to expand</div>
                    </div>
                </div>

                <?php if (count($thumbnails) > 1): ?>
                    <div class="thumbnails-container flex md:grid md:grid-cols-5 gap-2.5 overflow-x-auto pb-2 scrollbar-none" id="thumbnailsStrip">
                        <?php foreach ($thumbnails as $index => $thumb):
                            $thumbPath = safe_html((string) $thumb['path']);
                            $isActive = $index === 0 ? 'border-[#183028] ring-2 ring-[#183028]/25' : 'border-[#E6DDD3] hover:border-[#B48E54]';
                        ?>
                            <button type="button"
                                class="thumbnail-item flex-shrink-0 w-16 h-16 md:w-auto md:h-auto aspect-square rounded-lg overflow-hidden cursor-pointer border-2 <?php echo $isActive; ?> transition-all bg-white p-0"
                                data-index="<?php echo $index; ?>"
                                data-src="<?php echo $thumbPath; ?>"
                                data-title="<?php echo safe_html((string) $thumb['title']); ?>"
                                aria-label="View image <?php echo $index + 1; ?>"
                                aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <img src="<?php echo $thumbPath; ?>" alt="" class="w-full h-full object-cover pointer-events-none">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Event Details -->
        <div class="lg:col-span-6 product-details space-y-6">
            <div>
                <span class="tbe-eyebrow mb-2">
                    <?php echo safe_html(ucwords((string) ($category_title ?? 'Atelier Workshop'))); ?>
                </span>
                <h1 class="font-serif text-2xl sm:text-5xl font-medium text-[#1C1A18] leading-tight mb-4 sm:mb-5">
                    <?php echo safe_html(ucwords((string) $event_title)); ?>
                </h1>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 mb-5">
                    <?php if (!empty($eventDate)): ?>
                        <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4">
                            <i class="ri-calendar-event-line text-xl text-[#9B5637] mb-1.5 block" aria-hidden="true"></i>
                            <span class="text-[11px] uppercase tracking-wider text-[#5E5650] block">Date</span>
                            <span class="text-sm font-semibold text-[#1C1A18]"><?php echo safe_html($eventDate); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($eventTime)): ?>
                        <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4">
                            <i class="ri-time-line text-xl text-[#9B5637] mb-1.5 block" aria-hidden="true"></i>
                            <span class="text-[11px] uppercase tracking-wider text-[#5E5650] block">Time</span>
                            <span class="text-sm font-semibold text-[#1C1A18]"><?php echo safe_html($eventTime . ((!empty($eventEndTime)) ? ' - ' . $eventEndTime : '')); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event_location)): ?>
                        <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4 col-span-2 sm:col-span-1">
                            <i class="ri-map-pin-line text-xl text-[#9B5637] mb-1.5 block" aria-hidden="true"></i>
                            <span class="text-[11px] uppercase tracking-wider text-[#5E5650] block">Location</span>
                            <span class="text-sm font-semibold text-[#1C1A18]"><?php echo safe_html($event_location); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($inventory_current_stock != 0): ?>
                    <span class="inline-flex items-center gap-1.5 bg-[#ECFDF5] text-[#166534] border border-[#BBF7D0] px-3.5 py-1 rounded-full text-xs font-semibold mb-4">
                        <i class="ri-user-add-line" aria-hidden="true"></i>
                        <?php echo (int) $inventory_current_stock; ?> Seats Available
                    </span>
                <?php endif; ?>

                <div class="flex items-baseline flex-wrap gap-3">
                    <span class="text-4xl font-serif font-semibold text-[#1C1A18]">
                        <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?>
                    </span>
                    <?php if ($inventory_price != $inventory_mrp && $inventory_mrp > 0): ?>
                        <span class="text-lg text-[#5E5650] line-through">
                            <?php echo safe_html($currencySymbol . ' ' . number_format((float) $inventory_mrp)); ?>
                        </span>
                        <span class="tbe-badge tbe-badge-savings">
                            Save <?php echo round((1 - $inventory_price / $inventory_mrp) * 100); ?>%
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($event_highlight)): ?>
                <div class="bg-[#EFE8DC]/75 border border-[#E6DDD3] rounded-xl p-6">
                    <h2 class="font-serif text-xl font-medium text-[#1C1A18] mb-2">Workshop Highlights</h2>
                    <p class="text-sm sm:text-base text-[#5E5650] leading-relaxed">
                        <?php echo nl2br(safe_html((string) $event_highlight)); ?>
                    </p>
                </div>
            <?php endif; ?>

            <input type="hidden" id="productInfo" data-item-type="event"
                value="<?php echo safe_html($event_id . '_' . $effectivePrice . '_1'); ?>">

            <div class="flex flex-col sm:flex-row gap-3.5 desktop-default-cta">
                <button type="button" id="addToCart" class="flex-1 tbe-btn-forest py-3.5">
                    <i class="ri-ticket-line text-base" aria-hidden="true"></i>
                    <span>Reserve Your Seat</span>
                </button>
                <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                    class="tbe-btn-outline border-[#183028] text-[#183028] hover:bg-[#183028] hover:text-white whitespace-nowrap">
                    <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
                    <span>Inquire via WhatsApp</span>
                </a>
            </div>

            <div class="bg-white border border-[#E6DDD3] rounded-xl p-6">
                <h2 class="font-serif text-xl font-medium text-[#1C1A18] mb-4">Session Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-[#EFE8DC] pb-2">
                        <dt class="text-[#5E5650]">Category</dt>
                        <dd class="font-semibold text-[#1C1A18]"><?php echo safe_html(ucwords((string) $category_title)); ?></dd>
                    </div>
                    <?php if (!empty($event_location)): ?>
                        <div class="flex justify-between gap-4 border-b border-[#EFE8DC] pb-2">
                            <dt class="text-[#5E5650]">Venue</dt>
                            <dd class="font-semibold text-[#1C1A18] text-right"><?php echo safe_html(ucwords((string) $event_location)); ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($inventory_current_stock != 0): ?>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#5E5650]">Available Capacity</dt>
                            <dd class="font-semibold text-[#183028]"><?php echo (int) $inventory_current_stock; ?> Seats</dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- About Workshop -->
    <section class="bg-white border border-[#E6DDD3] rounded-xl p-6 sm:p-10 mb-14">
        <span class="tbe-eyebrow mb-2">Full Curriculum</span>
        <h2 class="font-serif text-2xl sm:text-3xl font-medium text-[#1C1A18] mb-4">About This Workshop</h2>
        <div class="sameFont text-[#5E5650] text-base leading-relaxed">
            <?php echo nl2br((string) $event_description); ?>
        </div>
    </section>

    <?php if (!empty($event_map)): ?>
        <section class="mb-10">
            <h2 class="font-serif text-2xl sm:text-3xl font-medium text-[#1C1A18] mb-5">Atelier Venue Location</h2>
            <div class="bg-white border border-[#E6DDD3] rounded-xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 bg-[#FAF7F2] border-b border-[#E6DDD3] flex items-center gap-2 text-sm font-semibold text-[#1C1A18]">
                    <i class="ri-map-pin-2-line text-[#9B5637]" aria-hidden="true"></i>
                    <span><?php echo safe_html((string) $event_location); ?></span>
                </div>
                <div class="map-container border-0 rounded-none">
                    <?php echo $event_map; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Sticky Event Booking Bar -->
<div class="tbe-pdp-sticky desktop-sticky-cta hidden md:block">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between gap-6">
        <div class="min-w-0">
            <p class="font-serif text-base font-medium text-[#1C1A18] truncate"><?php echo safe_html(ucwords((string) $event_title)); ?></p>
            <p class="text-xs text-[#5E5650]"><?php echo safe_html($eventDate); ?> &bull; <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="tbe-btn-outline text-xs py-2.5 px-4">WhatsApp</a>
            <button type="button" data-trigger-add-to-cart class="tbe-btn-forest text-xs py-2.5 px-6">
                <span>Reserve Seat</span>
            </button>
        </div>
    </div>
</div>

<!-- Mobile Fixed Bottom Booking Bar -->
<div class="tbe-mobile-sticky-bar md:hidden">
    <div class="flex items-center gap-3">
        <div class="min-w-0 shrink-0">
            <p class="text-[10px] uppercase tracking-widest text-[#5E5650] font-semibold">Seat Fee</p>
            <p class="text-base font-serif font-semibold text-[#183028] leading-tight">
                <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?>
            </p>
        </div>
        <button type="button" data-trigger-add-to-cart class="flex-1 tbe-btn-forest py-3 min-h-[44px]">
            <i class="ri-ticket-line" aria-hidden="true"></i>
            <span>Reserve Seat</span>
        </button>
        <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
            aria-label="Inquire on WhatsApp"
            class="w-11 h-11 shrink-0 inline-flex items-center justify-center rounded-md bg-[#EFE8DC] text-[#183028] border border-[#D4C8BA]">
            <i class="ri-whatsapp-line text-xl" aria-hidden="true"></i>
        </a>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="productLightboxModal" class="lightbox-modal fixed inset-0 z-[100] hidden bg-[#1C1A18]/95 backdrop-blur-md flex-col select-none" role="dialog" aria-modal="true" aria-label="Workshop Image Gallery">
    <div class="tbe-lightbox-topbar flex items-center justify-between px-4 sm:px-6 pb-3 bg-black/50 border-b border-white/10 z-30 shrink-0">
        <div class="flex items-center gap-3 min-w-0 flex-1 mr-2">
            <span class="font-serif text-sm font-medium text-white truncate max-w-[150px] sm:max-w-md"><?php echo safe_html(ucwords((string) $event_title)); ?></span>
            <span id="lightboxCounter" class="text-xs text-[#EFE8DC] font-mono bg-white/10 px-2.5 py-0.5 rounded whitespace-nowrap shrink-0">1 of <?php echo count($thumbnails); ?></span>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" id="lightboxZoomOut" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom out"><i class="ri-zoom-out-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxZoomIn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom in"><i class="ri-zoom-in-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxZoomReset" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Reset zoom"><i class="ri-refresh-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxCloseBtn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/20 hover:bg-[#9B5637] text-white flex items-center justify-center transition ml-1" aria-label="Close gallery"><i class="ri-close-line text-xl" aria-hidden="true"></i></button>
        </div>
    </div>
    <div class="flex-1 relative overflow-hidden flex items-center justify-center p-2 sm:p-6" id="lightboxStage">
        <button type="button" id="lightboxPrevBtn" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Previous image"><i class="ri-arrow-left-s-line text-2xl" aria-hidden="true"></i></button>
        <div class="w-full h-full flex items-center justify-center overflow-hidden" id="lightboxViewport">
            <img src="<?php echo safe_html((string) $event_image); ?>" id="lightboxImage" alt="<?php echo safe_html(ucwords((string) $event_title)); ?>" class="max-w-full max-h-full object-contain select-none transition-transform duration-150">
        </div>
        <button type="button" id="lightboxNextBtn" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Next image"><i class="ri-arrow-right-s-line text-2xl" aria-hidden="true"></i></button>
    </div>
    <?php if (count($thumbnails) > 1): ?>
        <div class="tbe-lightbox-thumbbar px-4 pt-3 bg-black/60 border-t border-white/10 overflow-x-auto flex items-center justify-center gap-2.5 z-20 scrollbar-none" id="lightboxThumbnailsStrip">
            <?php foreach ($thumbnails as $index => $thumb):
                $thumbPath = safe_html((string) $thumb['path']);
                $isActive = $index === 0 ? 'border-[#B48E54] ring-2 ring-[#B48E54]' : 'border-white/20 opacity-60 hover:opacity-100';
            ?>
                <button type="button"
                    class="lightbox-thumb-item w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 rounded-lg overflow-hidden cursor-pointer border-2 <?php echo $isActive; ?> transition-all bg-white/5 p-0"
                    data-index="<?php echo $index; ?>"
                    data-src="<?php echo $thumbPath; ?>"
                    aria-label="Select image <?php echo $index + 1; ?>">
                    <img src="<?php echo $thumbPath; ?>" alt="" class="w-full h-full object-cover select-none pointer-events-none">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo USER_JS ?>product-gallery.min.js?v=<?php echo assetVersion('js/product-gallery.min.js'); ?>"></script>
<?php
require_once 'footer.php';
?>
