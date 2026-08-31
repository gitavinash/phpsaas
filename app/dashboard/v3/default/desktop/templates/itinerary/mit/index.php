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

if (!function_exists('renderPolicyContent')) {
    /**
     * Renders policy content in Word-document style.
     * - If DB content exists: outputs it directly (HTML stripped of inline styles, or plain text as <p>).
     * - If empty: outputs the $fallbackHtml string as-is.
     */
    function renderPolicyContent($rawContent, $fallbackHtml = '')
    {
        if (empty($rawContent) || !is_string($rawContent) || trim($rawContent) === '') {
            if (!empty($fallbackHtml)) {
                echo $fallbackHtml;
            }
            return;
        }

        $trimmed = trim($rawContent);
        if (strpos($trimmed, '<') !== false && strpos($trimmed, '>') !== false) {
            // Strip only inline style attributes to keep HTML structure
            $cleaned = preg_replace('/\s*style="[^"]*"/i', '', $trimmed);
            echo $cleaned;
        } else {
            // Plain text: split into lines and render as <p> blocks
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $trimmed)));
            foreach ($lines as $line) {
                echo '<p>' . htmlspecialchars($line) . '</p>';
            }
        }
    }
}
if (!function_exists('renderPolicyFormattedContent')) {
    function renderPolicyFormattedContent($rawContent, $fallbackHtml = '')
    {
        renderPolicyContent($rawContent, $fallbackHtml);
    }
}

// Dynamic Site & Company Info
$siteName     = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Company Name');
$sitePhone    = defined('SITE_MOBILE') ? SITE_MOBILE : (defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? ''));
$siteEmail    = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress  = defined('COMPANY_ADDRESS') ? COMPANY_ADDRESS : (defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? ''));
$siteWhatsapp = defined('COMPANY_WHATSAPP_NUMBER') ? COMPANY_WHATSAPP_NUMBER : (defined('SITE_WHATSAPP') ? SITE_WHATSAPP : ($company_whatsapp_number ?? ($whatsappNumber ?? '')));
$siteUrl      = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteDomain   = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($company_site_domain ?? parse_url($siteUrl, PHP_URL_HOST) ?? 'blumox.com'));
$siteLogo     = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));
$themeColor   = defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#e76317';
$bgColor      = defined('SITE_BACKGROUND_COLOUR') ? SITE_BACKGROUND_COLOUR : '#26679c';

// Social media JSON object handling
$socialMediaList = [];
if (!empty($company_social_media)) {
    $socialMediaList = is_array($company_social_media) ? $company_social_media : (json_decode($company_social_media, true) ?: []);
} elseif (defined('COMPANY_SOCIAL_MEDIA')) {
    $rawSoc = COMPANY_SOCIAL_MEDIA;
    $socialMediaList = is_array($rawSoc) ? $rawSoc : (json_decode($rawSoc, true) ?: []);
} elseif (defined('SETTING_COMPANY_PROFILE')) {
    $prof = json_decode(SETTING_COMPANY_PROFILE, true);
    if (is_array($prof)) {
        if (!empty($prof['companyFacebookProfile'])) $socialMediaList['facebook'] = $prof['companyFacebookProfile'];
        if (!empty($prof['companyTwitterProfile'])) $socialMediaList['x'] = $prof['companyTwitterProfile'];
        if (!empty($prof['companyLinkedinProfile'])) $socialMediaList['linkedin'] = $prof['companyLinkedinProfile'];
        if (!empty($prof['companyInstagramProfile'])) $socialMediaList['instagram'] = $prof['companyInstagramProfile'];
        if (!empty($prof['companyYoutubeProfile'])) $socialMediaList['youtube'] = $prof['companyYoutubeProfile'];
    }
}

// Pricing & Payment Overview Cost Variables
$totalPriceVal  = (float)($selling_price ?? ($price ?? ($totalPrice ?? ($pkgPriceAdult ?? 0))));
$adultCount     = max(1, (int)($adults ?? ($adults_count ?? 1)));
$childCount     = (int)($children ?? ($children_count ?? 0));
$infantCount    = (int)($infants ?? ($infants_count ?? 0));

$adultPriceVal  = (float)($tour_package_price_selling_adult ?? ($adultPrice ?? ($pkgPriceAdult ?? 0)));
if ($adultPriceVal <= 0 && $totalPriceVal > 0) {
    $adultPriceVal = round($totalPriceVal / $adultCount, 2);
}
$childPriceVal  = (float)($tour_package_price_selling_child ?? ($childPrice ?? 0));
$infantPriceVal = (float)($tour_package_price_selling_infant ?? ($infantPrice ?? 0));

$adultSubtotal  = $adultPriceVal * $adultCount;
$childSubtotal  = $childPriceVal * $childCount;
$infantSubtotal = $infantPriceVal * $infantCount;

$advance30Percent = round($totalPriceVal * 0.30, 2);
$balance70Percent = $totalPriceVal - $advance30Percent;

$gstPercentVal  = defined('GST_PERCENTAGE') ? (float)GST_PERCENTAGE : 5.0;
$basePriceVal   = $totalPriceVal > 0 ? round($totalPriceVal / (1 + ($gstPercentVal / 100)), 2) : 0;
$gstAmountVal   = $totalPriceVal - $basePriceVal;

// Clean quote reference
$rawQuoteRef = $quoteId ?? ($quotationId ?? ($tour_package_id ?? rand(1000, 9999)));
$cleanQuoteRef = '#' . ltrim((string)$rawQuoteRef, '#');

// Construct Pay Now URL & WhatsApp payment link
$cleanWhatsappDigits = preg_replace('/[^\d]/', '', (string)$siteWhatsapp);
$payNowUrl = '';
if (!empty($payment_link)) {
    $payNowUrl = $payment_link;
} elseif (!empty($quotationId) && (int)$quotationId > 0) {
    $encodedQuoteId = base64_encode((string)$quotationId);
    $payNowUrl = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . "/payment/{$encodedQuoteId}/";
} elseif (!empty($cleanWhatsappDigits)) {
    $payNowUrl = "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I would like to make payment for Quotation " . $cleanQuoteRef . " (" . ($tour_package_title ?? 'Tour Package') . ") - Amount: INR " . number_format($totalPriceVal, 2));
} else {
    $payNowUrl = "tel:" . preg_replace('/[^\d\+]/', '', (string)$sitePhone);
}

$waPayUrl = !empty($cleanWhatsappDigits)
    ? "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I am confirming quotation " . $cleanQuoteRef . " (" . ($tour_package_title ?? 'Tour Package') . "). Please share payment link / QR code for INR " . number_format($totalPriceVal, 2))
    : '';

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

// Company Bank & UPI Details
$companyBankName      = defined('COMPANY_BANK_NAME') ? COMPANY_BANK_NAME : ($bank_name ?? ($bankDetailsMap['bank_name'] ?? ''));
$companyBankAcName    = defined('COMPANY_BANK_AC_NAME') ? COMPANY_BANK_AC_NAME : ($bank_ac_name ?? ($bankDetailsMap['bank_ac_name'] ?? ''));
$companyBankAcNumber  = defined('COMPANY_BANK_AC_NUMBER') ? COMPANY_BANK_AC_NUMBER : ($bank_ac_number ?? ($bankDetailsMap['bank_ac_number'] ?? ''));
$companyBankIfsc      = defined('COMPANY_BANK_IFSC') ? COMPANY_BANK_IFSC : ($bank_ifsc ?? ($bankDetailsMap['bank_ifsc'] ?? ''));
$companyBankAddress   = defined('COMPANY_BANK_ADDRESS') ? COMPANY_BANK_ADDRESS : ($bank_address ?? ($bankDetailsMap['bank_address'] ?? ''));
$companyBankDetails   = defined('COMPANY_BANK_DETAILS') ? COMPANY_BANK_DETAILS : ($bank_details ?? ($bankDetailsMap['bank_details'] ?? ''));
$companyUpiId         = defined('COMPANY_UPI_ID') ? COMPANY_UPI_ID : ($upi_id ?? ($bankDetailsMap['upi_id'] ?? ($upi_details ?? ($bankDetailsMap['upi_details'] ?? ''))));
$hasBankDetails       = !empty($companyBankAcNumber) || !empty($companyBankName) || !empty($companyUpiId) || !empty($companyBankDetails);

// Dynamic UPI QR Code URL Generation
$upiPayeeName = !empty($companyBankAcName) ? $companyBankAcName : $siteName;
$upiPayAmt    = ($advance30Percent > 0) ? $advance30Percent : $totalPriceVal;
$upiQrUrl     = '';
if (!empty($companyUpiId)) {
    $upiPayload = "upi://pay?pa=" . rawurlencode($companyUpiId) . "&pn=" . rawurlencode($upiPayeeName) . "&am=" . number_format($upiPayAmt, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Quote " . $cleanQuoteRef);
    $upiQrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=" . urlencode($upiPayload);
} elseif (!empty($payNowUrl) && strpos($payNowUrl, 'http') === 0) {
    $upiQrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=" . urlencode($payNowUrl);
}

// Quote Validity & Pax Summary for Pricing Card
$validUntilDate = date('F j, Y', strtotime('+7 days'));
$paxSummaryParts = [];
if ($adultCount > 0) $paxSummaryParts[] = "{$adultCount} Adult" . ($adultCount > 1 ? 's' : '');
if ($childCount > 0) $paxSummaryParts[] = "{$childCount} Child" . ($childCount > 1 ? 'ren' : '');
if ($infantCount > 0) $paxSummaryParts[] = "{$infantCount} Infant" . ($infantCount > 1 ? 's' : '');
$paxSummaryText = !empty($paxSummaryParts) ? implode(', ', $paxSummaryParts) : '2 Adults';

// Policy & Terms Variables
$rawItineraryPolicy    = $itinerary_policy ?? '';
$rawPaymentPolicy      = ($payment_policy ?? ($tour_package_payment_policy ?? ''));
$rawCancellationPolicy = ($cancellation_policy ?? ($tour_package_cancellation_policy ?? ''));
$rawTermsConditions    = ($terms_and_conditions ?? ($tour_package_terms_and_conditions ?? ''));

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
            width: 210mm;
            /* A4 width */
            min-height: 297mm;
            /* A4 height */
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
        .days-page:nth-of-type(1) .hero .default-day-image {
            background: linear-gradient(135deg, #5e1ba9, #c2185b);
        }

        .days-page:nth-of-type(2) .hero .default-day-image {
            background: linear-gradient(135deg, #1565c0, #0277bd);
        }

        .days-page:nth-of-type(3) .hero .default-day-image {
            background: linear-gradient(135deg, #0288d1, #7b1fa2);
        }

        .days-page:nth-of-type(4) .hero .default-day-image {
            background: linear-gradient(135deg, #00796b, #558b2f);
        }

        .days-page:nth-of-type(5) .hero .default-day-image {
            background: linear-gradient(135deg, #e65100, #c62828);
        }

        .days-page:nth-of-type(6) .hero .default-day-image {
            background: linear-gradient(135deg, #c2185b, #6a1b9a);
        }

        .days-page:nth-of-type(7) .hero .default-day-image {
            background: linear-gradient(135deg, #00695c, #004d40);
        }

        .days-page:nth-of-type(8) .hero .default-day-image {
            background: linear-gradient(135deg, #1565c0, #1976d2);
        }

        .days-page:nth-of-type(9) .hero .default-day-image {
            background: linear-gradient(135deg, #0277bd, #00838f);
        }

        .days-page:nth-of-type(10) .hero .default-day-image {
            background: linear-gradient(135deg, #6a1b9a, #1565c0);
        }

        .greenGradient {
            background: linear-gradient(135deg, #00796b, #558b2f) !important;
        }

        .redGradient {
            background: linear-gradient(135deg, #e65100, #c62828) !important;
        }

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

        .costGradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }

        .policyGradient {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color)) !important;
        }

        /* Tour Pricing Section */
        #page-payment .hero {
            height: 48mm !important;
            min-height: 70mm !important;
            max-height: 48mm !important;
        }

        #page-payment .container {
            padding: 10px 45px 14px 45px;
            max-height: calc(297mm - 48mm - 28mm);
            overflow: visible;
        }

        #page-payment .top-heading {
            margin-bottom: 6px;
        }

        .pricing-main-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            box-sizing: border-box;
            width: 100%;
            font-family: "Mulish", sans-serif;
        }

        /* Top Quotation Banner */
        .pricing-header-banner {
            background: #fff8f3;
            border: 1px solid #fed7aa;
            border-left: 3.5px solid var(--primary-color);
            border-radius: 10px;
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 11px;
        }

        .pricing-header-content {
            flex: 1;
            min-width: 0;
        }

        .pricing-header-type {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 2px;
        }

        .pricing-header-type .quote-ref-tag {
            color: #64748b;
            font-weight: 600;
            margin-left: 4px;
            background: #ffffff;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid #fed7aa;
            font-size: 10px;
        }

        .pricing-package-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 3px 0;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pricing-package-meta {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pricing-package-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pricing-package-meta span i {
            color: var(--primary-color);
            font-size: 13px;
        }

        .badge-best-value {
            background: var(--primary-color);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: none !important;
        }

        /* Breakdown Price Rows */
        .pricing-breakdown-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 9px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pricing-breakdown-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            color: #334155;
        }

        .pricing-breakdown-label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #475569;
            font-weight: 600;
            font-size: 15px;
        }

        .pricing-breakdown-label i {
            color: #64748b;
            font-size: 16px;
        }

        .pricing-breakdown-val {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
        }

        .pricing-breakdown-val.gst-highlight {
            color: #15803d;
        }

        /* Total Price Section */
        .pricing-total-block {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-bottom: 9px;
        }

        .pricing-total-label {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 1px;
        }

        .pricing-total-amount {
            font-size: 26px;
            font-weight: 900;
            color: var(--primary-color);
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        /* Large CTA Button (No Shadow) */
        .btn-book-now {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            background: var(--primary-color);
            color: #ffffff !important;
            padding: 10px 16px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: none !important;
            transition: all 0.15s ease;
            box-sizing: border-box;
            text-align: center;
            font-family: "Mulish", sans-serif;
            border: none;
            cursor: pointer;
        }

        .btn-book-now:hover {
            box-shadow: none !important;
            opacity: 0.92;
        }

        .pricing-validity {
            font-size: 10.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            margin-bottom: 0;
            font-weight: 500;
        }

        .pricing-validity strong {
            color: #1e293b;
        }

        /* 2-Column Bank Remittance & UPI QR Section (Outside Main Pricing Card) */
        .bank-upi-section {
            display: grid;
            grid-template-columns: 1fr 190px;
            gap: 14px;
            margin-top: 10px;
            align-items: stretch;
            font-family: "Mulish", sans-serif;
            width: 100%;
            box-sizing: border-box;
        }

        .bank-details-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .section-micro-title {
            font-size: 15px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
            gap: 7px;
            padding-bottom: 8px;
            margin-bottom: 4px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .section-micro-title i {
            color: var(--primary-color);
            font-size: 18px;
        }

        .bank-details-stack {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .bank-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            padding: 5px 0;
            border-bottom: 1px solid #f8fafc;
        }

        .bank-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .bank-label {
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
        }

        .bank-value {
            color: #1e293b;
            font-size: 15px;
            font-weight: 600;
            text-align: right;
        }

        .bank-value strong {
            color: #0f172a;
            font-weight: 700;
            font-size: 15px;
        }

        .ac-num-highlight {
            letter-spacing: 0.5px;
            color: #0f172a;
            font-weight: 800;
            font-size: 15px;
        }

        /* Right Column: QR Code + UPI Address below QR */
        .upi-qr-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .qr-box-wrapper {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upi-qr-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 4px;
        }

        .qr-placeholder {
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .qr-placeholder i {
            font-size: 32px;
            color: var(--primary-color);
        }

        .qr-details {
            margin-top: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .qr-title {
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .qr-title i {
            font-size: 15px;
            color: var(--primary-color);
        }

        .upi-address-text {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 4px;
            background: #fff7ed;
            padding: 3px 8px;
            border-radius: 5px;
            border: 1px solid #fed7aa;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upi-address-text i {
            font-size: 9px;
        }

        /* Legal Policies & Terms Page Layout */
        /* ── Important Policies – plain document layout ── */
        #page-policies .hero {
            height: 42mm !important;
            min-height: 42mm !important;
            max-height: 42mm !important;
        }

        #page-policies .container {
            padding: 8px 50px 10px 50px;
            max-height: calc(297mm - 42mm - 28mm);
            overflow: visible;
        }

        #page-policies .top-heading {
            margin-bottom: 6px;
        }

        /* Plain document body */
        .legal-doc-body {
            /* font-family: "Mulish", Georgia, serif; */
            font-size: 12px;
            color: #111111;
            line-height: 1.75;
            width: 100%;
        }

        .legal-doc-section {
            margin-bottom: 8px;
        }

        .legal-doc-section:last-child {
            margin-bottom: 0;
        }

        .legal-doc-section-title {
            font-size: 10px;
            font-weight: 800;
            color: #000000;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            border-bottom: 0.5px solid #aaaaaa;
            padding-bottom: 2px;
        }

        .legal-doc-body p {
            margin: 0 0 2px 0;
            font-size: 12px;
            color: #111111;
            line-height: 1.75;
        }

        .legal-doc-body ul,
        .legal-doc-body ol {
            margin: 0 0 2px 0;
            padding-left: 16px;
        }

        .legal-doc-body li {
            font-size: 12px;
            color: #111111;
            line-height: 1.75;
            margin-bottom: 1px;
        }

        .legal-doc-body strong {
            font-weight: 700;
            color: #000000;
        }

        .legal-doc-body h3,
        .legal-doc-body h4,
        .legal-doc-body h5 {
            font-size: 12px;
            font-weight: 700;
            color: #000000;
            margin: 2px 0 1px 0;
        }

        .legal-doc-footer-note {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 0.5px solid #aaaaaa;
            font-size: 8.8px;
            color: #444444;
            line-height: 1.4;
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
            flex-shrink: 0;
        }

        .contact-item-full {
            grid-column: 1 / -1;
        }

        /* Company Social Section */
        .company-social-section {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }

        .social-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .company-social-links {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .social-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .social-badge-btn i {
            font-size: 15px;
            color: var(--badge-accent, #0284c7);
        }

        .social-badge-btn:hover {
            border-color: var(--badge-accent, #0284c7);
            background: #f8fafc;
            transform: translateY(-1px);
        }

        /* ── Salesperson / Booking Manager Card ── */
        .sp-card {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 16px;
            padding: 14px 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid var(--primary-color);
            border-radius: 10px;
        }

        .sp-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid var(--primary-color);
        }

        .sp-avatar-initials {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            font-family: "Mulish", sans-serif;
        }

        .sp-info {
            flex: 1;
            min-width: 0;
        }

        .sp-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--primary-color);
            margin-bottom: 2px;
        }

        .sp-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            font-family: "Mulish", sans-serif;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .sp-desig {
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
        }

        .sp-contacts {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .sp-contact-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            color: #334155;
            font-weight: 500;
        }

        .sp-contact-item i {
            font-size: 14px;
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

            #page-payment .hero {
                min-height: 48mm !important;
                max-height: 48mm !important;
                height: 48mm !important;
            }

            #page-policies .hero {
                min-height: 42mm !important;
                max-height: 42mm !important;
                height: 42mm !important;
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
    <!-- <?php if (empty($hideActions) && empty($isPreview)): ?>
        <div class="v3-pdf-actions-bar">
            <button type="button" class="btn-pdf-action btn-pdf-download" id="btnDownloadPDF" onclick="downloadItineraryPDF()">
                <i class="ri-file-pdf-line"></i> Download PDF
            </button>
            <button type="button" class="btn-pdf-action btn-pdf-print" onclick="window.print()">
                <i class="ri-printer-line"></i> Print
            </button>
        </div>
    <?php endif; ?> -->

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

        <!-- PAYMENT & TOUR PACKAGE OVERVIEW COST PAGE -->
        <section class="page days-page" id="page-payment">
            <header class="hero">
                <div class="default-day-image costGradient">
                    <h2>Pricing</h2>
                </div>
            </header>

            <div class="container">
                <div class="top-heading">
                    <span class="bar">|</span>
                    <h2>Tour Pricing</h2>
                </div>

                <div class="payment-summary-wrapper">
                    <!-- Pricing Card -->
                    <div class="pricing-main-card">

                        <!-- 1. Top Quotation Banner -->
                        <div class="pricing-header-banner">
                            <div class="pricing-header-content">
                                <div class="pricing-header-type">
                                    <i class="ri-file-text-line"></i> Tour Proposal Quotation
                                    <span class="quote-ref-tag"><?php echo htmlspecialchars($cleanQuoteRef); ?></span>
                                </div>
                                <h3 class="pricing-package-title"><?php echo htmlspecialchars($pkgTitle); ?></h3>
                                <div class="pricing-package-meta">
                                    <span><i class="ri-group-line"></i> <?php echo htmlspecialchars($paxSummaryText); ?></span>
                                    <span>•</span>
                                    <span><i class="ri-calendar-2-line"></i> <?php echo $totalNightsCount; ?> Nights, <?php echo $totalDaysCount; ?> Days</span>
                                    <?php if (!empty($pkgCity)): ?>
                                        <span>•</span>
                                        <span><i class="ri-map-pin-2-line"></i> <?php echo htmlspecialchars($pkgCity); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($pkgHotel)): ?>
                                        <span>•</span>
                                        <span><i class="ri-hotel-line"></i> <?php echo htmlspecialchars(cleanProposalLabel($pkgHotel)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="badge-best-value"><i class="ri-sparkling-fill"></i> Best Value</span>
                        </div>

                        <!-- 2. Clean Price Breakdown (Adult, Child, Infant & GST) -->
                        <div class="pricing-breakdown-list">
                            <div class="pricing-breakdown-row">
                                <span class="pricing-breakdown-label">Adult Price ( <?php echo max(1, $adultCount); ?> Adult<?php echo $adultCount > 1 ? 's' : ''; ?><?php if ($adultPriceVal > 0): ?> @ &#8377;<?php echo number_format($adultPriceVal, 2); ?><?php endif; ?> )
                                </span>
                                <span class="pricing-breakdown-val">&#8377;<?php echo number_format($adultSubtotal > 0 ? $adultSubtotal : $basePriceVal, 2); ?></span>
                            </div>

                            <?php if ($childCount > 0): ?>
                                <div class="pricing-breakdown-row">
                                    <span class="pricing-breakdown-label">
                                        Child Price ( <?php echo $childCount; ?> Child<?php echo $childCount > 1 ? 'ren' : ''; ?><?php if ($childPriceVal > 0): ?> @ &#8377;<?php echo number_format($childPriceVal, 2); ?><?php endif; ?> )
                                    </span>
                                    <span class="pricing-breakdown-val">&#8377;<?php echo number_format($childSubtotal, 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($infantCount > 0): ?>
                                <div class="pricing-breakdown-row">
                                    <span class="pricing-breakdown-label">
                                        Infant Price ( <?php echo $infantCount; ?> Infant<?php echo $infantCount > 1 ? 's' : ''; ?><?php if ($infantPriceVal > 0): ?> @ &#8377;<?php echo number_format($infantPriceVal, 2); ?><?php endif; ?> )
                                    </span>
                                    <span class="pricing-breakdown-val">&#8377;<?php echo number_format($infantSubtotal, 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="pricing-breakdown-row">
                                <span class="pricing-breakdown-label">
                                    GST ( <?php echo $gstPercentVal; ?>% )
                                </span>
                                <span class="pricing-breakdown-val gst-highlight">+ &#8377;<?php echo number_format($gstAmountVal, 2); ?></span>
                            </div>
                        </div>

                        <!-- 3. Total Price Block (Clean & Minimal) -->
                        <div class="pricing-total-block">
                            <span class="pricing-total-label">Total Price</span>
                            <span class="pricing-total-amount">&#8377;<?php echo number_format($totalPriceVal, 2); ?></span>
                        </div>

                        <!-- 4. Prominent CTA Button (No Shadow) -->
                        <?php if (!empty($payNowUrl)): ?>
                            <a href="<?php echo htmlspecialchars($payNowUrl); ?>" target="_blank" rel="noopener" class="btn-book-now">
                                <span>Click here to Book Now</span>
                            </a>
                        <?php endif; ?>

                        <!-- 5. Validity Date (Matching Reference Design) -->
                        <div class="pricing-validity">
                            <i class="ri-calendar-event-line"></i> Prices valid until <strong><?php echo $validUntilDate; ?></strong>
                        </div>

                    </div>

                    <!-- 6. Direct Bank Remittance & Dynamic UPI QR Section (Outside Box, 2 Columns) -->
                    <?php if ($hasBankDetails || !empty($upiQrUrl)): ?>
                        <div class="bank-upi-section">
                            <!-- Left Column: Bank Details -->
                            <div class="bank-details-box">
                                <div class="section-micro-title">
                                    <i class="ri-bank-line"></i>
                                    <span>Direct Bank Transfer Details</span>
                                </div>
                                <div class="bank-details-stack">
                                    <?php if (!empty($companyBankAcName)): ?>
                                        <div class="bank-row">
                                            <span class="bank-label">Account Holder</span>
                                            <span class="bank-value"><strong><?php echo htmlspecialchars($companyBankAcName); ?></strong></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($companyBankAcNumber)): ?>
                                        <div class="bank-row">
                                            <span class="bank-label">Account Number</span>
                                            <span class="bank-value"><strong class="ac-num-highlight"><?php echo htmlspecialchars($companyBankAcNumber); ?></strong></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($companyBankIfsc)): ?>
                                        <div class="bank-row">
                                            <span class="bank-label">IFSC Code</span>
                                            <span class="bank-value"><strong><?php echo htmlspecialchars($companyBankIfsc); ?></strong></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($companyBankName)): ?>
                                        <div class="bank-row">
                                            <span class="bank-label">Bank Name</span>
                                            <span class="bank-value"><?php echo htmlspecialchars($companyBankName); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($companyBankAddress)): ?>
                                        <div class="bank-row">
                                            <span class="bank-label">Branch</span>
                                            <span class="bank-value"><?php echo htmlspecialchars($companyBankAddress); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right Column: QR Code + UPI Address below QR -->
                            <div class="upi-qr-box">
                                <div class="qr-box-wrapper">
                                    <?php if (!empty($upiQrUrl)): ?>
                                        <img src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="UPI QR Code" class="upi-qr-img" />
                                    <?php else: ?>
                                        <div class="qr-placeholder">
                                            <i class="ri-qr-code-line"></i>
                                            <span>Scan &amp; Pay</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="qr-details">
                                    <div class="qr-title"><i class="ri-qr-scan-2-line"></i> Scan &amp; Pay</div>
                                    <?php if (!empty($companyUpiId)): ?>
                                        <div class="upi-address-text" title="<?php echo htmlspecialchars($companyUpiId); ?>">
                                            <i class="ri-wallet-3-line"></i> <?php echo htmlspecialchars($companyUpiId); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="qr-sub">GPay • PhonePe • UPI</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

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
                        <?php if (defined('SITE_MOBILE') && !empty(SITE_MOBILE)): ?>
                            <div class="contact-item">
                                <i class="ri-phone-line"></i>
                                <span><strong>Phone:</strong> <?php echo htmlspecialchars(SITE_MOBILE); ?></span>
                            </div>
                        <?php elseif (!empty($sitePhone)): ?>
                            <div class="contact-item">
                                <i class="ri-phone-line"></i>
                                <span><strong>Phone:</strong> <?php echo htmlspecialchars($sitePhone); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_WHATSAPP_NUMBER') && !empty(COMPANY_WHATSAPP_NUMBER)): ?>
                            <div class="contact-item">
                                <i class="ri-whatsapp-line" style="color: #25D366;"></i>
                                <span><strong>WhatsApp:</strong> <?php echo htmlspecialchars(COMPANY_WHATSAPP_NUMBER); ?></span>
                            </div>
                        <?php elseif (!empty($siteWhatsapp)): ?>
                            <div class="contact-item">
                                <i class="ri-whatsapp-line" style="color: #25D366;"></i>
                                <span><strong>WhatsApp:</strong> <?php echo htmlspecialchars($siteWhatsapp); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('SITE_EMAIL') && !empty(SITE_EMAIL)): ?>
                            <div class="contact-item">
                                <i class="ri-mail-line"></i>
                                <span><strong>Email:</strong> <?php echo htmlspecialchars(SITE_EMAIL); ?></span>
                            </div>
                        <?php elseif (!empty($siteEmail)): ?>
                            <div class="contact-item">
                                <i class="ri-mail-line"></i>
                                <span><strong>Email:</strong> <?php echo htmlspecialchars($siteEmail); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_SITE_DOMAIN') && !empty(COMPANY_SITE_DOMAIN)): ?>
                            <div class="contact-item">
                                <i class="ri-global-line"></i>
                                <span><strong>Website:</strong> <?php echo htmlspecialchars(COMPANY_SITE_DOMAIN); ?></span>
                            </div>
                        <?php elseif (!empty($siteUrl) || !empty($siteDomain)): ?>
                            <div class="contact-item">
                                <i class="ri-global-line"></i>
                                <span><strong>Website:</strong> <?php echo htmlspecialchars($siteDomain); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_ADDRESS') && !empty(COMPANY_ADDRESS)): ?>
                            <div class="contact-item contact-item-full">
                                <i class="ri-map-pin-line" style="color: var(--primary-color);"></i>
                                <span><strong>Address:</strong> <?php echo htmlspecialchars(COMPANY_ADDRESS); ?></span>
                            </div>
                        <?php elseif (!empty($siteAddress)): ?>
                            <div class="contact-item contact-item-full">
                                <i class="ri-map-pin-line" style="color: var(--primary-color);"></i>
                                <span><strong>Address:</strong> <?php echo htmlspecialchars($siteAddress); ?></span>
                            </div>
                        <?php endif; ?>

                    </div>

                    <?php
                    $socialIcons = [
                        'facebook'  => ['icon' => 'ri-facebook-circle-fill', 'color' => '#1877F2', 'label' => 'Facebook'],
                        'instagram' => ['icon' => 'ri-instagram-fill',       'color' => '#E4405F', 'label' => 'Instagram'],
                        'x'         => ['icon' => 'ri-twitter-x-fill',       'color' => '#0f172a', 'label' => 'X (Twitter)'],
                        'twitter'   => ['icon' => 'ri-twitter-x-fill',       'color' => '#0f172a', 'label' => 'Twitter'],
                        'linkedin'  => ['icon' => 'ri-linkedin-box-fill',    'color' => '#0A66C2', 'label' => 'LinkedIn'],
                        'youtube'   => ['icon' => 'ri-youtube-fill',         'color' => '#FF0000', 'label' => 'YouTube'],
                    ];
                    $hasSocial = false;
                    if (!empty($socialMediaList) && is_array($socialMediaList)) {
                        foreach ($socialMediaList as $p => $u) {
                            if (!empty($u) && is_string($u) && trim($u) !== '') {
                                $hasSocial = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <?php if ($hasSocial): ?>
                        <div class="company-social-section">
                            <h4 class="social-section-title">Follow &amp; Connect</h4>
                            <div class="company-social-links">
                                <?php foreach ($socialMediaList as $platform => $url):
                                    if (empty($url) || !is_string($url) || trim($url) === '') continue;
                                    $pKey = strtolower(trim($platform));
                                    $meta = $socialIcons[$pKey] ?? ['icon' => 'ri-share-forward-line', 'color' => '#0284c7', 'label' => ucfirst($pKey)];
                                ?>
                                    <a href="<?php echo htmlspecialchars(trim($url)); ?>" target="_blank" rel="noopener" class="social-badge-btn" style="--badge-accent: <?php echo $meta['color']; ?>;">
                                        <i class="<?php echo $meta['icon']; ?>"></i>
                                        <span><?php echo htmlspecialchars($meta['label']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $spName   = $salesPersonName  ?? '';
                $spEmail  = $salesPersonEmail  ?? '';
                $spMobile = $salesPersonMobile ?? '';
                $spImage  = $salesPersonImage  ?? '';
                $spDesig  = $salesPersonDesig  ?? 'Travel Consultant';
                if (!empty($spName)):
                    $spWords    = preg_split('/\s+/', trim($spName));
                    $spInitials = strtoupper(mb_substr($spWords[0], 0, 1) . (count($spWords) > 1 ? mb_substr(end($spWords), 0, 1) : ''));
                ?>
                    <!-- Salesperson / Booking Manager Card -->
                    <div class="sp-card">
                        <?php if (!empty($spImage)): ?>
                            <img src="<?php echo htmlspecialchars($spImage); ?>" alt="<?php echo htmlspecialchars($spName); ?>" class="sp-avatar">
                        <?php else: ?>
                            <div class="sp-avatar-initials"><?php echo htmlspecialchars($spInitials); ?></div>
                        <?php endif; ?>
                        <div class="sp-info">
                            <div class="sp-label">Your Booking Manager</div>
                            <div class="sp-name"><?php echo htmlspecialchars($spName); ?></div>
                            <div class="sp-desig"><?php echo htmlspecialchars($spDesig); ?></div>
                            <div class="sp-contacts">
                                <?php if (!empty($spMobile)): ?>
                                    <span class="sp-contact-item">
                                        <i class="ri-phone-fill"></i>
                                        <?php echo htmlspecialchars($spMobile); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($spEmail)): ?>
                                    <span class="sp-contact-item">
                                        <i class="ri-mail-fill"></i>
                                        <?php echo htmlspecialchars($spEmail); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <!-- BOOKING POLICIES, CANCELLATION & TERMS PAGE (LAST PAGE) -->
        <section class="page days-page" id="page-policies">
            <header class="hero">
                <div class="default-day-image policyGradient">
                    <h2>Our Policies</h2>
                </div>
            </header>

            <div class="container">
                <!-- <div class="top-heading">
                    <span class="bar">|</span>
                    <h2>Terms, Policies &amp; Booking Conditions</h2>
                </div> -->

                <div class="legal-doc-body">

                    <?php if (!empty($rawItineraryPolicy)): ?>
                        <!-- Itinerary Policy from DB (/itinenary-policy/) -->
                        <div class="legal-doc-section">
                            <!-- <h3 class="legal-doc-section-title">Itinerary &amp; Booking Policy</h3> -->
                            <?php renderPolicyContent($rawItineraryPolicy); ?>
                        </div>
                    <?php else: ?>

                        <!-- 1. Payment Policy -->
                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">1. Payment Policy</h3>
                            <?php
                            $fallbackPayment = implode('', [
                                '<ul>',
                                '<li><strong>Booking Advance:</strong> A non-refundable 50% advance payment is required at the time of booking confirmation to block hotel rooms, vehicles, and permits.</li>',
                                '<li><strong>Balance Payment:</strong> The remaining 50% must be paid on or before arrival at the first hotel / destination check-in.</li>',
                                '<li><strong>Accepted Modes:</strong> Payments are accepted via company bank transfer, verified UPI, or official payment gateway link only. Cash payments to field staff are not accepted.</li>',
                                '<li><strong>Invoice:</strong> A GST invoice will be issued upon receipt of advance payment.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawPaymentPolicy, $fallbackPayment);
                            ?>
                        </div>

                        <!-- 2. Cancellation & Refund Policy -->
                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">2. Cancellation &amp; Refund Policy</h3>
                            <?php
                            $fallbackCancel = implode('', [
                                '<ul>',
                                '<li><strong>More than 15 days before departure:</strong> 20% of total package cost is retained as booking/admin fee.</li>',
                                '<li><strong>7 to 14 days before departure:</strong> 50% of total package cost is non-refundable.</li>',
                                '<li><strong>Less than 7 days / No-Show:</strong> 100% of the package cost is forfeited. No refund is applicable.</li>',
                                '<li><strong>Non-Refundable Components:</strong> Flight tickets, peak-season hotel bookings, wildlife park entry permits, and monument fees are non-refundable as per third-party supplier terms.</li>',
                                '<li><strong>Refund Processing:</strong> Eligible refunds are processed to the original payment source within 5&ndash;7 business working days.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawCancellationPolicy, $fallbackCancel);
                            ?>
                        </div>

                        <!-- 3. Terms & Conditions -->
                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">3. Terms &amp; Conditions</h3>
                            <?php
                            $fallbackTerms = implode('', [
                                '<ul>',
                                '<li><strong>Hotel Check-In / Check-Out:</strong> Standard check-in time is 12:00 PM&ndash;2:00 PM; check-out is 11:00 AM. Early check-in or late check-out is subject to room availability and may incur additional charges.</li>',
                                '<li><strong>Vehicle Usage:</strong> Private vehicles are deployed on a point-to-point basis for the agreed itinerary route only. 24-hour disposal or unscheduled stops are not included.</li>',
                                '<li><strong>Photo ID:</strong> All travelers must carry a valid Government-issued photo ID (Aadhaar, Passport, or Voter ID) throughout the trip.</li>',
                                '<li><strong>Package Amendments:</strong> Changes to dates, destinations, or services after confirmation are subject to availability and may attract amendment charges.</li>',
                                '<li><strong>Responsibility:</strong> The company acts as a facilitator and is not responsible for losses arising from third-party service failures, including airlines, hotels, or transport operators.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawTermsConditions, $fallbackTerms);
                            ?>
                        </div>

                        <!-- 4. Force Majeure & Jurisdiction -->
                        <!-- <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">4. Force Majeure &amp; Jurisdiction</h3>
                            <ul>
                                <li><strong>Force Majeure:</strong> <?php echo htmlspecialchars($siteName); ?> shall not be liable for delays, cancellations, or additional costs arising from events beyond its control, including but not limited to natural disasters, government orders, strikes, roadblocks, weather disruptions, or flight cancellations.</li>
                                <li><strong>Governing Law:</strong> This agreement is governed by the laws of India under the Indian Contract Act, 1872.</li>
                                <li><strong>Jurisdiction:</strong> Any dispute arising from this booking shall be subject to the exclusive jurisdiction of courts at the registered office of <?php echo htmlspecialchars($siteName); ?>.</li>
                            </ul>
                        </div> -->

                    <?php endif; ?>

                    <!-- <div class="legal-doc-footer-note">
                        By remitting the booking advance or providing written/digital confirmation of this itinerary, the client agrees to and accepts all terms, cancellation schedules, and booking conditions stated above.
                    </div> -->

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

                    points.push({
                        x,
                        y
                    });
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
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
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

        // 4. Auto-Download on page load if requested (?download=true or ?pdf=1)
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const dlParam = urlParams.get('download');
            const pdfParam = urlParams.get('pdf');
            const shouldAutoDownload = dlParam === 'true' || dlParam === '1' || dlParam === 'yes' || pdfParam === '1' || pdfParam === 'true' || <?php echo (!empty($autoDownload) || !empty($isDownload)) ? 'true' : 'false'; ?>;
            if (shouldAutoDownload) {
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        downloadItineraryPDF();
                    }, 600);
                });
            }
        })();
    </script>
</body>

</html>