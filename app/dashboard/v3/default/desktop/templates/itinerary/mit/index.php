<?php

/**
 * V3 Itinerary Template: MIT Multi-Page Proposal Layout
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/mit/index.php
 * Description: MIT-style multi-page travel proposal layout with dynamic wave hero headers,
 *              A4-dimension pages, day-wise cards with cycling gradients, inclusions/exclusions,
 *              company footer decor, and high-fidelity PDF download matching download-itinerary-mit.php.
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
            // Replace all kinds of line breaks, tags, and commas with newlines to shift each item to its own line
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

if (!function_exists('cleanProposalLabel')) {
    function cleanProposalLabel($val)
    {
        if (empty($val)) return '';
        $clean = str_replace(['_', '-'], ' ', (string)$val);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return ucwords(strtolower(trim($clean)));
    }
}

// Dynamic Site & Company Info
$siteName    = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Musafir India Trip');
$sitePhone   = defined('SITE_MOBILE') ? SITE_MOBILE : (defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? ''));
$siteEmail   = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress = defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? '');
$siteUrl     = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteDomain  = defined('SITE_DOMAIN') ? SITE_DOMAIN : ($company_site_domain ?? parse_url($siteUrl, PHP_URL_HOST) ?? 'musafirindiatrip.com');
$siteLogo    = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));
$themeColor  = defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#e76317';
$bgColor     = defined('SITE_BACKGROUND_COLOUR') ? SITE_BACKGROUND_COLOUR : '#26679c';

// Package Variables
$pkgTitle       = $tour_package_title ?? ($itinerary_title ?? ($title ?? 'Tour Package Itinerary'));
$pkgCity        = $tour_package_city ?? ($city ?? 'Destination');
$pkgState       = $tour_package_state ?? '';
$pkgCountry     = $tour_package_country ?? '';
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

if (is_string($pkgDays)) {
    $pkgDays = json_decode($pkgDays, true) ?: [];
}

$heroFeatureImage = fixImageUrl($tour_package_image ?? ($gallery_image ?? ($featureImage ?? '')));

$totalDaysCount = !empty($pkgDays) ? count($pkgDays) : 1;
$totalNightsCount = max(1, $totalDaysCount - 1);
if (!empty($pkgDuration)) {
    $totalNightsCount = (int)$pkgDuration;
    $totalDaysCount = $totalNightsCount + 1;
}

// Comma-separated or tag-separated items split into discrete lines
$incList = parseBulletList($pkgInclusions);
$excList = parseBulletList($pkgExclusions);

// Format cleaner overview
$cleanOverview = trim((string)$pkgOverview);
if (!empty($cleanOverview) && strpos($cleanOverview, '<') === false) {
    $cleanOverview = '<p>' . nl2br(htmlspecialchars($cleanOverview)) . '</p>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($pkgTitle . ' | ' . $siteName); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --primary-color: <?php echo htmlspecialchars($themeColor); ?>;
            --secondary-color: #fb923c;
            --dark-text: #111111;
            --medium-text: #333333;
            --light-text: #666666;
            --light-gray: #767676;
            --background: <?php echo htmlspecialchars($bgColor); ?>;
            --white: #ffffff;
            --accent-success: #22c55e;
            --accent-error: #ff4d4f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            background: #eef2f6;
            color: var(--dark-text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding: 24px 0 60px;
        }

        /* Top Action Bar */
        .v3-pdf-actions-bar {
            position: fixed;
            top: 16px;
            right: 20px;
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 8px 14px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15);
            border: 1px solid #e2e8f0;
        }

        .btn-pdf-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-pdf-download {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
        }

        .btn-pdf-download:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-pdf-print {
            background: #ffffff;
            color: var(--dark-text);
            border: 1px solid #cbd5e1;
        }

        .btn-pdf-print:hover {
            background: #f8fafc;
        }

        /* A4 Page Configuration */
        .page {
            position: relative;
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            height: 297mm;
            margin: 0 auto 30px auto;
            background: #ffffff;
            page-break-before: always;
            page-break-after: always;
            page-break-inside: avoid;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        .page:first-child {
            page-break-before: auto;
        }

        /* Hero Section - 25-30% of A4 height */
        .hero {
            position: relative;
            width: 100%;
            min-height: 74.25mm;
            max-height: 89.1mm;
            height: 89.1mm;
            overflow: visible;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .refid {
            position: absolute;
            bottom: 24px;
            right: 24px;
            font-size: 0.85rem;
            color: #fff;
            padding: 6px 14px;
            border-radius: 8px;
            background: var(--secondary-color);
            font-weight: 700;
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        /* Default Day Image Fallback */
        .days-page .hero .default-day-image {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            text-align: center;
            color: #fff;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            position: relative;
            overflow: hidden;
        }

        .days-page .hero .default-day-image h2 {
            font-size: 4em;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 6px 20px rgba(0, 0, 0, 0.35), 0 2px 6px rgba(0, 0, 0, 0.2);
            padding: 0 40px;
            box-sizing: border-box;
        }

        /* Hero Wave Overlay */
        .hero-wave-container {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 42px;
            z-index: 5;
            overflow: hidden;
            pointer-events: none;
        }

        .hero-wave-container svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Cycle gradients per day matching download-itinerary-mit.php */
        .days-page:nth-of-type(1) .hero .default-day-image { background: linear-gradient(135deg, #5e1ba9, #c2185b); }
        .days-page:nth-of-type(2) .hero .default-day-image { background: linear-gradient(135deg, #1565c0, #0277bd); }
        .days-page:nth-of-type(3) .hero .default-day-image { background: linear-gradient(135deg, #0288d1, #7b1fa2); }
        .days-page:nth-of-type(4) .hero .default-day-image { background: linear-gradient(135deg, #00796b, #558b2f); }
        .days-page:nth-of-type(5) .hero .default-day-image { background: linear-gradient(135deg, #e65100, #c62828); }
        .days-page:nth-of-type(6) .hero .default-day-image { background: linear-gradient(135deg, #c2185b, #6a1b9a); }
        .days-page:nth-of-type(7) .hero .default-day-image { background: linear-gradient(135deg, #00695c, #004d40); }
        .days-page:nth-of-type(8) .hero .default-day-image { background: linear-gradient(135deg, #1565c0, #1976d2); }
        .days-page:nth-of-type(9) .hero .default-day-image { background: linear-gradient(135deg, #0277bd, #00838f); }
        .days-page:nth-of-type(10) .hero .default-day-image { background: linear-gradient(135deg, #6a1b9a, #1565c0); }

        .greenGradient { background: linear-gradient(135deg, #00796b, #558b2f) !important; }
        .redGradient { background: linear-gradient(135deg, #e65100, #c62828) !important; }

        /* Container Layout */
        .container {
            width: 100%;
            padding: 20px 45px;
            max-height: calc(297mm - 89.1mm - 55mm);
            overflow: hidden;
        }

        /* Title Section */
        .title-section {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            text-align: center;
        }

        .title-section h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark-text);
            letter-spacing: 0.2px;
            padding: 0 14px;
            line-height: 1.3;
        }

        .line-left,
        .line-right {
            color: var(--secondary-color);
            font-size: 36px;
            font-weight: 800;
        }

        /* Stats Grid */
        .stats {
            margin-top: 24px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            justify-content: center;
            text-align: center;
            flex-wrap: wrap;
        }

        .stats .box {
            background: var(--primary-color);
            width: 180px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px 14px;
            border-radius: 10px;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stats .box p:first-child {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }

        .stats .box p:last-child {
            font-size: 1.05rem;
            font-weight: 800;
        }

        /* Top Heading & Overview */
        .top-section {
            margin-top: 10px;
            padding: 0 10px;
        }

        .top-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .top-heading .bar {
            color: var(--secondary-color);
            font-size: 24px;
            font-weight: bold;
        }

        .top-heading h2 {
            margin: 0;
            font-size: 22px;
            color: var(--dark-text);
            font-weight: 800;
        }

        .paragraph {
            font-size: 15px;
            line-height: 1.7;
            color: var(--medium-text);
        }

        /* Day Page Content */
        .day-header {
            position: relative;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin: 20px 0 16px 0;
            padding-bottom: 10px;
        }

        .day-header::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 999px;
            opacity: 0.7;
        }

        .day-number-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .day-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--dark-text);
            text-transform: capitalize;
            letter-spacing: 0.3px;
        }

        .day-content {
            color: var(--medium-text);
            font-size: 15px;
            line-height: 1.7;
        }

        .day-stay-box {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            background: #f1f8ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 13px;
            font-weight: 700;
        }

        /* Inclusions / Exclusions */
        .climax-section {
            width: 100%;
            margin-top: 10px;
        }

        .tour_info_wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .tour_info_card {
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .tour_included {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 5px solid var(--accent-success);
        }

        .tour_excluded {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 5px solid var(--accent-error);
        }

        .tour_info_title {
            font-size: 18px;
            font-weight: 800;
            color: var(--dark-text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tour_info_list {
            list-style: none;
            padding: 0;
        }

        .tour_info_list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            padding: 8px 12px;
            margin-bottom: 6px;
            border-radius: 6px;
            line-height: 1.5;
            color: var(--medium-text);
            background: rgba(255, 255, 255, 0.7);
        }

        .tour_check {
            color: var(--accent-success);
            font-weight: bold;
            font-size: 16px;
            width: 22px;
            height: 22px;
            background: rgba(34, 197, 94, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tour_cross {
            color: var(--accent-error);
            font-weight: bold;
            font-size: 16px;
            width: 22px;
            height: 22px;
            background: rgba(255, 77, 79, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Company & Contact Card */
        .company-contact-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-top: 20px;
        }

        .company-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-top: 14px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: #334155;
        }

        .contact-item i {
            font-size: 18px;
            color: var(--primary-color);
        }

        /* Page Footer */
        .page-footer-decor {
            position: absolute;
            bottom: 16px;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 0 45px;
            box-sizing: border-box;
        }

        .bottom-decor {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        .center-logo {
            max-height: 38px;
            width: auto;
            object-fit: contain;
        }

        .bottom-domain {
            font-size: 11px;
            color: #64748b;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .bottom-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .v3-pdf-actions-bar {
                display: none !important;
            }

            .page {
                width: 210mm;
                height: 297mm;
                min-height: 297mm;
                margin: 0;
                padding: 0;
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
                box-shadow: none;
                border-radius: 0;
            }

            .page:first-child {
                page-break-before: auto;
            }

            .hero {
                min-height: 74.25mm;
                max-height: 89.1mm;
                height: 89.1mm;
                page-break-inside: avoid;
            }

            .hero img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        }
    </style>
</head>

<body>

    <!-- Floating Top Action Bar -->
    <div class="v3-pdf-actions-bar">
        <button type="button" class="btn-pdf-action btn-pdf-download" id="btnDownloadPDF" onclick="downloadItineraryPDF()">
            <i class="ri-file-pdf-line"></i> Download PDF
        </button>
        <button type="button" class="btn-pdf-action btn-pdf-print" onclick="window.print()">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>

    <div id="pdfContentWrapper">

        <!-- PAGE 1: COVER & OVERVIEW -->
        <section class="page" id="page-1">
            <header class="hero">
                <?php if (!empty($heroFeatureImage)): ?>
                    <img src="<?php echo htmlspecialchars($heroFeatureImage); ?>" alt="<?php echo htmlspecialchars($pkgTitle); ?>">
                <?php else: ?>
                    <div class="default-day-image">
                        <h2><?php echo htmlspecialchars($pkgCity); ?></h2>
                    </div>
                <?php endif; ?>
                <div class="refid">
                    Ref. id: #<?php echo htmlspecialchars((string)$quoteRef); ?>
                </div>
            </header>

            <div class="container">
                <div class="title-section">
                    <span class="line-left">|</span>
                    <h1><?php echo htmlspecialchars($pkgTitle); ?></h1>
                    <span class="line-right">|</span>
                </div>

                <div class="stats">
                    <div class="box">
                        <p>Travel Duration</p>
                        <p><strong><?php echo $totalNightsCount; ?>N / <?php echo $totalDaysCount; ?>D</strong></p>
                    </div>

                    <div class="box">
                        <p>Location</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgCity)); ?></strong></p>
                    </div>

                    <div class="box">
                        <p>Theme / Style</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgTheme)); ?></strong></p>
                    </div>

                    <?php if (!empty($pkgHotel)): ?>
                        <div class="box">
                            <p>Stay Option</p>
                            <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgHotel)); ?></strong></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="top-section">
                    <div class="top-heading">
                        <span class="bar">|</span>
                        <h2>Tour Overview</h2>
                    </div>
                    <div class="paragraph">
                        <?php echo $cleanOverview ?: '<p>Experience a memorable and delightful journey customized for you.</p>'; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- DAY-WISE PAGES (1 PAGE PER DAY) -->
        <?php
        $dayIndex = 1;
        foreach ($pkgDays as $day):
            $dayNumStr = str_pad($dayIndex, 2, '0', STR_PAD_LEFT);
            $dayTitle = $day['day'] ?? ($day['title'] ?? "Day {$dayIndex}");
            // Strip out "Day X - " prefix if present
            $titleParts = explode(' - ', $dayTitle, 2);
            $cleanTitle = isset($titleParts[1]) ? trim($titleParts[1]) : trim($dayTitle);

            $dayImg = fixImageUrl($day['feature_image'] ?? ($day['image'] ?? ''));
            if (empty($dayImg) && !empty($gallery_image_list)) {
                $galList = is_string($gallery_image_list) ? json_decode($gallery_image_list, true) : $gallery_image_list;
                if (is_array($galList) && isset($galList[$dayIndex - 1]['path'])) {
                    $dayImg = fixImageUrl($galList[$dayIndex - 1]['path']);
                }
            }
            $dayOverview = $day['overview'] ?? ($day['description'] ?? '');
            $dayStay = $day['stay'] ?? '';
        ?>
            <section class="page days-page" id="page-day-<?php echo $dayIndex; ?>">
                <header class="hero">
                    <?php if (!empty($dayImg)): ?>
                        <img src="<?php echo htmlspecialchars($dayImg); ?>" alt="<?php echo htmlspecialchars($cleanTitle); ?>">
                    <?php else: ?>
                        <div class="default-day-image">
                            <h2>Day <?php echo $dayNumStr; ?></h2>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="container">
                    <div class="day-header">
                        <div class="day-number-badge">Day <?php echo $dayNumStr; ?></div>
                        <h2 class="day-title"><?php echo htmlspecialchars($cleanTitle); ?></h2>
                    </div>

                    <div class="day-content">
                        <?php echo !empty($dayOverview) ? (strpos($dayOverview, '<') !== false ? $dayOverview : nl2br(htmlspecialchars($dayOverview))) : '<p>Sightseeing, transfers, and exploration according to custom schedule.</p>'; ?>
                    </div>

                    <?php if (!empty($dayStay)): ?>
                        <div class="day-stay-box">
                            <i class="ri-hotel-bed-line"></i> Overnight Stay: <?php echo htmlspecialchars(cleanProposalLabel($dayStay)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php
            $dayIndex++;
        endforeach;
        ?>

        <!-- INCLUSIONS PAGE -->
        <?php if (!empty($incList)): ?>
            <section class="page days-page" id="page-inclusions">
                <header class="hero">
                    <div class="default-day-image greenGradient">
                        <h2>Inclusions</h2>
                    </div>
                </header>

                <div class="container">
                    <div class="climax-section">
                        <div class="tour_info_wrapper">
                            <div class="tour_info_card tour_included">
                                <h3 class="tour_info_title">
                                    <i class="ri-check-double-line text-green-600"></i> What's Included in Your Package:
                                </h3>
                                <ul class="tour_info_list">
                                    <?php foreach ($incList as $inc): if (empty($inc)) continue; ?>
                                        <li>
                                            <div class="tour_check"><i class="ri-check-line"></i></div>
                                            <span><?php echo htmlspecialchars(ucfirst($inc)); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- EXCLUSIONS PAGE -->
        <?php if (!empty($excList)): ?>
            <section class="page days-page" id="page-exclusions">
                <header class="hero">
                    <div class="default-day-image redGradient">
                        <h2>Exclusions</h2>
                    </div>
                </header>

                <div class="container">
                    <div class="climax-section">
                        <div class="tour_info_wrapper">
                            <div class="tour_info_card tour_excluded">
                                <h3 class="tour_info_title">
                                    <i class="ri-close-circle-line text-red-600"></i> Not Included in Your Package:
                                </h3>
                                <ul class="tour_info_list">
                                    <?php foreach ($excList as $exc): if (empty($exc)) continue; ?>
                                        <li>
                                            <div class="tour_cross"><i class="ri-close-line"></i></div>
                                            <span><?php echo htmlspecialchars(ucfirst($exc)); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- COMPANY & CONTACT DETAILS PAGE -->
        <section class="page days-page" id="page-contact">
            <header class="hero">
                <div class="default-day-image">
                    <h2>Important Info</h2>
                </div>
            </header>

            <div class="container">
                <div class="top-heading">
                    <span class="bar">|</span>
                    <h2>Company &amp; Reservation Support</h2>
                </div>

                <div class="company-contact-card">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 6px; color: var(--dark-text);">
                        <?php echo htmlspecialchars($siteName); ?>
                    </h3>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 14px;">
                        Authorized Travel Partner &amp; Destination Management Specialist
                    </p>

                    <div class="company-contact-grid">
                        <?php if (!empty($sitePhone)): ?>
                            <div class="contact-item">
                                <i class="ri-phone-line"></i>
                                <span><strong>Phone:</strong> <?php echo htmlspecialchars($sitePhone); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($siteEmail)): ?>
                            <div class="contact-item">
                                <i class="ri-mail-line"></i>
                                <span><strong>Email:</strong> <?php echo htmlspecialchars($siteEmail); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($siteUrl) || !empty($siteDomain)): ?>
                            <div class="contact-item">
                                <i class="ri-global-line"></i>
                                <span><strong>Website:</strong> <?php echo htmlspecialchars($siteDomain); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pkgOutVehicle)): ?>
                            <div class="contact-item">
                                <i class="ri-car-line"></i>
                                <span><strong>Vehicle:</strong> <?php echo htmlspecialchars($pkgOutVehicle); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Procedural Wave Generator & Page Number Injection Script -->
    <script>
        function initMitTemplateEffects() {
            const pages = document.querySelectorAll('.page');
            const totalPages = pages.length;

            // 1. Inject bottom decor & page numbering footer on every A4 page
            pages.forEach((page, index) => {
                if (page.querySelector('.page-footer-decor')) return;
                const footer = document.createElement('div');
                footer.className = 'page-footer-decor';
                const pageNumber = "Page " + (index + 1) + " of " + totalPages;
                
                footer.innerHTML = `
                    <div class="bottom-decor">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogo); ?>" class="center-logo" alt="<?php echo htmlspecialchars($siteName); ?>">
                        <?php endif; ?>
                        <span class="bottom-domain"><?php echo htmlspecialchars($siteDomain); ?></span>
                    </div>
                    <div class="bottom-meta-row">
                        <span>Powered by Aleya.in</span>
                        <span>${pageNumber}</span>
                    </div>
                `;
                page.appendChild(footer);
            });

            // 2. Smooth Procedural Bezier Wave Generator
            function generateWavePath() {
                const steps = 25 + Math.floor(Math.random() * 15);
                const points = [];

                for (let i = 0; i <= steps; i++) {
                    const progress = i / steps;
                    const x = progress * 100;

                    const peakFreq1 = 1.5 + Math.random() * 2.5;
                    const peakFreq2 = 2 + Math.random() * 3;
                    const amplitude1 = 8 + Math.random() * 18;
                    const amplitude2 = 5 + Math.random() * 12;

                    const wave1 = Math.sin(progress * Math.PI * peakFreq1) * amplitude1;
                    const wave2 = Math.cos(progress * Math.PI * peakFreq2) * amplitude2;
                    const baseHeight = 22 + Math.random() * 8;
                    const y = Math.max(4, Math.min(52, baseHeight + wave1 + wave2));

                    points.push({ x, y });
                }

                let pathData = 'M 0,100 L 0,100';
                for (let i = 0; i < points.length; i++) {
                    if (i === 0) {
                        pathData += ` L ${points[i].x.toFixed(2)},${points[i].y.toFixed(2)}`;
                    } else {
                        const prev = points[i - 1];
                        const curr = points[i];
                        const tension = 0.35;
                        const dx = curr.x - prev.x;
                        const cp1x = prev.x + dx * tension;
                        const cp1y = prev.y;
                        const cp2x = curr.x - dx * tension;
                        const cp2y = curr.y;
                        pathData += ` C ${cp1x.toFixed(2)},${cp1y.toFixed(2)} ${cp2x.toFixed(2)},${cp2y.toFixed(2)} ${curr.x.toFixed(2)},${curr.y.toFixed(2)}`;
                    }
                }
                pathData += ' L 100,100 Z';
                return pathData;
            }

            // Apply SVG wave overlay to all .hero headers
            const heroElements = document.querySelectorAll('.hero');
            heroElements.forEach((hero) => {
                if (hero.querySelector('.hero-wave-container')) return;
                const waveContainer = document.createElement('div');
                waveContainer.className = 'hero-wave-container';

                const wavePath = generateWavePath();
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 100 100');
                svg.setAttribute('preserveAspectRatio', 'none');

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', wavePath);
                path.setAttribute('fill', '#ffffff');
                path.setAttribute('stroke', 'none');

                svg.appendChild(path);
                waveContainer.appendChild(svg);
                hero.appendChild(waveContainer);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener("DOMContentLoaded", initMitTemplateEffects);
        } else {
            initMitTemplateEffects();
        }

        // 3. Client-side PDF Exporter Function
        function downloadItineraryPDF() {
            const btn = document.getElementById('btnDownloadPDF');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Generating PDF...';
            }

            const element = document.getElementById('pdfContentWrapper');
            const opt = {
                margin: 0,
                filename: '<?php echo preg_replace('/[^a-zA-Z0-9_-]/', '_', $pkgTitle); ?>_Itinerary.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-pdf-line"></i> Download PDF';
                }
            }).catch((err) => {
                console.error(err);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-pdf-line"></i> Download PDF';
                }
                window.print();
            });
        }
    </script>
</body>

</html>