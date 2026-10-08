<?php
/**
 * V3 Itinerary Template: Modern Card Layout
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/modern.php
 * Description: Modern card layout with gradient banners, day photo cards, stay cards, dynamic company contact, and A4 PDF export.
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

// Dynamic Site & Company Info
$siteName    = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Travel Partner');
$sitePhone   = defined('SITE_MOBILE') ? SITE_MOBILE : (defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? ''));
$siteEmail   = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress = defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? '');
$siteUrl     = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteLogo    = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));

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

$incList = parseBulletList($pkgInclusions);
$excList = parseBulletList($pkgExclusions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($siteName . ' - ' . $pkgTitle); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --mod-primary: #4f46e5;
            --mod-secondary: #7c3aed;
            --mod-dark: #0f172a;
            --mod-text: #1e293b;
            --mod-muted: #64748b;
            --mod-border: #e2e8f0;
            --mod-bg: #f8fafc;
            --mod-card: #ffffff;
            --radius-md: 16px;
            --radius-sm: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--mod-bg);
            color: var(--mod-text);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 50px;
        }

        .v3-pdf-actions-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            padding: 8px 14px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15);
            border: 1px solid var(--mod-border);
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
            background: linear-gradient(135deg, var(--mod-primary), var(--mod-secondary));
            color: #ffffff;
        }

        .btn-pdf-download:hover { opacity: 0.92; transform: translateY(-1px); }

        .btn-pdf-print {
            background: #ffffff;
            color: var(--mod-text);
            border: 1px solid var(--mod-border);
        }

        .modern-wrapper {
            max-width: 1060px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: var(--mod-card);
            border-radius: var(--radius-md);
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            border: 1px solid var(--mod-border);
            margin-bottom: 24px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--mod-dark);
            font-size: 20px;
            font-weight: 800;
        }

        .brand-logo-img { max-height: 46px; width: auto; object-fit: contain; }

        .brand-badge {
            background: #eef2ff;
            color: var(--mod-primary);
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
        }

        .hero-card {
            min-height: 400px;
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 34px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.15);
        }

        .hero-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(30, 27, 75, 0.2) 0%, rgba(30, 27, 75, 0.9) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            background: linear-gradient(135deg, var(--mod-primary), var(--mod-secondary));
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .hero-title {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 16px;
        }

        .hero-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero-pill {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .mod-card {
            background: var(--mod-card);
            border-radius: var(--radius-md);
            padding: 28px;
            border: 1px solid var(--mod-border);
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--mod-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--mod-dark);
        }

        /* Modern Day Cards Grid */
        .day-cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .day-block {
            background: var(--mod-card);
            border: 1px solid var(--mod-border);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .day-block-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border-bottom: 1px solid var(--mod-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .day-badge-num {
            background: var(--mod-primary);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .day-block-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--mod-dark);
            flex: 1;
            margin-left: 12px;
        }

        .day-block-body {
            padding: 20px;
            font-size: 14px;
            color: #334155;
            line-height: 1.7;
        }

        .day-img-holder {
            margin: 14px 0;
            height: 200px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--mod-border);
        }

        .day-img-holder img { width: 100%; height: 100%; object-fit: cover; }

        .stay-block {
            margin-top: 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stay-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #dcfce7;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .farewell-block {
            margin-top: 16px;
            background: #fdf4ff;
            border: 1px solid #f5d0fe;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .farewell-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #fae8ff;
            color: #a21caf;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* Inclusions / Exclusions */
        .inc-exc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .inc-col, .exc-col {
            border-radius: var(--radius-md);
            padding: 20px;
        }

        .inc-col { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .exc-col { background: #fef2f2; border: 1px solid #fecaca; }

        .inc-exc-title {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .inc-col .inc-exc-title { color: #16a34a; }
        .exc-col .inc-exc-title { color: #dc2626; }

        .inc-exc-list { list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #334155; }
        .inc-col .inc-exc-list li i { color: #16a34a; margin-right: 4px; }
        .exc-col .inc-exc-list li i { color: #dc2626; margin-right: 4px; }

        /* Company Contact Card */
        .company-contact-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            border-radius: var(--radius-md);
            padding: 30px;
            color: #ffffff;
            margin-bottom: 24px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .contact-item {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-sm);
            padding: 14px;
        }

        .contact-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #c7d2fe;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .contact-val {
            font-size: 13.5px;
            font-weight: 600;
            color: #ffffff;
        }

        .contact-val a { color: #a5b4fc; text-decoration: none; }

        .site-footer {
            text-align: center;
            font-size: 12.5px;
            color: var(--mod-muted);
            padding-top: 14px;
        }

        .credit-note {
            margin-top: 6px;
            font-size: 12px;
        }

        .credit-note a { color: var(--mod-primary); text-decoration: none; font-weight: 700; }

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
            .no-print {
                display: none !important;
            }
            .modern-wrapper {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .mod-card,
            .hero-card,
            .company-contact-card,
            .day-block,
            .stay-pill-card,
            .inc-exc-grid {
                box-shadow: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 16px !important;
                border: 1px solid #e2e8f0 !important;
            }
        }
    </style>
</head>
<body>

    <?php if (empty($hideActions) && empty($isPreview)): ?>
    <div class="v3-pdf-actions-bar">
        <button type="button" class="btn-pdf-action btn-pdf-download" id="btnDownloadModernPDF">
            <i class="ri-file-download-line"></i> Download Itinerary PDF
        </button>
        <button type="button" class="btn-pdf-action btn-pdf-print" onclick="window.print()">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>
    <?php endif; ?>

    <div class="modern-wrapper" id="itineraryDocument">

        <header class="site-header">
            <a href="<?php echo htmlspecialchars(!empty($siteUrl) ? $siteUrl : '#'); ?>" class="brand-logo">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="brand-logo-img" />
                <?php else: ?>
                    <span><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </a>
            <div class="brand-badge">Quote Ref: #<?php echo htmlspecialchars((string)$quoteRef); ?></div>
        </header>

        <section class="hero-card">
            <?php if (!empty($heroFeatureImage)): ?>
                <img src="<?php echo htmlspecialchars($heroFeatureImage); ?>" alt="<?php echo htmlspecialchars($pkgTitle); ?>" />
                <div class="hero-gradient"></div>
            <?php endif; ?>
            <div class="hero-content">
                <div class="hero-badge"><i class="ri-sparkling-fill"></i> Modern Holiday Package</div>
                <h1 class="hero-title"><?php echo htmlspecialchars($pkgTitle); ?></h1>
                <div class="hero-pills">
                    <span class="hero-pill"><i class="ri-time-line"></i> <?php echo $totalNightsCount; ?>N / <?php echo $totalDaysCount; ?>D</span>
                    <span class="hero-pill"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($pkgCity); ?></span>
                    <span class="hero-pill"><i class="ri-compass-3-line"></i> <?php echo htmlspecialchars(ucfirst($pkgTheme)); ?></span>
                    <?php if (!empty($pkgPriceAdult)): ?>
                        <span class="hero-pill" style="background: rgba(79, 70, 229, 0.5);"><i class="ri-money-rupee-circle-line"></i> ₹ <?php echo number_format((float)$pkgPriceAdult); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if (!empty($pkgOverview)): ?>
        <section class="mod-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-information-line"></i></div>
                <h2 class="section-title">Tour Overview</h2>
            </div>
            <div style="font-size: 14px; color: #334155; line-height: 1.8;">
                <?php echo $pkgOverview; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="mod-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-calendar-todo-line"></i></div>
                <h2 class="section-title">Day-Wise Itinerary</h2>
            </div>

            <div class="day-cards-grid">
                <?php 
                $dayIdx = 1;
                foreach ($pkgDays as $d): 
                    $dayLabel = is_array($d) ? ($d['day'] ?? ($d['title'] ?? "Day $dayIdx")) : "Day $dayIdx";
                    $overview = is_array($d) ? ($d['overview'] ?? ($d['content'] ?? '')) : '';
                    $isHotelHidden = is_array($d) && (
                        (isset($d['show_hotel']) && ($d['show_hotel'] === 0 || $d['show_hotel'] === '0' || $d['show_hotel'] === false)) ||
                        !empty($d['hide_hotel']) ||
                        !empty($d['is_hotel_hidden'])
                    );
                    $stay = (!$isHotelHidden && is_array($d)) ? ($d['stay'] ?? ($d['hotel'] ?? '')) : '';
                    $img = is_array($d) ? fixImageUrl($d['feature_image'] ?? ($d['image'] ?? '')) : '';
                    $isLast = ($dayIdx === $totalDaysCount);
                ?>
                    <div class="day-block">
                        <div class="day-block-header">
                            <span class="day-badge-num">Day <?php echo $dayIdx; ?></span>
                            <h3 class="day-block-title"><?php echo htmlspecialchars($dayLabel); ?></h3>
                        </div>
                        <div class="day-block-body">
                            <div><?php echo !empty($overview) ? $overview : '<p>Sightseeing and transfers as scheduled.</p>'; ?></div>

                            <?php if (!empty($img)): ?>
                                <div class="day-img-holder">
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($dayLabel); ?>" />
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($stay)): ?>
                                <div class="stay-block">
                                    <div class="stay-icon"><i class="ri-hotel-bed-line"></i></div>
                                    <div>
                                        <div style="font-size: 13.5px; font-weight: 700; color: #166534;"><?php echo htmlspecialchars($stay); ?></div>
                                        <div style="font-size: 12px; color: #15803d;">Overnight Stay in <?php echo htmlspecialchars($pkgCity); ?></div>
                                    </div>
                                </div>
                            <?php elseif ($isLast): ?>
                                <div class="farewell-block">
                                    <div class="farewell-icon-box"><i class="ri-flight-takeoff-line"></i></div>
                                    <div>
                                        <div style="font-size: 13.5px; font-weight: 700; color: #86198f;">End of a Wonderful Trip! ✨</div>
                                        <div style="font-size: 12px; color: #a21caf;">Transfer for onward journey. Thank you for choosing us!</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    $dayIdx++;
                endforeach; 
                ?>
            </div>
        </section>

        <?php if (!empty($incList) || !empty($excList)): ?>
        <section class="mod-card">
            <div class="section-header">
                <div class="section-icon"><i class="ri-shield-check-line"></i></div>
                <h2 class="section-title">Inclusions & Exclusions</h2>
            </div>
            <div class="inc-exc-grid">
                <?php if (!empty($incList)): ?>
                <div class="inc-col">
                    <div class="inc-exc-title"><i class="ri-checkbox-circle-fill"></i> Package Inclusions</div>
                    <ul class="inc-exc-list">
                        <?php foreach ($incList as $inc): ?>
                            <li><i class="ri-check-line"></i> <?php echo htmlspecialchars($inc); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($excList)): ?>
                <div class="exc-col">
                    <div class="inc-exc-title"><i class="ri-close-circle-fill"></i> Package Exclusions</div>
                    <ul class="inc-exc-list">
                        <?php foreach ($excList as $exc): ?>
                            <li><i class="ri-close-line"></i> <?php echo htmlspecialchars($exc); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Company Contact Information -->
        <section class="company-contact-card">
            <h3 style="font-size: 18px; font-weight: 800;"><?php echo htmlspecialchars($siteName); ?></h3>
            <p style="font-size: 13px; color: #c7d2fe; margin-top: 2px;">Your dedicated travel planning specialist</p>

            <div class="contact-grid">
                <?php if (!empty($sitePhone)): ?>
                    <div class="contact-item">
                        <span class="contact-label">Phone / WhatsApp</span>
                        <span class="contact-val"><a href="tel:<?php echo htmlspecialchars($sitePhone); ?>"><?php echo htmlspecialchars($sitePhone); ?></a></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteEmail)): ?>
                    <div class="contact-item">
                        <span class="contact-label">Email Support</span>
                        <span class="contact-val"><a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>"><?php echo htmlspecialchars($siteEmail); ?></a></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteAddress)): ?>
                    <div class="contact-item">
                        <span class="contact-label">Office Address</span>
                        <span class="contact-val"><?php echo htmlspecialchars($siteAddress); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($siteUrl)): ?>
                    <div class="contact-item">
                        <span class="contact-label">Website</span>
                        <span class="contact-val"><a href="<?php echo htmlspecialchars($siteUrl); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars(preg_replace('/^https?:\/\//i', '', rtrim($siteUrl, '/'))); ?></a></span>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer & Aleya Credit -->
        <footer class="site-footer">
            <p>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($siteName); ?></strong>. All rights reserved.</p>
            <div class="credit-note">
                Powered by <a href="https://aleya.in" target="_blank" rel="noopener">Aleya</a> — Travel Agency Management Software
            </div>
        </footer>

    </div>

    <script>
        async function downloadItineraryPDF() {
            const btn = document.getElementById('btnDownloadModernPDF');
            const orig = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Generating PDF...';
            }

            try {
                const jspdfModule = window.jspdf || (typeof jsPDF !== 'undefined' ? { jsPDF } : null);
                if (!jspdfModule || !jspdfModule.jsPDF) {
                    throw new Error("jsPDF library is not loaded.");
                }
                const pdf = new jspdfModule.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const element = document.getElementById('itineraryDocument');
                if (!element) {
                    throw new Error("Itinerary document element not found.");
                }

                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: '#ffffff',
                    logging: false,
                    imageTimeout: 10000
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

                pdf.save("<?php echo preg_replace('/[^A-Za-z0-9_-]/', '_', $pkgTitle); ?>_Modern_Proposal.pdf");
                await new Promise(resolve => setTimeout(resolve, 400));
                return true;
            } catch (e) {
                console.error('PDF generation error:', e);
                window.print();
                throw e;
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                }
            }
        }
        window.downloadItineraryPDF = downloadItineraryPDF;

        const btnModernPdf = document.getElementById('btnDownloadModernPDF');
        if (btnModernPdf) {
            btnModernPdf.addEventListener('click', function() {
                downloadItineraryPDF().catch(function(e) {
                    console.error(e);
                });
            });
        }

        // Auto-Download on page load if requested (?download=true or ?pdf=1)
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const dlParam = urlParams.get('download');
            const pdfParam = urlParams.get('pdf');
            const shouldAutoDownload = dlParam === 'true' || dlParam === '1' || dlParam === 'yes' || pdfParam === '1' || pdfParam === 'true' || <?php echo (!empty($autoDownload) || !empty($isDownload)) ? 'true' : 'false'; ?>;
            if (shouldAutoDownload) {
                let executed = false;
                const runAutoDownload = function() {
                    if (executed) return;
                    executed = true;
                    setTimeout(function() {
                        downloadItineraryPDF().catch(function(e) {
                            console.error('Auto download failed:', e);
                        });
                    }, 400);
                };

                if (document.readyState === 'complete') {
                    runAutoDownload();
                } else {
                    window.addEventListener('load', runAutoDownload);
                    setTimeout(runAutoDownload, 2500);
                }
            }
        })();
    </script>
</body>
</html>