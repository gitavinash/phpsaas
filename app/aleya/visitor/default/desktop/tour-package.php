<?php
/**
 * Luxury Tour Package Landing Page
 * Dynamically rendered from database data
 */

// Helper functions defined at top
if (!function_exists('fixImageUrl')) {
    function fixImageUrl($url)
    {
        if (empty($url)) return '';
        $url = trim($url);
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }
        if (defined('ROOT') && file_exists(ROOT . '/' . ltrim($url, '/'))) {
            return $url;
        }
        if (strpos($url, '/upload/crm.') === 0) {
            return 'https://crm.musafirindiatrip.com' . $url;
        }
        if (strpos($url, '/upload/') === 0) {
            if (defined('ROOT') && file_exists(ROOT . $url)) {
                return $url;
            }
            return 'https://crm.musafirindiatrip.com' . $url;
        }
        return $url;
    }
}

if (!function_exists('strafter')) {
    function strafter($string, $substring)
    {
        $pos = strpos($string, $substring);
        return $pos === false ? '' : substr($string, $pos + strlen($substring));
    }
}

// Hero Featured Cover Image
$heroFeatureImage = fixImageUrl($tour_package_image ?? ($gallery_image ?? ''));

// Separate images into Unassigned Gallery and Day-Wise Gallery
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
            if ($item['url'] === $fixedUrl) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $dayWiseImages[$dayInt][] = $imgData;
        }
    } else {
        $exists = false;
        foreach ($unassignedGalleryImages as $item) {
            if ($item['url'] === $fixedUrl) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $unassignedGalleryImages[] = $imgData;
        }
    }
};

// 1. Process $gallery_image_list from current DB row
if (!empty($gallery_image_list)) {
    $listData = json_decode($gallery_image_list, true);
    if (is_array($listData)) {
        foreach ($listData as $gItem) {
            $rawUrl = is_array($gItem) ? ($gItem['path'] ?? $gItem['url'] ?? $gItem['image'] ?? '') : $gItem;
            $imgTitle = is_array($gItem) ? ($gItem['name'] ?? $gItem['title'] ?? $gItem['caption'] ?? '') : '';
            $dayVal = is_array($gItem) ? ($gItem['day'] ?? 0) : 0;
            $registerImage($rawUrl, $imgTitle, $dayVal);
        }
    }
}

// 2. Query all gallery entries linked to this tour package in DB
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
    $galDbQuery = "SELECT gallery_image, gallery_thumbnail, gallery_title, gallery_image_list FROM " . TABLE_PREFIX . "gallery WHERE gallery_category = '" . (int)$tour_package_id . "' AND gallery_site_id = " . SITE_ID . " AND gallery_type = 'tour_package'";
    $galResult = mysqli_query($connection, $galDbQuery);
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

// If no gallery images found, add featured image as fallback
if (empty($unassignedGalleryImages) && empty($dayWiseImages) && !empty($gallery_image)) {
    $registerImage($gallery_image, $tour_package_title ?? 'Featured Photo', 0);
}

// Consolidate full photo slider list
$allSliderPhotos = [];
if (!empty($unassignedGalleryImages)) {
    foreach ($unassignedGalleryImages as $uImg) {
        $allSliderPhotos[] = $uImg;
    }
}
foreach ($dayWiseImages as $dNum => $dPhotos) {
    foreach ($dPhotos as $dImg) {
        $isDup = false;
        foreach ($allSliderPhotos as $existing) {
            if ($existing['url'] === $dImg['url']) {
                $isDup = true;
                break;
            }
        }
        if (!$isDup) {
            $allSliderPhotos[] = $dImg;
        }
    }
}

// Fetch all price tiers from DB for this package
$dbPackagePrices = [];
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
    $priceDbQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_parent_id = '" . (int)$tour_package_id . "' AND tour_package_price_site_id = " . SITE_ID . " ORDER BY tour_package_price_selling_adult ASC";
    $priceResult = mysqli_query($connection, $priceDbQuery);
    if ($priceResult && mysqli_num_rows($priceResult) > 0) {
        while ($priceRow = mysqli_fetch_assoc($priceResult)) {
            $dbPackagePrices[] = $priceRow;
        }
    }
}

// Fetch day-wise assigned hotels for this package
$dayWiseHotels = [];
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && !empty($tour_package_id)) {
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
    $hotelResult = mysqli_query($connection, $hotelDbQuery);
    if ($hotelResult && mysqli_num_rows($hotelResult) > 0) {
        while ($hRow = mysqli_fetch_assoc($hotelResult)) {
            $nightNum = (int)$hRow['tph_night'];
            if ($nightNum > 0) {
                $dayWiseHotels[$nightNum] = $hRow;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo defined('SITE_NAME') ? SITE_NAME : 'Travel' ?> - <?php echo htmlspecialchars($tour_package_title ?? 'Tour Package') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            body {
                padding-bottom: 40px;
            }
        }

        .luxury-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px 16px;
        }

        @media (min-width: 768px) {
            .luxury-wrapper {
                padding: 40px 24px;
            }
        }

        /* --- Header & Brand --- */
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
            max-height: 52px;
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
            font-size: 1.4rem;
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

        /* --- Hero Card (Feature Image Background - Visible & Premium) --- */
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
            .hero-title {
                font-size: 2.75rem;
            }
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

        /* --- Section Cards --- */
        .luxury-card {
            background: var(--color-bg-card);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--color-border);
        }

        @media (min-width: 768px) {
            .luxury-card {
                padding: 36px 32px;
            }
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
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        @media (min-width: 768px) {
            .section-title {
                font-size: 1.75rem;
            }
        }

        /* --- Overview Content --- */
        .overview-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: var(--color-text-main);
        }

        .overview-content p {
            margin-bottom: 1rem;
        }

        .overview-content p:last-child {
            margin-bottom: 0;
        }

        /* --- Main Photo Gallery (Unassigned Images) --- */
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

        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }

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
            .itinerary-timeline {
                padding-left: 36px;
            }
        }

        .itinerary-timeline::before {
            content: '';
            position: absolute;
            top: 24px;
            bottom: 24px;
            left: 7px;
            width: 3px;
            background: linear-gradient(to bottom, var(--color-accent), #cbd5e1);
            border-radius: var(--radius-full);
        }

        @media (min-width: 768px) {
            .itinerary-timeline::before {
                left: 15px;
            }
        }

        .timeline-item {
            position: relative;
            margin-bottom: 24px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -20px;
            top: 16px;
            transform: translateX(-50%);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--color-bg-card);
            border: 4px solid var(--color-accent);
            box-shadow: 0 0 0 4px var(--color-accent-light);
            z-index: 2;
        }

        @media (min-width: 768px) {
            .timeline-marker {
                /* left: -36px; */
                width: 22px;
                height: 22px;
                border-width: 5px;
            }
        }

        .day-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: var(--transition);
        }

        .day-card-header {
            padding: 18px 20px;
            background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }

        .day-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: var(--color-primary);
            color: var(--color-accent);
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .day-title {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-primary);
            flex-grow: 1;
        }

        .toggle-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-bg-main);
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .day-card.open .toggle-icon {
            transform: rotate(180deg);
            background: var(--color-accent-light);
            color: var(--color-accent);
        }

        .day-card-body {
            padding: 20px;
            color: var(--color-text-main);
            font-size: 0.95rem;
            line-height: 1.7;
            border-top: 1px solid var(--color-border);
            display: block;
        }

        /* --- Day Wise Photos --- */
        .day-gallery-container {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px dashed var(--color-border);
        }

        .day-gallery-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .day-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }

        .day-gallery-item {
            aspect-ratio: 4 / 3;
            border-radius: var(--radius-sm);
            overflow: hidden;
            cursor: pointer;
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-sm);
            background: var(--color-primary-light);
        }

        .day-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .day-gallery-item:hover img {
            transform: scale(1.1);
        }

        /* --- Day-Wise Hotel Card Styling --- */
        .day-hotel-card {
            margin-top: 20px;
            background: #f8fafc;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .day-hotel-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-md);
        }

        .hotel-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            background: #f1f5f9;
            border-bottom: 1px solid var(--color-border);
        }

        .hotel-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--color-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hotel-type-pill {
            background: var(--color-accent-light);
            color: var(--color-accent);
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid rgba(197, 155, 39, 0.3);
        }

        .hotel-card-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 18px;
        }

        @media (min-width: 600px) {
            .hotel-card-body {
                flex-direction: row;
                align-items: flex-start;
            }
        }

        .hotel-thumb-box {
            width: 100%;
            max-width: 180px;
            aspect-ratio: 4 / 3;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
            cursor: pointer;
            border: 1px solid var(--color-border);
            background: var(--color-primary-light);
        }

        .hotel-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .hotel-thumb-box:hover img {
            transform: scale(1.08);
        }

        .hotel-details-box {
            flex-grow: 1;
        }

        .hotel-name {
            font-family: var(--font-heading);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 6px;
        }

        .hotel-location {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin-bottom: 10px;
        }

        .hotel-room-type {
            font-size: 0.875rem;
            color: var(--color-text-main);
            margin-bottom: 10px;
        }

        .btn-hotel-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.825rem;
            font-weight: 700;
            color: var(--color-accent);
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-hotel-link:hover {
            color: var(--color-primary);
            transform: translateX(3px);
        }

        .category-info-text {
            font-size: 0.95rem;
            color: var(--color-text-main);
        }

        .category-info-text p {
            margin-top: 4px;
            color: var(--color-text-muted);
            font-size: 0.85rem;
        }

        .legal-disclaimer {
            padding: 10px 18px;
            background: #fffbe6;
            border-top: 1px dashed #fef08a;
            font-size: 0.775rem;
            color: #854d0e;
            line-height: 1.5;
        }

        /* --- Farewell Card (Last Day Memories Gesture) --- */
        .farewell-card {
            margin-top: 20px;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            border-radius: var(--radius-md);
            padding: 24px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            box-shadow: var(--shadow-sm);
        }

        @media (max-width: 600px) {
            .farewell-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }

        .farewell-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #f59e0b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        }

        .farewell-content {
            flex-grow: 1;
        }

        .farewell-title {
            font-family: var(--font-heading);
            font-size: 1.3rem;
            font-weight: 700;
            color: #78350f;
            margin-bottom: 8px;
        }

        .farewell-text {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #92400e;
            margin-bottom: 14px;
        }

        .farewell-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border: 1px solid #fcd34d;
            color: #b45309;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 0.825rem;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        /* --- Inclusions & Exclusions --- */
        .inc-exc-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .inc-exc-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .inc-card,
        .exc-card {
            border-radius: var(--radius-md);
            padding: 24px;
            border: 1px solid var(--color-border);
            height: 100%;
        }

        .inc-card {
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
            border-color: #bbf7d0;
        }

        .exc-card {
            background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%);
            border-color: #fecaca;
        }

        .inc-exc-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .inc-card .inc-exc-header {
            color: #15803d;
        }

        .exc-card .inc-exc-header {
            color: #b91c1c;
        }

        .inc-exc-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .inc-exc-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.925rem;
            color: var(--color-text-main);
        }

        .list-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .inc-card .list-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .exc-card .list-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        /* --- Pricing & Table --- */
        .pricing-table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
        }

        .luxury-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        .luxury-table th {
            background: var(--color-primary);
            color: #ffffff;
            padding: 16px 20px;
            font-weight: 600;
            font-family: var(--font-heading);
            letter-spacing: 0.5px;
        }

        .luxury-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--color-border);
            color: var(--color-text-main);
        }

        .luxury-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .luxury-table tbody tr:last-child td {
            border-bottom: none;
        }

        .price-highlight {
            font-weight: 700;
            color: var(--color-accent);
            font-size: 1.1rem;
        }

        /* --- Policies --- */
        .policy-card {
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 20px 24px;
            border-left: 4px solid var(--color-accent);
            margin-bottom: 16px;
        }

        .policy-card:last-child {
            margin-bottom: 0;
        }

        .policy-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .policy-list {
            padding-left: 20px;
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }

        .policy-list li {
            margin-bottom: 6px;
        }

        /* --- Full-Screen Lightbox Slider Modal --- */
        .lightbox-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(10px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lightbox-modal.active {
            display: flex;
        }

        .lightbox-slide-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-width: 90vw;
            max-height: 85vh;
        }

        .lightbox-content {
            max-width: 85vw;
            max-height: 75vh;
            border-radius: var(--radius-md);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            object-fit: contain;
            transition: opacity 0.2s ease;
        }

        .lightbox-caption-bar {
            margin-top: 12px;
            color: #cbd5e1;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: center;
        }

        .lightbox-counter-badge {
            position: absolute;
            bottom: 24px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 52px;
            height: 52px;
            border-radius: 50%;
            font-size: 2rem;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            user-select: none;
            z-index: 10001;
        }

        .lightbox-nav:hover {
            background: var(--color-accent);
            color: #ffffff;
            border-color: var(--color-accent);
            transform: translateY(-50%) scale(1.08);
        }

        .lightbox-prev {
            left: 24px;
        }

        .lightbox-next {
            right: 24px;
        }

        .lightbox-close {
            position: absolute;
            top: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 46px;
            height: 46px;
            border-radius: 50%;
            font-size: 1.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            z-index: 10001;
        }

        .lightbox-close:hover {
            background: rgba(239, 68, 68, 0.8);
            border-color: #ef4444;
        }

        /* --- Desktop Share Modal --- */
        .share-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            z-index: 10005;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .share-modal.active {
            display: flex;
        }

        .share-modal-card {
            background: var(--color-bg-card);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            max-width: 440px;
            width: 100%;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--color-border);
            animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }

        .share-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .share-modal-header h3 {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .share-modal-close {
            background: var(--color-bg-main);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            font-size: 1.3rem;
            color: var(--color-text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .share-modal-close:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .share-modal-subtitle {
            font-size: 0.9rem;
            color: var(--color-text-muted);
            margin-bottom: 24px;
        }

        .share-options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .share-option-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            background: var(--color-bg-card);
            color: var(--color-text-main);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .share-option-btn:hover {
            border-color: var(--color-accent);
            background: var(--color-accent-light);
            transform: translateY(-2px);
        }

        .share-option-btn.wa-btn {
            background: #f0fdf4;
            border-color: #86efac;
            color: #15803d;
        }

        .share-option-btn.wa-btn:hover {
            background: #25d366;
            border-color: #25d366;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.35);
        }

        .share-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }

        .copy-icon { background: #e0f2fe; color: #0284c7; }
        .wa-icon { background: #25d366; color: #ffffff; }
        .email-icon { background: #fef3c7; color: #d97706; }
        .fb-icon { background: #dbeafe; color: #2563eb; }

        .share-option-btn.wa-btn:hover .wa-icon {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
            transform: scale(1.1);
        }

        @media (max-width: 600px) {
            .lightbox-prev { left: 10px; width: 42px; height: 42px; font-size: 1.6rem; }
            .lightbox-next { right: 10px; width: 42px; height: 42px; font-size: 1.6rem; }
            .lightbox-close { top: 16px; right: 16px; width: 40px; height: 40px; }
            .share-options-grid { grid-template-columns: 1fr; }
        }

        /* --- Footer & Credit Strap --- */
        .luxury-footer {
            text-align: center;
            padding: 32px 20px;
            color: var(--color-text-muted);
            font-size: 0.875rem;
            border-top: 1px solid var(--color-border);
            margin-top: 40px;
        }

        .credit-strap {
            margin-top: 8px;
            font-size: 0.825rem;
            color: var(--color-text-muted);
            letter-spacing: 0.5px;
        }

        .credit-strap a {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
        }

        .credit-strap a:hover {
            color: var(--color-primary);
            text-decoration: underline;
        }

        /* --- Mobile Sticky Action Bar --- */
        .mobile-cta-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 999;
        }

        @media (min-width: 992px) {
            .mobile-cta-bar {
                display: none;
            }
        }

        .cta-info label {
            display: block;
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .cta-info span {
            font-size: 1rem;
            font-weight: 700;
            color: #fbbf24;
        }

        .btn-cta-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #c59b27 0%, #9a7312 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 12px 20px;
            border-radius: var(--radius-full);
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        /* --- Print Styling --- */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding-bottom: 0 !important;
            }

            .luxury-wrapper {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .site-header,
            .mobile-cta-bar,
            .btn-action-outline {
                display: none !important;
            }

            .hero-card {
                background: #f1f5f9 !important;
                color: #000000 !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                padding: 24px !important;
                page-break-inside: avoid;
            }

            .hero-title {
                color: #000000 !important;
            }

            .hero-greeting,
            .meta-text span,
            .meta-text label {
                color: #334155 !important;
            }

            .luxury-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                page-break-inside: avoid;
                margin-bottom: 20px !important;
                padding: 20px !important;
            }

            .toggle-icon {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="luxury-wrapper">

        <!-- Header Bar with Site Logo & Share Package -->
        <header class="site-header">
            <a href="#" class="brand-logo">
                <?php
                $siteLogoResolved = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : '');
                if (!empty($siteLogoResolved)):
                ?>
                    <img src="<?php echo htmlspecialchars($siteLogoResolved); ?>" alt="<?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Logo'); ?>" class="brand-logo-img" />
                <?php else: ?>
                    <div class="brand-badge"><?php echo substr(defined('SITE_NAME') ? SITE_NAME : 'T', 0, 1) ?></div>
                    <div class="company-name"><?php echo defined('SITE_NAME') ? SITE_NAME : 'Travel App' ?></div>
                <?php endif; ?>
            </a>
            <div>
                <button onclick="handleSharePackage()" class="btn-action-outline btn-share" title="Share Tour Package">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                    </svg>
                    Share Package
                </button>
            </div>
        </header>

        <!-- Premium Hero Cover Card (Vibrant Feature Image Background) -->
        <section class="hero-card" <?php if (!empty($heroFeatureImage)): ?>style="background: linear-gradient(to top, rgba(15, 23, 42, 0.92) 0%, rgba(15, 23, 42, 0.45) 50%, rgba(15, 23, 42, 0.15) 100%), url('<?php echo htmlspecialchars($heroFeatureImage); ?>') center/cover no-repeat;" <?php endif; ?>>
            <div class="hero-tag">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Exclusive Tour Package
            </div>

            <h1 class="hero-title"><?php echo htmlspecialchars($tour_package_title ?? 'Tour Package') ?></h1>

            <div class="hero-meta-grid">
                <?php if (isset($tour_package_duration)): ?>
                    <div class="meta-pill">
                        <div class="meta-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="meta-text">
                            <label>Duration</label>
                            <span><?php echo (int)$tour_package_duration ?> Nights / <?php echo (int)$tour_package_duration + 1 ?> Days</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $locations = array_filter([$tour_package_city ?? '', $tour_package_state ?? '', $tour_package_country ?? '']);
                if (!empty($locations)):
                ?>
                    <div class="meta-pill">
                        <div class="meta-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="meta-text">
                            <label>Destination</label>
                            <span><?php echo htmlspecialchars(implode(', ', $locations)) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($tour_package_price_selling_adult)): ?>
                    <div class="meta-pill">
                        <div class="meta-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="meta-text">
                            <label>Starting Price</label>
                            <span>₹ <?php echo number_format((float)$tour_package_price_selling_adult) ?> / person</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tour Overview Section (Outside Hero) -->
        <?php if (!empty($tour_package_overview)): ?>
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="section-title">Tour Overview</h2>
            </div>
            <div class="overview-content">
                <?php echo $tour_package_overview; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Unassigned Photo Gallery (With Initial Limit & View More) -->
        <?php if (!empty($unassignedGalleryImages)): 
            $initialLimit = 6;
            $totalCount = count($unassignedGalleryImages);
        ?>
            <section class="luxury-card">
                <div class="section-header">
                    <div class="section-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="section-title">Photo Gallery (<?php echo $totalCount; ?>)</h2>
                </div>

                <div class="gallery-grid" id="main-gallery-grid">
                    <?php foreach ($unassignedGalleryImages as $iIndex => $imgItem): 
                        $isHidden = $iIndex >= $initialLimit;
                    ?>
                        <div class="gallery-item <?php echo $isHidden ? 'gallery-hidden-item' : ''; ?>" 
                             style="<?php echo $isHidden ? 'display: none;' : ''; ?>"
                             onclick="openLightboxIndex(<?php echo $iIndex; ?>)">
                            <img src="<?php echo htmlspecialchars($imgItem['url']); ?>" alt="<?php echo htmlspecialchars($imgItem['title']); ?>" loading="lazy" />
                            <?php if (!empty($imgItem['title'])): ?>
                                <div class="gallery-caption"><?php echo htmlspecialchars($imgItem['title']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalCount > $initialLimit): ?>
                <div class="gallery-more-wrapper">
                    <button id="btn-toggle-gallery" class="btn-action-outline btn-gallery-more" onclick="toggleGalleryMore()">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>View All Photos (+<?php echo ($totalCount - $initialLimit); ?>)</span>
                    </button>
                </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- Itinerary Section (With Day-Wise Photos & Day-Wise Stay Information) -->
        <?php
        $itineraryData = json_decode($tour_package_itinerary ?? '[]', TRUE);
        if (!empty($itineraryData) && is_array($itineraryData)):
            $totalDaysCount = count($itineraryData);
        ?>
            <section class="luxury-card">
                <div class="section-header">
                    <div class="section-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path>
                        </svg>
                    </div>
                    <h2 class="section-title">Itinerary</h2>
                </div>

                <div class="itinerary-timeline">
                    <?php foreach ($itineraryData as $index => $item):
                        $dayRawLabel = $item['day'] ?? ('Day ' . ($index + 1));
                        $overview = $item['overview'] ?? '';

                        // Day badge pill: ONLY show "Day X"
                        $badgeText = 'Day ' . ($index + 1);

                        $titleText = $dayRawLabel;

                        $currentDayNum = $index + 1;
                        $isLastDay = ($index === $totalDaysCount - 1);

                        $dayPhotos = $dayWiseImages[$currentDayNum] ?? ($currentDayNum === 1 ? ($dayWiseImages[0] ?? []) : []);
                        $assignedHotel = $dayWiseHotels[$currentDayNum] ?? null;

                        // Determine dynamic stay city for this day (handles city transitions e.g. "Jaipur to Udaipur" -> "Udaipur")
                        $stayCity = $item['city'] ?? '';
                        $fullText = $dayRawLabel . ' ' . strip_tags($overview);

                        if (empty($stayCity)) {
                            if (preg_match('/\b(?:to|towards)\s+(Jaipur|Udaipur|Manali|Shimla|Dharamshala|Amritsar|Kovalam|Agra|Delhi|Chandigarh|Kullu|Kochi|Munnar|Thekkady|Alleppey|Jodhpur|Jaisalmer|Srinagar|Gulmarg|Pahalgam|Leh|Ladakh|Gangtok|Darjeeling)\b/i', $fullText, $toMatch)) {
                                $stayCity = ucwords(strtolower($toMatch[1]));
                            } elseif (preg_match('/\b(Jaipur|Udaipur|Manali|Shimla|Dharamshala|Amritsar|Kovalam|Agra|Delhi|Chandigarh|Kullu|Kochi|Munnar|Thekkady|Alleppey|Jodhpur|Jaisalmer|Srinagar|Gulmarg|Pahalgam|Leh|Ladakh|Gangtok|Darjeeling)\b/i', $fullText, $cityMatch)) {
                                $stayCity = ucwords(strtolower($cityMatch[1]));
                            } else {
                                $stayCity = !empty($tour_package_city) ? $tour_package_city : 'Destination';
                            }
                        }
                    ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="day-card open">
                                <div class="day-card-header" onclick="toggleDay(this)">
                                    <span class="day-badge"><?php echo htmlspecialchars(trim($badgeText)); ?></span>
                                    <h3 class="day-title"><?php echo htmlspecialchars(trim($titleText)); ?></h3>
                                    <div class="toggle-icon">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="day-card-body">
                                    <?php if (!empty($overview)): ?>
                                        <div><?php echo $overview; ?></div>
                                    <?php endif; ?>

                                    <!-- Day-Wise Photos -->
                                    <?php if (!empty($dayPhotos)): ?>
                                        <div class="day-gallery-container">
                                            <div class="day-gallery-title">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Day <?php echo $currentDayNum; ?> Photos
                                            </div>
                                            <div class="day-gallery-grid">
                                                <?php foreach ($dayPhotos as $dPhoto): ?>
                                                    <div class="day-gallery-item" onclick="openLightboxUrl('<?php echo htmlspecialchars($dPhoto['url']); ?>')">
                                                        <img src="<?php echo htmlspecialchars($dPhoto['url']); ?>" alt="<?php echo htmlspecialchars($dPhoto['title']); ?>" loading="lazy" />
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Day Stay vs Last Day Farewell Card -->
                                    <?php if ($isLastDay): ?>
                                        <div class="farewell-card">
                                            <div class="farewell-icon">
                                                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="farewell-content">
                                                <h4 class="farewell-title">End of a Magical Journey! ✨</h4>
                                                <p class="farewell-text">
                                                    As your tour comes to a wonderful conclusion, you carry home a treasure of priceless memories, warm smiles, and beautiful stories. Thank you for traveling with <strong><?php echo defined('SITE_NAME') ? SITE_NAME : 'us'; ?></strong>!
                                                </p>
                                                <div class="farewell-tag">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    Wishing You Safe Travels & Happy Journeys Ahead!
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($assignedHotel): ?>
                                            <div class="day-hotel-card assigned-hotel">
                                                <div class="hotel-card-header">
                                                    <div class="hotel-badge-tag">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V5m0 0h2M9 5h2"></path></svg>
                                                        Night Stay in <?php echo htmlspecialchars($stayCity); ?>
                                                    </div>
                                                    <span class="hotel-type-pill"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $assignedHotel['property_type'] ?? 'Hotel'))); ?></span>
                                                </div>

                                                <div class="hotel-card-body">
                                                    <?php 
                                                    $hotelThumb = fixImageUrl($assignedHotel['property_thumbnail'] ?? '');
                                                    if (!empty($hotelThumb)): 
                                                    ?>
                                                    <div class="hotel-thumb-box" onclick="openLightboxUrl('<?php echo htmlspecialchars($hotelThumb); ?>')">
                                                        <img src="<?php echo htmlspecialchars($hotelThumb); ?>" alt="<?php echo htmlspecialchars($assignedHotel['property_title']); ?>" loading="lazy" />
                                                    </div>
                                                    <?php endif; ?>

                                                    <div class="hotel-details-box">
                                                        <h4 class="hotel-name"><?php echo htmlspecialchars($assignedHotel['property_title']); ?></h4>
                                                        
                                                        <?php 
                                                        $locParts = array_filter([$assignedHotel['property_address'] ?? '', $assignedHotel['property_city'] ?? '', $assignedHotel['property_state'] ?? '', $assignedHotel['property_country'] ?? '']);
                                                        if (!empty($locParts)):
                                                        ?>
                                                        <div class="hotel-location">
                                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                            <span><?php echo htmlspecialchars(implode(', ', $locParts)); ?></span>
                                                        </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($assignedHotel['tph_room_type'])): ?>
                                                        <div class="hotel-room-type">
                                                            <strong>Room Type:</strong> <?php echo htmlspecialchars(ucwords($assignedHotel['tph_room_type'])); ?>
                                                        </div>
                                                        <?php endif; ?>

                                                        <?php 
                                                        $hLink = !empty($assignedHotel['link_url']) ? $assignedHotel['link_url'] : ('/hotel/' . $assignedHotel['property_id'] . '/');
                                                        ?>
                                                        <a href="<?php echo htmlspecialchars($hLink); ?>" class="btn-hotel-link" target="_blank" rel="noopener">
                                                            View Hotel Details
                                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="legal-disclaimer">
                                                    * <strong>Note:</strong> Subject to availability at booking. If room at <?php echo htmlspecialchars($assignedHotel['property_title']); ?> is unavailable, an equivalent or similar property in <?php echo htmlspecialchars($stayCity); ?> will be provided.
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php 
                                            $catName = !empty($tour_package_hotel) ? ucwords(str_replace('_', ' ', $tour_package_hotel)) : (!empty($dbPackagePrices[0]['tour_package_price_hotel']) ? $dbPackagePrices[0]['tour_package_price_hotel'] : '3 Star / Deluxe Category Hotel');
                                            ?>
                                            <div class="day-hotel-card category-hotel">
                                                <div class="hotel-card-header">
                                                    <div class="hotel-badge-tag">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V5m0 0h2M9 5h2"></path></svg>
                                                        Night Stay in <?php echo htmlspecialchars($stayCity); ?>
                                                    </div>
                                                    <span class="hotel-type-pill"><?php echo htmlspecialchars($catName); ?></span>
                                                </div>
                                                <div class="hotel-card-body category-body">
                                                    <div class="category-info-text">
                                                        <strong>Overnight Stay in <?php echo htmlspecialchars($stayCity); ?></strong>
                                                        <p><?php echo htmlspecialchars($catName); ?> or similar property.</p>
                                                    </div>
                                                </div>
                                                <div class="legal-disclaimer">
                                                    * <strong>Note:</strong> Accommodation will be provided in <?php echo htmlspecialchars($catName); ?> or a similar property in <?php echo htmlspecialchars($stayCity); ?> subject to availability at the time of booking.
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Inclusions & Exclusions Section -->
        <?php
        $inclusionsList = !empty($tour_package_inclusions) ? array_filter(array_map('trim', explode(',', $tour_package_inclusions))) : [];
        $exclusionsList = !empty($tour_package_exclusions) ? array_filter(array_map('trim', explode(',', $tour_package_exclusions))) : [];

        if (!empty($inclusionsList) || !empty($exclusionsList)):
        ?>
            <section class="luxury-card">
                <div class="section-header">
                    <div class="section-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h2 class="section-title">Inclusions & Exclusions</h2>
                </div>

                <div class="inc-exc-grid">
                    <?php if (!empty($inclusionsList)): ?>
                        <div class="inc-card">
                            <div class="inc-exc-header">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                                Package Inclusions
                            </div>
                            <ul class="inc-exc-list">
                                <?php foreach ($inclusionsList as $inc): ?>
                                    <li>
                                        <div class="list-icon">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span><?php echo htmlspecialchars(ucfirst(strtolower($inc))); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($exclusionsList)): ?>
                        <div class="exc-card">
                            <div class="inc-exc-header">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Package Exclusions
                            </div>
                            <ul class="inc-exc-list">
                                <?php foreach ($exclusionsList as $exc): ?>
                                    <li>
                                        <div class="list-icon">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <span><?php echo htmlspecialchars(ucfirst(strtolower($exc))); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Hotel Details and Package Costs (Dynamic DB Data) -->
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V5m0 0h2M9 5h2"></path>
                    </svg>
                </div>
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
                                    <td><?php echo htmlspecialchars($pTier['tour_package_price_hotel'] ?? ($tour_package_hotel ? ucwords(str_replace('_', ' ', $tour_package_hotel)) : 'Standard Hotel')); ?></td>
                                    <td class="price-highlight">₹ <?php echo number_format((float)($pTier['tour_package_price_selling_adult'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif (!empty($tour_package_price_selling_adult)): ?>
                            <tr>
                                <td><strong>Standard Package</strong></td>
                                <td><?php echo htmlspecialchars(!empty($tour_package_hotel) ? ucwords(str_replace('_', ' ', $tour_package_hotel)) : 'Selected Partner Hotels'); ?></td>
                                <td class="price-highlight">₹ <?php echo number_format((float)$tour_package_price_selling_adult); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><strong>Custom Quotation</strong></td>
                                <td>Selected Partner Hotels</td>
                                <td class="price-highlight">On Request</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Notes and Cancellation Policy -->
        <section class="luxury-card">
            <div class="section-header">
                <div class="section-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="section-title">Notes & Policy Information</h2>
            </div>

            <div class="policy-card">
                <h4>
                    <svg width="18" height="18" fill="none" stroke="var(--color-accent)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Notes
                </h4>
                <ul class="policy-list">
                    <li>Hotels are subject to availability at the time of reservation.</li>
                    <li>We are not responsible for any natural calamity (landslides, bad weather, etc.) during the tour.</li>
                    <li>Vehicle AC will be used only on flat land surfaces, not on hilly areas.</li>
                    <li>Local sightseeing will be covered only if time and situation permit.</li>
                </ul>
            </div>

            <div class="policy-card">
                <h4>
                    <svg width="18" height="18" fill="none" stroke="var(--color-danger)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                    </svg>
                    Cancellation Policy
                </h4>
                <ul class="policy-list">
                    <li>20% cancellation of the package cost after confirmation/issuing the hotel voucher.</li>
                    <li>30% cancellation of the package cost before 10 days of the travel date.</li>
                    <li>50% cancellation of the package cost before 7 days of the travel date.</li>
                    <li>100% cancellation of the package cost before 3 days of the travel date.</li>
                </ul>
            </div>
        </section>

        <!-- Footer -->
        <footer class="luxury-footer">
            <p>&copy; <?php echo date('Y') ?> <strong><?php echo defined('SITE_NAME') ? SITE_NAME : 'Travel' ?></strong>. All rights reserved.</p>
            <div class="credit-strap">
                Powered by <a href="https://aleya.in" target="_blank" rel="noopener"><strong>Aleya</strong></a>
            </div>
        </footer>

    </div>

    <!-- Mobile Sticky Action Bar -->
    <div class="mobile-cta-bar">
        <div class="cta-info">
            <label><?php echo htmlspecialchars($tour_package_title ?? 'Tour Package'); ?></label>
            <span><?php echo isset($tour_package_duration) ? ((int)$tour_package_duration . ' Nights') : 'Custom Tour'; ?></span>
        </div>
        <button onclick="handleSharePackage()" class="btn-cta-gold">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
            Share Package
        </button>
    </div>

    <!-- Full-Screen Lightbox Slider Modal -->
    <div id="lightbox-modal" class="lightbox-modal" onclick="handleLightboxBackdropClick(event)">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <button class="lightbox-nav lightbox-prev" onclick="prevSlide(event)">&lsaquo;</button>

        <div class="lightbox-slide-wrapper">
            <img id="lightbox-img" class="lightbox-content" src="" alt="Enlarged Gallery Photo" />
            <div id="lightbox-caption" class="lightbox-caption-bar"></div>
        </div>

        <button class="lightbox-nav lightbox-next" onclick="nextSlide(event)">&rsaquo;</button>
        <div id="lightbox-counter" class="lightbox-counter-badge">1 / 1</div>
    </div>

    <!-- Desktop Share Modal -->
    <div id="share-modal" class="share-modal" onclick="closeShareModal(event)">
        <div class="share-modal-card">
            <div class="share-modal-header">
                <h3>Share Tour Package</h3>
                <button class="share-modal-close" onclick="closeShareModal(event)">&times;</button>
            </div>
            <p class="share-modal-subtitle">Share this package itinerary with friends, family, or clients:</p>

            <div class="share-options-grid">
                <button class="share-option-btn copy-btn" onclick="copyPackageLink()">
                    <div class="share-icon copy-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span id="copy-btn-text">Copy Link</span>
                </button>

                <a id="share-wa-link" href="#" target="_blank" rel="noopener" class="share-option-btn wa-btn">
                    <div class="share-icon wa-icon">
                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.758.459 3.474 1.33 4.982l-1.413 5.163 5.284-1.385c1.455.793 3.096 1.21 4.782 1.211h.005c5.505 0 9.988-4.479 9.989-9.985 0-2.667-1.037-5.176-2.922-7.062-1.886-1.886-4.394-2.923-7.065-2.923zm5.834 14.162c-.247.692-1.229 1.309-1.996 1.474-.526.113-1.212.203-3.522-.751-2.956-1.223-4.858-4.228-5.006-4.425-.148-.197-1.203-1.603-1.203-3.056 0-1.453.762-2.167 1.032-2.463.27-.296.59-.37.787-.37.197 0 .394.002.566.01.182.008.431-.069.674.514.247.592.838 2.046.912 2.194.074.148.123.321.025.518-.099.197-.148.32-.296.493-.148.173-.311.387-.444.519-.148.148-.302.309-.13.604.173.296.767 1.267 1.649 2.052 1.133 1.01 2.088 1.323 2.384 1.471.296.148.468.123.641-.074.173-.197.739-.863.936-1.159.197-.296.394-.247.665-.148.271.099 1.724.814 2.02 1.011.296.197.493.296.566.419.074.123.074.715-.173 1.407z"/>
                        </svg>
                    </div>
                    <span>WhatsApp</span>
                </a>

                <a id="share-email-link" href="#" class="share-option-btn email-btn">
                    <div class="share-icon email-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span>Email</span>
                </a>

                <a id="share-fb-link" href="#" target="_blank" rel="noopener" class="share-option-btn fb-btn">
                    <div class="share-icon fb-icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <span>Facebook</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Interactive JS -->
    <script>
        const galleryItemsList = <?php echo json_encode($allSliderPhotos); ?>;
        let currentImageIndex = 0;

        function handleSharePackage() {
            const packageTitle = <?php echo json_encode($tour_package_title ?? 'Tour Package'); ?>;
            const currentUrl = window.location.href;

            // Use native share on mobile devices if supported
            if (navigator.share && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                navigator.share({
                    title: packageTitle,
                    text: 'Check out this tour package: ' + packageTitle,
                    url: currentUrl
                }).catch(err => {
                    console.log('Native share dismissed, opening share modal fallback');
                    openShareModal();
                });
            } else {
                openShareModal();
            }
        }

        function openShareModal() {
            const currentUrl = window.location.href;
            const packageTitle = <?php echo json_encode($tour_package_title ?? 'Tour Package'); ?>;

            document.getElementById('share-wa-link').href = `https://api.whatsapp.com/send?text=${encodeURIComponent('Check out this tour package: ' + packageTitle + ' ' + currentUrl)}`;
            document.getElementById('share-email-link').href = `mailto:?subject=${encodeURIComponent(packageTitle)}&body=${encodeURIComponent('Hi,\n\nCheck out this tour package itinerary:\n' + currentUrl)}`;
            document.getElementById('share-fb-link').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`;

            document.getElementById('share-modal').classList.add('active');
        }

        function closeShareModal(e) {
            if (!e || e.target.id === 'share-modal' || e.target.classList.contains('share-modal-close')) {
                document.getElementById('share-modal').classList.remove('active');
            }
        }

        function copyPackageLink() {
            const url = window.location.href;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    const btnText = document.getElementById('copy-btn-text');
                    btnText.innerText = 'Copied!';
                    setTimeout(() => { btnText.innerText = 'Copy Link'; }, 2000);
                }).catch(() => { fallbackCopyText(url); });
            } else {
                fallbackCopyText(url);
            }
        }

        function fallbackCopyText(url) {
            const dummy = document.createElement('input');
            document.body.appendChild(dummy);
            dummy.value = url;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            const btnText = document.getElementById('copy-btn-text');
            btnText.innerText = 'Copied!';
            setTimeout(() => { btnText.innerText = 'Copy Link'; }, 2000);
        }

        function openLightboxIndex(index) {
            if (!galleryItemsList || galleryItemsList.length === 0) return;
            currentImageIndex = index;
            if (currentImageIndex < 0) currentImageIndex = 0;
            if (currentImageIndex >= galleryItemsList.length) currentImageIndex = galleryItemsList.length - 1;

            updateLightboxSlide();
            document.getElementById('lightbox-modal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function openLightboxUrl(url) {
            if (!galleryItemsList) return;
            const foundIndex = galleryItemsList.findIndex(item => item.url === url);
            if (foundIndex !== -1) {
                openLightboxIndex(foundIndex);
            } else {
                document.getElementById('lightbox-img').src = url;
                document.getElementById('lightbox-caption').innerText = '';
                document.getElementById('lightbox-counter').innerText = '';
                document.getElementById('lightbox-modal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function updateLightboxSlide() {
            const item = galleryItemsList[currentImageIndex];
            if (!item) return;
            const img = document.getElementById('lightbox-img');
            const caption = document.getElementById('lightbox-caption');
            const counter = document.getElementById('lightbox-counter');

            img.src = item.url;
            caption.innerText = item.title || '';
            counter.innerText = `${currentImageIndex + 1} / ${galleryItemsList.length}`;
        }

        function prevSlide(e) {
            if (e) e.stopPropagation();
            if (!galleryItemsList || galleryItemsList.length <= 1) return;
            currentImageIndex = (currentImageIndex - 1 + galleryItemsList.length) % galleryItemsList.length;
            updateLightboxSlide();
        }

        function nextSlide(e) {
            if (e) e.stopPropagation();
            if (!galleryItemsList || galleryItemsList.length <= 1) return;
            currentImageIndex = (currentImageIndex + 1) % galleryItemsList.length;
            updateLightboxSlide();
        }

        function closeLightbox() {
            document.getElementById('lightbox-modal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function handleLightboxBackdropClick(e) {
            if (e.target.id === 'lightbox-modal' || e.target.classList.contains('lightbox-close')) {
                closeLightbox();
            }
        }

        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('lightbox-modal');
            if (modal.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    prevSlide();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                } else if (e.key === 'Escape') {
                    closeLightbox();
                }
            }

            const shareModal = document.getElementById('share-modal');
            if (shareModal.classList.contains('active') && e.key === 'Escape') {
                closeShareModal();
            }
        });

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

        function toggleDay(headerElement) {
            const dayCard = headerElement.closest('.day-card');
            dayCard.classList.toggle('open');
            const body = dayCard.querySelector('.day-card-body');
            if (body) {
                body.style.display = dayCard.classList.contains('open') ? 'block' : 'none';
            }
        }
    </script>

</body>

</html>