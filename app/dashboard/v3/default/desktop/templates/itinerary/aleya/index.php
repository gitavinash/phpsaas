<?php

/**
 * V3 Itinerary Template: Royal Heritage Bespoke Atelier (Aleya Atelier)
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/aleya/index.php
 * Description: Ultra-luxury, bespoke royal heritage travel dossier. Double gold filigree frames,
 *              Playfair Display and Cormorant serif typography, heraldic crest accents,
 *              palatial residence cards, imperial tariff certificate, dedicated A4 pages,
 *              Aleya credit footer, and zero-break PDF generation.
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
$siteName     = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? '');
$sitePhone    = defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? '');
$siteEmail    = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress  = defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? '');
$siteWhatsapp = defined('SITE_WHATSAPP') ? SITE_WHATSAPP : ($company_whatsapp ?? $sitePhone);
$siteUrl      = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteDomain   = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($company_site_domain ?? parse_url($siteUrl, PHP_URL_HOST) ?? 'aleya.in'));
$siteLogo     = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));
$themeColor   = defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#c59b27';

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
    $payNowUrl = "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I wish to arrange remittance for Royal Expedition " . $cleanQuoteRef . " - Amount: INR " . number_format($totalPriceVal, 2));
} else {
    $payNowUrl = "tel:" . preg_replace('/[^\d\+]/', '', (string)$sitePhone);
}

// Package Variables
$pkgTitle       = capitalizeFirstWord(cleanItineraryUnicode($tour_package_title ?? ($itinerary_title ?? ($title ?? 'The Royal Expedition'))));
$pkgCity        = cleanItineraryUnicode($tour_package_city ?? ($city ?? 'Estate Destination'));
$pkgState       = cleanItineraryUnicode($tour_package_state ?? '');
$pkgCountry     = cleanItineraryUnicode($tour_package_country ?? '');
$pkgDuration    = $tour_package_duration ?? ($numberOfDays ?? ($duration ?? 0));
$pkgTheme       = cleanItineraryUnicode($tour_package_theme ?? ($travelStyle ?? 'Royal Heritage Leisure'));
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
    $upiPayload = "upi://pay?pa=" . rawurlencode($companyUpiId) . "&pn=" . rawurlencode($upiPayeeName) . "&am=" . number_format($upiPayAmt, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Royal Proposal " . $cleanQuoteRef);
    $upiQrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=" . urlencode($upiPayload);
}

// Validity & Pax Summary
$validUntilDate = date('F j, Y', strtotime('+7 days'));
$paxSummaryParts = [];
if ($adultCount > 0) $paxSummaryParts[] = "{$adultCount} Adult" . ($adultCount > 1 ? 's' : '');
if ($childCount > 0) $paxSummaryParts[] = "{$childCount} Child" . ($childCount > 1 ? 'ren' : '');
if ($infantCount > 0) $paxSummaryParts[] = "{$infantCount} Infant" . ($infantCount > 1 ? 's' : '');
$paxSummaryText = !empty($paxSummaryParts) ? implode(', ', $paxSummaryParts) : '2 Distinguished Patrons';

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
$formatCategoryName = function ($rawCategory, $stars = '') {
    $cat = trim((string)$rawCategory);
    if (empty($cat) && !empty($stars)) {
        $cat = $stars;
    }
    if (empty($cat)) {
        return 'Palatial Heritage Residence';
    }
    $catLower = strtolower($cat);
    $catMap = [
        '3_star_hotel' => 'Classic Heritage Boutique',
        '4_star_hotel' => 'Premium Grand Manor',
        '5_star_hotel' => '5★ Palatial Luxury Estate',
        'budget_hotel' => 'Comfort Residence',
        'deluxe_hotel' => 'Deluxe Imperial Manor',
        'luxury_hotel' => 'Royal Heritage Palace',
        'resort'       => 'Grand Palatial Resort'
    ];
    return $catMap[$catLower] ?? ucwords(str_replace('_', ' ', $cat));
};

// Helper: Clean meal plan
$cleanMealPlan = function ($rawMeal) {
    if (empty($rawMeal)) return 'Royal European Plan (EP)';
    $m = strtoupper(trim((string)$rawMeal));
    $mealMap = [
        'CP'  => 'Continental Plan (Royal Breakfast Included)',
        'MAP' => 'Modified American Plan (Breakfast & Royal Dinner)',
        'AP'  => 'American Plan (All Palatial Gourmet Meals)',
        'EP'  => 'European Plan (Room Only)'
    ];
    return $mealMap[$m] ?? $rawMeal;
};

// Helper: Category default thumbnail
$getCategoryThumbnail = function ($categoryName, $stayName = '') {
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

$checkTransitOrTripEnd = function ($d, $idx, $total, $title, $overview) {
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
        'onward journey',
        'proceed to onward journey',
        'overnight journey',
        'overnight bus',
        'overnight volvo',
        'overnight train',
        'tour concludes',
        'tour conclude',
        'trip concludes',
        'trip conclude',
        'trip ends',
        'tour ends',
        'airport drop',
        'railway station drop',
        'drop at airport',
        'drop at railway station',
        'drop at bus stand'
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

// Roman Numeral Helper for Royal Heritage theme
if (!function_exists('toRomanNumerals')) {
    function toRomanNumerals($num)
    {
        $n = intval($num);
        $result = '';
        $lookup = [
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        foreach ($lookup as $roman => $value) {
            $matches = intval($n / $value);
            $result .= str_repeat($roman, $matches);
            $n = $n % $value;
        }
        return $result ?: 'I';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pkgTitle); ?> - Royal Heritage Atelier</title>

    <!-- Google Fonts & Remix Icon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400;1,600&family=Mulish:wght@300;400;500;600;700;800&family=Cinzel:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --royal-obsidian: #0b1320;
            --royal-navy: #111e33;
            --royal-gold: #c59b27;
            --royal-gold-light: #e6ca65;
            --royal-gold-dark: #926f14;
            --royal-parchment: #fdfbf7;
            --royal-cream: #f7f4ed;
            --royal-border: #e2d099;
            --royal-border-subtle: rgba(197, 155, 39, 0.35);
            --royal-text-dark: #0f172a;
            --royal-text-muted: #57534e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Mulish', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ede8dc;
            color: var(--royal-text-dark);
            padding: 24px 0 60px 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar */
        .v3-pdf-actions-bar {
            position: fixed;
            top: 16px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(11, 19, 32, 0.95);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 999px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--royal-gold);
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
            background: linear-gradient(135deg, #c59b27 0%, #d4af37 50%, #926f14 100%);
            color: #0b1320;
            box-shadow: 0 4px 12px rgba(197, 155, 39, 0.35);
            font-family: 'Cinzel', serif;
            letter-spacing: 0.5px;
        }

        .btn-pdf-download:hover {
            opacity: 0.94;
            transform: translateY(-1px);
        }

        .btn-pdf-print {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-pdf-print:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* A4 Page Container: 210mm x 297mm */
        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            margin: 0 auto 30px auto;
            background: var(--royal-parchment);
            page-break-before: always;
            page-break-after: always;
            page-break-inside: avoid;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(11, 19, 32, 0.14);
            border-radius: 4px;
        }

        .page:first-child {
            page-break-before: auto;
        }

        /* Classical Double Gold Hairline Border Frame for A4 */
        .page-double-frame {
            position: absolute;
            inset: 12mm;
            border: 1px solid var(--royal-border);
            pointer-events: none;
            z-index: 10;
        }

        .page-double-frame-inner {
            position: absolute;
            inset: 2.5mm;
            border: 1px solid rgba(197, 155, 39, 0.4);
            pointer-events: none;
        }

        /* Corner Flourishes */
        .corner-flourish {
            position: absolute;
            width: 14px;
            height: 14px;
            border: 2px solid var(--royal-gold);
        }

        .corner-tl {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }

        .corner-tr {
            top: -2px;
            right: -2px;
            border-left: none;
            border-bottom: none;
        }

        .corner-bl {
            bottom: -2px;
            left: -2px;
            border-right: none;
            border-top: none;
        }

        .corner-br {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }

        /* Regal Atelier Header Strip */
        .atelier-top-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 45px 12px 45px;
            background: #ffffff;
            border-bottom: 2px solid #f2e9d2;
        }

        .atelier-brand-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .atelier-logo {
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        .atelier-brand-title {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--royal-obsidian);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .atelier-brand-subtitle {
            font-size: 9px;
            color: var(--royal-gold-dark);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .atelier-badge-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .atelier-seal-badge {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            background: #fdf6e2;
            padding: 4px 12px;
            border-radius: 4px;
            border: 1px solid var(--royal-border);
            letter-spacing: 0.5px;
        }

        /* Page Main Padding */
        .atelier-page-body {
            padding: 16px 45px 0 45px;
            max-height: calc(297mm - 60mm - 40mm);
            overflow: hidden;
        }

        /* COVER PAGE: Royal Architectural Stage */
        .atelier-cover-plate {
            position: relative;
            width: 100%;
            height: 82mm;
            border: 1px solid var(--royal-border);
            padding: 4px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(11, 19, 32, 0.08);
            margin-bottom: 16px;
        }

        .atelier-cover-img-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: var(--royal-obsidian);
        }

        .atelier-cover-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .atelier-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(11, 19, 32, 0.25) 0%, rgba(11, 19, 32, 0.82) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 22px 24px;
        }

        .atelier-ribbon-tag {
            align-self: flex-start;
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #ffffff;
            background: rgba(197, 155, 39, 0.85);
            padding: 5px 14px;
            border-radius: 2px;
            border: 1px solid #ffd875;
            text-transform: uppercase;
        }

        .atelier-title-block h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 27px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: 0.2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 6px;
        }

        .atelier-dest-sub {
            font-family: 'Cinzel', serif;
            font-size: 12px;
            color: #ffd875;
            letter-spacing: 1px;
        }

        /* 4 Pillars Ledger */
        .atelier-pillars-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .atelier-pillars-grid.has-5-pillars {
            grid-template-columns: repeat(5, 1fr);
        }

        .atelier-pillar-box {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 10px 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(11, 19, 32, 0.03);
            border-top: 3px solid var(--royal-gold);
        }

        .pillar-roman {
            font-family: 'Cinzel', serif;
            font-size: 9px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1.5px;
            margin-bottom: 2px;
        }

        .pillar-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--royal-text-muted);
            margin-bottom: 4px;
        }

        .pillar-value {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--royal-obsidian);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Curator's Prologue Box */
        .atelier-prologue-box {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 18px 22px;
            position: relative;
            box-shadow: 0 4px 14px rgba(11, 19, 32, 0.04);
        }

        .prologue-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .prologue-crest {
            color: var(--royal-gold);
            font-size: 14px;
        }

        .prologue-title {
            font-family: 'Cinzel', serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--royal-obsidian);
            text-transform: uppercase;
        }

        .prologue-narrative {
            font-size: 13.5px;
            line-height: 1.7;
            color: #334155;
            text-align: justify;
        }

        .prologue-privileges {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #f2e9d2;
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            color: var(--royal-gold-dark);
            letter-spacing: 0.5px;
        }

        /* DAY BY DAY PAGES: Classical Chapters */
        .atelier-chapter-bar {
            text-align: center;
            margin-bottom: 12px;
        }

        .chapter-eyebrow {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .chapter-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 21px;
            font-weight: 700;
            color: var(--royal-obsidian);
            line-height: 1.25;
        }

        .chapter-filigree {
            color: var(--royal-gold);
            font-size: 11px;
            margin-top: 3px;
            letter-spacing: 6px;
        }

        .atelier-day-plate {
            position: relative;
            width: 100%;
            height: 62mm;
            border: 1px solid var(--royal-border);
            padding: 3px;
            background: #ffffff;
            box-shadow: 0 4px 14px rgba(11, 19, 32, 0.05);
            margin-bottom: 14px;
        }

        .atelier-day-plate img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .atelier-day-plate-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background: var(--royal-obsidian);
            color: #ffffff;
            font-family: 'Cinzel', serif;
        }

        .atelier-day-plate-dest {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(11, 19, 32, 0.85);
            color: #ffd875;
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            padding: 4px 12px;
            border: 1px solid var(--royal-gold);
            letter-spacing: 1px;
        }

        /* Day Narrative Prose */
        .atelier-narrative-card {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 14px 18px;
            margin-bottom: 12px;
        }

        .atelier-narrative-eyebrow {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .atelier-narrative-text {
            font-size: 13.5px;
            line-height: 1.65;
            color: #334155;
        }

        /* Palatial Residence Card */
        .atelier-residence-card {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            border-left: 4px solid var(--royal-gold);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(11, 19, 32, 0.03);
        }

        .atelier-residence-thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border: 1px solid var(--royal-border);
            padding: 2px;
            background: #ffffff;
            flex-shrink: 0;
        }

        .atelier-residence-info {
            flex: 1;
            overflow: hidden;
        }

        .residence-coronet {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .residence-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--royal-obsidian);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 3px;
        }

        .residence-meta {
            font-size: 11.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .residence-meal-pill {
            font-weight: 700;
            color: #065f46;
            background: #ecfdf5;
            padding: 2px 8px;
            border-radius: 2px;
            border: 1px solid #a7f3d0;
        }

        /* Chauffeur Service Bar */
        .atelier-chauffeur-bar {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .chauffeur-title {
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--royal-obsidian);
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chauffeur-vehicle {
            font-weight: 700;
            color: var(--royal-gold-dark);
        }

        /* PRICING & SETTLEMENT PAGE: Imperial Tariff Certificate */
        .atelier-certificate-stage {
            border: 2px solid var(--royal-gold);
            padding: 4px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(197, 155, 39, 0.1);
        }

        .atelier-certificate-inner {
            border: 1px solid var(--royal-border);
            padding: 20px 24px;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 1px solid #f2e9d2;
            padding-bottom: 12px;
        }

        .certificate-crest {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .certificate-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--royal-obsidian);
            margin: 4px 0;
        }

        .certificate-total-box {
            background: #fdfbf4;
            border: 1px solid var(--royal-border);
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .total-tariff-label {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .total-tariff-amt {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 30px;
            font-weight: 700;
            color: var(--royal-obsidian);
        }

        .settlement-schedule-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .settlement-card {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 10px 14px;
            border-top: 3px solid var(--royal-gold);
        }

        .settlement-card-lbl {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .settlement-card-amt {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--royal-obsidian);
            margin-top: 2px;
        }

        /* Classical Ledger Table */
        .atelier-ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .atelier-ledger-table th {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: left;
            padding: 8px 12px;
            border-bottom: 2px solid var(--royal-border);
            background: #fdfbf4;
        }

        .atelier-ledger-table td {
            padding: 10px 12px;
            font-size: 13px;
            color: var(--royal-obsidian);
            border-bottom: 1px solid #f2e9d2;
        }

        .atelier-ledger-table .amt-cell {
            text-align: right;
            font-weight: 700;
            font-family: 'Mulish', sans-serif;
        }

        /* Treasury Coordinates Deck */
        .atelier-treasury-deck {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 12px;
        }

        .treasury-wire-card {
            background: #fdfbf4;
            border: 1px solid var(--royal-border);
            padding: 12px 14px;
        }

        .treasury-title {
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #f2e9d2;
            padding-bottom: 4px;
        }

        .treasury-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
        }

        .treasury-field-lbl {
            font-size: 10px;
            color: var(--royal-text-muted);
            text-transform: uppercase;
        }

        .treasury-field-val {
            font-weight: 700;
            color: var(--royal-obsidian);
        }

        .treasury-qr-card {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .treasury-qr-img {
            width: 84px;
            height: 84px;
            border: 1px solid var(--royal-gold);
            padding: 3px;
            margin-bottom: 6px;
        }

        .treasury-qr-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* INCLUSIONS & EXCLUSIONS: Dual Classical Columns */
        .atelier-privileges-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
        }

        .atelier-privilege-item {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 10px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .privilege-bullet {
            color: var(--royal-gold);
            font-size: 13px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .privilege-text {
            font-size: 12.5px;
            line-height: 1.5;
            color: var(--royal-obsidian);
        }

        /* CONCIERGE & SALON PAGE */
        .atelier-concierge-deck {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 14px;
        }

        .atelier-concierge-card {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 18px 20px;
            box-shadow: 0 4px 14px rgba(11, 19, 32, 0.03);
        }

        .concierge-card-title {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
            border-bottom: 1px solid #f2e9d2;
            padding-bottom: 6px;
        }

        .concierge-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .concierge-icon {
            color: var(--royal-gold);
            font-size: 16px;
            flex-shrink: 0;
        }

        /* CHARTER POLICIES */
        .atelier-policy-block {
            background: #ffffff;
            border: 1px solid var(--royal-border);
            padding: 14px 18px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(11, 19, 32, 0.02);
        }

        .atelier-policy-article {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #f2e9d2;
            padding-bottom: 4px;
        }

        .atelier-policy-content {
            font-size: 12.5px;
            line-height: 1.65;
            color: #334155;
        }

        /* PDF FOOTER DECOR (Matching MIT/Aleya requirement) */
        .page-footer-decor {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 52px;
            box-sizing: border-box;
            padding: 0 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            border-top: 1px solid #f2e9d2;
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
            font-family: 'Cinzel', serif;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--royal-gold-dark);
            letter-spacing: 0.5px;
        }

        .bottom-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #78716c;
            font-weight: 600;
        }

        .meta-credit {
            color: var(--royal-gold-dark);
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
            }
        }
    </style>
</head>

<body>

    <!-- Floating Top Bar for PDF Actions -->
    <div class="v3-pdf-actions-bar" id="v3-pdf-actions-bar">
        <button class="btn-pdf-action btn-pdf-download" id="btnDownloadPDF">
            <i class="ri-file-pdf-line"></i> Download Dossier PDF
        </button>
        <button class="btn-pdf-action btn-pdf-print" id="btnPrintPage">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>

    <!-- ================= PAGE 1: COVER & OVERVIEW DOSSIER ================= -->
    <div class="page" id="page-1">
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?><?php if (!empty($formattedTravelDate)): ?> · <?php echo htmlspecialchars($formattedTravelDate); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="atelier-page-body">
            <!-- Architectural Scenic Plate -->
            <div class="atelier-cover-plate">
                <div class="atelier-cover-img-wrapper">
                    <?php if (!empty($heroFeatureImage)): ?>
                        <img src="<?php echo htmlspecialchars($heroFeatureImage); ?>" alt="<?php echo htmlspecialchars($pkgTitle); ?>">
                    <?php else: ?>
                        <div style="height:100%; display:grid; place-items:center; color:#ffffff; font-family:'Playfair Display',serif;">
                            <h2><?php echo htmlspecialchars($pkgCity); ?></h2>
                        </div>
                    <?php endif; ?>

                    <div class="atelier-cover-overlay">
                        <div class="atelier-ribbon-tag">
                            ❖ Bespoke Private Expedition
                        </div>
                        <div class="atelier-title-block">
                            <h1><?php echo htmlspecialchars($pkgTitle); ?></h1>
                            <div class="atelier-dest-sub">
                                <?php echo htmlspecialchars($pkgCity); ?><?php echo !empty($pkgState) ? ', ' . htmlspecialchars($pkgState) : ''; ?> · Handcrafted Edition
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Four Imperial Pillars Ledger -->
            <div class="atelier-pillars-grid <?php echo !empty($formattedTravelDate) ? 'has-5-pillars' : ''; ?>">
                <div class="atelier-pillar-box">
                    <div class="pillar-roman">PILLAR I</div>
                    <div class="pillar-title">Duration</div>
                    <div class="pillar-value"><?php echo toRomanNumerals($totalDaysCount); ?> Days · <?php echo toRomanNumerals($totalNightsCount); ?> Nights</div>
                </div>
                <?php if (!empty($formattedTravelDate)): ?>
                <div class="atelier-pillar-box">
                    <div class="pillar-roman">COMMENCING</div>
                    <div class="pillar-title">Travel Date</div>
                    <div class="pillar-value"><?php echo htmlspecialchars($formattedTravelDate); ?></div>
                </div>
                <?php endif; ?>
                <div class="atelier-pillar-box">
                    <div class="pillar-roman">PILLAR II</div>
                    <div class="pillar-title">Distinguished Patrons</div>
                    <div class="pillar-value"><?php echo htmlspecialchars($paxSummaryText); ?></div>
                </div>
                <div class="atelier-pillar-box">
                    <div class="pillar-roman">PILLAR III</div>
                    <div class="pillar-title">Expedition Style</div>
                    <div class="pillar-value"><?php echo htmlspecialchars($pkgTheme); ?></div>
                </div>
                <div class="atelier-pillar-box">
                    <div class="pillar-roman">PILLAR IV</div>
                    <div class="pillar-title">Palatial Stays</div>
                    <div class="pillar-value"><?php echo htmlspecialchars($formatCategoryName($pkgHotel)); ?></div>
                </div>
            </div>

            <!-- The Curator's Prologue -->
            <div class="atelier-prologue-box">
                <div class="prologue-header">
                    <span class="prologue-crest">❖</span>
                    <span class="prologue-title">The Curator's Dispatch</span>
                    <span class="prologue-crest">❖</span>
                </div>
                <div class="prologue-narrative">
                    <?php if (!empty($cleanOverview)): ?>
                        <?php echo $cleanOverview; ?>
                    <?php else: ?>
                        <p>It is our consummate honor to present this bespoke travel itinerary. Curated exclusively for your party, combining majestic landscapes, aristocratic estate residences, and discreet private transfers for an unmatched expedition.</p>
                    <?php endif; ?>
                </div>
                <div class="prologue-privileges">
                    <span>❖ Private Chauffeur</span>
                    <span>❖ Palatial Estates</span>
                    <span>❖ Handpicked Dining</span>
                    <span>❖ Dedicated Concierge</span>
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
            $hotelName = $assignedHotel['property_title'] ?? ($day['hotel'] ?? ($day['stay'] ?? 'Grand Palatial Stay'));
            $hotelThumb = fixImageUrl($assignedHotel['property_thumbnail'] ?? $getCategoryThumbnail($pkgHotel, $hotelName));
            $hotelRoom = $assignedHotel['tph_room_type'] ?? ($day['room_type'] ?? 'Deluxe Palatial Room');
            $hotelMeal = $cleanMealPlan($day['meal'] ?? ($day['meal_plan'] ?? 'CP'));
    ?>
            <div class="page" id="<?php echo $dayPageId; ?>">
                <!-- Classical Double Frame -->
                <div class="page-double-frame">
                    <span class="corner-flourish corner-tl"></span>
                    <span class="corner-flourish corner-tr"></span>
                    <span class="corner-flourish corner-bl"></span>
                    <span class="corner-flourish corner-br"></span>
                    <div class="page-double-frame-inner"></div>
                </div>

                <!-- Regal Atelier Header Strip -->
                <div class="atelier-top-strip">
                    <div class="atelier-brand-left">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                        <?php else: ?>
                            <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="atelier-badge-right">
                        <div class="atelier-seal-badge">
                            COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · CH. <?php echo toRomanNumerals($dayNum); ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="atelier-page-body">
                    <!-- Chapter Title -->
                    <div class="atelier-chapter-bar">
                        <div class="chapter-eyebrow">Chapter <?php echo toRomanNumerals($dayNum); ?> · Day <?php echo sprintf('%02d', $dayNum); ?><?php if (!empty($dayDateWithDay)): ?> · <?php echo $dayDateWithDay; ?><?php endif; ?></div>
                        <h2 class="chapter-title"><?php echo htmlspecialchars($dayTitle); ?></h2>
                        <div class="chapter-filigree">♦ ♦ ♦</div>
                    </div>

                    <!-- Scenic Photographic Plate -->
                    <div class="atelier-day-plate">
                        <?php if (!empty($dayImage)): ?>
                            <img src="<?php echo htmlspecialchars($dayImage); ?>" alt="<?php echo htmlspecialchars($dayTitle); ?>">
                        <?php else: ?>
                            <div class="atelier-day-plate-placeholder">
                                <h2>DAY <?php echo $dayNum; ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?></h2>
                            </div>
                        <?php endif; ?>
                        <div class="atelier-day-plate-dest">
                            EXCURSION ESTATE · <?php echo htmlspecialchars($dayCity); ?>
                        </div>
                    </div>

                    <!-- Narrative Box -->
                    <div class="atelier-narrative-card">
                        <div class="atelier-narrative-eyebrow">
                            ❖ The Curated Program & Moments
                        </div>
                        <div class="atelier-narrative-text">
                            <?php if (!empty($dayDesc)): ?>
                                <?php echo (strpos($dayDesc, '<') !== false) ? $dayDesc : nl2br(htmlspecialchars($dayDesc)); ?>
                            <?php else: ?>
                                <p>Enjoy handpicked cultural and scenic excursions arranged under the aegis of your private concierge. All stops and scenic pauses are tailored to your party's leisure.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Residence Card -->
                    <?php if (!$isTransit): ?>
                        <div class="atelier-residence-card">
                            <img src="<?php echo htmlspecialchars($hotelThumb); ?>" alt="<?php echo htmlspecialchars($hotelName); ?>" class="atelier-residence-thumb">
                            <div class="atelier-residence-info">
                                <div class="residence-coronet">
                                    ★★★★★ The Palatial Residence · Night <?php echo $dayNum; ?>
                                </div>
                                <div class="residence-name"><?php echo htmlspecialchars($hotelName); ?></div>
                                <div class="residence-meta">
                                    <span><?php echo htmlspecialchars($hotelRoom); ?></span>
                                    <span>·</span>
                                    <span class="residence-meal-pill"><?php echo htmlspecialchars($hotelMeal); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="atelier-residence-card" style="border-left-color:#b91c1c; background:#fef2f2;">
                            <div style="font-size:24px; color:#b91c1c; padding:0 8px;"><i class="ri-flight-takeoff-line"></i></div>
                            <div class="atelier-residence-info">
                                <div class="residence-coronet" style="color:#b91c1c;">Conduit & Tour Conclusion</div>
                                <div class="residence-name" style="font-size:15px;">Departure & Onward Flight Transfers</div>
                                <div class="residence-meta">Discreet private chauffeur transfer to airport / transit terminal.</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Chauffeur Conduit Bar -->
                    <div class="atelier-chauffeur-bar">
                        <div class="chauffeur-title">
                            <i class="ri-roadster-line" style="color:var(--royal-gold);"></i> Dedicated Chauffeur Service
                        </div>
                        <div class="chauffeur-vehicle">
                            <?php echo !empty($pkgLocVehicle) ? htmlspecialchars($pkgLocVehicle) : (!empty($pkgOutVehicle) ? htmlspecialchars($pkgOutVehicle) : 'Private Dedicated Luxury Chauffeur Vehicle'); ?>
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

    <!-- ================= PRICING & SETTLEMENT PAGE ================= -->
    <div class="page" id="page-payment">
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · TARIFF
                </div>
            </div>
        </div>

        <div class="atelier-page-body">
            <!-- Imperial Tariff Certificate -->
            <div class="atelier-certificate-stage">
                <div class="atelier-certificate-inner">
                    <div class="certificate-header">
                        <div class="certificate-crest">❖ Official Remittance Certificate ❖</div>
                        <h2 class="certificate-title">Imperial Tariff & Settlement</h2>
                        <div style="font-size:12px; color:#64748b; font-family:'Cinzel',serif;">Charter Reference: <?php echo htmlspecialchars($cleanQuoteRef); ?> · Valid Through <?php echo $validUntilDate; ?></div>
                    </div>

                    <div class="certificate-total-box">
                        <div>
                            <div class="total-tariff-label">Total Expedition Cost</div>
                            <div style="font-size:11.5px; color:#64748b;"><?php echo htmlspecialchars($paxSummaryText); ?> · Taxes Included</div>
                        </div>
                        <div class="total-tariff-amt">₹<?php echo number_format($totalPriceVal, 2); ?></div>
                    </div>

                    <div class="settlement-schedule-row">
                        <div class="settlement-card">
                            <div class="settlement-card-lbl">30% Retainer Deposit to Confirm</div>
                            <div class="settlement-card-amt">₹<?php echo number_format($advance30Percent, 2); ?></div>
                        </div>
                        <div class="settlement-card">
                            <div class="settlement-card-lbl">70% Final Settlement Prior / Arrival</div>
                            <div class="settlement-card-amt">₹<?php echo number_format($balance70Percent, 2); ?></div>
                        </div>
                    </div>

                    <!-- Classical Ledger Table -->
                    <table class="atelier-ledger-table">
                        <thead>
                            <tr>
                                <th>Patron Classification</th>
                                <th style="text-align:center;">Guests</th>
                                <th class="amt-cell">Standard Tariff (INR)</th>
                                <th class="amt-cell">Subtotal (INR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Adult Patrons</strong> (Royal Double Occupancy)</td>
                                <td style="text-align:center;"><?php echo $adultCount; ?></td>
                                <td class="amt-cell">₹<?php echo number_format($adultPriceVal, 2); ?></td>
                                <td class="amt-cell">₹<?php echo number_format($adultSubtotal, 2); ?></td>
                            </tr>
                            <?php if ($childCount > 0): ?>
                                <tr>
                                    <td><strong>Child Patrons</strong> (Junior Suite Accommodation)</td>
                                    <td style="text-align:center;"><?php echo $childCount; ?></td>
                                    <td class="amt-cell">₹<?php echo number_format($childPriceVal, 2); ?></td>
                                    <td class="amt-cell">₹<?php echo number_format($childSubtotal, 2); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($infantCount > 0): ?>
                                <tr>
                                    <td><strong>Infant Patrons</strong></td>
                                    <td style="text-align:center;"><?php echo $infantCount; ?></td>
                                    <td class="amt-cell">₹<?php echo number_format($infantPriceVal, 2); ?></td>
                                    <td class="amt-cell">₹<?php echo number_format($infantSubtotal, 2); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr style="background:#fdfbf4; font-weight:800;">
                                <td colspan="3" style="font-family:'Cinzel',serif; font-size:11px; letter-spacing:0.5px;">Net Total Expedition Cost</td>
                                <td class="amt-cell" style="color:var(--royal-gold-dark); font-size:15px;">₹<?php echo number_format($totalPriceVal, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Treasury Coordinates Deck -->
                    <div class="atelier-treasury-deck">
                        <div class="treasury-wire-card">
                            <div class="treasury-title">❖ Official Wire Remittance Coordinates</div>
                            <div class="treasury-fields">
                                <div>
                                    <div class="treasury-field-lbl">Treasury Bank</div>
                                    <div class="treasury-field-val"><?php echo htmlspecialchars($companyBankName ?: 'HDFC Bank Ltd.'); ?></div>
                                </div>
                                <div>
                                    <div class="treasury-field-lbl">Beneficiary</div>
                                    <div class="treasury-field-val"><?php echo htmlspecialchars($companyBankAcName ?: $siteName); ?></div>
                                </div>
                                <div>
                                    <div class="treasury-field-lbl">Account Number</div>
                                    <div class="treasury-field-val"><?php echo htmlspecialchars($companyBankAcNumber ?: '98765432100'); ?></div>
                                </div>
                                <div>
                                    <div class="treasury-field-lbl">IFSC Code</div>
                                    <div class="treasury-field-val"><?php echo htmlspecialchars($companyBankIfsc ?: 'HDFC0001234'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="treasury-qr-card">
                            <div class="treasury-qr-img">
                                <?php if (!empty($upiQrUrl)): ?>
                                    <img src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="Scan to Remit">
                                <?php else: ?>
                                    <div style="height:100%; display:grid; place-items:center; font-size:9px; color:#64748b;">UPI QR</div>
                                <?php endif; ?>
                            </div>
                            <div style="font-size:10px; font-weight:700; color:var(--royal-gold-dark); font-family:'Cinzel',serif;">
                                UPI: <?php echo htmlspecialchars($companyUpiId ?: 'travel@upi'); ?>
                            </div>
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
                <span class="meta-page-num" data-page-calc="true">Page <?php echo ($totalDaysCount + 2); ?> of <?php echo ($totalDaysCount + 5); ?></span>
            </div>
        </div>
    </div>

    <!-- ================= INCLUSIONS PAGE ================= -->
    <div class="page" id="page-inclusions">
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · INCLUSIONS
                </div>
            </div>
        </div>

        <div class="atelier-page-body">
            <div class="atelier-chapter-bar" style="margin-bottom:14px;">
                <div class="chapter-eyebrow">Privileges & Accoutrements</div>
                <h2 class="chapter-title">Complimentary Royal Privileges</h2>
                <div class="chapter-filigree">❖ ❖ ❖</div>
            </div>

            <div class="atelier-privileges-grid">
                <?php if (!empty($incList)): ?>
                    <?php foreach ($incList as $inc): ?>
                        <div class="atelier-privilege-item">
                            <span class="privilege-bullet">❖</span>
                            <div class="privilege-text"><?php echo htmlspecialchars($inc); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet">❖</span>
                        <div class="privilege-text">Handpicked palatial luxury estate accommodations as detailed in itinerary.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet">❖</span>
                        <div class="privilege-text">Daily lavish royal breakfast at every estate property.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet">❖</span>
                        <div class="privilege-text">Dedicated private air-conditioned vehicle with professional uniformed chauffeur.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet">❖</span>
                        <div class="privilege-text">All interstate permits, highway tolls, parking access, and driver allowances.</div>
                    </div>
                <?php endif; ?>
            </div>

            <div style="background:#ffffff; border:1px solid var(--royal-border); padding:14px 18px; margin-top:16px; border-left:4px solid var(--royal-gold); font-size:12px; line-height:1.6; color:#475569;">
                <strong style="color:var(--royal-obsidian); font-family:'Cinzel',serif;">Curator's Note:</strong> All privileged inclusions are formally locked upon token deposit. Special dietary requirements, private butler requests, or bespoke route modifications can be orchestrated directly through your dedicated concierge.
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
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · EXCLUSIONS
                </div>
            </div>
        </div>

        <div class="atelier-page-body">
            <div class="atelier-chapter-bar" style="margin-bottom:14px;">
                <div class="chapter-eyebrow">Personal Discretion</div>
                <h2 class="chapter-title">Exclusions & Discretionary Charges</h2>
                <div class="chapter-filigree">◇ ◇ ◇</div>
            </div>

            <div class="atelier-privileges-grid">
                <?php if (!empty($excList)): ?>
                    <?php foreach ($excList as $exc): ?>
                        <div class="atelier-privilege-item">
                            <span class="privilege-bullet" style="color:#b91c1c;">◇</span>
                            <div class="privilege-text"><?php echo htmlspecialchars($exc); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet" style="color:#b91c1c;">◇</span>
                        <div class="privilege-text">Commercial or chartered flights and train tickets unless specified in writing.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet" style="color:#b91c1c;">◇</span>
                        <div class="privilege-text">Monument admissions, palace camera fees, and guided heritage site passes.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet" style="color:#b91c1c;">◇</span>
                        <div class="privilege-text">Personal laundry, minibar amenities, telephone services, and gratuities.</div>
                    </div>
                    <div class="atelier-privilege-item">
                        <span class="privilege-bullet" style="color:#b91c1c;">◇</span>
                        <div class="privilege-text">Comprehensive private travel and medical indemnity insurance.</div>
                    </div>
                <?php endif; ?>
            </div>

            <div style="background:#ffffff; border:1px solid #fecaca; padding:14px 18px; margin-top:16px; border-left:4px solid #b91c1c; font-size:12px; line-height:1.6; color:#7f1d1d;">
                <strong style="color:#7f1d1d; font-family:'Cinzel',serif;">Discretionary Arrangements:</strong> Supplementary excursions such as private helicopter charters, high-altitude gondolas, river rafting, or alpine equestrian escorts may be coordinated directly via your concierge on demand.
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
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · CONCIERGE
                </div>
            </div>
        </div>

        <div class="atelier-page-body">
            <div class="atelier-chapter-bar" style="margin-bottom:14px;">
                <div class="chapter-eyebrow">Private Assistance</div>
                <h2 class="chapter-title">Your Dedicated Expedition Concierge</h2>
                <div class="chapter-filigree">❖ ❖ ❖</div>
            </div>

            <div class="atelier-concierge-deck">
                <!-- Personal Concierge Card -->
                <div class="atelier-concierge-card">
                    <div class="concierge-card-title">❖ Bespoke Travel Specialist</div>
                    <div class="concierge-row">
                        <i class="ri-phone-line concierge-icon"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Private Telephone Wire</div>
                            <a href="tel:<?php echo htmlspecialchars($sitePhone); ?>" style="font-weight:700; color:var(--royal-obsidian); text-decoration:none;"><?php echo htmlspecialchars($sitePhone); ?></a>
                        </div>
                    </div>
                    <div class="concierge-row">
                        <i class="ri-whatsapp-line concierge-icon" style="color:#059669;"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Priority WhatsApp Desk</div>
                            <a href="https://wa.me/<?php echo $cleanWhatsappDigits; ?>" style="font-weight:700; color:var(--royal-obsidian); text-decoration:none;"><?php echo htmlspecialchars($siteWhatsapp); ?></a>
                        </div>
                    </div>
                    <div class="concierge-row">
                        <i class="ri-mail-line concierge-icon"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Diplomatic Correspondence</div>
                            <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>" style="font-weight:700; color:var(--royal-obsidian); text-decoration:none;"><?php echo htmlspecialchars($siteEmail); ?></a>
                        </div>
                    </div>
                </div>

                <!-- Salon & Atelier Coordinates -->
                <div class="atelier-concierge-card">
                    <div class="concierge-card-title">❖ Atelier Headquarters & Salon</div>
                    <div class="concierge-row">
                        <i class="ri-map-pin-line concierge-icon"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Salon Address</div>
                            <div style="font-weight:700; color:var(--royal-obsidian);"><?php echo htmlspecialchars($siteAddress); ?></div>
                        </div>
                    </div>
                    <div class="concierge-row">
                        <i class="ri-global-line concierge-icon"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Digital Atelier</div>
                            <div style="font-weight:700; color:var(--royal-obsidian);"><?php echo htmlspecialchars($siteDomain); ?></div>
                        </div>
                    </div>
                    <div class="concierge-row">
                        <i class="ri-time-line concierge-icon"></i>
                        <div>
                            <div style="font-size:10px; color:#78716c; text-transform:uppercase;">Concierge Reception Hours</div>
                            <div style="font-weight:700; color:var(--royal-obsidian);">Daily: 9:00 AM – 9:00 PM IST</div>
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

    <!-- ================= CHARTER POLICIES (Dynamic Dynamic Multi-Page) ================= -->
    <div class="page" id="page-policies">
        <!-- Classical Double Frame -->
        <div class="page-double-frame">
            <span class="corner-flourish corner-tl"></span>
            <span class="corner-flourish corner-tr"></span>
            <span class="corner-flourish corner-bl"></span>
            <span class="corner-flourish corner-br"></span>
            <div class="page-double-frame-inner"></div>
        </div>

        <!-- Regal Atelier Header Strip -->
        <div class="atelier-top-strip">
            <div class="atelier-brand-left">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="atelier-logo">
                <?php else: ?>
                    <div class="atelier-brand-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>
            <div class="atelier-badge-right">
                <div class="atelier-seal-badge">
                    COLLECTION · <?php echo htmlspecialchars($cleanQuoteRef); ?> · CHARTER
                </div>
            </div>
        </div>

        <div class="atelier-page-body policy-flow-container">
            <div class="atelier-chapter-bar" style="margin-bottom:14px;">
                <div class="chapter-eyebrow">Governance & Protocol</div>
                <h2 class="chapter-title">Expedition Charter & Governance</h2>
                <div class="chapter-filigree">❖ ❖ ❖</div>
            </div>

            <div class="atelier-policy-wrapper">
                <!-- Payment Policy -->
                <div class="atelier-policy-block">
                    <div class="atelier-policy-article">Article I · Token Retainer & Settlement Charter</div>
                    <div class="atelier-policy-content">
                        <?php if (!empty($rawPaymentPolicy)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawPaymentPolicy, '<') !== false) ? cleanItineraryUnicode($rawPaymentPolicy) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawPaymentPolicy)))); ?>
                        <?php else: ?>
                            A non-refundable 30% earnest retainer is required upon proposal ratification to secure estate bookings and chauffeur assets. The balance 70% must be fully settled prior to or upon arrival as instructed by your concierge.
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cancellation Policy -->
                <div class="atelier-policy-block">
                    <div class="atelier-policy-article">Article II · Cancellation & Rescheduling Charter</div>
                    <div class="atelier-policy-content">
                        <?php if (!empty($rawCancellationPolicy)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawCancellationPolicy, '<') !== false) ? cleanItineraryUnicode($rawCancellationPolicy) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawCancellationPolicy)))); ?>
                        <?php else: ?>
                            Written cancellation received 30 or more days prior to departure allows full remittance less nominal dossier retention fees. Between 15 to 30 days, 50% tariff applies. Cancellations within 14 days incur full retention.
                        <?php endif; ?>
                    </div>
                </div>

                <!-- General Terms -->
                <div class="atelier-policy-block">
                    <div class="atelier-policy-article">Article III · General Travel Protocol & Indemnity</div>
                    <div class="atelier-policy-content">
                        <?php if (!empty($rawTermsConditions)): ?>
                            <?php echo capitalizeParagraphs((strpos($rawTermsConditions, '<') !== false) ? cleanItineraryUnicode($rawTermsConditions) : nl2br(htmlspecialchars(cleanItineraryUnicode($rawTermsConditions)))); ?>
                        <?php else: ?>
                            All distinguished patrons must possess valid government identification. Route timings may be dynamically optimized to accommodate alpine conditions, local security protocols, or road governance.
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

            const blocks = Array.from(firstPolPage.querySelectorAll(".atelier-policy-block"));
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
                        const newPolWrapper = newPage.querySelector(".atelier-policy-wrapper");
                        if (newPolWrapper) newPolWrapper.innerHTML = "";
                    }
                    firstPolPage.parentNode.insertBefore(newPage, currentTargetPage.nextSibling);
                    currentTargetPage = newPage;
                    currentTargetContainer = newPage.querySelector(".atelier-policy-wrapper") || newPage.querySelector(".policy-flow-container");
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
                loader.style.cssText = 'position:fixed; inset:0; background:rgba(11,19,32,0.88); backdrop-filter:blur(6px); z-index:999999; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ffffff; font-family:"Playfair Display",serif;';
                loader.innerHTML = `
                    <div style="width:48px; height:48px; border:4px solid rgba(197,155,39,0.3); border-top-color:#c59b27; border-radius:50%; animation:v3spin 0.8s linear infinite; margin-bottom:16px;"></div>
                    <div id="v3-pdf-loading-text" style="font-size:16px; font-weight:700; color:#ffd875;">Rendering Royal Dossier...</div>
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

        async function downloadItineraryPDF(shouldSave = true) {
            const btn = document.getElementById('btnDownloadPDF');
            if (btn && shouldSave !== false) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line"></i> Preparing Dossier...';
            }

            try {
                if (shouldSave !== false) showPageLoader('Rendering Royal Expedition Dossier...');
                paginatePolicyPages();
                updatePageNumbers();

                const pages = document.querySelectorAll(".page");
                if (!pages || pages.length === 0) {
                    throw new Error("No pages found.");
                }

                const jspdfModule = window.jspdf || (typeof jsPDF !== 'undefined' ? {
                    jsPDF
                } : null);
                if (!jspdfModule || !jspdfModule.jsPDF) {
                    throw new Error("jsPDF library not loaded.");
                }

                const pdf = new jspdfModule.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const isBgRender = (shouldSave === false);

                for (let i = 0; i < pages.length; i++) {
                    if (!isBgRender) showPageLoader(`Rendering page ${i + 1} of ${pages.length}...`);
                    if (typeof onProgress === 'function') {
                        try { onProgress(i + 1, pages.length); } catch (pe) {}
                    }

                    const canvas = await html2canvas(pages[i], {
                        scale: isBgRender ? 1.25 : 1.6,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: '#fdfbf7',
                        logging: false,
                        imageTimeout: 8000,
                        windowWidth: pages[i].scrollWidth || 794,
                        windowHeight: pages[i].scrollHeight || 1123,
                        onclone: function (clonedDoc) {
                            const imgs = clonedDoc.querySelectorAll('img');
                            imgs.forEach(function (img) {
                                img.crossOrigin = 'anonymous';
                                img.loading = 'eager';
                            });
                        }
                    });

                    let imgData = null;
                    try {
                        imgData = canvas.toDataURL('image/jpeg', isBgRender ? 0.80 : 0.92);
                    } catch (canvasErr) {
                        console.warn('Canvas export warning on page ' + (i + 1), canvasErr);
                        try { imgData = canvas.toDataURL('image/png'); } catch (ce2) { imgData = null; }
                    }

                    if (imgData) {
                        const imgProps = pdf.getImageProperties(imgData);
                        const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;
                        pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, Math.min(pageHeight, pdfImgHeight));
                    }

                    if (i < pages.length - 1) {
                        pdf.addPage();
                    }
                }

                if (!isBgRender) {
                    showPageLoader('Finalizing PDF Download...');
                    <?php
                    $safePdfTitle = preg_replace('/_+/', '_', preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace('&', 'and', cleanItineraryUnicode($pkgTitle))));
                    $safePdfTitle = trim($safePdfTitle, '_');
                    if (empty($safePdfTitle)) $safePdfTitle = 'Royal_Expedition';
                    ?>
                    pdf.save("<?php echo $safePdfTitle; ?>_Dossier.pdf");
                }
                const pdfDataUri = pdf.output('datauristring');
                return pdfDataUri;
            } catch (err) {
                console.error("PDF export error:", err);
                if (shouldSave !== false) window.print();
                throw err;
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-pdf-line"></i> Download Dossier PDF';
                }
                hidePageLoader();
            }
        }
        window.downloadItineraryPDF = downloadItineraryPDF;

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