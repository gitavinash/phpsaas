<?php
/**
 * V3 Luxury Itinerary Proposal Template (Aleya Design Clone)
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/aleya.php
 * Reference: app/aleya/visitor/default/desktop/tour-package.php
 * 
 * Features:
 * - 1:1 match with Aleya luxury tour package landing page
 * - Dynamic data binding with DB fallback
 * - Full image and day-wise photo support
 * - Overnight stay property cards with room type, address, thumbnail & details link
 * - Inclusions & Exclusions side-by-side
 * - Hotel details & pricing cost tiers table
 * - Important notes & cancellation policy
 * - Complete Company Contact Information section
 * - Aleya Credit Note
 * - Continuous smooth web view + Multi-page A4 PDF download support
 */

if (!function_exists('fixImageUrl')) {
    function fixImageUrl($url)
    {
        if (empty($url)) return '';
        $url = trim($url);
        if (preg_match('/^https?:\/\//i', $url)) return $url;

        // Resolve active base domain dynamically
        $baseUrl = '';
        if (defined('SITE_URL') && !empty(SITE_URL)) {
            $baseUrl = rtrim(SITE_URL, '/');
        } elseif (defined('SITE_DOMAIN') && !empty(SITE_DOMAIN)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $baseUrl = $protocol . rtrim(SITE_DOMAIN, '/');
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
        }

        if (!empty($baseUrl)) {
            return $baseUrl . '/' . ltrim($url, '/');
        }

        return '/' . ltrim($url, '/');
    }
}

// Dynamic Site & Company Information (No Dummy Data)
$siteName    = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Travel Partner');
$sitePhone   = defined('SITE_MOBILE') ? SITE_MOBILE : (defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? ''));
$siteEmail   = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress = defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? '');
$siteUrl     = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteLogo    = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));

// Package Variables
$pkgTitle       = $tour_package_title ?? ($itinerary_title ?? ($title ?? 'Tour Package Itinerary'));
$pkgCity        = $tour_package_city ?? ($city ?? '');
$pkgState       = $tour_package_state ?? ($state ?? '');
$pkgCountry     = $tour_package_country ?? ($country ?? '');
$pkgDuration    = $tour_package_duration ?? ($numberOfDays ?? ($duration ?? 0));
$pkgTheme       = $tour_package_theme ?? ($travelStyle ?? 'Standard');
$pkgHotel       = $tour_package_hotel ?? ($hotel ?? '');
$pkgPriceAdult  = $tour_package_price_selling_adult ?? ($selling_price ?? ($price ?? 0));
$pkgOutVehicle  = $tour_package_outstation_vehicle ?? ($outstationVehicle ?? '');
$pkgLocVehicle  = $tour_package_localsightseen_vehicle ?? ($localSightseeingVehicle ?? '');
$pkgOverview    = $tour_package_overview ?? ($itinerary_overview ?? ($overview ?? ''));
$pkgInclusions  = $tour_package_inclusions ?? ($inclusions ?? '');
$pkgExclusions  = $tour_package_exclusions ?? ($exclusions ?? '');
$pkgDays        = $itineraries ?? ($itinerary ?? ($days ?? []));
$quoteRef       = $quoteId ?? ($quotationId ?? ($tour_package_id ?? rand(1000, 9999)));

// Decode itinerary array if passed as JSON string
if (is_string($pkgDays)) {
    $pkgDays = json_decode($pkgDays, true) ?: [];
}

// Hero Featured Cover Image
$heroFeatureImage = fixImageUrl($tour_package_image ?? ($gallery_image ?? ($featureImage ?? '')));

// Image separation into unassigned gallery and day-wise gallery
$unassignedGalleryImages = [];
$dayWiseImages = [];

$registerImage = function ($rawUrl, $imgTitle, $dayNum) use (&$unassignedGalleryImages, &$dayWiseImages) {
    $fixedUrl = fixImageUrl($rawUrl);
    if (empty($fixedUrl)) return;
    $imgData = [
        'url' => $fixedUrl,
        'title' => !empty($imgTitle) ? $imgTitle : 'Tour Photo'
    ];
    $dayInt = (int)$dayNum;
    if ($dayInt > 0) {
        if (!isset($dayWiseImages[$dayInt])) {
            $dayWiseImages[$dayInt] = [];
        }
        $exists = false;
        foreach ($dayWiseImages[$dayInt] as $item) {
            if ($item['url'] === $fixedUrl) { $exists = true; break; }
        }
        if (!$exists) $dayWiseImages[$dayInt][] = $imgData;
    } else {
        $exists = false;
        foreach ($unassignedGalleryImages as $item) {
            if ($item['url'] === $fixedUrl) { $exists = true; break; }
        }
        if (!$exists) $unassignedGalleryImages[] = $imgData;
    }
};

// 1. Process $gallery_image_list if present
if (!empty($gallery_image_list)) {
    $listData = is_string($gallery_image_list) ? json_decode($gallery_image_list, true) : $gallery_image_list;
    if (is_array($listData)) {
        foreach ($listData as $gItem) {
            $rawUrl = is_array($gItem) ? ($gItem['path'] ?? $gItem['url'] ?? $gItem['image'] ?? '') : $gItem;
            $imgTitle = is_array($gItem) ? ($gItem['name'] ?? $gItem['title'] ?? $gItem['caption'] ?? '') : '';
            $dayVal = is_array($gItem) ? ($gItem['day'] ?? 0) : 0;
            $registerImage($rawUrl, $imgTitle, $dayVal);
        }
    }
}

// 2. Process day item feature images
if (!empty($pkgDays) && is_array($pkgDays)) {
    $dIdx = 1;
    foreach ($pkgDays as $dItem) {
        if (is_array($dItem)) {
            $dImg = $dItem['feature_image'] ?? ($dItem['image'] ?? ($dItem['img'] ?? ''));
            if (!empty($dImg)) {
                $registerImage($dImg, $dItem['day'] ?? "Day $dIdx", $dIdx);
            }
        }
        $dIdx++;
    }
}

// 3. Query DB for gallery images if connected
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
    $galDbQuery = "SELECT gallery_image, gallery_thumbnail, gallery_title, gallery_image_list FROM " . TABLE_PREFIX . "gallery WHERE gallery_category = '" . (int)$tour_package_id . "' AND gallery_site_id = " . SITE_ID . " AND gallery_type = 'tour_package'";
    $galResult = @mysqli_query($connection, $galDbQuery);
    if ($galResult && mysqli_num_rows($galResult) > 0) {
        while ($galRow = mysqli_fetch_assoc($galResult)) {
            if (!empty($galRow['gallery_image_list'])) {
                $subList = json_decode($galRow['gallery_image_list'], true);
                if (is_array($subList)) {
                    foreach ($subList as $sItem) {
                        $sRawUrl = is_array($sItem) ? ($sItem['path'] ?? $sItem['url'] ?? $sItem['image'] ?? '') : $sItem;
                        $sTitle = is_array($sItem) ? ($sItem['name'] ?? $sItem['title'] ?? '') : '';
                        $sDay = is_array($sItem) ? ($sItem['day'] ?? 0) : 0;
                        $registerImage($sRawUrl, $sTitle, $sDay);
                    }
                }
            } elseif (!empty($galRow['gallery_image'])) {
                $registerImage($galRow['gallery_image'], $galRow['gallery_title'] ?? '', 0);
            }
        }
    }
}

// Fallback to hero cover image if no gallery exists
if (empty($unassignedGalleryImages) && empty($dayWiseImages) && !empty($heroFeatureImage)) {
    $registerImage($heroFeatureImage, $pkgTitle, 0);
}

// Consolidate all photos for lightbox slider
$allSliderPhotos = [];
foreach ($unassignedGalleryImages as $uImg) {
    $allSliderPhotos[] = $uImg;
}
foreach ($dayWiseImages as $dNum => $dPhotos) {
    foreach ($dPhotos as $dImg) {
        $isDup = false;
        foreach ($allSliderPhotos as $existing) {
            if ($existing['url'] === $dImg['url']) { $isDup = true; break; }
        }
        if (!$isDup) $allSliderPhotos[] = $dImg;
    }
}

// Fetch price tiers from DB if available
$dbPackagePrices = [];
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
    $priceDbQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_parent_id = '" . (int)$tour_package_id . "' AND tour_package_price_site_id = " . SITE_ID . " ORDER BY tour_package_price_selling_adult ASC";
    $priceResult = @mysqli_query($connection, $priceDbQuery);
    if ($priceResult && mysqli_num_rows($priceResult) > 0) {
        while ($priceRow = mysqli_fetch_assoc($priceResult)) {
            $dbPackagePrices[] = $priceRow;
        }
    }
}

// Day-wise hotels from DB or passed array
if (!isset($dayWiseHotels) || !is_array($dayWiseHotels)) {
    $dayWiseHotels = [];
}
if (empty($dayWiseHotels) && isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
    $hotelDbQuery = "
        SELECT 
            tph.tph_night,
            tph.tph_room_type,
            p.property_id,
            p.property_title,
            p.property_thumbnail,
            p.property_gallery,
            p.property_address,
            p.property_city,
            p.property_state,
            p.property_country,
            p.property_type,
            p.property_description,
            l.link_url
        FROM " . TABLE_PREFIX . "tour_package_hotel tph
        INNER JOIN " . TABLE_PREFIX . "property p ON p.property_id = tph.tph_hotel_id
        LEFT JOIN " . TABLE_PREFIX . "link l ON (l.link_relation_id = p.property_id AND l.link_type = 'property')
        WHERE tph.tph_package_id = '" . (int)$tour_package_id . "' AND tph.tph_site_id = " . SITE_ID;
    $hotelResult = @mysqli_query($connection, $hotelDbQuery);
    if ($hotelResult && mysqli_num_rows($hotelResult) > 0) {
        while ($hRow = mysqli_fetch_assoc($hotelResult)) {
            $nightNum = (int)$hRow['tph_night'];
            if ($nightNum > 0) {
                $dayWiseHotels[$nightNum] = $hRow;
            }
        }
    }
}

// Calculate Days & Nights
$totalDaysCount = !empty($pkgDays) ? count($pkgDays) : 1;
$totalNightsCount = max(1, $totalDaysCount - 1);
if (!empty($pkgDuration)) {
    $totalNightsCount = (int)$pkgDuration;
    $totalDaysCount = $totalNightsCount + 1;
}

// Location string
$locations = array_filter([$pkgCity, $pkgState, $pkgCountry]);
$locationText = !empty($locations) ? implode(', ', $locations) : 'Incredible Destinations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($siteName . ' - ' . $pkgTitle); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

    <!-- jsPDF & html2canvas for Client-side PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --font-heading: 'Playfair Display', Georgia, serif;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;

            --color-primary: #0f172a;
            --color-primary-light: #1e293b;
            --color-accent: #c59b27;
            --color-accent-light: #fef8ec;
            --color-accent-hover: #b0871d;
            --color-accent-glow: rgba(197, 155, 39, 0.25);

            --color-bg-main: #f8fafc;
            --color-bg-card: #ffffff;
            --color-text-main: #1e293b;
            --color-text-muted: #64748b;
            --color-border: #e2e8f0;

            --color-success: #10b981;
            --color-danger: #ef4444;

            --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --shadow-lg: 0 20px 35px -10px rgba(15, 23, 42, 0.1);
            --shadow-gold: 0 10px 25px -5px rgba(197, 155, 39, 0.3);

            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-full: 9999px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--color-bg-main);
            color: var(--color-text-main);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 90px;
        }

        @media (min-width: 992px) {
            body { padding-bottom: 40px; }
        }

        /* --- Floating PDF Download Bar --- */
        .v3-pdf-actions-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            padding: 8px 14px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--color-border);
        }

        .btn-pdf-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-pdf-download {
            background: var(--color-accent);
            color: #ffffff;
            box-shadow: var(--shadow-gold);
        }

        .btn-pdf-download:hover {
            background: var(--color-accent-hover);
            transform: translateY(-1px);
        }

        .btn-pdf-print {
            background: #ffffff;
            color: var(--color-text-main);
            border: 1px solid var(--color-border);
        }

        .btn-pdf-print:hover {
            background: #f1f5f9;
        }

        /* --- Continuous Web Layout Wrapper --- */
        .luxury-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px 16px;
        }

        @media (min-width: 768px) {
            .luxury-wrapper {
                padding: 36px 24px;
            }
        }

        /* --- Header Bar --- */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 28px;
            background: var(--color-bg-card);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            margin-bottom: 28px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo-img {
            max-height: 50px;
            width: auto;
            object-fit: contain;
        }

        .brand-badge {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--color-primary), #334155);
            color: var(--color-accent);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: var(--shadow-sm);
        }

        .company-name {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: -0.5px;
        }

        .btn-action-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: var(--radius-full);
            border: 1px solid var(--color-border);
            background: var(--color-bg-card);
            color: var(--color-text-main);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-action-outline:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
            background: var(--color-accent-light);
            transform: translateY(-1px);
        }

        /* --- Hero Card --- */
        .hero-card {
            min-height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            border-radius: var(--radius-lg);
            padding: 32px 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background-size: cover;
            background-position: center;
            background-color: #0f172a;
        }

        @media (min-width: 768px) {
            .hero-card {
                min-height: 480px;
                padding: 44px;
            }
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(197, 155, 39, 0.5);
            color: #fbbf24;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            width: fit-content;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: 2rem;
            line-height: 1.25;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        @media (min-width: 768px) {
            .hero-title { font-size: 2.75rem; }
        }

        .hero-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.25);
        }

        .meta-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            padding: 12px 18px;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .meta-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(197, 155, 39, 0.3);
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .meta-text label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #cbd5e1;
        }

        .meta-text span {
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
        }

        /* --- Luxury Cards --- */
        .luxury-card {
            background: var(--color-bg-card);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--color-border);
        }

        @media (min-width: 768px) {
            .luxury-card { padding: 36px 32px; }
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--color-bg-main);
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: var(--color-accent);
            border-radius: var(--radius-full);
        }

        .section-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--color-accent-light);
            color: var(--color-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        @media (min-width: 768px) {
            .section-title { font-size: 1.75rem; }
        }

        /* --- Overview Content --- */
        .overview-content {
            font-size: 1.02rem;
            line-height: 1.8;
            color: var(--color-text-main);
        }

        /* --- Photo Gallery --- */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .gallery-item {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            aspect-ratio: 4 / 3;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            cursor: pointer;
            background: var(--color-primary-light);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-caption { opacity: 1; }

        .gallery-more-wrapper {
            text-align: center;
            margin-top: 24px;
        }

        .btn-gallery-more {
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 600;
            border-color: var(--color-accent);
            color: var(--color-accent);
        }

        .btn-gallery-more:hover {
            background: var(--color-accent);
            color: #ffffff;
        }

        /* --- Timeline Itinerary --- */
        .itinerary-timeline {
            position: relative;
            padding-left: 20px;
        }

        @media (min-width: 768px) {
            .itinerary-timeline { padding-left: 36px; }
        }

        .itinerary-timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 24px;
            bottom: 24px;
            width: 2px;
            background: linear-gradient(to bottom, var(--color-accent) 0%, #cbd5e1 100%);
        }

        @media (min-width: 768px) {
            .itinerary-timeline::before { left: 15px; }
        }

        .timeline-item {
            position: relative;
            margin-bottom: 28px;
        }

        .timeline-item:last-child { margin-bottom: 0; }

        .timeline-marker {
            position: absolute;
            left: -20px;
            top: 24px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--color-accent);
            border: 3px solid var(--color-bg-card);
            box-shadow: 0 0 0 2px var(--color-accent);
            z-index: 2;
        }

        @media (min-width: 768px) {
            .timeline-marker {
                left: -36px;
                width: 20px;
                height: 20px;
                top: 24px;
            }
        }

        .day-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .day-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-md);
        }

        .day-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            cursor: pointer;
            background: var(--color-bg-card);
            transition: var(--transition);
            user-select: none;
            gap: 16px;
        }

        .day-card.open .day-card-header {
            background: var(--color-bg-main);
            border-bottom: 1px solid var(--color-border);
        }

        .day-badge {
            background: var(--color-accent);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .day-title {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-primary);
            flex: 1;
            line-height: 1.4;
        }

        .toggle-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
            font-size: 16px;
        }

        .day-card.open .toggle-icon {
            transform: rotate(180deg);
            background: var(--color-accent-light);
            color: var(--color-accent);
        }

        .day-card-body {
            padding: 24px;
            color: var(--color-text-main);
            line-height: 1.8;
            font-size: 0.98rem;
        }

        /* --- Day-Wise Photo Gallery Inside Day Card --- */
        .day-gallery-container {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed var(--color-border);
        }

        .day-gallery-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .day-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }

        .day-gallery-item {
            position: relative;
            border-radius: var(--radius-sm);
            overflow: hidden;
            aspect-ratio: 4 / 3;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            cursor: pointer;
            background: #e2e8f0;
        }

        .day-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .day-gallery-item:hover img { transform: scale(1.08); }

        /* --- Hotel Stay Card Inside Day --- */
        .day-hotel-card {
            margin-top: 20px;
            background: #ffffff;
            border: 1px solid #bbf7d0;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .day-hotel-card.assigned-hotel {
            border-color: #86efac;
            background: linear-gradient(to right, #f0fdf4 0%, #ffffff 100%);
        }

        .hotel-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            background: #dcfce7;
            border-bottom: 1px solid #bbf7d0;
        }

        .hotel-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hotel-type-pill {
            font-size: 0.75rem;
            font-weight: 700;
            background: #16a34a;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: var(--radius-full);
        }

        .hotel-card-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 16px 18px;
        }

        @media (min-width: 576px) {
            .hotel-card-body {
                flex-direction: row;
                align-items: center;
            }
        }

        .hotel-thumb-box {
            width: 100%;
            height: 120px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--color-border);
            background: #e2e8f0;
            cursor: pointer;
        }

        @media (min-width: 576px) {
            .hotel-thumb-box {
                width: 140px;
                height: 95px;
            }
        }

        .hotel-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .hotel-thumb-box:hover img { transform: scale(1.06); }

        .hotel-details-box { flex: 1; }

        .hotel-name {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
            color: #14532d;
            margin-bottom: 4px;
        }

        .hotel-location {
            font-size: 0.85rem;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }

        .hotel-room-type {
            font-size: 0.85rem;
            color: #15803d;
            margin-bottom: 10px;
        }

        .btn-hotel-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #16a34a;
            text-decoration: none;
            border: 1px solid #86efac;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            background: #ffffff;
            transition: var(--transition);
        }

        .btn-hotel-link:hover {
            background: #16a34a;
            color: #ffffff;
        }

        /* --- Farewell Card --- */
        .farewell-card {
            margin-top: 20px;
            background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
            border: 1px solid #f5d0fe;
            border-radius: var(--radius-md);
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: var(--shadow-sm);
        }

        @media (min-width: 576px) {
            .farewell-card {
                flex-direction: row;
                align-items: center;
                gap: 20px;
            }
        }

        .farewell-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-md);
            background: #f0abfc;
            color: #86198f;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 24px;
        }

        .farewell-title {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
            color: #701a75;
            margin-bottom: 4px;
        }

        .farewell-text {
            font-size: 0.9rem;
            color: #86198f;
            line-height: 1.6;
        }

        /* --- Inclusions & Exclusions --- */
        .inc-exc-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .inc-exc-grid { grid-template-columns: 1fr 1fr; }
        }

        .inc-card, .exc-card {
            border-radius: var(--radius-md);
            padding: 24px;
            border: 1px solid var(--color-border);
        }

        .inc-card {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .exc-card {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .inc-exc-header {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .inc-card .inc-exc-header { color: #16a34a; }
        .exc-card .inc-exc-header { color: #dc2626; }

        .inc-exc-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .inc-exc-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--color-text-main);
        }

        .inc-card .inc-exc-list li i {
            color: #16a34a;
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .exc-card .inc-exc-list li i {
            color: #dc2626;
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* --- Pricing & Cost Table --- */
        .pricing-table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
        }

        .luxury-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .luxury-table th {
            background: #f8fafc;
            color: var(--color-primary);
            font-weight: 700;
            padding: 16px 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--color-border);
        }

        .luxury-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--color-border);
            font-size: 0.95rem;
        }

        .luxury-table tr:last-child td { border-bottom: none; }

        .price-highlight {
            font-weight: 800;
            color: var(--color-accent);
            font-size: 1.1rem;
        }

        /* --- Policy Cards --- */
        .policy-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .policy-grid { grid-template-columns: 1fr 1fr; }
        }

        .policy-card {
            background: #f8fafc;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 22px;
        }

        .policy-card h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 12px;
        }

        .policy-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--color-text-muted);
        }

        .policy-list li {
            position: relative;
            padding-left: 16px;
        }

        .policy-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--color-accent);
            font-weight: bold;
        }

        /* --- Company Contact Information Section --- */
        .company-contact-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: var(--radius-lg);
            padding: 34px 28px;
            color: #ffffff;
            margin-bottom: 32px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .contact-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: rgba(197, 155, 39, 0.2);
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .contact-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .contact-item {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .contact-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(197, 155, 39, 0.25);
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .contact-item-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            display: block;
            margin-bottom: 2px;
        }

        .contact-item-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            word-break: break-word;
        }

        .contact-item-value a {
            color: #fbbf24;
            text-decoration: none;
        }

        .contact-item-value a:hover { text-decoration: underline; }

        /* --- Footer & Aleya Credit --- */
        .luxury-footer {
            text-align: center;
            padding: 28px 0;
            border-top: 1px solid var(--color-border);
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }

        .credit-strap {
            margin-top: 10px;
            font-size: 0.82rem;
            color: var(--color-text-muted);
        }

        .credit-strap a {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 700;
        }

        .credit-strap a:hover { text-decoration: underline; }

        /* Lightbox Modal */
        .lightbox-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lightbox-modal.active { display: flex; }

        .lightbox-slide-wrapper {
            max-width: 90vw;
            max-height: 85vh;
            text-align: center;
        }

        .lightbox-content {
            max-width: 100%;
            max-height: 80vh;
            border-radius: var(--radius-sm);
            object-fit: contain;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .lightbox-caption-bar {
            color: #ffffff;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .lightbox-close, .lightbox-nav {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .lightbox-close { top: 24px; right: 24px; }
        .lightbox-prev { left: 24px; top: 50%; transform: translateY(-50%); }
        .lightbox-next { right: 24px; top: 50%; transform: translateY(-50%); }
        .lightbox-close:hover, .lightbox-nav:hover { background: rgba(255, 255, 255, 0.4); }

        /* --- PDF & Print Optimizations (A4 Pagination) --- */
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 12mm 10mm;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 11pt !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .v3-pdf-actions-bar,
            .no-print,
            .lightbox-modal {
                display: none !important;
            }

            .luxury-wrapper {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .luxury-card,
            .hero-card,
            .company-contact-card,
            .price-table-card,
            .inc-card,
            .exc-card,
            .property-card {
                box-shadow: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 20px !important;
                border: 1px solid #e2e8f0 !important;
            }

            .hero-card {
                min-height: 260px !important;
                background-color: #0f172a !important;
                color: #ffffff !important;
            }

            .timeline-item,
            .day-card {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating PDF Export Action Bar -->
    <?php if (empty($hideActions) && empty($isPreview)): ?>
    <div class="v3-pdf-actions-bar">
        <button type="button" class="btn-pdf-action btn-pdf-download" id="btnDownloadAleyaPDF">
            <i class="ri-file-download-line"></i> Download Itinerary PDF
        </button>
        <button type="button" class="btn-pdf-action btn-pdf-print" onclick="window.print()">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>
    <?php endif; ?>

    <!-- Main Itinerary Document Container -->
    <div class="luxury-wrapper" id="itineraryDocument">

        <!-- Top Header & Brand Bar -->
        <header class="site-header">
            <a href="<?php echo htmlspecialchars(!empty($siteUrl) ? $siteUrl : '#'); ?>" class="brand-logo">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="brand-logo-img" />
                <?php else: ?>
                    <div class="brand-badge"><?php echo htmlspecialchars(substr($siteName, 0, 1)); ?></div>
                    <div class="company-name"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </a>
            <div>
                <span class="btn-action-outline">
                    <i class="ri-bookmark-3-line"></i> Ref #<?php echo htmlspecialchars((string)$quoteRef); ?>
                </span>
            </div>
        </header>

        <!-- Premium Hero Cover Card (Vibrant Feature Image Background) -->
        <section class="hero-card" <?php if (!empty($heroFeatureImage)): ?>style="background: linear-gradient(to top, rgba(15, 23, 42, 0.92) 0%, rgba(15, 23, 42, 0.45) 50%, rgba(15, 23, 42, 0.15) 100%), url('<?php echo htmlspecialchars($heroFeatureImage); ?>') center/cover no-repeat;"<?php endif; ?>>
            <div class="hero-tag">
                <i class="ri-vip-crown-fill"></i> Exclusive Tour Package
            </div>

            <h1 class="hero-title"><?php echo htmlspecialchars($pkgTitle); ?></h1>

            <div class="hero-meta-grid">
                <div class="meta-pill">
                    <div class="meta-icon"><i class="ri-time-line"></i></div>
                    <div class="meta-text">
                        <label>Duration</label>
                        <span><?php echo $totalNightsCount; ?> Nights / <?php echo $totalDaysCount; ?> Days</span>
                    </div>
                </div>

                <div class="meta-pill">
                    <div class="meta-icon"><i class="ri-map-pin-line"></i></div>
                    <div class="meta-text">
                        <label>Destination</label>
                        <span><?php echo htmlspecialchars($locationText); ?></span>
                    </div>
                </div>

                <?php if (!empty($pkgPriceAdult)): ?>
                    <div class="meta-pill">
                        <div class="meta-icon"><i class="ri-money-rupee-circle-line"></i></div>
                        <div class="meta-text">
                            <label>Starting Price</label>
                            <span>₹ <?php echo number_format((float)$pkgPriceAdult); ?> / person</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tour Overview -->
        <?php if (!empty($pkgOverview)): ?>
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-information-line"></i></div>
                <h2 class="section-title">Tour Overview</h2>
            </div>
            <div class="overview-content">
                <?php echo $pkgOverview; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Photo Gallery (If images exist) -->
        <?php if (!empty($unassignedGalleryImages)): 
            $initialLimit = 6;
            $totalGalCount = count($unassignedGalleryImages);
        ?>
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-image-line"></i></div>
                <h2 class="section-title">Photo Gallery (<?php echo $totalGalCount; ?>)</h2>
            </div>

            <div class="gallery-grid" id="main-gallery-grid">
                <?php foreach ($unassignedGalleryImages as $iIndex => $imgItem): 
                    $isHidden = $iIndex >= $initialLimit;
                ?>
                    <div class="gallery-item <?php echo $isHidden ? 'gallery-hidden-item' : ''; ?>" 
                         style="<?php echo $isHidden ? 'display: none;' : ''; ?>"
                         onclick="openLightboxUrl('<?php echo htmlspecialchars($imgItem['url']); ?>', '<?php echo htmlspecialchars($imgItem['title']); ?>')">
                        <img src="<?php echo htmlspecialchars($imgItem['url']); ?>" alt="<?php echo htmlspecialchars($imgItem['title']); ?>" loading="lazy" />
                        <?php if (!empty($imgItem['title'])): ?>
                            <div class="gallery-caption"><?php echo htmlspecialchars($imgItem['title']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalGalCount > $initialLimit): ?>
            <div class="gallery-more-wrapper">
                <button type="button" id="btn-toggle-gallery" class="btn-action-outline btn-gallery-more" onclick="toggleGalleryMore()">
                    <i class="ri-image-line"></i>
                    <span>View All Photos (+<?php echo ($totalGalCount - $initialLimit); ?>)</span>
                </button>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- Detailed Day-by-Day Timeline Itinerary -->
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-calendar-todo-line"></i></div>
                <h2 class="section-title">Day-Wise Itinerary</h2>
            </div>

            <div class="itinerary-timeline">
                <?php 
                $dayIndex = 1;
                foreach ($pkgDays as $item):
                    $dayRawLabel = is_array($item) ? ($item['day'] ?? ($item['title'] ?? ("Day " . $dayIndex))) : ("Day " . $dayIndex);
                    $overview = is_array($item) ? ($item['overview'] ?? ($item['content'] ?? '')) : '';
                    $isHotelHidden = is_array($item) && (
                        (isset($item['show_hotel']) && ($item['show_hotel'] === 0 || $item['show_hotel'] === '0' || $item['show_hotel'] === false)) ||
                        !empty($item['hide_hotel']) ||
                        !empty($item['is_hotel_hidden'])
                    );
                    $stayName = (!$isHotelHidden && is_array($item)) ? ($item['stay'] ?? ($item['hotel'] ?? '')) : '';
                    $currentDayNum = $dayIndex;
                    $isLastDay = ($dayIndex === $totalDaysCount);

                    $dayPhotos = $dayWiseImages[$currentDayNum] ?? [];
                    $assignedHotel = !$isHotelHidden ? ($dayWiseHotels[$currentDayNum] ?? null) : null;

                    // Determine stay city
                    $stayCity = is_array($item) ? ($item['city'] ?? '') : '';
                    if (empty($stayCity)) {
                        $stayCity = !empty($pkgCity) ? $pkgCity : 'Destination';
                    }
                ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="day-card open">
                            <div class="day-card-header" onclick="toggleDay(this)">
                                <span class="day-badge">Day <?php echo $currentDayNum; ?></span>
                                <h3 class="day-title"><?php echo htmlspecialchars(trim($dayRawLabel)); ?></h3>
                                <div class="toggle-icon"><i class="ri-arrow-down-s-line"></i></div>
                            </div>

                            <div class="day-card-body">
                                <?php if (!empty($overview)): ?>
                                    <div><?php echo $overview; ?></div>
                                <?php else: ?>
                                    <p>Sightseeing, experiences, and comfortable transfers as per the curated plan.</p>
                                <?php endif; ?>

                                <!-- Day-Wise Photos -->
                                <?php if (!empty($dayPhotos)): ?>
                                    <div class="day-gallery-container">
                                        <div class="day-gallery-title">
                                            <i class="ri-image-2-line"></i> Day <?php echo $currentDayNum; ?> Sightseeing Photos
                                        </div>
                                        <div class="day-gallery-grid">
                                            <?php foreach ($dayPhotos as $dPhoto): ?>
                                                <div class="day-gallery-item" onclick="openLightboxUrl('<?php echo htmlspecialchars($dPhoto['url']); ?>', '<?php echo htmlspecialchars($dPhoto['title']); ?>')">
                                                    <img src="<?php echo htmlspecialchars($dPhoto['url']); ?>" alt="<?php echo htmlspecialchars($dPhoto['title']); ?>" loading="lazy" />
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Day Stay vs Last Day Farewell Card -->
                                <?php if ($isLastDay): ?>
                                    <div class="farewell-card">
                                        <div class="farewell-icon"><i class="ri-flight-takeoff-line"></i></div>
                                        <div>
                                            <h4 class="farewell-title">End of a Magical Journey! ✨</h4>
                                            <p class="farewell-text">
                                                As your tour comes to a wonderful conclusion, you carry home a treasure of priceless memories, warm smiles, and beautiful stories. Thank you for traveling with <strong><?php echo htmlspecialchars($siteName); ?></strong>!
                                            </p>
                                        </div>
                                    </div>
                                <?php elseif ($assignedHotel): ?>
                                    <div class="day-hotel-card assigned-hotel">
                                        <div class="hotel-card-header">
                                            <div class="hotel-badge-tag">
                                                <i class="ri-hotel-bed-line"></i> Night Stay in <?php echo htmlspecialchars($stayCity); ?>
                                            </div>
                                            <span class="hotel-type-pill"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $assignedHotel['property_type'] ?? 'Hotel'))); ?></span>
                                        </div>

                                        <div class="hotel-card-body">
                                            <?php 
                                            $hotelThumb = fixImageUrl($assignedHotel['property_thumbnail'] ?? '');
                                            if (!empty($hotelThumb)): 
                                            ?>
                                                <div class="hotel-thumb-box" onclick="openLightboxUrl('<?php echo htmlspecialchars($hotelThumb); ?>', '<?php echo htmlspecialchars($assignedHotel['property_title']); ?>')">
                                                    <img src="<?php echo htmlspecialchars($hotelThumb); ?>" alt="<?php echo htmlspecialchars($assignedHotel['property_title']); ?>" loading="lazy" />
                                                </div>
                                            <?php endif; ?>

                                            <div class="hotel-details-box">
                                                <h4 class="hotel-name"><?php echo htmlspecialchars($assignedHotel['property_title']); ?></h4>
                                                
                                                <?php 
                                                $locParts = array_filter([$assignedHotel['property_address'] ?? '', $assignedHotel['property_city'] ?? '', $assignedHotel['property_state'] ?? '']);
                                                if (!empty($locParts)):
                                                ?>
                                                    <div class="hotel-location">
                                                        <i class="ri-map-pin-line"></i>
                                                        <span><?php echo htmlspecialchars(implode(', ', $locParts)); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($assignedHotel['tph_room_type'])): ?>
                                                    <div class="hotel-room-type">
                                                        <strong>Room Type:</strong> <?php echo htmlspecialchars(ucwords($assignedHotel['tph_room_type'])); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($assignedHotel['link_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($assignedHotel['link_url']); ?>" class="btn-hotel-link" target="_blank" rel="noopener">
                                                        View Hotel Details <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif (!empty($stayName)): ?>
                                    <div class="day-hotel-card assigned-hotel">
                                        <div class="hotel-card-header">
                                            <div class="hotel-badge-tag">
                                                <i class="ri-hotel-bed-line"></i> Night Stay in <?php echo htmlspecialchars($stayCity); ?>
                                            </div>
                                            <span class="hotel-type-pill">Confirmed Stay</span>
                                        </div>
                                        <div class="hotel-card-body">
                                            <div class="hotel-details-box">
                                                <h4 class="hotel-name"><?php echo htmlspecialchars($stayName); ?></h4>
                                                <div class="hotel-location"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($stayCity); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                    $dayIndex++;
                endforeach; 
                ?>
            </div>
        </section>

        <!-- Inclusions & Exclusions Section -->
        <?php
        if (!function_exists('parseBulletList')) {
            function parseBulletList($input)
            {
                if (empty($input)) return [];
                $items = [];
                if (is_array($input)) {
                    foreach ($input as $item) {
                        $subItems = parseBulletList($item);
                        foreach ($subItems as $sub) {
                            $items[] = $sub;
                        }
                    }
                } else {
                    $str = str_replace(['<br>', '<br/>', '<br />', '</li>', '<p>', '</p>', ','], "\n", (string)$input);
                    $rawLines = explode("\n", strip_tags($str));
                    foreach ($rawLines as $line) {
                        $line = trim($line);
                        $line = preg_replace('/^[•\-\*✓✗✔✕\d\.\)]+\s*/u', '', $line);
                        $line = trim($line);
                        if (!empty($line)) {
                            $items[] = $line;
                        }
                    }
                }
                return array_values(array_unique($items));
            }
        }

        $inclusionsList = parseBulletList($pkgInclusions);
        $exclusionsList = parseBulletList($pkgExclusions);

        if (!empty($inclusionsList) || !empty($exclusionsList)):
        ?>
            <section class="luxury-card">
                <div class="section-header">
                    <div class="section-icon"><i class="ri-shield-check-line"></i></div>
                    <h2 class="section-title">Inclusions & Exclusions</h2>
                </div>

                <div class="inc-exc-grid">
                    <?php if (!empty($inclusionsList)): ?>
                        <div class="inc-card">
                            <div class="inc-exc-header">
                                <i class="ri-checkbox-circle-fill"></i>
                                Package Inclusions
                            </div>
                            <ul class="inc-exc-list">
                                <?php foreach ($inclusionsList as $inc): ?>
                                    <li>
                                        <i class="ri-check-line"></i>
                                        <span><?php echo htmlspecialchars(ucfirst(strtolower($inc))); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($exclusionsList)): ?>
                        <div class="exc-card">
                            <div class="inc-exc-header">
                                <i class="ri-close-circle-fill"></i>
                                Package Exclusions
                            </div>
                            <ul class="inc-exc-list">
                                <?php foreach ($exclusionsList as $exc): ?>
                                    <li>
                                        <i class="ri-close-line"></i>
                                        <span><?php echo htmlspecialchars(ucfirst(strtolower($exc))); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Hotel Details & Pricing Table -->
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-money-rupee-circle-line"></i></div>
                <h2 class="section-title">Hotel Details & Package Costs</h2>
            </div>

            <div class="pricing-table-wrapper">
                <table class="luxury-table">
                    <thead>
                        <tr>
                            <th>Package / Category Type</th>
                            <th>Hotel Details</th>
                            <th>Total Cost (INR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dbPackagePrices)): ?>
                            <?php foreach ($dbPackagePrices as $pTier): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars(ucwords($pTier['tour_package_price_type'] ?? 'Standard')); ?></strong></td>
                                    <td><?php echo htmlspecialchars($pTier['tour_package_price_hotel'] ?? ($pkgHotel ? ucwords(str_replace('_', ' ', $pkgHotel)) : 'Deluxe Hotel')); ?></td>
                                    <td class="price-highlight">₹ <?php echo number_format((float)($pTier['tour_package_price_selling_adult'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif (!empty($pkgPriceAdult)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(ucfirst($pkgTheme)); ?> Package</strong></td>
                                <td><?php echo htmlspecialchars(!empty($pkgHotel) ? ucwords(str_replace('_', ' ', $pkgHotel)) : 'Selected Category Partner Hotels'); ?></td>
                                <td class="price-highlight">₹ <?php echo number_format((float)$pkgPriceAdult); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><strong>Custom Quotation</strong></td>
                                <td>Selected Partner Hotels</td>
                                <td class="price-highlight">Quotation on Request</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Notes and Cancellation Policy -->
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-file-list-3-line"></i></div>
                <h2 class="section-title">Notes & Policy Information</h2>
            </div>

            <div class="policy-grid">
                <div class="policy-card">
                    <h4><i class="ri-information-line" style="color: var(--color-accent);"></i> Important Tour Notes</h4>
                    <ul class="policy-list">
                        <li>Hotels are subject to availability at the time of final reservation.</li>
                        <li>Vehicle AC will be operated on plain routes as per scheduled itinerary.</li>
                        <li>Sightseeing is subject to local weather conditions and operational road permits.</li>
                        <li>Please carry valid government-issued ID proofs during the journey.</li>
                    </ul>
                </div>

                <div class="policy-card">
                    <h4><i class="ri-alert-line" style="color: var(--color-danger);"></i> Cancellation Policy</h4>
                    <ul class="policy-list">
                        <li>20% cancellation of the package cost after booking confirmation.</li>
                        <li>30% cancellation of the package cost before 10 days of the travel date.</li>
                        <li>50% cancellation of the package cost before 7 days of the travel date.</li>
                        <li>100% cancellation within 3 days of departure date.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Dedicated Company Contact Information Card (Real Dynamic Data) -->
        <section class="company-contact-card">
            <div class="contact-header">
                <div class="contact-icon"><i class="ri-customer-service-2-line"></i></div>
                <div>
                    <h3 class="contact-title"><?php echo htmlspecialchars($siteName); ?></h3>
                    <p style="font-size: 0.85rem; color: #cbd5e1;">Your Trusted Travel Planning Partner</p>
                </div>
            </div>

            <div class="contact-grid">
                <?php if (!empty($sitePhone)): ?>
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="ri-phone-line"></i></div>
                        <div>
                            <span class="contact-item-label">Helpline & WhatsApp</span>
                            <span class="contact-item-value"><a href="tel:<?php echo htmlspecialchars($sitePhone); ?>"><?php echo htmlspecialchars($sitePhone); ?></a></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteEmail)): ?>
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="ri-mail-line"></i></div>
                        <div>
                            <span class="contact-item-label">Email Support</span>
                            <span class="contact-item-value"><a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>"><?php echo htmlspecialchars($siteEmail); ?></a></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteAddress)): ?>
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="ri-building-line"></i></div>
                        <div>
                            <span class="contact-item-label">Head Office Address</span>
                            <span class="contact-item-value"><?php echo htmlspecialchars($siteAddress); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteUrl)): ?>
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="ri-global-line"></i></div>
                        <div>
                            <span class="contact-item-label">Website</span>
                            <span class="contact-item-value"><a href="<?php echo htmlspecialchars($siteUrl); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars(preg_replace('/^https?:\/\//i', '', rtrim($siteUrl, '/'))); ?></a></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer with Aleya Credit Note -->
        <footer class="luxury-footer">
            <p>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($siteName); ?></strong>. All rights reserved.</p>
            <div class="credit-strap">
                Powered by <a href="https://aleya.in" target="_blank" rel="noopener">Aleya</a> — Travel Agency Management Software
            </div>
        </footer>

    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox-modal" class="lightbox-modal" onclick="if(event.target === this) closeLightbox();">
        <button type="button" class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <div class="lightbox-slide-wrapper">
            <img id="lightbox-img" class="lightbox-content" src="" alt="Gallery Preview" />
            <div id="lightbox-caption" class="lightbox-caption-bar"></div>
        </div>
    </div>

    <script>
        function toggleDay(headerElement) {
            const dayCard = headerElement.closest('.day-card');
            dayCard.classList.toggle('open');
            const body = dayCard.querySelector('.day-card-body');
            if (body) {
                body.style.display = dayCard.classList.contains('open') ? 'block' : 'none';
            }
        }

        function toggleGalleryMore() {
            const hiddenItems = document.querySelectorAll('.gallery-hidden-item');
            const btn = document.getElementById('btn-toggle-gallery');
            let isExpanded = btn.getAttribute('data-expanded') === 'true';

            hiddenItems.forEach(item => {
                item.style.display = isExpanded ? 'none' : 'block';
            });

            if (isExpanded) {
                btn.setAttribute('data-expanded', 'false');
                btn.querySelector('span').innerText = 'View All Photos (+' + hiddenItems.length + ')';
            } else {
                btn.setAttribute('data-expanded', 'true');
                btn.querySelector('span').innerText = 'Show Less';
            }
        }

        function openLightboxUrl(url, title) {
            const modal = document.getElementById('lightbox-modal');
            const img = document.getElementById('lightbox-img');
            const cap = document.getElementById('lightbox-caption');
            img.src = url;
            cap.innerText = title || '';
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const modal = document.getElementById('lightbox-modal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });

        // Client-side PDF Generation with A4 slicing
        document.getElementById('btnDownloadAleyaPDF').addEventListener('click', async function() {
            const btn = this;
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Generating PDF...';

            try {
                // Ensure all day cards are expanded during PDF generation
                document.querySelectorAll('.day-card').forEach(c => {
                    c.classList.add('open');
                    const b = c.querySelector('.day-card-body');
                    if (b) b.style.display = 'block';
                });

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const element = document.getElementById('itineraryDocument');

                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                });

                const imgData = canvas.toDataURL('image/jpeg', 0.95);
                const imgWidth = pageWidth;
                const pageCanvasHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = pageCanvasHeight;
                let position = 0;

                pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, pageCanvasHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - pageCanvasHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, pageCanvasHeight);
                    heightLeft -= pageHeight;
                }

                pdf.save("<?php echo preg_replace('/[^A-Za-z0-9_-]/', '_', $pkgTitle); ?>_Aleya_Itinerary.pdf");
            } catch (err) {
                console.error('PDF Error:', err);
                alert('Could not generate PDF directly. Using print view instead.');
                window.print();
            } finally {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        });

        // Auto-Download on page load if requested (?download=true or ?pdf=1)
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const dlParam = urlParams.get('download');
            const pdfParam = urlParams.get('pdf');
            const shouldAutoDownload = dlParam === 'true' || dlParam === '1' || dlParam === 'yes' || pdfParam === '1' || pdfParam === 'true' || <?php echo (!empty($autoDownload) || !empty($isDownload)) ? 'true' : 'false'; ?>;
            if (shouldAutoDownload) {
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        const btn = document.getElementById('btnDownloadAleyaPDF');
                        if (btn) btn.click();
                    }, 600);
                });
            }
        })();
    </script>
</body>
</html>