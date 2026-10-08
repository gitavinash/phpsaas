<?php
/**
 * V3 Itinerary Template: Neo-Modernist Travel Deck (Modern Studio)
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/modern/index.php
 * Description: Contemporary, high-tech travel editorial deck. Rounded geometry (16-24px radii),
 *              floating glassmorphic pills, vibrant violet/indigo/cyan accents,
 *              capsule stat decks, floating day visual banners, modern digital remittance cards,
 *              dedicated A4 multi-page export, Aleya credit footer, and zero-break PDF generation.
 */

if (!function_exists('fixImageUrl')) {
    function fixImageUrl($url)
    {
        if (empty($url)) return '';
        $url = trim($url);
        if (preg_match('/^https?:\/\//i', $url)) return $url;

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

if (!function_exists('cleanItineraryUnicode')) {
    function cleanItineraryUnicode($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = cleanItineraryUnicode($v);
            }
            return $val;
        }
        if (!is_string($val) || $val === '') {
            return $val;
        }

        // 1. Unicode escape sequences: \u0026, \u2013, \u2014, etc.
        $val = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($m) {
            return mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE');
        }, $val);

        // 2. Erroneously unescaped hex artifacts
        $val = preg_replace('/\bu0026\b/i', '&', $val);
        $val = preg_replace('/\bu0040\b/i', '@', $val);
        $val = preg_replace('/\bu2013\b/i', '–', $val);
        $val = preg_replace('/\bu2014\b/i', '—', $val);

        // 3. Repeatedly collapse double-encoded &amp;amp;
        while (str_contains($val, '&amp;amp;')) {
            $val = str_replace('&amp;amp;', '&amp;', $val);
        }

        // 4. Normalize HTML entities
        $maxLoops = 3;
        while ($maxLoops-- > 0 && preg_match('/&[a-zA-Z0-9#]+;/', $val)) {
            $decoded = html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $val) break;
            $val = $decoded;
        }

        // 5. Standalone or erroneous "amp;" with missing leading ampersand
        $val = preg_replace('/(?<=\s|^)amp;(?=\s|[.,!?]|$)/i', '&', $val);
        $val = preg_replace('/\bamp;\b/i', '&', $val);

        return $val;
    }
}

if (!function_exists('capitalizeFirstWord')) {
    function capitalizeFirstWord($text)
    {
        if (empty($text) || !is_string($text)) return $text;
        $trimmed = trim($text);
        // Capitalize the first alphabetical character, preserving leading punctuation or numbers (e.g., 5%, 24/7, quotes)
        return preg_replace_callback('/^([^a-zA-Z\p{L}]*)([a-z\p{Ll}])/u', function ($m) {
            return $m[1] . mb_strtoupper($m[2], 'UTF-8');
        }, $trimmed);
    }
}

if (!function_exists('capitalizeParagraphs')) {
    function capitalizeParagraphs($htmlOrText)
    {
        if (empty($htmlOrText) || !is_string($htmlOrText)) return $htmlOrText;
        if (str_contains($htmlOrText, '<')) {
            // Capitalize after opening HTML tags: p, li, div, headings, or br tags
            return preg_replace_callback('/(<\s*(?:p|li|div|h[1-6]|span)[^>]*>|<br\s*\/?>\s*|^)([^<a-zA-Z\p{L}]*)([a-z\p{Ll}])/u', function ($m) {
                return $m[1] . $m[2] . mb_strtoupper($m[3], 'UTF-8');
            }, $htmlOrText);
        } else {
            $lines = explode("\n", $htmlOrText);
            foreach ($lines as $i => $l) {
                $lines[$i] = capitalizeFirstWord($l);
            }
            return implode("\n", $lines);
        }
    }
}

if (!function_exists('parseBulletList')) {
    function parseBulletList($input)
    {
        if (empty($input)) return [];
        $input = cleanItineraryUnicode($input);
        $items = [];
        if (is_array($input)) {
            foreach ($input as $item) {
                $subItems = parseBulletList($item);
                foreach ($subItems as $sub) {
                    $items[] = $sub;
                }
            }
        } else {
            $rawStr = (string)$input;
            $hasLineBreaks = (
                str_contains($rawStr, '<br') || 
                str_contains($rawStr, '</li') || 
                str_contains($rawStr, '</p') || 
                str_contains($rawStr, "\n") || 
                str_contains($rawStr, "\r")
            );

            if ($hasLineBreaks) {
                // Split strictly by line/paragraph tags, never splitting commas in sentences/parentheses
                $str = str_replace(['<br>', '<br/>', '<br />', '</li>', '<p>', '</p>'], "\n", $rawStr);
                $rawLines = explode("\n", strip_tags($str));
            } else {
                // Single-line string: split on commas only if OUTSIDE parentheses
                $cleanStr = strip_tags($rawStr);
                $splitRegex = '/,(?![^(]*\))/';
                $rawLines = preg_split($splitRegex, $cleanStr);
            }

            foreach ($rawLines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // Strip leading bullet glyphs or numbered list prefixes like "1. ", "1) ", "(1) ", "• ", "- "
                // Safely retain standalone percentages and numbers like "5% GST" or "24/7 Support"
                $line = preg_replace('/^(\(?\d+[\.\)\-:]\s*|[•\-\*✓✗✔✕►▪▫–—]\s*)/u', '', $line);
                $line = trim($line);

                if (!empty($line)) {
                    $line = capitalizeFirstWord($line);
                    $items[] = $line;
                }
            }
        }
        return array_values(array_unique(array_filter($items)));
    }
}

// Global & Company context
$siteName     = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Travel Experiences');
$sitePhone    = defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? '+91 98765 43210');
$siteEmail    = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? 'concierge@aleya.in');
$siteAddress  = defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? 'Bespoke Travel Studio');
$siteWhatsapp = defined('SITE_WHATSAPP') ? SITE_WHATSAPP : ($company_whatsapp ?? $sitePhone);
$siteUrl      = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteDomain   = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($company_site_domain ?? parse_url($siteUrl, PHP_URL_HOST) ?? 'aleya.in'));
$siteLogo     = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));
$themeColor   = defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#6366f1';

// Social media list handling
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

// Pricing & Breakdown Variables
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
$finalPriceVal   = $totalPriceVal + $gstAmountVal;

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
    $payNowUrl = "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I would like to make payment for Quotation " . $cleanQuoteRef . " - Amount: INR " . number_format($totalPriceVal, 2));
} else {
    $payNowUrl = "tel:" . preg_replace('/[^\d\+]/', '', (string)$sitePhone);
}

// Package Variables
$pkgTitle       = capitalizeFirstWord(cleanItineraryUnicode($tour_package_title ?? ($itinerary_title ?? ($title ?? 'Curated Modern Journey'))));
$pkgCity        = cleanItineraryUnicode($tour_package_city ?? ($city ?? 'Destination'));
$pkgState       = cleanItineraryUnicode($tour_package_state ?? '');
$pkgCountry     = cleanItineraryUnicode($tour_package_country ?? '');
$pkgDuration    = $tour_package_duration ?? ($numberOfDays ?? ($duration ?? 0));
$pkgTheme       = cleanItineraryUnicode($tour_package_theme ?? ($travelStyle ?? 'Contemporary Leisure'));
$pkgHotel       = cleanItineraryUnicode($tour_package_hotel ?? ($hotel ?? ''));
$pkgPriceAdult  = $tour_package_price_selling_adult ?? ($selling_price ?? ($price ?? 0));
$pkgOutVehicle  = cleanItineraryUnicode($tour_package_outstation_vehicle ?? ($outstationVehicle ?? ''));
$pkgLocVehicle  = cleanItineraryUnicode($tour_package_localsightseen_vehicle ?? ($localSightseeingVehicle ?? ''));
$pkgOverview    = cleanItineraryUnicode($tour_package_overview ?? ($itinerary_overview ?? ($overview ?? '')));
$cleanOverview  = (strpos($pkgOverview, '<') !== false) ? $pkgOverview : nl2br(htmlspecialchars($pkgOverview, ENT_QUOTES, 'UTF-8', false));
$cleanOverview  = capitalizeParagraphs($cleanOverview);
$pkgInclusions  = cleanItineraryUnicode($tour_package_inclusions ?? ($inclusions ?? ''));
$pkgExclusions  = cleanItineraryUnicode($tour_package_exclusions ?? ($exclusions ?? ''));
$pkgDays        = cleanItineraryUnicode($itineraries ?? ($itinerary ?? ($days ?? [])));

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

$incList = parseBulletList($pkgInclusions);
$excList = parseBulletList($pkgExclusions);

// Bank & UPI Details
$companyBankName      = defined('COMPANY_BANK_NAME') ? COMPANY_BANK_NAME : ($bank_name ?? ($bankDetailsMap['bank_name'] ?? ''));
$companyBankAcName    = defined('COMPANY_BANK_AC_NAME') ? COMPANY_BANK_AC_NAME : ($bank_ac_name ?? ($bankDetailsMap['bank_ac_name'] ?? ''));
$companyBankAcNumber  = defined('COMPANY_BANK_AC_NUMBER') ? COMPANY_BANK_AC_NUMBER : ($bank_ac_number ?? ($bankDetailsMap['bank_ac_number'] ?? ''));
$companyBankIfsc      = defined('COMPANY_BANK_IFSC') ? COMPANY_BANK_IFSC : ($bank_ifsc ?? ($bankDetailsMap['bank_ifsc'] ?? ''));
$companyBankAddress   = defined('COMPANY_BANK_ADDRESS') ? COMPANY_BANK_ADDRESS : ($bank_address ?? ($bankDetailsMap['bank_address'] ?? ''));
$companyBankDetails   = defined('COMPANY_BANK_DETAILS') ? COMPANY_BANK_DETAILS : ($bank_details ?? ($bankDetailsMap['bank_details'] ?? ''));
$companyUpiId         = defined('COMPANY_UPI_ID') ? COMPANY_UPI_ID : ($upi_id ?? ($bankDetailsMap['upi_id'] ?? ($upi_details ?? ($bankDetailsMap['upi_details'] ?? ''))));
$hasRealBankDetails   = !empty($companyBankAcNumber) || !empty($companyBankName) || !empty($companyBankDetails);
$hasUpiDetails        = !empty($companyUpiId);

// Dynamic UPI QR Code URL Generation
$upiPayeeName = !empty($companyBankAcName) ? $companyBankAcName : $siteName;
$upiPayAmt    = ($advance30Percent > 0) ? $advance30Percent : $totalPriceVal;
$upiQrUrl     = '';
if ($hasUpiDetails) {
    $upiPayload = "upi://pay?pa=" . rawurlencode($companyUpiId) . "&pn=" . rawurlencode($upiPayeeName) . "&am=" . number_format($upiPayAmt, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Quote " . $cleanQuoteRef);
    $upiQrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=" . urlencode($upiPayload);
}

// Validity & Pax Summary
$validUntilDate = date('F j, Y', strtotime('+7 days'));
$paxSummaryParts = [];
if ($adultCount > 0) $paxSummaryParts[] = "{$adultCount} Adult" . ($adultCount > 1 ? 's' : '');
if ($childCount > 0) $paxSummaryParts[] = "{$childCount} Child" . ($childCount > 1 ? 'ren' : '');
if ($infantCount > 0) $paxSummaryParts[] = "{$infantCount} Infant" . ($infantCount > 1 ? 's' : '');
$paxSummaryText = !empty($paxSummaryParts) ? implode(', ', $paxSummaryParts) : '2 Adults';

// Travel Date Resolution
$travelDateRaw = !empty($travelDate) ? $travelDate : (!empty($travel_date) ? $travel_date : (!empty($quotation_travel_date) ? $quotation_travel_date : (!empty($postData['travelDate']) ? $postData['travelDate'] : (!empty($postData['travel_date']) ? $postData['travel_date'] : ''))));
$travelTimestamp = null;
if (!empty($travelDateRaw) && is_string($travelDateRaw)) {
    $cleanDateStr = trim($travelDateRaw);
    if ($cleanDateStr !== '' && $cleanDateStr !== '0000-00-00' && $cleanDateStr !== '1970-01-01') {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $cleanDateStr)) {
            $cleanDateStr = str_replace('/', '-', $cleanDateStr);
        }
        $ts = strtotime($cleanDateStr);
        if ($ts !== false && $ts > 0) {
            $travelTimestamp = $ts;
        }
    }
}
$formattedTravelDate = ($travelTimestamp && $travelTimestamp > 0) ? date('d M Y', $travelTimestamp) : '';

// Policy & Terms Variables
$rawItineraryPolicy    = $itinerary_policy ?? '';
$rawPaymentPolicy      = ($payment_policy ?? ($tour_package_payment_policy ?? ''));
$rawCancellationPolicy = ($cancellation_policy ?? ($tour_package_cancellation_policy ?? ''));
$rawTermsConditions    = ($terms_and_conditions ?? ($tour_package_terms_and_conditions ?? ''));

// Helper: Format hotel category
$formatCategoryName = function($rawCategory, $stars = '') {
    $cat = trim((string)$rawCategory);
    if (empty($cat) && !empty($stars)) {
        $cat = $stars;
    }
    if (empty($cat)) {
        return 'Boutique Hotel';
    }
    $catLower = strtolower($cat);
    $catMap = [
        '3_star_hotel' => '3★ Boutique Comfort',
        '4_star_hotel' => '4★ Premium Contemporary',
        '5_star_hotel' => '5★ Luxury Retreat',
        'budget_hotel' => 'Standard Comfort',
        'deluxe_hotel' => 'Deluxe Stay',
        'luxury_hotel' => 'Luxury Villa / Suite',
        'resort'       => 'Premium Resort'
    ];
    return $catMap[$catLower] ?? ucwords(str_replace('_', ' ', $cat));
};

// Helper: Clean meal plan
$cleanMealPlan = function($rawMeal) {
    if (empty($rawMeal)) return 'Room Only (EP)';
    $m = strtoupper(trim((string)$rawMeal));
    $mealMap = [
        'CP'  => 'CP · Daily Breakfast Included',
        'MAP' => 'MAP · Breakfast & Gourmet Dinner',
        'AP'  => 'AP · All Meals Included',
        'EP'  => 'EP · Room Only'
    ];
    return $mealMap[$m] ?? $rawMeal;
};

// Helper: Category default thumbnail
$getCategoryThumbnail = function($categoryName, $stayName = '') {
    $combined = strtolower($categoryName . ' ' . $stayName);
    if (str_contains($combined, 'houseboat')) {
        return 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=400&auto=format&fit=crop&q=80';
    }
    if (str_contains($combined, 'camp') || str_contains($combined, 'tent')) {
        return 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=400&auto=format&fit=crop&q=80';
    }
    if (str_contains($combined, '5 star') || str_contains($combined, 'luxury')) {
        return 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&auto=format&fit=crop&q=80';
    }
    if (str_contains($combined, 'resort') || str_contains($combined, 'retreat')) {
        return 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&auto=format&fit=crop&q=80';
    }
    if (str_contains($combined, '4 star')) {
        return 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&auto=format&fit=crop&q=80';
    }
    return 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&auto=format&fit=crop&q=80';
};

// Preload assigned package hotels if available in DB
$dayWiseHotels = [];
$packageIdForHotels = (int)($quotationData['quotation_type_id'] ?? ($tour_package_id ?? ($postData['package_id'] ?? ($postData['tour_package_id'] ?? 0))));
if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && $packageIdForHotels > 0) {
    if (empty($gallery_image_list)) {
        $galQ = "SELECT gallery_image_list FROM " . TABLE_PREFIX . "gallery WHERE gallery_category = {$packageIdForHotels} AND gallery_site_id = " . (int)SITE_ID . " AND gallery_type = 'tour_package' AND gallery_status = 1 LIMIT 1";
        if ($gRes = @mysqli_query($connection, $galQ)) {
            if ($gRow = mysqli_fetch_assoc($gRes)) {
                $gallery_image_list = $gRow['gallery_image_list'];
            }
        }
    }

    $hotelDbQuery = "
        SELECT 
            tph.tph_night,
            tph.tph_room_type,
            p.property_id,
            p.property_title,
            p.property_thumbnail,
            p.property_address,
            p.property_city,
            p.property_state,
            p.property_type
        FROM " . TABLE_PREFIX . "tour_package_hotel tph
        INNER JOIN " . TABLE_PREFIX . "property p ON p.property_id = tph.tph_hotel_id
        WHERE tph.tph_package_id = '{$packageIdForHotels}' AND tph.tph_site_id = " . SITE_ID;
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

$propertyLookupCache = [];

$checkTransitOrTripEnd = function($d, $idx, $total, $title, $overview) {
    $combined = strtolower(trim((string)$title . ' ' . (string)$overview));
    $cityStr = strtolower(trim((string)($d['city'] ?? ($d['stay_city'] ?? ''))));
    $stayStr = strtolower(trim((string)($d['stay'] ?? ($d['hotel'] ?? ''))));

    if ($stayStr !== '' && (
        str_contains($stayStr, 'no stay') || 
        str_contains($stayStr, 'no hotel') || 
        str_contains($stayStr, 'overnight journey') || 
        str_contains($stayStr, 'tour end') || 
        str_contains($stayStr, 'trip end') || 
        str_contains($stayStr, 'conclude')
    )) {
        return true;
    }

    if ($cityStr !== '' && (
        str_contains($cityStr, 'transit') || 
        str_contains($cityStr, 'overnight') || 
        str_contains($cityStr, 'no stay') || 
        str_contains($cityStr, 'tour end') || 
        str_contains($cityStr, 'trip end')
    )) {
        return true;
    }

    $transitPhrases = [
        'onward journey', 'proceed to onward journey', 'overnight journey', 'overnight bus',
        'overnight volvo', 'overnight train', 'tour concludes', 'tour conclude',
        'trip concludes', 'trip conclude', 'trip ends', 'tour ends', 'airport drop',
        'railway station drop', 'drop at airport', 'drop at railway station', 'drop at bus stand'
    ];

    foreach ($transitPhrases as $tp) {
        if (str_contains($combined, $tp)) {
            return true;
        }
    }

    if ($idx === $total) {
        if (
            str_contains($combined, 'departure') || 
            str_contains($combined, 'depart') || 
            str_contains($combined, 'drop') || 
            str_contains($combined, 'return') || 
            str_contains($combined, 'fly back') || 
            str_contains($combined, 'back home')
        ) {
            return true;
        }
    }

    return false;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pkgTitle); ?> - Modern Travel Deck</title>

    <!-- Google Fonts & Remix Icon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --modern-indigo: #4f46e5;
            --modern-violet: #7c3aed;
            --modern-cyan: #06b6d4;
            --modern-pink: #ec4899;
            --modern-emerald: #10b981;
            --modern-dark: #0f172a;
            --modern-slate: #334155;
            --modern-muted: #64748b;
            --modern-bg-light: #f8fafc;
            --modern-card-bg: #ffffff;
            --modern-border: #e2e8f0;
            --modern-border-subtle: rgba(226, 232, 240, 0.8);
            --modern-gradient-main: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #9333ea 100%);
            --modern-gradient-accent: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --modern-gradient-glow: linear-gradient(90deg, #4f46e5, #06b6d4, #ec4899);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #e6ecf4;
            color: var(--modern-dark);
            padding: 24px 0 60px 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar (Glassmorphic Floating Pill) */
        .v3-pdf-actions-bar {
            position: fixed;
            top: 16px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            padding: 8px 14px;
            border-radius: 999px;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
            border: 1px solid rgba(79, 70, 229, 0.25);
        }

        .btn-pdf-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-pdf-download {
            background: var(--modern-gradient-main);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .btn-pdf-download:hover {
            opacity: 0.94;
            transform: translateY(-1px);
        }

        .btn-pdf-print {
            background: #ffffff;
            color: var(--modern-dark);
            border: 1px solid var(--modern-border);
        }

        .btn-pdf-print:hover {
            background: #f1f5f9;
        }

        /* A4 Page Container: 210mm x 297mm */
        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            margin: 0 auto 30px auto;
            background: #ffffff;
            page-break-before: always;
            page-break-after: always;
            page-break-inside: avoid;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.09);
            border-radius: 12px;
        }

        .page:first-child {
            page-break-before: auto;
        }

        /* Modern Studio Top Bar */
        .modern-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 40px 12px 40px;
            background: #ffffff;
            border-bottom: 1px solid var(--modern-border);
        }

        .modern-bar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-studio-logo {
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        .modern-brand-tag {
            font-size: 13px;
            font-weight: 800;
            color: var(--modern-indigo);
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .modern-bar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modern-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: var(--modern-emerald);
            background: #ecfdf5;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #a7f3d0;
            text-transform: uppercase;
        }

        .modern-status-badge .dot {
            width: 6px;
            height: 6px;
            background: var(--modern-emerald);
            border-radius: 50%;
        }

        .modern-ref-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 800;
            color: #ffffff;
            background: var(--modern-dark);
            padding: 4px 12px;
            border-radius: 999px;
            letter-spacing: 0.4px;
        }

        /* Modern Deck Main Padding Body */
        .modern-page-body {
            padding: 18px 40px 0 40px;
            max-height: calc(297mm - 60mm - 40mm);
            overflow: hidden;
        }

        /* COVER PAGE: Neo-Modernist Floating Hero Deck */
        .modern-hero-stage {
            position: relative;
            width: 100%;
            height: 82mm;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
            background: var(--modern-gradient-main);
        }

        .modern-hero-stage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .modern-hero-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #ffffff;
            background: var(--modern-gradient-main);
            padding: 20px;
            text-align: center;
        }

        .modern-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.75) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 24px;
        }

        .modern-overlay-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-glass-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-dest-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modern-overlay-bottom {
            color: #ffffff;
        }

        .modern-hero-eyebrow {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a5f3fc;
            margin-bottom: 4px;
        }

        .modern-hero-title {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            max-width: 90%;
        }

        /* 4 Capsule Row */
        .modern-capsule-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 16px;
            margin-bottom: 16px;
        }

        .modern-capsule-row.has-travel-date {
            grid-template-columns: repeat(5, 1fr);
        }

        .modern-capsule {
            background: #ffffff;
            border: 1px solid var(--modern-border);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .modern-capsule-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--modern-indigo);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .modern-capsule-text {
            overflow: hidden;
        }

        .modern-capsule-lbl {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--modern-muted);
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .modern-capsule-val {
            font-size: 13px;
            font-weight: 800;
            color: var(--modern-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Bento Overview Card */
        .modern-bento-overview {
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 16px 20px;
            position: relative;
            margin-bottom: 16px;
        }

        .modern-bento-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .modern-bento-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--modern-indigo);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-bento-tag i {
            font-size: 16px;
        }

        .modern-bento-para {
            font-size: 13px;
            line-height: 1.65;
            color: var(--modern-slate);
            font-family: 'Inter', sans-serif;
            margin-bottom: 12px;
        }

        .modern-bento-para p {
            margin-bottom: 8px;
        }

        .modern-tag-deck {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .modern-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--modern-slate);
            background: #ffffff;
            border: 1px solid var(--modern-border);
            padding: 4px 10px;
            border-radius: 999px;
        }

        .modern-tag-pill i {
            color: var(--modern-indigo);
        }

        /* Route Milestone Strip */
        .modern-route-deck {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--modern-border);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-route-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--modern-indigo);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .modern-route-nodes {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            flex: 1;
        }

        .modern-node-chip {
            font-size: 11px;
            font-weight: 700;
            color: var(--modern-dark);
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .modern-node-arrow {
            font-size: 10px;
            color: var(--modern-muted);
        }

        /* DAY BY DAY PAGES: High-Tech Inset Visual & Modern Cards */
        .modern-day-banner {
            position: relative;
            width: 100%;
            height: 64mm;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            background: var(--modern-gradient-main);
            margin-bottom: 16px;
        }

        .modern-day-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .modern-day-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px 20px;
        }

        .modern-day-banner-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-day-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: var(--modern-indigo);
            font-size: 12px;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .modern-day-banner-bottom h2 {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.3;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        /* Day Body & Narrative Card */
        .modern-day-narrative-card {
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 16px 20px;
            margin-bottom: 14px;
        }

        .modern-day-narrative-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .modern-plan-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--modern-indigo);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-day-text {
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--modern-slate);
            font-family: 'Inter', sans-serif;
            margin-bottom: 12px;
        }

        .modern-day-text p {
            margin-bottom: 8px;
        }

        /* Modern Hotel Capsule Card */
        .modern-hotel-capsule {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            margin-bottom: 12px;
        }

        .modern-hotel-thumb {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
            background: #e2e8f0;
        }

        .modern-hotel-info {
            flex: 1;
            overflow: hidden;
        }

        .modern-hotel-top-tag {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 3px;
        }

        .modern-hotel-badge {
            font-size: 10px;
            font-weight: 800;
            color: var(--modern-violet);
            background: #f5f3ff;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .modern-hotel-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--modern-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .modern-hotel-chips {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .modern-hotel-chip {
            font-size: 11px;
            font-weight: 600;
            color: var(--modern-slate);
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .modern-hotel-chip.meal {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 700;
        }

        /* Modern Transit Capsule */
        .modern-transit-capsule {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--modern-border);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-transit-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #e0f2fe;
            color: #0284c7;
            display: grid;
            place-items: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .modern-transit-text {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--modern-dark);
            flex: 1;
        }

        .modern-transit-sub {
            font-size: 11px;
            font-weight: 500;
            color: var(--modern-muted);
        }

        /* PRICING & PAYMENT PAGE: Neo-Modernist Checkout Deck */
        .modern-pricing-stage {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .modern-price-hero-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
            border-radius: 20px;
            padding: 22px 26px;
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(67, 56, 202, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-price-hero-left .price-tagline {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a5b4fc;
            margin-bottom: 4px;
        }

        .modern-price-hero-left .big-price {
            font-size: 34px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }

        .modern-price-hero-left .price-meta {
            font-size: 12px;
            color: #c7d2fe;
            margin-top: 4px;
        }

        .modern-price-hero-right {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .modern-installment-pill {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .modern-installment-pill.advance {
            background: #10b981;
            border-color: #34d399;
        }

        /* Modern Breakdown Card */
        .modern-table-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            margin-top: 14px;
            margin-bottom: 14px;
        }

        .modern-breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-breakdown-table th {
            background: #f8fafc;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--modern-muted);
            letter-spacing: 0.5px;
            text-align: left;
            border-bottom: 1px solid var(--modern-border);
        }

        .modern-breakdown-table td {
            padding: 11px 16px;
            font-size: 13px;
            color: var(--modern-dark);
            border-bottom: 1px solid #f1f5f9;
        }

        .modern-breakdown-table tr:last-child td {
            border-bottom: none;
        }

        .modern-breakdown-table .num-col {
            text-align: right;
            font-weight: 700;
        }

        .modern-breakdown-table tr.total-row td {
            background: #f8fafc;
            font-weight: 800;
            color: var(--modern-indigo);
            font-size: 14px;
        }

        /* Modern Remittance Dual Deck */
        .modern-remit-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 14px;
        }

        .modern-bank-card {
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 16px 18px;
            position: relative;
        }

        .modern-bank-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .modern-bank-tag {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--modern-indigo);
            letter-spacing: 0.5px;
        }

        .modern-bank-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .modern-bank-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .modern-bank-lbl {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--modern-muted);
        }

        .modern-bank-val {
            font-size: 13px;
            font-weight: 800;
            color: var(--modern-dark);
        }

        .modern-upi-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        }

        .modern-qr-frame {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            border: 2px solid var(--modern-indigo);
            padding: 4px;
            background: #ffffff;
            margin-bottom: 8px;
        }

        .modern-qr-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .modern-upi-id-pill {
            background: #eef2ff;
            color: var(--modern-indigo);
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 999px;
            letter-spacing: 0.3px;
        }

        /* INCLUSIONS & EXCLUSIONS: Modern Bento Deck */
        .modern-inclusions-grid,
        .modern-exclusions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
        }

        .modern-clause-card {
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--modern-border);
            padding: 10px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .modern-check-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ecfdf5;
            color: var(--modern-emerald);
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .modern-cross-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fef2f2;
            color: #ef4444;
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .modern-clause-text {
            font-size: 12.5px;
            line-height: 1.45;
            color: var(--modern-dark);
            font-weight: 600;
        }

        .modern-advisory-box {
            background: #eff6ff;
            border-radius: 14px;
            border: 1px solid #bfdbfe;
            padding: 14px 18px;
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-advisory-icon {
            font-size: 22px;
            color: #2563eb;
            flex-shrink: 0;
        }

        .modern-advisory-text {
            font-size: 12px;
            line-height: 1.5;
            color: #1e40af;
        }

        /* SPECIALIST & CONTACT PAGE */
        .modern-specialist-deck {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 14px;
        }

        .modern-contact-card {
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid var(--modern-border);
            padding: 18px 20px;
        }

        .modern-contact-card-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--modern-indigo);
            letter-spacing: 0.5px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modern-contact-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .modern-contact-icon-pill {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--modern-indigo);
            display: grid;
            place-items: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .modern-contact-info-lbl {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--modern-muted);
        }

        .modern-contact-info-val {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--modern-dark);
            text-decoration: none;
        }

        /* POLICIES & TERMS */
        .modern-policy-container {
            margin-top: 14px;
        }

        .modern-policy-block {
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid var(--modern-border);
            padding: 14px 18px;
            margin-bottom: 12px;
        }

        .modern-policy-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--modern-indigo);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .modern-policy-content {
            font-size: 12px;
            line-height: 1.6;
            color: var(--modern-slate);
            font-family: 'Inter', sans-serif;
        }

        /* PDF FOOTER DECOR (Matching MIT/Aleya requirement) */
        .page-footer-decor {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 52px;
            box-sizing: border-box;
            padding: 0 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
        }

        .bottom-decor {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }

        .decor-logo {
            max-height: 22px;
            width: auto;
            object-fit: contain;
        }

        .decor-company {
            font-size: 11px;
            font-weight: 700;
            color: var(--modern-muted);
        }

        .bottom-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
        }

        .meta-credit {
            color: var(--modern-indigo);
            font-weight: 700;
        }

        .meta-page-num {
            font-weight: 700;
        }

        /* Print media rules */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .v3-pdf-actions-bar {
                display: none !important;
            }
            .page {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Top Bar for PDF Actions -->
    <div class="v3-pdf-actions-bar" id="v3-pdf-actions-bar">
        <button class="btn-pdf-action btn-pdf-download" id="btnDownloadPDF">
            <i class="ri-file-pdf-line"></i> Download PDF
        </button>
        <button class="btn-pdf-action btn-pdf-print" id="btnPrintPage">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>

    <!-- ================= PAGE 1: COVER & OVERVIEW DECK ================= -->
    <div class="page" id="page-1">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-status-badge">
                    <span class="dot"></span> Active Proposal
                </div>
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?><?php if (!empty($formattedTravelDate)): ?> · <?php echo htmlspecialchars($formattedTravelDate); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <!-- Floating Hero Stage -->
            <div class="modern-hero-stage">
                <?php if (!empty($heroFeatureImage)): ?>
                    <img src="<?php echo htmlspecialchars($heroFeatureImage); ?>" alt="<?php echo htmlspecialchars($pkgTitle); ?>">
                <?php else: ?>
                    <div class="modern-hero-placeholder">
                        <h2><?php echo htmlspecialchars($pkgCity); ?></h2>
                    </div>
                <?php endif; ?>

                <div class="modern-hero-overlay">
                    <div class="modern-overlay-top">
                        <div class="modern-glass-pill">
                            <i class="ri-sparkling-fill"></i> Curated Experience
                        </div>
                        <div class="modern-dest-pill">
                            <i class="ri-map-pin-2-fill"></i> <?php echo htmlspecialchars($pkgCity); ?><?php echo !empty($pkgState) ? ', ' . htmlspecialchars($pkgState) : ''; ?>
                        </div>
                    </div>
                    <div class="modern-overlay-bottom">
                        <div class="modern-hero-eyebrow">Tailored Travel Deck</div>
                        <h1 class="modern-hero-title"><?php echo htmlspecialchars($pkgTitle); ?></h1>
                    </div>
                </div>
            </div>

            <!-- 4 Capsule Stats Row -->
            <div class="modern-capsule-row <?php echo !empty($formattedTravelDate) ? 'has-travel-date' : ''; ?>">
                <div class="modern-capsule">
                    <div class="modern-capsule-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="modern-capsule-text">
                        <div class="modern-capsule-lbl">Duration</div>
                        <div class="modern-capsule-val"><?php echo $totalDaysCount; ?>D / <?php echo $totalNightsCount; ?>N</div>
                    </div>
                </div>
                <?php if (!empty($formattedTravelDate)): ?>
                <div class="modern-capsule">
                    <div class="modern-capsule-icon">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <div class="modern-capsule-text">
                        <div class="modern-capsule-lbl">Travel Date</div>
                        <div class="modern-capsule-val"><?php echo htmlspecialchars($formattedTravelDate); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="modern-capsule">
                    <div class="modern-capsule-icon">
                        <i class="ri-group-line"></i>
                    </div>
                    <div class="modern-capsule-text">
                        <div class="modern-capsule-lbl">Travelers</div>
                        <div class="modern-capsule-val"><?php echo htmlspecialchars($paxSummaryText); ?></div>
                    </div>
                </div>
                <div class="modern-capsule">
                    <div class="modern-capsule-icon">
                        <i class="ri-compass-3-line"></i>
                    </div>
                    <div class="modern-capsule-text">
                        <div class="modern-capsule-lbl">Style</div>
                        <div class="modern-capsule-val"><?php echo htmlspecialchars($pkgTheme); ?></div>
                    </div>
                </div>
                <div class="modern-capsule">
                    <div class="modern-capsule-icon">
                        <i class="ri-hotel-line"></i>
                    </div>
                    <div class="modern-capsule-text">
                        <div class="modern-capsule-lbl">Stays</div>
                        <div class="modern-capsule-val"><?php echo htmlspecialchars($formatCategoryName($pkgHotel)); ?></div>
                    </div>
                </div>
            </div>

            <!-- Bento Overview Card -->
            <div class="modern-bento-overview">
                <div class="modern-bento-header">
                    <div class="modern-bento-tag">
                        <i class="ri-file-text-line"></i> Expedition Overview
                    </div>
                </div>
                <div class="modern-bento-para">
                    <?php if (!empty($cleanOverview)): ?>
                        <?php echo $cleanOverview; ?>
                    <?php else: ?>
                        <p>Welcome to your personalized itinerary. Handcrafted with precision, scenic discovery, premium accommodations, and seamless transfers for an unforgettable journey.</p>
                    <?php endif; ?>
                </div>
                <div class="modern-tag-deck">
                    <span class="modern-tag-pill"><i class="ri-check-line"></i> Private AC Transfers</span>
                    <span class="modern-tag-pill"><i class="ri-check-line"></i> Curated Stays</span>
                    <span class="modern-tag-pill"><i class="ri-check-line"></i> Daily Breakfast</span>
                    <span class="modern-tag-pill"><i class="ri-check-line"></i> 24/7 Specialist Support</span>
                </div>
            </div>

            <!-- Route Nodes Strip -->
            <?php if (!empty($pkgDays) && count($pkgDays) > 1): ?>
                <div class="modern-route-deck">
                    <div class="modern-route-label">Route Plan:</div>
                    <div class="modern-route-nodes">
                        <?php foreach ($pkgDays as $rIdx => $rDay): 
                            $nodeCity = !empty($rDay['city']) ? $rDay['city'] : (!empty($rDay['stay_city']) ? $rDay['stay_city'] : 'Day ' . ($rIdx + 1));
                            $nodeDate = ($travelTimestamp && $travelTimestamp > 0) ? date('d M', strtotime("+{$rIdx} days", $travelTimestamp)) : '';
                        ?>
                            <span class="modern-node-chip">
                                <?php echo htmlspecialchars($nodeCity); ?><?php if (!empty($nodeDate)): ?> (<?php echo $nodeDate; ?>)<?php endif; ?>
                            </span>
                            <?php if ($rIdx < count($pkgDays) - 1): ?>
                                <span class="modern-node-arrow"><i class="ri-arrow-right-s-line"></i></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page 1 of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= DAY BY DAY PAGES (1 Page Per Day) ================= -->
    <?php 
    if (!empty($pkgDays)): 
        foreach ($pkgDays as $index => $day):
            $dayNum = $index + 1;
            $dayOffset = $index;
            $dayTimestamp = null;
            if (!empty($day['date']) || !empty($day['day_date'])) {
                $rawDDate = trim((string)($day['date'] ?? $day['day_date']));
                $dTs = strtotime($rawDDate);
                if ($dTs !== false && $dTs > 0) {
                    $dayTimestamp = $dTs;
                }
            }
            if (!$dayTimestamp && $travelTimestamp) {
                $dayTimestamp = strtotime("+{$dayOffset} days", $travelTimestamp);
            }
            $dayDateStr = $dayTimestamp ? date('d M Y', $dayTimestamp) : '';
            $dayDateWithDay = $dayTimestamp ? date('D, d M Y', $dayTimestamp) : '';
            $dayPageId = 'page-day-' . $dayNum;
            $dayTitle = capitalizeFirstWord(cleanItineraryUnicode($day['title'] ?? ($day['day_title'] ?? "Day {$dayNum} Experience")));
            $dayDesc = capitalizeParagraphs(cleanItineraryUnicode($day['desc'] ?? ($day['description'] ?? ($day['overview'] ?? ''))));
            $dayCity = cleanItineraryUnicode($day['city'] ?? ($day['stay_city'] ?? $pkgCity));
            $dayImage = fixImageUrl($day['image'] ?? ($day['day_image'] ?? ''));
            $isTransit = $checkTransitOrTripEnd($day, $dayNum, $totalDaysCount, $dayTitle, $dayDesc);

            // Hotel info for this night
            $assignedHotel = $dayWiseHotels[$dayNum] ?? null;
            $hotelName = $assignedHotel['property_title'] ?? ($day['hotel'] ?? ($day['stay'] ?? 'Boutique Stay'));
            $hotelThumb = fixImageUrl($assignedHotel['property_thumbnail'] ?? $getCategoryThumbnail($pkgHotel, $hotelName));
            $hotelRoom = $assignedHotel['tph_room_type'] ?? ($day['room_type'] ?? 'Deluxe Room');
            $hotelMeal = $cleanMealPlan($day['meal'] ?? ($day['meal_plan'] ?? 'CP'));
    ?>
    <div class="page" id="<?php echo $dayPageId; ?>">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Day <?php echo $dayNum; ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <!-- Modern Inset Day Banner -->
            <div class="modern-day-banner">
                <?php if (!empty($dayImage)): ?>
                    <img src="<?php echo htmlspecialchars($dayImage); ?>" alt="<?php echo htmlspecialchars($dayTitle); ?>">
                <?php else: ?>
                    <div class="modern-hero-placeholder">
                        <h2>DAY <?php echo $dayNum; ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?></h2>
                    </div>
                <?php endif; ?>

                <div class="modern-day-banner-overlay">
                    <div class="modern-day-banner-top">
                        <div class="modern-day-badge">
                            <i class="ri-calendar-event-line"></i> Day <?php echo sprintf('%02d', $dayNum); ?> of <?php echo sprintf('%02d', $totalDaysCount); ?><?php if (!empty($dayDateWithDay)): ?> &bull; <?php echo $dayDateWithDay; ?><?php endif; ?>
                        </div>
                        <div class="modern-dest-pill">
                            <i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($dayCity); ?>
                        </div>
                    </div>
                    <div class="modern-day-banner-bottom">
                        <h2><?php echo htmlspecialchars($dayTitle); ?></h2>
                    </div>
                </div>
            </div>

            <!-- Day Narrative Card -->
            <div class="modern-day-narrative-card">
                <div class="modern-day-narrative-header">
                    <div class="modern-plan-label">
                        <i class="ri-navigation-line"></i> Planned Activities & Sightseeing
                    </div>
                </div>
                <div class="modern-day-text">
                    <?php if (!empty($dayDesc)): ?>
                        <?php echo (strpos($dayDesc, '<') !== false) ? $dayDesc : nl2br(htmlspecialchars($dayDesc)); ?>
                    <?php else: ?>
                        <p>Immerse yourself in today's scenic sights and local highlights. Transfer arrangements and curated stops are scheduled as per the day's travel flow.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Accommodation Capsule -->
            <?php if (!$isTransit): ?>
                <div class="modern-hotel-capsule">
                    <img src="<?php echo htmlspecialchars($hotelThumb); ?>" alt="<?php echo htmlspecialchars($hotelName); ?>" class="modern-hotel-thumb">
                    <div class="modern-hotel-info">
                        <div class="modern-hotel-top-tag">
                            <span class="modern-hotel-badge">Night <?php echo $dayNum; ?> Stay</span>
                            <span style="font-size:11px; color:#64748b;"><i class="ri-star-fill" style="color:#eab308;"></i> <?php echo htmlspecialchars($formatCategoryName($pkgHotel)); ?></span>
                        </div>
                        <div class="modern-hotel-name"><?php echo htmlspecialchars($hotelName); ?></div>
                        <div class="modern-hotel-chips">
                            <span class="modern-hotel-chip"><i class="ri-hotel-bed-line"></i> <?php echo htmlspecialchars($hotelRoom); ?></span>
                            <span class="modern-hotel-chip meal"><i class="ri-restaurant-line"></i> <?php echo htmlspecialchars($hotelMeal); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-transit-capsule" style="background:#fef2f2; border-color:#fecaca; margin-bottom:12px;">
                    <div class="modern-transit-icon" style="background:#fee2e2; color:#ef4444;">
                        <i class="ri-flight-takeoff-line"></i>
                    </div>
                    <div>
                        <div class="modern-transit-text" style="color:#991b1b;">Departure & Onward Journey</div>
                        <div class="modern-transit-sub">No overnight stay required · Departure transfer as scheduled.</div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Transit Capsule -->
            <div class="modern-transit-capsule">
                <div class="modern-transit-icon">
                    <i class="ri-roadster-line"></i>
                </div>
                <div class="modern-transit-text">
                    <?php echo !empty($pkgLocVehicle) ? htmlspecialchars($pkgLocVehicle) : (!empty($pkgOutVehicle) ? htmlspecialchars($pkgOutVehicle) : 'Dedicated AC Private Vehicle'); ?>
                </div>
                <div class="modern-transit-sub">
                    Private Dedicated Chauffeur
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($dayNum + 1); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>
    <?php 
        endforeach; 
    endif; 
    ?>

    <!-- ================= PRICING & REMITTANCE PAGE ================= -->
    <div class="page" id="page-payment">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Pricing
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <!-- Modern Price Hero Card -->
            <div class="modern-price-hero-card">
                <div class="modern-price-hero-left">
                    <div class="price-tagline">Total Package Price</div>
                    <div class="big-price">₹<?php echo number_format($totalPriceVal, 2); ?></div>
                    <div class="price-meta"><?php echo htmlspecialchars($paxSummaryText); ?> · Taxes Included · Quote Valid Until <?php echo $validUntilDate; ?></div>
                </div>
                <div class="modern-price-hero-right">
                    <div class="modern-installment-pill advance">
                        <i class="ri-checkbox-circle-fill"></i> 30% Booking Advance: ₹<?php echo number_format($advance30Percent, 2); ?>
                    </div>
                    <div class="modern-installment-pill">
                        <i class="ri-time-line"></i> 70% Final Balance: ₹<?php echo number_format($balance70Percent, 2); ?>
                    </div>
                </div>
            </div>

            <!-- Modern Breakdown Table Card -->
            <div class="modern-table-card">
                <table class="modern-breakdown-table">
                    <thead>
                        <tr>
                            <th>Tariff Item / Passenger Category</th>
                            <th class="num-col">Count</th>
                            <th class="num-col">Rate (INR)</th>
                            <th class="num-col">Subtotal (INR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Adult Travelers</strong> (Twin Sharing Base)</td>
                            <td class="num-col"><?php echo $adultCount; ?></td>
                            <td class="num-col">₹<?php echo number_format($adultPriceVal, 2); ?></td>
                            <td class="num-col">₹<?php echo number_format($adultSubtotal, 2); ?></td>
                        </tr>
                        <?php if ($childCount > 0): ?>
                        <tr>
                            <td><strong>Child Travelers</strong> (With/Without Bed)</td>
                            <td class="num-col"><?php echo $childCount; ?></td>
                            <td class="num-col">₹<?php echo number_format($childPriceVal, 2); ?></td>
                            <td class="num-col">₹<?php echo number_format($childSubtotal, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($infantCount > 0): ?>
                        <tr>
                            <td><strong>Infant Travelers</strong></td>
                            <td class="num-col"><?php echo $infantCount; ?></td>
                            <td class="num-col">₹<?php echo number_format($infantPriceVal, 2); ?></td>
                            <td class="num-col">₹<?php echo number_format($infantSubtotal, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td colspan="3">Net Total Package Cost (Inclusive of all services)</td>
                            <td class="num-col">₹<?php echo number_format($totalPriceVal, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Remittance Dual Deck -->
            <div class="modern-remit-grid">
                <!-- Digital Bank Card -->
                <div class="modern-bank-card">
                    <div class="modern-bank-header">
                        <div class="modern-bank-tag"><i class="ri-bank-card-line"></i> Official Bank Remittance</div>
                    </div>
                    <div class="modern-bank-fields">
                        <div class="modern-bank-field">
                            <div class="modern-bank-lbl">Bank Name</div>
                            <div class="modern-bank-val"><?php echo htmlspecialchars($companyBankName ?: 'HDFC Bank Ltd.'); ?></div>
                        </div>
                        <div class="modern-bank-field">
                            <div class="modern-bank-lbl">Account Holder</div>
                            <div class="modern-bank-val"><?php echo htmlspecialchars($companyBankAcName ?: $siteName); ?></div>
                        </div>
                        <div class="modern-bank-field">
                            <div class="modern-bank-lbl">Account Number</div>
                            <div class="modern-bank-val"><?php echo htmlspecialchars($companyBankAcNumber ?: '98765432100'); ?></div>
                        </div>
                        <div class="modern-bank-field">
                            <div class="modern-bank-lbl">IFSC Code</div>
                            <div class="modern-bank-val"><?php echo htmlspecialchars($companyBankIfsc ?: 'HDFC0001234'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Instant UPI Terminal -->
                <div class="modern-upi-card">
                    <div class="modern-bank-tag" style="margin-bottom:8px;"><i class="ri-qr-code-line"></i> Instant UPI Pay</div>
                    <div class="modern-qr-frame">
                        <?php if (!empty($upiQrUrl)): ?>
                            <img src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="Scan to Pay">
                        <?php else: ?>
                            <div style="height:100%; display:grid; place-items:center; font-size:10px; color:#64748b;">Scan UPI</div>
                        <?php endif; ?>
                    </div>
                    <div class="modern-upi-id-pill">
                        UPI: <?php echo htmlspecialchars($companyUpiId ?: 'travel@upi'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($totalDaysCount + 2); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= INCLUSIONS PAGE ================= -->
    <div class="page" id="page-inclusions">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Inclusions
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <span style="font-size:11px; font-weight:800; color:var(--modern-emerald); text-transform:uppercase; letter-spacing:0.5px; background:#ecfdf5; padding:4px 10px; border-radius:999px;">
                    <i class="ri-checkbox-circle-fill"></i> What Is Included
                </span>
                <h2 style="font-size:18px; font-weight:800; color:var(--modern-dark); margin:0;">All-Inclusive Package Features</h2>
            </div>

            <div class="modern-inclusions-grid">
                <?php if (!empty($incList)): ?>
                    <?php foreach ($incList as $inc): ?>
                        <div class="modern-clause-card">
                            <div class="modern-check-icon"><i class="ri-check-line"></i></div>
                            <div class="modern-clause-text"><?php echo htmlspecialchars($inc); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="modern-clause-card">
                        <div class="modern-check-icon"><i class="ri-check-line"></i></div>
                        <div class="modern-clause-text">Comfortable accommodations as specified in itinerary.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-check-icon"><i class="ri-check-line"></i></div>
                        <div class="modern-clause-text">Daily buffet breakfast at all booked hotels.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-check-icon"><i class="ri-check-line"></i></div>
                        <div class="modern-clause-text">Private air-conditioned vehicle for transfers and sightseeing.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-check-icon"><i class="ri-check-line"></i></div>
                        <div class="modern-clause-text">Toll taxes, parking fees, driver allowances, and fuel charges.</div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="modern-advisory-box">
                <i class="ri-information-line modern-advisory-icon"></i>
                <div class="modern-advisory-text">
                    All inclusions are strictly confirmed upon booking confirmation. Any customized activity not listed above can be seamlessly arranged via your dedicated specialist.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($totalDaysCount + 3); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= EXCLUSIONS PAGE ================= -->
    <div class="page" id="page-exclusions">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Exclusions
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <span style="font-size:11px; font-weight:800; color:#ef4444; text-transform:uppercase; letter-spacing:0.5px; background:#fef2f2; padding:4px 10px; border-radius:999px;">
                    <i class="ri-close-circle-fill"></i> What Is Excluded
                </span>
                <h2 style="font-size:18px; font-weight:800; color:var(--modern-dark); margin:0;">Out-of-Pocket & Optional Expenses</h2>
            </div>

            <div class="modern-exclusions-grid">
                <?php if (!empty($excList)): ?>
                    <?php foreach ($excList as $exc): ?>
                        <div class="modern-clause-card">
                            <div class="modern-cross-icon"><i class="ri-close-line"></i></div>
                            <div class="modern-clause-text"><?php echo htmlspecialchars($exc); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="modern-clause-card">
                        <div class="modern-cross-icon"><i class="ri-close-line"></i></div>
                        <div class="modern-clause-text">Airfare, train tickets, or intercity flight tickets unless mentioned.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-cross-icon"><i class="ri-close-line"></i></div>
                        <div class="modern-clause-text">Entry tickets to monuments, museums, and camera fees.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-cross-icon"><i class="ri-close-line"></i></div>
                        <div class="modern-clause-text">Personal expenses like laundry, minibar, phone calls, and tips.</div>
                    </div>
                    <div class="modern-clause-card">
                        <div class="modern-cross-icon"><i class="ri-close-line"></i></div>
                        <div class="modern-clause-text">Travel insurance and medical emergency coverage.</div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="modern-advisory-box" style="background:#fffbeb; border-color:#fde68a;">
                <i class="ri-alert-line modern-advisory-icon" style="color:#d97706;"></i>
                <div class="modern-advisory-text" style="color:#92400e;">
                    Optional activities like gondola rides, pony rides, rafting, or snow sports can be booked directly on-site or pre-arranged through your travel specialist.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($totalDaysCount + 4); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= SPECIALIST & CONTACT PAGE ================= -->
    <div class="page" id="page-contact">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Specialist
                </div>
            </div>
        </div>

        <div class="modern-page-body">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <span style="font-size:11px; font-weight:800; color:var(--modern-indigo); text-transform:uppercase; letter-spacing:0.5px; background:#eef2ff; padding:4px 10px; border-radius:999px;">
                    <i class="ri-customer-service-2-fill"></i> Dedicated Support
                </span>
                <h2 style="font-size:18px; font-weight:800; color:var(--modern-dark); margin:0;">Your Personal Destination Specialist</h2>
            </div>

            <div class="modern-specialist-deck">
                <!-- Specialist Card -->
                <div class="modern-contact-card">
                    <div class="modern-contact-card-title">
                        <i class="ri-user-star-line"></i> Dedicated Travel Concierge
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill">
                            <i class="ri-phone-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Direct Phone Line</div>
                            <a href="tel:<?php echo htmlspecialchars($sitePhone); ?>" class="modern-contact-info-val"><?php echo htmlspecialchars($sitePhone); ?></a>
                        </div>
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill" style="background:#ecfdf5; color:#10b981;">
                            <i class="ri-whatsapp-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Instant WhatsApp</div>
                            <a href="https://wa.me/<?php echo $cleanWhatsappDigits; ?>" class="modern-contact-info-val"><?php echo htmlspecialchars($siteWhatsapp); ?></a>
                        </div>
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill" style="background:#fdf4ff; color:#c026d3;">
                            <i class="ri-mail-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Direct Email</div>
                            <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>" class="modern-contact-info-val"><?php echo htmlspecialchars($siteEmail); ?></a>
                        </div>
                    </div>
                </div>

                <!-- Agency HQ Card -->
                <div class="modern-contact-card">
                    <div class="modern-contact-card-title">
                        <i class="ri-building-line"></i> Agency Headquarters
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Studio Address</div>
                            <div class="modern-contact-info-val"><?php echo htmlspecialchars($siteAddress); ?></div>
                        </div>
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill">
                            <i class="ri-global-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Official Portal</div>
                            <div class="modern-contact-info-val"><?php echo htmlspecialchars($siteDomain); ?></div>
                        </div>
                    </div>
                    <div class="modern-contact-row">
                        <div class="modern-contact-icon-pill">
                            <i class="ri-time-line"></i>
                        </div>
                        <div>
                            <div class="modern-contact-info-lbl">Operational Hours</div>
                            <div class="modern-contact-info-val">Mon - Sat: 9:30 AM - 8:00 PM IST</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($totalDaysCount + 5); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= POLICIES & TERMS (Dynamic Dynamic Multi-Page) ================= -->
    <div class="page" id="page-policies">
        <!-- Top Studio Bar -->
        <div class="modern-top-bar">
            <div class="modern-bar-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="modern-studio-logo">
                <?php else: ?>
                    <span class="modern-brand-tag"><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </div>
            <div class="modern-bar-right">
                <div class="modern-ref-pill">
                    <?php echo htmlspecialchars($cleanQuoteRef); ?> · Policies
                </div>
            </div>
        </div>

        <div class="modern-page-body policy-flow-container">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <span style="font-size:11px; font-weight:800; color:var(--modern-indigo); text-transform:uppercase; letter-spacing:0.5px; background:#eef2ff; padding:4px 10px; border-radius:999px;">
                    <i class="ri-shield-check-line"></i> Governance
                </span>
                <h2 style="font-size:18px; font-weight:800; color:var(--modern-dark); margin:0;">Terms & Booking Guidelines</h2>
            </div>

            <div class="modern-policy-container">
                <!-- Payment Policy -->
                <div class="modern-policy-block">
                    <div class="modern-policy-tag"><i class="ri-secure-payment-line"></i> Payment & Advance Terms</div>
                    <div class="modern-policy-content">
                        <?php if (!empty($rawPaymentPolicy)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawPaymentPolicy, '<') !== false) ? cleanItineraryUnicode($rawPaymentPolicy) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawPaymentPolicy)))); ?>
                        <?php else: ?>
                            30% initial token deposit is required to confirm bookings and hotel reservations. The remaining 70% balance must be cleared prior to departure or upon arrival as agreed with your concierge.
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cancellation Policy -->
                <div class="modern-policy-block">
                    <div class="modern-policy-tag"><i class="ri-refund-line"></i> Cancellation & Refund Schedule</div>
                    <div class="modern-policy-content">
                        <?php if (!empty($rawCancellationPolicy)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawCancellationPolicy, '<') !== false) ? cleanItineraryUnicode($rawCancellationPolicy) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawCancellationPolicy)))); ?>
                        <?php else: ?>
                            Cancellations made 30+ days prior to departure are eligible for full refund less standard administrative processing charges. Cancellations within 15-30 days are subject to 50% retention.
                        <?php endif; ?>
                    </div>
                </div>

                <!-- General Terms -->
                <div class="modern-policy-block">
                    <div class="modern-policy-tag"><i class="ri-file-shield-line"></i> General Travel Conditions</div>
                    <div class="modern-policy-content">
                        <?php if (!empty($rawTermsConditions)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawTermsConditions, '<') !== false) ? cleanItineraryUnicode($rawTermsConditions) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawTermsConditions)))); ?>
                        <?php else: ?>
                            All travelers must carry valid government-issued photo identity cards. Itinerary sequence is subject to minor adjustments due to weather, local administrative directives, or unforeseen road conditions.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer-decor">
            <div class="bottom-decor">
                <span class="decor-company"><?php echo htmlspecialchars($siteDomain); ?></span>
            </div>
            <div class="bottom-meta-row">
                <span class="meta-credit">Powered by Aleya.in</span>
                <span class="meta-page-num" data-page-calc="true">Page X of Y</span>
            </div>
        </div>
    </div>

    <!-- Multi-Page Pagination & PDF Generator Engine -->
    <script>
        function updatePageNumbers() {
            const pages = document.querySelectorAll(".page");
            const total = pages.length;
            pages.forEach((p, idx) => {
                const numEl = p.querySelector(".meta-page-num");
                if (numEl) {
                    numEl.textContent = `Page ${idx + 1} of ${total}`;
                }
            });
        }

        function paginatePolicyPages() {
            const firstPolPage = document.getElementById("page-policies");
            if (!firstPolPage) return;

            const container = firstPolPage.querySelector(".policy-flow-container");
            if (!container) return;

            const blocks = Array.from(firstPolPage.querySelectorAll(".modern-policy-block"));
            if (blocks.length === 0) return;

            const maxAllowedHeight = 780; // safe threshold for A4 page flow
            let currentHeight = 0;
            let currentTargetPage = firstPolPage;
            let currentTargetContainer = container;
            let pageCounter = 1;

            blocks.forEach((block) => {
                const bHeight = block.offsetHeight || 120;
                if (currentHeight + bHeight > maxAllowedHeight && currentHeight > 0) {
                    pageCounter++;
                    const newPage = firstPolPage.cloneNode(true);
                    newPage.id = `page-policies-part-${pageCounter}`;
                    const newContainer = newPage.querySelector(".policy-flow-container");
                    if (newContainer) {
                        const newPolWrapper = newPage.querySelector(".modern-policy-container");
                        if (newPolWrapper) newPolWrapper.innerHTML = "";
                    }
                    firstPolPage.parentNode.insertBefore(newPage, currentTargetPage.nextSibling);
                    currentTargetPage = newPage;
                    currentTargetContainer = newPage.querySelector(".modern-policy-container") || newPage.querySelector(".policy-flow-container");
                    currentHeight = 0;
                }

                if (currentTargetPage !== firstPolPage) {
                    currentTargetContainer.appendChild(block);
                }
                currentHeight += bHeight;
            });

            updatePageNumbers();
        }

        window.addEventListener("DOMContentLoaded", () => {
            paginatePolicyPages();
            updatePageNumbers();
        });

        // PDF Generation via html2canvas & jspdf
        function showPageLoader(msgText) {
            let loader = document.getElementById('v3-pdf-loading-overlay');
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'v3-pdf-loading-overlay';
                loader.style.cssText = 'position:fixed; inset:0; background:rgba(15,23,42,0.85); backdrop-filter:blur(6px); z-index:999999; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ffffff; font-family:"Plus Jakarta Sans",sans-serif;';
                loader.innerHTML = `
                    <div style="width:48px; height:48px; border:4px solid rgba(255,255,255,0.2); border-top-color:#6366f1; border-radius:50%; animation:v3spin 0.8s linear infinite; margin-bottom:16px;"></div>
                    <div id="v3-pdf-loading-text" style="font-size:16px; font-weight:700;">Preparing Modern PDF Deck...</div>
                    <style>@keyframes v3spin { to { transform: rotate(360deg); } }</style>
                `;
                document.body.appendChild(loader);
            }
            const txt = document.getElementById('v3-pdf-loading-text');
            if (txt && msgText) txt.textContent = msgText;
            loader.style.display = 'flex';
        }

        function hidePageLoader() {
            const loader = document.getElementById('v3-pdf-loading-overlay');
            if (loader) loader.style.display = 'none';
        }

        async function downloadItineraryPDF() {
            const btn = document.getElementById('btnDownloadPDF');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line"></i> Generating...';
            }

            try {
                showPageLoader('Rendering Modern Itinerary Deck...');
                paginatePolicyPages();
                updatePageNumbers();

                const pages = document.querySelectorAll(".page");
                if (!pages || pages.length === 0) {
                    throw new Error("No pages found.");
                }

                const jspdfModule = window.jspdf || (typeof jsPDF !== 'undefined' ? { jsPDF } : null);
                if (!jspdfModule || !jspdfModule.jsPDF) {
                    throw new Error("jsPDF library not loaded.");
                }

                const pdf = new jspdfModule.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                for (let i = 0; i < pages.length; i++) {
                    showPageLoader(`Rendering page ${i + 1} of ${pages.length}...`);

                    const canvas = await html2canvas(pages[i], {
                        scale: 2,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: '#ffffff',
                        logging: false,
                        windowWidth: pages[i].scrollWidth || 794,
                        windowHeight: pages[i].scrollHeight || 1123
                    });

                    const imgData = canvas.toDataURL('image/jpeg', 0.96);
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;

                    pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, Math.min(pageHeight, pdfImgHeight));

                    if (i < pages.length - 1) {
                        pdf.addPage();
                    }
                }

                showPageLoader('Finalizing PDF Download...');
                <?php 
                $safePdfTitle = preg_replace('/_+/', '_', preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace('&', 'and', cleanItineraryUnicode($pkgTitle))));
                $safePdfTitle = trim($safePdfTitle, '_');
                if (empty($safePdfTitle)) $safePdfTitle = 'Modern_Tour_Package';
                ?>
                pdf.save("<?php echo $safePdfTitle; ?>_Deck.pdf");
                await new Promise(res => setTimeout(res, 400));
                return true;
            } catch (err) {
                console.error("PDF export error:", err);
                window.print();
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-pdf-line"></i> Download PDF';
                }
                hidePageLoader();
            }
        }

        const btnDl = document.getElementById('btnDownloadPDF');
        if (btnDl) {
            btnDl.addEventListener('click', downloadItineraryPDF);
        }

        const btnPr = document.getElementById('btnPrintPage');
        if (btnPr) {
            btnPr.addEventListener('click', () => window.print());
        }

        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const shouldAutoDownload = urlParams.get('download') === 'true' || urlParams.get('pdf') === 'true' || <?php echo (!empty($autoDownload) || !empty($isDownload)) ? 'true' : 'false'; ?>;
            if (shouldAutoDownload) {
                window.addEventListener('load', () => setTimeout(downloadItineraryPDF, 600));
            }
        })();
    </script>
</body>
</html>