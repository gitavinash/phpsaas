<?php
require_once UTIL_FUNCTIONS;

/* -------------------------------------------------
   Helpers
------------------------------------------------- */
function safe($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function safeText($v)
{
    return nl2br(safe($v));
}

/** Render star icons from hotel_type like "3_star" or "5" */
function renderStarIcons($hotel_type)
{
    preg_match('/(\d+)/', (string)$hotel_type, $matches);
    $stars = isset($matches[1]) ? (int)$matches[1] : 0;
    $filledStars = max(0, min($stars, 5));
    $emptyStars  = 5 - $filledStars;

    $output = '<div class="flex items-center">';
    for ($i = 0; $i < $filledStars; $i++) $output .= '<i class="ri-star-fill text-yellow-400"></i>';
    for ($i = 0; $i < $emptyStars; $i++) $output .= '<i class="ri-star-line text-yellow-400"></i>';
    $output .= '</div>';
    return $output;
}

/** Decode arbitrary JSON-ish payloads (plain, html-escaped, slashed) into array */
function decode_json_any($raw)
{
    if (is_array($raw)) return $raw;
    if (!is_string($raw) || $raw === '') return [];

    $j = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;

    $raw2 = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $j = json_decode($raw2, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;

    $raw3 = stripslashes($raw2);
    $j = json_decode($raw3, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;

    return [];
}

/** Normalize a single day entry to {title, overview} */
function normalize_day($day)
{
    $out = ['title' => '', 'overview' => ''];
    if (isset($day['day'])) {
        $parts = explode(' - ', (string)$day['day'], 2);
        $out['title'] = trim($parts[1] ?? $parts[0]);
    } elseif (isset($day['title'])) {
        $out['title'] = trim((string)$day['title']);
    }
    if (isset($day['overview'])) {
        $ov = (string)$day['overview'];
        // Replace u003C/u003E that sometimes sneak in
        $ov = str_replace(['u003C', 'u003E'], ['<', '>'], $ov);
        $out['overview'] = trim($ov);
    }
    return $out;
}

/** Build a night-indexed map from the concatenated hotel_list (hotelId:roomId:roomType:night,...) */
function build_hotel_map_from_concat($hotel_list_concat)
{
    $map = []; // nightIndex (0-based) => info
    if (!$hotel_list_concat) return $map;

    $items = array_filter(array_map('trim', explode(',', $hotel_list_concat)));
    foreach ($items as $row) {
        $parts = array_map('trim', explode(':', $row));
        if (count($parts) < 4) continue;
        [$hid, $rid, $rtype, $night] = $parts;
        $night = (int)$night;
        if ($night < 1) $night = 1;
        $map[$night - 1] = [
            'hotel_id' => (int)$hid,
            'room_id'  => (int)$rid,
            'room_type' => $rtype,
            'night'    => $night
        ];
    }
    ksort($map);
    return $map;
}

/* -------------------------------------------------
   Fetch Quotation + Package + Enquiry
------------------------------------------------- */
$quotationId = base64_decode(end($urlStructure));

$query = "
    SELECT 
        q.*, 
        tp.*, 
        e.*, 
        g.*, 
        ep.*,
        GROUP_CONCAT(
            CONCAT_WS(':',
                TRIM(tph.tph_hotel_id),
                TRIM(tph.tph_room_type_id),
                TRIM(tph.tph_room_type),
                TRIM(tph.tph_night)
            )
            ORDER BY tph.tph_night
            SEPARATOR ','
        ) AS hotel_list
    FROM " . TABLE_PREFIX . "quotation q
    INNER JOIN " . TABLE_PREFIX . "tour_package tp 
        ON q.quotation_type_id = tp.tour_package_id
    INNER JOIN " . TABLE_PREFIX . "enquiry e 
        ON e.enquiry_id = q.quotation_ref_id
    LEFT JOIN " . TABLE_PREFIX . "gallery g 
        ON g.gallery_category = tp.tour_package_id 
        AND g.gallery_site_id = " . SITE_ID . "
    LEFT JOIN " . TABLE_PREFIX . "enquiry_profile ep 
        ON ep.enquiry_profile_ref_id = e.enquiry_id 
        AND ep.status = 1
    LEFT JOIN " . TABLE_PREFIX . "tour_package_hotel tph 
        ON tph.tph_package_id = tp.tour_package_id
    WHERE q.quotation_id = '" . mysqli_real_escape_string($connection, $quotationId) . "' 
        AND q.quotation_type = 'tour_package' 
        AND q.quotation_status IN (0,1) 
        AND q.quotation_site_id = " . SITE_ID . "
    GROUP BY q.quotation_id
";

if (!($checkQuote = mysqli_query($connection, $query))) {
    die("Database query failed: " . mysqli_error($connection));
}
if (mysqli_num_rows($checkQuote) <= 0) {
    die("Quotation not found or inactive.");
}

extract(mysqli_fetch_assoc($checkQuote));

/* Enquiry message may carry structured keys; carefully extract */
if (!empty($enquiry_message)) {
    $tmp = decode_json_any($enquiry_message);
    if (is_array($tmp)) extract($tmp);
}

/* Build night-indexed hotel map */
$hotelMapByNight = build_hotel_map_from_concat($hotel_list);

/* Collect IDs to fetch details */
$hotelIds = [];
$roomIds  = [];
foreach ($hotelMapByNight as $info) {
    if (!empty($info['hotel_id'])) $hotelIds[] = (int)$info['hotel_id'];
    if (!empty($info['room_id']))  $roomIds[]  = (int)$info['room_id'];
}
$hotelIds = array_values(array_unique(array_filter($hotelIds)));
$roomIds  = array_values(array_unique(array_filter($roomIds)));

$hotelRoomDetails = []; // key "hotelId|roomId" => row
if ($hotelIds && $roomIds) {
    $hotelIdsIn = implode(',', array_map('intval', $hotelIds));
    $roomIdsIn  = implode(',', array_map('intval', $roomIds));

    $q2 = "
        SELECT 
            p.property_id,
            p.property_title, 
            p.property_thumbnail, 
            p.property_city, 
            p.property_state, 
            p.property_country, 
            p.property_type,
            r.room_id,
            r.room_feature_image, 
            r.room_type 
        FROM " . TABLE_PREFIX . "tour_package_hotel tph
        INNER JOIN " . TABLE_PREFIX . "property p ON p.property_id = tph.tph_hotel_id 
        INNER JOIN " . TABLE_PREFIX . "room r ON r.room_id = tph.tph_room_type_id
        WHERE tph.tph_hotel_id IN ($hotelIdsIn) 
          AND tph.tph_room_type_id IN ($roomIdsIn)
    ";
    if ($result = mysqli_query($connection, $q2)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = $row['property_id'] . '|' . $row['room_id'];
            $hotelRoomDetails[$key] = $row;
        }
    }
}

/* Decode quotation description & itinerary robustly */
$qd = decode_json_any($quotation_description);
$quotationDescription = $qd ?: [];
$quotationIninenary   = $quotationDescription['itinerary'] ?? [];

// Sometimes itinerary itself is a JSON string
if (is_string($quotationIninenary)) {
    $tmp = decode_json_any($quotationIninenary);
    if ($tmp) $quotationIninenary = $tmp;
}
$itineraries = is_array($quotationIninenary) ? $quotationIninenary : [];

/* Inclusions / exclusions */
$inclusions = array_filter(array_map('trim', explode(',', $quotationDescription['inclusions'] ?? '')));
$exclusions = array_filter(array_map('trim', explode(',', $quotationDescription['exclusions'] ?? '')));

/* Computed fields */
$dayCount          = 1;
$totalAdultPerson  = isset($number_of_adults) ? (int)$number_of_adults : 1;
$totalChildPerson  = isset($number_of_childrens) ? (int)$number_of_childrens : 0;
$numberOfDays      = isset($number_of_days) ? (int)$number_of_days : 4;

$quoteId        = '#' . getInitials(SITE_NAME) . '/' . base64_encode($quotation_ref_id) . '/' . $quotationId;
$pdfFileName    = strtolower(str_replace(' ', '_', trim(($enquiry_first_name ?? '') . ' ' . ($enquiry_last_name ?? '') . ' ' . ($tour_package_title ?? ''))) . '_itinerary_' . time() . '.pdf');

/* Pricing pre-calculation */
$days                  = $numberOfDays + 1;
$persons               = max(1, $totalAdultPerson);
$base_price_per_person = (float)($quotation_price ?? 0);
$mrp_per_person        = (float)($quotation_mrp   ?? 0);

$total_base_price = $base_price_per_person * $persons;
$total_mrp_price  = $mrp_per_person * $persons;

$gst_percent     = 5;
$gst_amount      = $total_base_price * ($gst_percent / 100);
$mrp_gst_total   = $total_mrp_price + ($total_mrp_price * $gst_percent / 100);

$final_price          = $total_base_price + $gst_amount;
$single_person_total  = $base_price_per_person + ($base_price_per_person * $gst_percent / 100);

$total_discount   = $mrp_gst_total - $final_price;
$discount_percent = ($mrp_gst_total > 0) ? round(($total_discount / $mrp_gst_total) * 100) : 0;

$gallery_image_fallback = $gallery_image ?? ($tour_package_cover ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo safe(($tour_package_title ?? 'Itinerary') . ' | ' . SITE_NAME); ?></title>

    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Pacifico&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />

    <style>
    /* Do NOT override Remix icons – it hid icons before */
    body {
        font-family: 'Inter', sans-serif;
    }

    .itinerary-day:not(:last-child) {
        border-bottom: 1px solid #e5e7eb;
    }

    .pdf-page {
        page-break-before: always;
        break-before: page;
    }

    .avoid-break,
    .itinerary-day,
    .itinerary-day * {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    </style>

    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#E76317',
                    secondary: '#FB923C'
                },
                borderRadius: {
                    'none': '0px',
                    'sm': '4px',
                    DEFAULT: '8px',
                    'md': '12px',
                    'lg': '16px',
                    'xl': '20px',
                    '2xl': '24px',
                    '3xl': '32px',
                    'full': '9999px',
                    'button': '8px'
                }
            }
        }
    }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body class="bg-gray-50">
    <div id="pdf-content" class="max-w-[760px] mx-auto bg-white shadow-sm rounded-lg overflow-hidden">

        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="<?php echo safe((defined('WEBSITE_LOGO') ? WEBSITE_LOGO : SITE_LOGO)); ?>"
                        style="height: 50px;" alt="<?php echo safe(SITE_NAME); ?>" />
                </div>
                <div class="flex items-center gap-6 text-gray-600 text-sm">
                    <a href="mailto:<?php echo safe(SITE_EMAIL); ?>" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-mail-line"></i><span><?php echo safe(SITE_EMAIL); ?></span>
                    </a>
                    <a href="tel:<?php echo safe(SITE_MOBILE); ?>" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-phone-line"></i><span><?php echo safe(SITE_MOBILE); ?></span>
                    </a>
                    <a href="<?php echo safe(SITE_DOMAIN); ?>" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-global-line"></i><span><?php echo safe(SITE_DOMAIN); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Hero -->
        <div class="relative h-[400px] overflow-hidden">
            <?php if ($gallery_image_fallback): ?>
            <img src="<?php echo safe($gallery_image_fallback); ?>" alt="<?php echo safe($tour_package_title ?? ''); ?>"
                class="w-full h-full object-cover" />
            <?php else: ?>
            <div class="w-full h-full bg-gray-200"></div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-4xl font-bold mb-3 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                                <?php echo safe($tour_package_title ?? 'Tour'); ?>
                            </h1>
                            <div class="flex items-center gap-6 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">
                                <div class="flex items-center gap-2">
                                    <i class="ri-map-pin-line"></i>
                                    <span><?php echo safe($tour_package_city ?? ''); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line"></i>
                                    <span>
                                        <?php
                                        $td = $travel_date ?? '';
                                        echo ($td ? date('d F, Y', strtotime($td)) : '');
                                        ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ri-time-line"></i>
                                    <span><?php echo safe($days); ?> Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-3xl font-bold whitespace-nowrap">₹
                                <?php echo safe(number_format(($quotation_price ?? 0), 2)); ?> /-</div>
                            <div class="text-sm text-white/75 whitespace-nowrap mb-2">per person</div>
                            <div class="text-sm text-white/75 whitespace-nowrap">Ref. id: <?php echo safe($quoteId); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Tour Overview</h2>
            <p class="mb-8"><?php echo safe($tour_package_overview ?? ''); ?></p>
        </div>

        <!-- Inc/Exc -->
        <div class="p-6 border-b border-gray-200 pdf-page">
            <div class="bg-blue-50 p-4 rounded-lg mb-4 mt-8">
                <h3 class="font-medium mb-2">What's Included:</h3>
                <ul class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($inclusions as $inclusion): if (!$inclusion) continue; ?>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-check-line"></i>
                        </div>
                        <span><?php echo safe(ucfirst($inclusion)); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="bg-red-50 p-4 rounded-lg mb-4 mt-8">
                <h3 class="font-medium mb-2">Not Included in Package:</h3>
                <ul class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($exclusions as $exclusion): if (!$exclusion) continue; ?>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-close-line"></i>
                        </div>
                        <span><?php echo safe(ucfirst($exclusion)); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Itinerary -->
        <section class="pdf-page">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Detailed Itinerary</h2>
                <?php
                echo '<div class="space-y-6">';
                if (!$itineraries || !is_array($itineraries)) {
                    echo '<div class="p-4 bg-amber-50 text-amber-800 rounded">No itinerary details available.</div>';
                } else {
                    $index = 0; // 0-based day index to align with hotelMapByNight
                    foreach ($itineraries as $rawDay) {
                        $norm = normalize_day($rawDay);
                        $title    = $norm['title'] ?: ('Day ' . $dayCount);
                        $overview = $norm['overview'];

                        echo '<div class="itinerary-day pb-6 avoid-break">';
                        echo '  <div class="flex items-center mb-3">';
                        echo '      <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center mr-3">';
                        echo '          <span class="font-medium">' . safe($dayCount) . '</span>';
                        echo '      </div>';
                        echo '      <h3 class="text-lg font-medium">' . safe($title) . '</h3>';
                        echo '  </div>';
                        echo '  <div class="pl-14">';

                        if ($overview !== '') {
                            // Render as HTML if looks like HTML; else safe text
                            $looksHtml = (strpos($overview, '<') !== false && strpos($overview, '>') !== false);
                            echo '<p class="mb-3">';
                            echo $looksHtml ? $overview : safeText($overview);
                            echo '</p>';
                        } else {
                            echo '<p class="mb-3 text-gray-500">Details will be finalized for this day.</p>';
                        }

                        // Accommodation for this night
                        if (isset($hotelMapByNight[$index])) {
                            $hm = $hotelMapByNight[$index];
                            $k  = ($hm['hotel_id'] ?? 0) . '|' . ($hm['room_id'] ?? 0);
                            if (isset($hotelRoomDetails[$k])) {
                                $h = $hotelRoomDetails[$k];
                                $thumb = $h['property_thumbnail'] ?: $h['room_feature_image'];
                                echo '<div class="mt-8 avoid-break">';
                                echo '  <h3 class="text-lg font-semibold mb-4">Accommodation Details</h3>';
                                echo '  <div class="space-y-4">';
                                echo '      <div class="bg-gray-50 p-4 rounded-lg">';
                                echo '          <div class="flex items-start">';
                                if ($thumb) {
                                    echo '              <img src="' . safe($thumb) . '" alt="' . safe($h['property_title']) . '" class="w-32 h-24 object-cover rounded mr-4"/>';
                                } else {
                                    echo '              <div class="w-32 h-24 bg-gray-200 rounded mr-4"></div>';
                                }
                                echo '              <div class="flex-1">';
                                echo '                  <h4 class="font-medium">' . safe($h['property_title']) . '</h4>';
                                echo '                  <div class="flex items-center text-sm text-gray-600 mt-1">';
                                echo '                      <div class="flex items-center">';
                                echo                           renderStarIcons($h['property_type']);
                                echo '                      </div>';
                                echo '                  </div>';
                                echo '                  <div class="text-sm text-gray-600 mt-1">';
                                echo '                      <div class="flex items-center gap-4 flex-wrap">';
                                echo '                          <span><i class="ri-wifi-line mr-1"></i> Free WiFi</span>';
                                echo '                          <span><i class="ri-restaurant-line mr-1"></i> Restaurant</span>';
                                echo '                          <span><i class="ri-parking-line mr-1"></i> Parking</span>';
                                echo '                      </div>';
                                echo '                  </div>';
                                echo '              </div>';
                                echo '          </div>';
                                echo '      </div>';
                                echo '  </div>';
                                echo '</div>';
                            }
                        }

                        echo '  </div>'; // .pl-14
                        echo '</div>';   // .itinerary-day
                        $dayCount++;
                        $index++;
                    }
                }
                echo '</div>';
                ?>
            </div>
        </section>

        <!-- Pricing -->
        <section class="pdf-page">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Tour Pricing</h2>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-5">
                    <div class="bg-primary/10 p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800">
                                <?php echo safe(($tour_package_title ?? 'Package') . ' - Quotation'); ?>
                            </h3>
                            <div class="px-3 py-1 bg-primary text-white rounded-full text-sm font-medium">Best Value
                            </div>
                        </div>
                        <p class="text-gray-600 mt-1">
                            <?php echo ($persons == 1 ? '1 Adult' : safe($persons) . ' Adults'); ?> |
                            4-Star Accommodation |
                            <?php echo safe($days); ?> Days, <?php echo safe($numberOfDays); ?> Nights
                        </p>
                    </div>

                    <?php $isDiscount = $mrp_gst_total > $final_price; ?>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="space-y-1">
                                <div class="text-3xl font-bold text-gray-900">
                                    ₹<?php echo number_format($final_price, 2); ?>
                                </div>
                                <?php if ($isDiscount): ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg text-gray-500 line-through">
                                        ₹<?php echo number_format($mrp_gst_total, 2); ?>
                                    </span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-sm font-medium">
                                        Save <?php echo safe($discount_percent); ?>%
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="text-right">
                                <div class="text-sm text-gray-500">Per Package</div>
                                <div class="text-sm text-gray-500">
                                    (₹<?php echo number_format($single_person_total, 2); ?> per person)
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 border-t border-b border-gray-200 py-4 mb-6">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-hotel-line text-primary"></i>
                                    <span>Base Price
                                        (<?php echo ($persons == 1 ? '1 Adult' : safe($persons) . ' Adults'); ?>)</span>
                                </div>
                                <span class="font-medium">₹<?php echo number_format($total_base_price, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-percent-line text-primary"></i>
                                    <span>GST (<?php echo safe($gst_percent); ?>%)</span>
                                </div>
                                <span class="font-medium">+ ₹<?php echo number_format($gst_amount, 2); ?></span>
                            </div>

                            <?php if ($isDiscount): ?>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-gift-line text-green-600"></i>
                                    <span>Special Discount</span>
                                </div>
                                <span class="font-medium text-green-600">-
                                    ₹<?php echo number_format($total_discount, 2); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="w-full">
                            <a href="#"
                                class="block w-full bg-primary hover:bg-primary/90 text-white py-4 px-6 rounded-button font-medium text-lg text-center">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="ri-calendar-line mt-0.5"></i>
                        <span>Prices valid until <?php echo date('F j, Y', strtotime('+7 days')); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Powered By -->
        <div class="p-4 bg-gray-100 text-center text-sm text-gray-600">
            <a href="https://aleya.in/?utm_source=itinerary">Powered by <span class="font-medium">Aleya</span> - A Smart
                Travel ERP Software</a>
        </div>
    </div>

    <!-- Auto-generate PDF -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const element = document.getElementById("pdf-content");
        if (!element) return;

        const opt = {
            filename: '<?php echo safe($pdfFileName); ?>',
            margin: [0.5, 0.4, 0.5, 0.4],
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                scrollY: 0,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['css', 'legacy'],
                before: '.pdf-page',
                avoid: ['.avoid-break']
            }
        };

        // Render from a clone to avoid layout shift
        const cloned = element.cloneNode(true);
        cloned.style.background = "white";

        const wrapper = document.createElement("div");
        wrapper.style.width = "100%";
        wrapper.style.maxWidth = "1000px";
        wrapper.style.margin = "0 auto";
        wrapper.style.padding = "0";
        wrapper.appendChild(cloned);
        document.body.appendChild(wrapper);

        html2pdf().set(opt).from(cloned).save().then(() => {
            document.body.removeChild(wrapper);
        });
    });
    </script>
</body>

</html>