<?php
$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'event') ? urlencode('i want to know about ' . $event_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

// First, fetch the tag for this event
$tagTitle = '';
if ($tagResult = mysqli_query($connection, "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id = {$event_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'event' LIMIT 1")) {
    if (mysqli_num_rows($tagResult) > 0) {
        $tagRow = mysqli_fetch_assoc($tagResult);
        $tagTitle = $tagRow['tag_title'];
    }
}

// Get related tags only if a tag exists for this event
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

// Fetch all tags for this product
$productTags = array();
if ($getProductTags = mysqli_query($connection, "SELECT DISTINCT tag_title FROM " . TABLE_PREFIX . "tag WHERE
tag_relation_id = {$event_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'event' ORDER BY tag_title")) {
    while ($tagRow = mysqli_fetch_assoc($getProductTags)) {
        if (isset($tagRow['tag_title']) && $tagRow['tag_title'] !== '') {
            $productTags[] = $tagRow['tag_title'];
        }
    }
}

$eventDate = !empty($event_start_date)
    ? date('d M Y', strtotime($event_start_date))
    : '';

$eventTime = (($event_start_time != '00:00:00')
    ? date('h:i A', strtotime($event_start_time))
    : '');

$eventEndTime = (($event_end_time != '00:00:00')
    ? date('h:i A', strtotime($event_end_time))
    : '');

require_once 'header.php';

?>

<style>
    @media (min-width: 768px) {
        .product-media-sticky {
            position: sticky;
            top: 6.5rem;
            align-self: flex-start;
        }

        .desktop-sticky-cta {
            position: fixed;
            bottom: 1.5rem;
            z-index: 60;
            opacity: 0;
            pointer-events: none;
            transform: translateY(0.5rem);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .desktop-sticky-cta.is-active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .desktop-default-cta.is-hidden {
            display: none;
        }
    }
</style>

<main class="container mx-auto px-4 py-8 pt-24 md:pb-32">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-gray-500 mb-6 overflow-x-auto" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1 sm:gap-2">
            <li>
                <a href="/" class="hover:text-primary transition-colors">Home</a>
            </li>
            <li class="text-gray-300">/</li>
            <li>
                <a href="#" class="hover:text-primary transition-colors truncate">
                    <?php echo htmlspecialchars($category_title, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-700 truncate" aria-current="page">
                <?php echo htmlspecialchars(ucwords($event_title), ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </nav>
    <!-- Product Hero Section -->
    <div class="flex flex-col md:flex-row gap-8 mb-16">
        <!-- Product Images -->
        <div class="md:w-1/2 product-media-sticky">
            <div class="product-image-container mb-4 rounded-lg overflow-hidden bg-gray-100">
                <div class="w-full aspect-square flex items-center justify-center">
                    <img src="<?php echo $event_image ?>" id="mainProductImage"
                        alt="<?php echo ucwords($event_title) ?>" class="w-full h-full object-contain">
                </div>
            </div>
            <div class="grid thumbnail grid-cols-5 gap-2">
                <?php
                $thumbnails = array();

                if ($event_gallery != '') {
                    $galleryImages = json_decode($event_gallery, true);
                    if (!empty($galleryImages)) {
                        $thumbnails[] = array('path' => $event_image, 'title' => $event_title);
                        foreach ($galleryImages as $gallery) {
                            $thumbnails[] = array('path' => $gallery['path'], 'title' => $event_title);
                        }
                    }
                } else {
                    $thumbnails[] = array('path' => $event_image, 'title' => $event_title);
                }

                foreach ($thumbnails as $index => $thumb) {
                    $isActive = $index === 0 ? 'border-primary ring-2 ring-primary' : 'border-gray-200 hover:border-gray-300';
                    echo "<div class='aspect-square rounded-md overflow-hidden cursor-pointer border-2 {$isActive} transition-all duration-200 bg-gray-50' data-src='{$thumb['path']}'>";
                    echo "<img src='{$thumb['path']}' alt='{$thumb['title']} Thumbnail " . ($index + 1) . "' class='w-full h-full object-contain p-1'>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <!-- Product Details -->
        <div class="md:w-1/2 product-details">

            <div class="inline-flex items-center bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <i class="ri-award-line mr-2"></i>
                <?php echo ucwords($category_title); ?>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                <?php echo ucwords($event_title); ?>
            </h1>

            <div class="grid grid-cols-3 gap-3 mb-6">

                <div class="bg-gray-50 rounded-xl p-4 border">
                    <div class="text-primary text-xl mb-2">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div class="text-xs text-gray-500">
                        DATE
                    </div>
                    <div class="font-semibold">
                        <?php echo $eventDate; ?>
                    </div>
                </div>

                <?php

                if (isset($eventTime) && !empty($eventTime)) { ?>
                    <div class="bg-gray-50 rounded-xl p-4 border">
                        <div class="text-primary text-xl mb-2">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="text-xs text-gray-500">
                            TIME
                        </div>
                        <div class="font-semibold">
                            <?php echo $eventTime . ((!empty($eventEndTime)) ? ' - ' . $eventEndTime : ''); ?>
                        </div>
                    </div>
                <?php
                }

                ?>

                <?php

                if (isset($event_location) && !empty($event_location)) { ?>
                    <div class="bg-gray-50 rounded-xl p-4 border">
                        <div class="text-primary text-xl mb-2">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <div class="text-xs text-gray-500">
                            LOCATION
                        </div>
                        <div class="font-semibold">
                            <?php echo $event_location; ?>
                        </div>
                    </div>
                <?php
                }

                ?>

            </div>

            <?php

            if ($inventory_current_stock != 0) { ?>

                <div class="flex items-center gap-3 mb-6">

                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                        <?php echo $inventory_current_stock; ?> Seats Available
                    </span>
                </div>
            <?php
            }
            ?>

            <div class="mb-8">

                <?php if ($inventory_price == $inventory_mrp || $inventory_mrp == 0) { ?>

                    <div class="text-5xl font-bold text-gray-900">
                        <?php echo $inventory_currency_symbol . number_format($inventory_price); ?>
                    </div>

                <?php } else { ?>

                    <div class="flex items-center gap-3 flex-wrap">

                        <span class="text-5xl font-bold text-gray-900">
                            <?php echo $inventory_currency_symbol . number_format($inventory_price); ?>
                        </span>

                        <span class="text-xl text-gray-400 line-through">
                            <?php echo $inventory_currency_symbol . number_format($inventory_mrp); ?>
                        </span>

                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-semibold">
                            Save <?php echo round((1 - $inventory_price / $inventory_mrp) * 100); ?>%
                        </span>

                    </div>

                <?php } ?>

            </div>

            <div class="bg-gradient-to-r from-primary/5 to-primary/10 border rounded-2xl p-6 mb-8">

                <h3 class="font-bold text-xl mb-3">
                    Workshop Highlights
                </h3>

                <p class="text-gray-700 leading-relaxed">
                    <?php echo nl2br($event_highlight); ?>
                </p>

            </div>

            <div class="flex gap-4 mb-8 desktop-default-cta">
                <p style="display:none" id="productInfo"
                    value="<?php echo $event_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
                </p>

                <button id="addToCart"
                    class="flex-1 bg-primary text-white py-3 px-6 rounded-button font-medium hover:bg-opacity-90 transition flex items-center justify-center whitespace-nowrap">

                    <i class="ri-ticket-line mr-2"></i>
                    Book Your Seat

                </button>

                <a href="<?php echo $whatsappUrl; ?>"
                    target="_blank"
                    class="flex items-center justify-center px-6 py-3 rounded-button font-medium text-green-800 bg-green-100 border border-green-300 hover:bg-green-200 hover:text-green-900 focus:outline-none focus:ring-2 focus:ring-green-400 transition whitespace-nowrap">

                    <i class="ri-whatsapp-line mr-2"></i>
                    WhatsApp

                </a>

            </div>

            <div class="bg-white border rounded-2xl p-6 mb-8">

                <h3 class="text-xl font-bold mb-4">
                    Event Information
                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between">
                        <span class="text-gray-500">Category</span>
                        <span class="font-semibold"><?php echo $category_title; ?></span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Organizer</span>
                        <span class="font-semibold"><?php echo ucwords($profile_display_name); ?></span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Venue</span>
                        <span class="font-semibold"><?php echo ucwords($event_location); ?></span>
                    </div>

                    <?php

                    if ($inventory_current_stock != 0) { ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Available Seats</span>
                            <span class="font-semibold"><?php echo $inventory_current_stock; ?></span>
                        </div>
                    <?php
                    }

                    ?>



                </div>

            </div>

        </div>
    </div>
    <section class="mb-16">

        <h2 class="text-3xl font-bold mb-6">
            About This Workshop
        </h2>

        <div class="prose max-w-none text-gray-700 text-lg leading-relaxed">

            <?php echo nl2br($event_description); ?>

        </div>

    </section>

    <?php if (!empty($event_map)) { ?>

        <section class="mt-10 mb-10">

            <h2 class="text-2xl font-bold mb-5">
                Event Location
            </h2>

            <div class="border rounded-2xl overflow-hidden">

                <div class="p-4 bg-gray-50 border-b">

                    <div class="font-semibold">
                        <?php echo $event_location; ?>
                    </div>

                </div>

                <div class="map-container">
                    <?php echo $event_map; ?>
                </div>

            </div>

        </section>

    <?php } ?>

    </div>
    </div>

    </div>

</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.addon-filter-btn');
        const productCards = document.querySelectorAll('.addon-product-card');
        const mediaSticky = document.querySelector('.product-media-sticky');
        const desktopStickyCta = document.querySelector('.desktop-sticky-cta');
        const desktopDefaultCta = document.querySelector('.desktop-default-cta');
        const productDetails = document.querySelector('.product-details');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const selectedCategory = this.getAttribute('data-category');

                // Update active button
                filterBtns.forEach(b => b.classList.remove('active', 'border-primary',
                    'text-primary'));
                filterBtns.forEach(b => b.classList.add('border-gray-300', 'text-gray-700'));
                this.classList.add('active', 'border-primary', 'text-primary');
                this.classList.remove('border-gray-300', 'text-gray-700');

                // Filter products
                productCards.forEach(card => {
                    if (selectedCategory === 'all') {
                        card.style.display = '';
                    } else {
                        const cardCategory = card.getAttribute('data-category');
                        card.style.display = cardCategory === selectedCategory ? '' :
                            'none';
                    }
                });
            });
        });

        if (mediaSticky && desktopStickyCta && productDetails) {
            const updateStickyCta = () => {
                if (window.innerWidth < 768) {
                    desktopStickyCta.classList.remove('is-active');
                    desktopStickyCta.style.left = '';
                    desktopStickyCta.style.width = '';
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.remove('is-hidden');
                    }
                    return;
                }

                const stickyTop = parseFloat(getComputedStyle(mediaSticky).top) || 0;
                const mediaRect = mediaSticky.getBoundingClientRect();
                const mediaHeight = mediaSticky.offsetHeight;
                const isStuck = mediaRect.top <= stickyTop + 1 &&
                    mediaRect.bottom >= stickyTop + mediaHeight - 1;

                if (isStuck) {
                    const detailsRect = productDetails.getBoundingClientRect();
                    desktopStickyCta.style.left = `${detailsRect.left}px`;
                    desktopStickyCta.style.width = `${detailsRect.width}px`;
                    desktopStickyCta.classList.add('is-active');
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.add('is-hidden');
                    }
                } else {
                    desktopStickyCta.classList.remove('is-active');
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.remove('is-hidden');
                    }
                }
            };

            updateStickyCta();
            window.addEventListener('scroll', updateStickyCta, {
                passive: true
            });
            window.addEventListener('resize', updateStickyCta);
        }
    });
</script>
<?php

require_once 'footer.php';

?>