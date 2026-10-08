<?php

/**
 * V3 Itinerary Template: Standard Executive Travel Dossier
 * Path: app/dashboard/v3/default/desktop/templates/itinerary/standard/index.php
 * Description: Executive corporate travel proposal layout with an architectural dossier aesthetic,
 *              structured journey ledger, tabular investment statement, light azure gradient accents,
 *              Aleya credit footer, and zero-break multi-page PDF export.
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

if (!function_exists('cleanProposalLabel')) {
    function cleanProposalLabel($val)
    {
        if (empty($val)) return '';
        $clean = cleanItineraryUnicode($val);
        $clean = str_replace(['_', '-'], ' ', (string)$clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return ucwords(strtolower(trim($clean)));
    }
}

if (!function_exists('renderPolicyContent')) {
    function renderPolicyContent($rawContent, $fallbackHtml = '')
    {
        if (empty($rawContent) || !is_string($rawContent) || trim($rawContent) === '') {
            if (!empty($fallbackHtml)) {
                echo capitalizeParagraphs($fallbackHtml);
            }
            return;
        }

        $trimmed = trim($rawContent);
        if (strpos($trimmed, '<') !== false && strpos($trimmed, '>') !== false) {
            $cleaned = preg_replace('/\s*style="[^"]*"/i', '', $trimmed);
            echo capitalizeParagraphs($cleaned);
        } else {
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $trimmed)));
            foreach ($lines as $line) {
                echo '<p>' . htmlspecialchars(capitalizeFirstWord($line)) . '</p>';
            }
        }
    }
}

// Dynamic Site & Company Info
$siteName     = defined('SITE_NAME') ? SITE_NAME : ($company_name ?? 'Executive Travel Desk');
$sitePhone    = defined('SITE_MOBILE') ? SITE_MOBILE : (defined('SITE_PHONE') ? SITE_PHONE : ($company_phone ?? ''));
$siteEmail    = defined('SITE_EMAIL') ? SITE_EMAIL : ($company_email ?? '');
$siteAddress  = defined('COMPANY_ADDRESS') ? COMPANY_ADDRESS : (defined('SITE_ADDRESS') ? SITE_ADDRESS : ($company_address ?? ''));
$siteWhatsapp = defined('COMPANY_WHATSAPP_NUMBER') ? COMPANY_WHATSAPP_NUMBER : (defined('SITE_WHATSAPP') ? SITE_WHATSAPP : ($company_whatsapp_number ?? ($whatsappNumber ?? '')));
$siteUrl      = defined('SITE_URL') ? SITE_URL : ($company_url ?? '');
$siteDomain   = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($company_site_domain ?? parse_url($siteUrl, PHP_URL_HOST) ?? 'aleya.in'));
$siteLogo     = fixImageUrl(defined('SITE_LOGO') ? SITE_LOGO : ($company_logo ?? ''));
$themeColor   = defined('SITE_THEME_COLOUR') ? SITE_THEME_COLOUR : '#0f4c81';

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
$pkgTitle       = capitalizeFirstWord(cleanItineraryUnicode($tour_package_title ?? ($itinerary_title ?? ($title ?? 'Executive Tour Proposal'))));
$pkgCity        = cleanItineraryUnicode($tour_package_city ?? ($city ?? 'Destination'));
$pkgState       = cleanItineraryUnicode($tour_package_state ?? '');
$pkgCountry     = cleanItineraryUnicode($tour_package_country ?? '');
$pkgDuration    = $tour_package_duration ?? ($numberOfDays ?? ($duration ?? 0));
$pkgTheme       = cleanItineraryUnicode($tour_package_theme ?? ($travelStyle ?? 'Executive Corporate'));
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

// Validity & Pax Summary for Pricing Card
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
$formatCategoryName = function ($rawCategory, $stars = '') {
    $cat = trim((string)$rawCategory);
    if (empty($cat) && !empty($stars)) {
        $cat = $stars;
    }
    if (empty($cat)) {
        return 'Executive Hotel';
    }
    $catLower = strtolower($cat);
    $catMap = [
        '3_star_hotel' => '3 Star Executive',
        '4_star_hotel' => '4 Star Premium',
        '5_star_hotel' => '5 Star Deluxe',
        'budget_hotel' => 'Standard Corporate',
        'deluxe_hotel' => 'Deluxe Business',
        'luxury_hotel' => 'Luxury Premier',
        'resort'       => 'Executive Resort',
        'houseboat'    => 'Deluxe Houseboat',
        'camp'         => 'Luxury Camp / Tent',
        'home_stay'    => 'Executive Homestay',
        'hotel'        => 'Executive Hotel'
    ];
    if (isset($catMap[$catLower])) {
        return $catMap[$catLower];
    }
    $cleaned = ucwords(str_replace(['_', '-'], ' ', $catLower));
    if (is_numeric(substr($cleaned, 0, 1)) && !str_contains(strtolower($cleaned), 'star')) {
        $cleaned .= ' Star Hotel';
    }
    return $cleaned;
};

// Helper: Format meal plan
$formatMealPlanName = function ($rawMeal) {
    $m = strtoupper(trim((string)$rawMeal));
    if (empty($m)) return '';
    $mealMap = [
        'CP'  => 'CP (Daily Breakfast Included)',
        'MAP' => 'MAP (Breakfast & Dinner)',
        'AP'  => 'AP (All Meals Included)',
        'EP'  => 'EP (Room Only)'
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pkgTitle); ?> - Executive Proposal</title>

    <!-- Google Fonts & Remix Icon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Mulish:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --std-navy-900: #0a192f;
            --std-navy-800: #0f2d59;
            --std-navy-700: #1a4480;
            --std-blue-500: #0284c7;
            --std-blue-400: #38bdf8;
            --std-cyan-light: #e0f2fe;
            --std-dark: #0f172a;
            --std-text-muted: #475569;
            --std-text-soft: #64748b;
            --std-border-light: #e2e8f0;
            --std-gradient-bar: linear-gradient(90deg, #0a192f 0%, #0284c7 50%, #38bdf8 85%, transparent 100%);
            --std-card-bg: #ffffff;
            --std-bg-tint: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #cbd5e1;
            color: var(--std-dark);
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
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            padding: 8px 14px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(10, 25, 47, 0.15);
            border: 1px solid var(--std-border-light);
        }

        .btn-pdf-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-pdf-download {
            background: linear-gradient(135deg, var(--std-navy-900), var(--std-blue-500));
            color: #ffffff;
        }

        .btn-pdf-download:hover {
            opacity: 0.94;
            transform: translateY(-1px);
        }

        .btn-pdf-print {
            background: #ffffff;
            color: var(--std-dark);
            border: 1px solid var(--std-border-light);
        }

        .btn-pdf-print:hover {
            background: #f1f5f9;
        }

        /* A4 Page Container (210mm x 297mm) */
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
            box-shadow: 0 10px 30px rgba(10, 25, 47, 0.08);
            border-radius: 2px;
        }

        .page:first-child {
            page-break-before: auto;
        }

        /* Executive Header Structure (Architectural Banner) */
        .exec-top-ledger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 45px 12px 45px;
            background: #ffffff;
            border-bottom: 1px solid var(--std-border-light);
        }

        .exec-brand-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .exec-brand-logo {
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        .exec-brand-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--std-navy-900);
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .exec-meta-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .exec-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .exec-ref-pill {
            background: #e0f2fe;
            color: #0369a1;
            border-color: #bae6fd;
            font-family: monospace;
            font-size: 11px;
        }

        /* Hero Panorama */
        .hero {
            position: relative;
            width: 100%;
            height: 78mm;
            max-height: 78mm;
            overflow: hidden;
            background: linear-gradient(135deg, var(--std-navy-900), var(--std-navy-700));
        }

        .hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .hero .default-day-image {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 40px;
            text-align: center;
            color: #ffffff;
            background: linear-gradient(135deg, var(--std-navy-900) 0%, #1e3a8a 50%, var(--std-blue-500) 100%);
        }

        .hero .default-day-image h2 {
            font-size: 2.8em;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            margin: 0;
            line-height: 1.2;
        }

        .hero-banner-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--std-gradient-bar);
            z-index: 5;
        }

        .refid {
            position: absolute;
            bottom: 18px;
            right: 45px;
            font-size: 11px;
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 4px;
            background: rgba(10, 25, 47, 0.85);
            font-weight: 700;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(56, 189, 248, 0.4);
            z-index: 10;
        }

        /* Container Layout */
        .container {
            width: 100%;
            padding: 18px 45px;
            max-height: calc(297mm - 78mm - 60mm);
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Cover Title Section with Precision Segment */
        .title-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .title-pre-eyebrow {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--std-blue-500);
            margin-bottom: 4px;
        }

        .title-section h1 {
            font-size: 25px;
            font-weight: 800;
            color: var(--std-navy-900);
            line-height: 1.25;
            letter-spacing: -0.2px;
            max-width: 90%;
        }

        .title-line-divider {
            width: 80px;
            height: 3px;
            background: var(--std-gradient-bar);
            border-radius: 2px;
            margin: 8px auto 0 auto;
        }

        /* Segmented Ledger Grid (Duration, Location, Theme, Stay) */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 14px;
            margin-bottom: 18px;
        }

        .stats.has-travel-date {
            grid-template-columns: repeat(5, 1fr);
        }

        .stats .box {
            background: var(--std-bg-tint);
            border: 1px solid var(--std-border-light);
            border-top: 3px solid var(--std-navy-700);
            border-radius: 6px;
            padding: 10px 12px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 3px;
            transition: none;
        }

        .stats .box p:first-child {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.6px;
            color: var(--std-text-soft);
        }

        .stats .box p:last-child {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--std-navy-900);
            line-height: 1.2;
        }

        /* Top Heading & Overview */
        .top-section {
            margin-top: 6px;
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-left: 4px solid var(--std-blue-500);
            border-radius: 6px;
        }

        .top-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .top-heading .bar {
            color: var(--std-blue-500);
            font-size: 20px;
            font-weight: 800;
        }

        .top-heading h2 {
            font-size: 17px;
            font-weight: 800;
            color: var(--std-navy-900);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .paragraph {
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--std-text-muted);
            font-family: 'Mulish', sans-serif;
        }

        /* Day-Wise Page Header & Activities */
        .day-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 6px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--std-border-light);
            position: relative;
        }

        .day-header::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 120px;
            height: 2px;
            background: var(--std-blue-500);
        }

        .day-number-badge {
            background: var(--std-navy-900);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            flex-shrink: 0;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(10, 25, 47, 0.15);
        }

        .day-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--std-navy-900);
            letter-spacing: -0.2px;
        }

        .day-content {
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--std-text-muted);
            font-family: 'Mulish', sans-serif;
            margin-bottom: 14px;
        }

        /* Logistics Ledger Strip (Stay City + Hotel + Transport) */
        .day-stay-plain-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .stay-city-plain-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: #f8fafc;
            border: 1px solid var(--std-border-light);
            border-radius: 4px;
            font-size: 12.5px;
            color: var(--std-dark);
        }

        .stay-city-plain-row i {
            color: var(--std-blue-500);
            font-size: 15px;
        }

        .stay-city-title {
            font-weight: 700;
            color: var(--std-text-soft);
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.5px;
        }

        .stay-city-val {
            font-weight: 800;
            color: var(--std-navy-900);
        }

        .stay-property-plain-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-left: 4px solid var(--std-navy-700);
            border-radius: 6px;
            box-sizing: border-box;
        }

        .stay-property-thumb-wrap {
            width: 100px;
            height: 72px;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f1f5f9;
            border: 1px solid var(--std-border-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stay-property-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .transport-thumb-wrap {
            background: #f0f9ff;
            border-color: #bae6fd;
        }

        .stay-property-details {
            flex: 1;
            min-width: 0;
        }

        .stay-property-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            flex-wrap: wrap;
        }

        .stay-property-title {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--std-navy-900);
            margin: 0;
        }

        .stay-property-tag {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 3px;
        }

        .category-tag {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .stay-property-meta-row {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 12px;
            color: var(--std-text-muted);
            flex-wrap: wrap;
        }

        .stay-property-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stay-property-meta-item i {
            color: var(--std-blue-500);
            font-size: 13px;
        }

        /* Inclusions & Exclusions Dossier Layout */
        .climax-section {
            width: 100%;
            margin-top: 6px;
        }

        .tour_info_card {
            border-radius: 6px;
            padding: 16px 20px;
            border: 1px solid var(--std-border-light);
            background: #ffffff;
        }

        .tour_included {
            border-left: 5px solid #16a34a;
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
        }

        .tour_excluded {
            border-left: 5px solid #dc2626;
            background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%);
        }

        .tour_info_title {
            font-size: 16px;
            font-weight: 800;
            color: var(--std-navy-900);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .tour_info_list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .tour_info_list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 7px 10px;
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-radius: 4px;
            font-size: 13px;
            color: var(--std-text-muted);
            line-height: 1.45;
        }

        .tour_check {
            color: #16a34a;
            font-size: 15px;
            flex-shrink: 0;
            font-weight: bold;
        }

        .tour_cross {
            color: #dc2626;
            font-size: 15px;
            flex-shrink: 0;
            font-weight: bold;
        }

        /* Tour Pricing Page (Executive Pricing Ledger) */
        #page-payment .hero {
            height: 44mm;
            max-height: 44mm;
        }

        #page-payment .container {
            padding: 12px 45px 14px 45px;
            max-height: calc(297mm - 44mm - 40mm);
            overflow: visible;
        }

        .pricing-main-card {
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 4px 14px rgba(10, 25, 47, 0.04);
            box-sizing: border-box;
            width: 100%;
        }

        .pricing-header-banner {
            background: #f8fafc;
            border: 1px solid var(--std-border-light);
            border-left: 4px solid var(--std-navy-900);
            border-radius: 6px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .pricing-header-type {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--std-blue-500);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }

        .quote-ref-tag {
            background: #ffffff;
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            font-family: monospace;
            color: var(--std-dark);
            font-size: 10px;
        }

        .pricing-package-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--std-navy-900);
            margin: 0;
            line-height: 1.25;
        }

        .pricing-package-meta {
            font-size: 12.5px;
            color: var(--std-text-soft);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-best-value {
            background: var(--std-navy-900);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }

        /* Invoice-style Pricing Table */
        .pricing-breakdown-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            border: 1px solid var(--std-border-light);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .pricing-breakdown-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 14px;
            font-size: 13.5px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .pricing-breakdown-row:nth-child(even) {
            background: #f8fafc;
        }

        .pricing-breakdown-row:last-child {
            border-bottom: none;
        }

        .pricing-breakdown-label {
            font-weight: 600;
            color: var(--std-dark);
        }

        .pricing-breakdown-val {
            font-weight: 800;
            color: var(--std-navy-900);
            font-family: monospace;
            font-size: 14px;
        }

        .pricing-breakdown-val.gst-highlight {
            color: #0369a1;
        }

        .pricing-total-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: linear-gradient(135deg, var(--std-navy-900), var(--std-navy-700));
            color: #ffffff;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .pricing-total-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #e2e8f0;
        }

        .pricing-total-amount {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.2px;
            font-family: monospace;
        }

        .btn-book-now {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            background: var(--std-blue-500);
            color: #ffffff !important;
            padding: 9px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            box-sizing: border-box;
            border: none;
            cursor: pointer;
        }

        .pricing-validity {
            font-size: 11px;
            color: var(--std-text-soft);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            justify-content: center;
        }

        .pricing-validity strong {
            color: var(--std-dark);
        }

        .bank-upi-section {
            display: grid;
            grid-template-columns: 1fr 180px;
            gap: 12px;
            margin-top: 10px;
            box-sizing: border-box;
        }

        .bank-upi-section.single-col {
            grid-template-columns: 1fr;
        }

        .bank-details-box {
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-radius: 6px;
            padding: 10px 14px;
        }

        .section-micro-title {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--std-navy-900);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-bottom: 6px;
            margin-bottom: 6px;
            border-bottom: 1px solid var(--std-border-light);
        }

        .bank-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            padding: 4px 0;
            border-bottom: 1px solid #f8fafc;
        }

        .bank-row:last-child {
            border-bottom: none;
        }

        .bank-label {
            color: var(--std-text-soft);
        }

        .bank-value {
            font-weight: 700;
            color: var(--std-dark);
            font-family: monospace;
        }

        .upi-qr-box {
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .qr-box-wrapper {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upi-qr-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--std-navy-900);
            margin-top: 4px;
        }

        .upi-address-text {
            font-size: 11px;
            font-weight: 700;
            color: var(--std-blue-500);
            background: #f0f9ff;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #bae6fd;
            margin-top: 2px;
            font-family: monospace;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Company & Contact Details Dossier */
        #page-policies .hero,
        .page-policies-page .hero,
        #page-contact .hero {
            height: 40mm;
            max-height: 40mm;
        }

        #page-policies .hero .default-day-image h2,
        .page-policies-page .hero .default-day-image h2,
        #page-contact .hero .default-day-image h2 {
            font-size: 2.2em;
        }

        #page-policies .container,
        .page-policies-page .container,
        #page-contact .container {
            padding: 10px 45px;
            max-height: calc(297mm - 40mm - 40mm);
            overflow: visible;
        }

        .company-contact-card {
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-radius: 6px;
            padding: 14px 18px;
            margin-top: 8px;
        }

        .company-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 14px;
            margin-top: 8px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: var(--std-dark);
        }

        .contact-item i {
            color: var(--std-blue-500);
            font-size: 15px;
        }

        .contact-item-full {
            grid-column: 1 / -1;
        }

        .company-social-section {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--std-border-light);
        }

        .social-section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--std-text-soft);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .company-social-links {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .social-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 4px;
            background: #f8fafc;
            border: 1px solid var(--std-border-light);
            font-size: 11px;
            font-weight: 600;
            color: var(--std-dark);
            text-decoration: none;
        }

        /* Executive Consultant Card */
        .sp-card {
            margin-top: 14px;
            padding: 16px 20px;
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            border-left: 4px solid var(--std-navy-900);
            border-radius: 6px;
        }

        .sp-card-top {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sp-avatar {
            width: 52px;
            height: 52px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--std-border-light);
            display: block;
        }

        .sp-avatar-initials {
            width: 52px;
            height: 52px;
            border-radius: 4px;
            background: var(--std-navy-900);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
        }

        .sp-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--std-blue-500);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .sp-name {
            font-size: 16px;
            font-weight: 800;
            color: var(--std-navy-900);
            margin: 2px 0;
        }

        .sp-desig {
            font-size: 11.5px;
            color: var(--std-text-soft);
        }

        .sp-contacts-large-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--std-border-light);
        }

        .sp-contact-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid var(--std-border-light);
            border-radius: 4px;
            text-decoration: none;
            color: inherit;
        }

        .sp-contact-box-full {
            grid-column: 1 / -1;
        }

        .sp-icon-circle {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            background: #ffffff;
            border: 1px solid var(--std-border-light);
            color: var(--std-navy-900);
        }

        .sp-field-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--std-text-soft);
            display: block;
        }

        .sp-field-value {
            font-size: 12px;
            font-weight: 700;
            color: var(--std-navy-900);
        }

        /* Policies Document Layout */
        .legal-doc-body {
            font-size: 11.5px;
            color: #1e293b;
            line-height: 1.65;
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
            color: var(--std-navy-900);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid var(--std-border-light);
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        .legal-doc-body p,
        .legal-doc-body li {
            font-size: 11.5px;
            color: #334155;
            line-height: 1.6;
        }

        .legal-doc-body ul,
        .legal-doc-body ol {
            padding-left: 16px;
            margin-bottom: 4px;
        }

        /* ── Page Footer Decor with Aleya Credit (Same as MIT Template) ── */
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
            max-height: 34px;
            width: auto;
            object-fit: contain;
        }

        .bottom-domain {
            font-size: 10.5px;
            color: #64748b;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .bottom-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
        }

        /* Loader Overlay */
        .itinerary-loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 25, 47, 0.92);
            backdrop-filter: blur(8px);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        .loader-spinner {
            width: 44px;
            height: 44px;
            border: 3.5px solid rgba(255, 255, 255, 0.2);
            border-top-color: var(--std-blue-400);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 14px;
        }

        .loader-status-text {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
            }

            .v3-pdf-actions-bar,
            .itinerary-loader-overlay {
                display: none !important;
            }

            .page {
                box-shadow: none !important;
                margin: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }
        }
    </style>
</head>

<body>

    <!-- Loading Overlay -->
    <div class="itinerary-loader-overlay" id="itinerary-page-loader">
        <div class="loader-spinner"></div>
        <div class="loader-status-text" id="loaderStatusMsg">Preparing Executive Itinerary...</div>
    </div>

    <!-- Floating Top Action Bar -->
    <div class="v3-pdf-actions-bar">
        <button type="button" class="btn-pdf-action btn-pdf-download" id="btnDownloadPDF">
            <i class="ri-file-pdf-line"></i> Download PDF
        </button>
        <button type="button" class="btn-pdf-action btn-pdf-print" id="btnPrintPage">
            <i class="ri-printer-line"></i> Print
        </button>
    </div>

    <div id="pdfContentWrapper">

        <!-- PAGE 1: COVER & OVERVIEW -->
        <section class="page" id="page-1">
            <div class="exec-top-ledger">
                <div class="exec-brand-left">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                    <?php else: ?>
                        <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </div>
                <div class="exec-meta-right">
                    <span class="exec-tag-pill">Executive Proposal</span>
                    <?php if (!empty($formattedTravelDate)): ?>
                        <span class="exec-tag-pill"><i class="ri-calendar-line"></i> Travel Date: <?php echo htmlspecialchars($formattedTravelDate); ?></span>
                    <?php endif; ?>
                    <span class="exec-tag-pill exec-ref-pill"><?php echo htmlspecialchars((string)$cleanQuoteRef); ?></span>
                </div>
            </div>

            <header class="hero">
                <?php if (!empty($heroFeatureImage)): ?>
                    <img src="<?php echo htmlspecialchars($heroFeatureImage); ?>" alt="<?php echo htmlspecialchars($pkgTitle); ?>">
                <?php else: ?>
                    <div class="default-day-image">
                        <h2><?php echo htmlspecialchars(!empty($pkgTitle) ? $pkgTitle : (!empty($pkgCity) ? $pkgCity : 'Executive Proposal')); ?></h2>
                    </div>
                <?php endif; ?>
                <div class="hero-banner-line"></div>
                <div class="refid">
                    Ref. id: #<?php echo htmlspecialchars((string)$cleanQuoteRef); ?>
                </div>
            </header>

            <div class="container">
                <div class="title-section">
                    <span class="title-pre-eyebrow">Official Travel Itinerary &amp; Proposal</span>
                    <h1><?php echo htmlspecialchars($pkgTitle); ?></h1>
                    <div class="title-line-divider"></div>
                </div>

                <div class="stats <?php echo !empty($formattedTravelDate) ? 'has-travel-date' : ''; ?>">
                    <div class="box">
                        <p>Duration</p>
                        <p><strong><?php echo $totalNightsCount; ?>N / <?php echo $totalDaysCount; ?>D</strong></p>
                    </div>

                    <?php if (!empty($formattedTravelDate)): ?>
                        <div class="box">
                            <p>Travel Date</p>
                            <p><strong><?php echo htmlspecialchars($formattedTravelDate); ?></strong></p>
                        </div>
                    <?php endif; ?>

                    <div class="box">
                        <p>Destination</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgCity)); ?></strong></p>
                    </div>

                    <div class="box">
                        <p>Theme / Style</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgTheme)); ?></strong></p>
                    </div>

                    <div class="box">
                        <p>Stay Standard</p>
                        <p><strong><?php echo htmlspecialchars($formatCategoryName($pkgHotel)); ?></strong></p>
                    </div>
                </div>

                <div class="top-section">
                    <div class="top-heading">
                        <span class="bar"><i class="ri-compass-3-line"></i></span>
                        <h2>Executive Briefing &amp; Tour Overview</h2>
                    </div>
                    <div class="paragraph">
                        <?php echo $cleanOverview ?: '<p>A meticulously planned travel proposal designed for efficiency, comfort, and memorable destination experiences.</p>'; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- DAY-WISE PAGES (1 PAGE PER DAY) -->
        <?php
        $dayIndex = 1;
        foreach ($pkgDays as $day):
            $dayNumStr = str_pad($dayIndex, 2, '0', STR_PAD_LEFT);
            $dayOffset = $dayIndex - 1;
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
            $dayTitle = cleanItineraryUnicode($day['day'] ?? ($day['title'] ?? "Day {$dayIndex}"));
            $titleParts = explode(' - ', $dayTitle, 2);
            $cleanTitle = capitalizeFirstWord(cleanItineraryUnicode(isset($titleParts[1]) ? trim($titleParts[1]) : trim($dayTitle)));

            $dayImg = fixImageUrl($day['feature_image'] ?? ($day['image'] ?? ($day['day_image'] ?? '')));
            if (empty($dayImg) && !empty($gallery_image_list)) {
                $galList = is_string($gallery_image_list) ? json_decode($gallery_image_list, true) : $gallery_image_list;
                if (is_array($galList)) {
                    foreach ($galList as $gItem) {
                        if (isset($gItem['day']) && (int)$gItem['day'] === $dayIndex && !empty($gItem['path'])) {
                            $dayImg = fixImageUrl($gItem['path']);
                            break;
                        }
                    }
                    if (empty($dayImg) && isset($galList[$dayIndex - 1]['path'])) {
                        $dayImg = fixImageUrl($galList[$dayIndex - 1]['path']);
                    }
                }
            }
            $dayOverview = capitalizeParagraphs(cleanItineraryUnicode($day['overview'] ?? ($day['description'] ?? ($day['desc'] ?? ''))));

            $rawStayCity = trim((string)($day['stay_city'] ?? ($day['city'] ?? '')));
            $stayCityClean = cleanProposalLabel($rawStayCity);
            $stayCityLower = strtolower($rawStayCity);

            $builderHotelId = (int)($day['hotel_id'] ?? ($day['property_id'] ?? 0));
            $rawBuilderHotel = trim((string)($day['hotel_title'] ?? ($day['hotel'] ?? ($day['stay'] ?? ($day['stay_option'] ?? ($day['hotel_name'] ?? ''))))));
            $builderHotelClean = cleanProposalLabel($rawBuilderHotel);
            $builderHotelLower = strtolower($rawBuilderHotel);

            $isHotelExplicitlyHidden = (
                (isset($day['show_hotel']) && ($day['show_hotel'] === 0 || $day['show_hotel'] === '0' || $day['show_hotel'] === false)) ||
                !empty($day['hide_hotel']) ||
                !empty($day['is_hotel_hidden'])
            );

            $isTransitStay = (
                str_contains($stayCityLower, 'transit') ||
                str_contains($stayCityLower, 'overnight') ||
                str_contains($stayCityLower, 'no stay') ||
                str_contains($stayCityLower, 'no hotel') ||
                str_contains($stayCityLower, 'trip end') ||
                str_contains($stayCityLower, 'tour end') ||
                in_array($builderHotelLower, ['no stay', 'no hotel', 'no_hotel', 'transit', 'in transit', 'overnight', 'overnight journey', 'tour end', 'trip end'])
            );

            $propTitle = '';
            $propImage = '';
            $propLocation = '';
            $propCategory = '';
            $propRoomType = '';
            $propMealPlan = '';

            if ($isHotelExplicitlyHidden) {
                $propTitle = '';
            } elseif ($isTransitStay) {
                $isTransitOrTripEnd = true;
                $stayCityClean = (str_contains($stayCityLower, 'end') || str_contains($stayCityLower, 'departure')) ? 'Tour End / Departure' : 'Overnight / In Transit';
            } else {
                if (!empty($builderHotelClean) || $builderHotelId > 0) {
                    $propTitle = $builderHotelClean;
                    if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX')) {
                        $pRow = null;
                        if ($builderHotelId > 0) {
                            $pQ = "SELECT property_id, property_title, property_thumbnail, property_address, property_city, property_type FROM " . TABLE_PREFIX . "property WHERE property_site_id = " . (int)SITE_ID . " AND property_id = {$builderHotelId} LIMIT 1";
                            if ($pR = @mysqli_query($connection, $pQ)) {
                                $pRow = mysqli_fetch_assoc($pR);
                            }
                        }
                        if (!$pRow && !empty($propTitle)) {
                            $cacheKey = strtolower($propTitle);
                            if (!isset($propertyLookupCache[$cacheKey])) {
                                $cleanEscName = mysqli_real_escape_string($connection, $propTitle);
                                $pQuery = "SELECT property_id, property_title, property_thumbnail, property_address, property_city, property_type FROM " . TABLE_PREFIX . "property WHERE property_site_id = " . (int)SITE_ID . " AND (property_title = '{$cleanEscName}' OR property_title LIKE '{$cleanEscName}%') LIMIT 1";
                                $pRes = @mysqli_query($connection, $pQuery);
                                $propertyLookupCache[$cacheKey] = ($pRes && mysqli_num_rows($pRes) > 0) ? mysqli_fetch_assoc($pRes) : false;
                            }
                            $pRow = !empty($propertyLookupCache[$cacheKey]) ? $propertyLookupCache[$cacheKey] : null;
                        }
                        if (!empty($pRow)) {
                            $propTitle = $pRow['property_title'];
                            $propImage = fixImageUrl($pRow['property_thumbnail'] ?? '');
                            $locArr = array_filter([$pRow['property_address'] ?? '', $pRow['property_city'] ?? '']);
                            $propLocation = implode(', ', $locArr);
                            $propCategory = $formatCategoryName($pRow['property_type'] ?? '', $day['hotel_stars'] ?? '');
                        }
                    }

                    if (!empty($propTitle)) {
                        if (empty($propCategory) && !empty($day['hotel_stars'])) {
                            $propCategory = $formatCategoryName($day['hotel_stars']);
                        }
                        if (!empty($day['room_type'])) {
                            $propRoomType = ucwords(str_replace('_', ' ', trim((string)$day['room_type'])));
                        }
                        if (!empty($day['meal_plan'])) {
                            $propMealPlan = $formatMealPlanName($day['meal_plan']);
                        }
                    }
                }

                if (empty($propTitle)) {
                    $assignedHotel = $dayWiseHotels[$dayIndex] ?? null;
                    if (!empty($assignedHotel)) {
                        $propTitle = $assignedHotel['property_title'] ?? '';
                        $propImage = fixImageUrl($assignedHotel['property_thumbnail'] ?? '');
                        $locArr = array_filter([$assignedHotel['property_address'] ?? '', $assignedHotel['property_city'] ?? '']);
                        $propLocation = implode(', ', $locArr);
                        $propCategory = $formatCategoryName($assignedHotel['property_type'] ?? '', $day['hotel_stars'] ?? '');
                        $propRoomType = !empty($day['room_type']) ? ucwords(str_replace('_', ' ', trim((string)$day['room_type']))) : (!empty($assignedHotel['tph_room_type']) ? ucwords(str_replace('_', ' ', $assignedHotel['tph_room_type'])) : '');
                        if (!empty($day['meal_plan'])) {
                            $propMealPlan = $formatMealPlanName($day['meal_plan']);
                        }
                    }
                }

                if (empty($propTitle)) {
                    $isPotentialTransit = $checkTransitOrTripEnd($day, $dayIndex, count($pkgDays), $cleanTitle, $dayOverview);
                    if (!$isPotentialTransit && !empty($pkgHotel) && is_string($pkgHotel) && trim($pkgHotel) !== '') {
                        $propTitle = cleanProposalLabel($pkgHotel);
                    }
                }

                $isTransitOrTripEnd = empty($propTitle) && $checkTransitOrTripEnd($day, $dayIndex, count($pkgDays), $cleanTitle, $dayOverview);
                if (empty($stayCityClean)) {
                    $stayCityClean = !$isTransitOrTripEnd ? $pkgCity : 'Overnight / In Transit';
                }

                if (!empty($propTitle)) {
                    if (empty($propCategory)) $propCategory = $formatCategoryName($day['hotel_stars'] ?? 'Executive Hotel');
                    if (empty($propLocation)) $propLocation = $stayCityClean ?: $pkgCity;
                    if (empty($propImage)) $propImage = $getCategoryThumbnail($propCategory, $propTitle);
                }
            }

            $dayVehicle = trim((string)($day['vehicle'] ?? ($day['transport'] ?? ($day['transportation'] ?? ''))));
            if (empty($dayVehicle) || strtolower($dayVehicle) === 'custom' || strtolower($dayVehicle) === 'private sedan') {
                $isFirstDay = ($dayIndex === 1);
                $isLastDay  = ($dayIndex === count($pkgDays));
                if ($isFirstDay || $isLastDay) {
                    $dayVehicle = !empty($pkgOutVehicle) ? $pkgOutVehicle : (!empty($pkgLocVehicle) ? $pkgLocVehicle : ($dayVehicle ?: 'Executive Private Sedan'));
                } else {
                    $dayVehicle = !empty($pkgLocVehicle) ? $pkgLocVehicle : (!empty($pkgOutVehicle) ? $pkgOutVehicle : ($dayVehicle ?: 'Executive Private Sedan'));
                }
            }
            $dayVehicleClean = cleanProposalLabel($dayVehicle);
            $dayTransferNotes = trim((string)($day['transfer_notes'] ?? ''));
        ?>
            <section class="page days-page" id="page-day-<?php echo $dayIndex; ?>">
                <div class="exec-top-ledger">
                    <div class="exec-brand-left">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                        <?php else: ?>
                            <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="exec-meta-right">
                        <span class="exec-tag-pill">Day <?php echo $dayNumStr; ?> of <?php echo str_pad($totalDaysCount, 2, '0', STR_PAD_LEFT); ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?></span>
                    </div>
                </div>

                <header class="hero">
                    <?php if (!empty($dayImg)): ?>
                        <img src="<?php echo htmlspecialchars($dayImg); ?>" alt="<?php echo htmlspecialchars($cleanTitle); ?>">
                    <?php else: ?>
                        <div class="default-day-image">
                            <h2>Day <?php echo $dayNumStr; ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?></h2>
                        </div>
                    <?php endif; ?>
                    <div class="hero-banner-line"></div>
                </header>

                <div class="container">
                    <div class="day-header">
                        <div class="day-number-badge">Day <?php echo $dayNumStr; ?><?php if (!empty($dayDateWithDay)): ?> &bull; <?php echo $dayDateWithDay; ?><?php endif; ?></div>
                        <h2 class="day-title"><?php echo htmlspecialchars($cleanTitle); ?></h2>
                    </div>

                    <div class="day-content">
                        <?php echo !empty($dayOverview) ? (strpos($dayOverview, '<') !== false ? $dayOverview : nl2br(htmlspecialchars($dayOverview, ENT_QUOTES, 'UTF-8', false))) : '<p>Sightseeing, transfers, and executive itinerary schedule.</p>'; ?>
                    </div>

                    <?php if (!empty($stayCityClean) || !empty($propTitle) || !empty($dayVehicleClean)): ?>
                        <div class="day-stay-plain-wrap">
                            <?php if (!empty($stayCityClean) || $isTransitOrTripEnd): ?>
                                <div class="stay-city-plain-row">
                                    <i class="ri-map-pin-2-fill"></i>
                                    <span class="stay-city-title">Overnight Destination:</span>
                                    <span class="stay-city-val"><?php echo htmlspecialchars($stayCityClean ?: ($isTransitOrTripEnd ? 'Overnight / In Transit' : $pkgCity)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($propTitle)): ?>
                                <div class="stay-property-plain-card">
                                    <?php if (!empty($propImage)): ?>
                                        <div class="stay-property-thumb-wrap">
                                            <img src="<?php echo htmlspecialchars($propImage); ?>" alt="<?php echo htmlspecialchars($propTitle); ?>" class="stay-property-thumb" loading="lazy">
                                        </div>
                                    <?php endif; ?>

                                    <div class="stay-property-details">
                                        <div class="stay-property-top">
                                            <h4 class="stay-property-title"><?php echo htmlspecialchars($propTitle); ?></h4>
                                            <?php if (!empty($propCategory)): ?>
                                                <span class="stay-property-tag category-tag">
                                                    <i class="ri-star-fill"></i> <?php echo htmlspecialchars($propCategory); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="stay-property-meta-row">
                                            <?php if (!empty($propLocation)): ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($propLocation); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($propRoomType)): ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-door-open-line"></i> <?php echo htmlspecialchars($propRoomType); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($propMealPlan)): ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-restaurant-line"></i> <?php echo htmlspecialchars($propMealPlan); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($dayVehicleClean)): ?>
                                <div class="stay-property-plain-card stay-transport-plain-card">
                                    <div class="stay-property-thumb-wrap transport-thumb-wrap">
                                        <i class="ri-roadster-fill" style="font-size: 32px; color: var(--std-blue-500);"></i>
                                    </div>

                                    <div class="stay-property-details">
                                        <div class="stay-property-top">
                                            <h4 class="stay-property-title"><?php echo htmlspecialchars($dayVehicleClean); ?></h4>
                                        </div>

                                        <div class="stay-property-meta-row">
                                            <span class="stay-property-meta-item">
                                                <i class="ri-route-line"></i> <?php echo htmlspecialchars($stayCityClean ?: 'Dedicated Route Transit'); ?>
                                            </span>
                                            <?php if (!empty($dayTransferNotes)): ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-information-line"></i> <?php echo htmlspecialchars($dayTransferNotes); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-shield-check-line"></i> Dedicated Executive Vehicle &amp; Fuel Included
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
            <div class="exec-top-ledger">
                <div class="exec-brand-left">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                    <?php else: ?>
                        <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </div>
                <div class="exec-meta-right">
                    <span class="exec-tag-pill">Payment Details</span>
                </div>
            </div>

            <header class="hero">
                <div class="default-day-image" style="background: linear-gradient(135deg, var(--std-navy-900), #1e40af);">
                    <h2>Pricing</h2>
                </div>
                <div class="hero-banner-line"></div>
            </header>

            <div class="container">
                <div class="top-heading">
                    <span class="bar"><i class="ri-bank-card-line"></i></span>
                    <h2>Formal Proposal Quotation</h2>
                </div>

                <div class="payment-summary-wrapper">
                    <div class="pricing-main-card">
                        <div class="pricing-header-banner">
                            <div class="pricing-header-content">
                                <div class="pricing-header-type">
                                    <i class="ri-file-list-3-line"></i> Proposal Quotation
                                    <span class="quote-ref-tag"><?php echo htmlspecialchars($cleanQuoteRef); ?></span>
                                </div>
                                <h3 class="pricing-package-title"><?php echo htmlspecialchars($pkgTitle); ?></h3>
                                <div class="pricing-package-meta">
                                    <span><i class="ri-group-line"></i> <?php echo htmlspecialchars($paxSummaryText); ?></span>
                                    <span>•</span>
                                    <span><i class="ri-calendar-line"></i> <?php echo $totalNightsCount; ?>N / <?php echo $totalDaysCount; ?>D</span>
                                    <?php if (!empty($pkgCity)): ?>
                                        <span>•</span>
                                        <span><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($pkgCity); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="badge-best-value">Corporate Best Rate</span>
                        </div>

                        <!-- Itemized Price Table -->
                        <div class="pricing-breakdown-list">
                            <div class="pricing-breakdown-row">
                                <span class="pricing-breakdown-label">
                                    Adult Price ( <?php echo max(1, $adultCount); ?> Adult<?php echo $adultCount > 1 ? 's' : ''; ?><?php if ($adultPriceVal > 0): ?> @ &#8377;<?php echo number_format($adultPriceVal, 2); ?><?php endif; ?> )
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
                                        Infant Charges ( <?php echo $infantCount; ?> Infant<?php echo $infantCount > 1 ? 's' : ''; ?><?php if ($infantPriceVal > 0): ?> @ &#8377;<?php echo number_format($infantPriceVal, 2); ?><?php endif; ?> )
                                    </span>
                                    <span class="pricing-breakdown-val">&#8377;<?php echo number_format($infantSubtotal, 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="pricing-breakdown-row">
                                <span class="pricing-breakdown-label">Applicable GST / Service Tax ( <?php echo $gstPercentVal; ?>% )</span>
                                <span class="pricing-breakdown-val gst-highlight">+ &#8377;<?php echo number_format($gstAmountVal, 2); ?></span>
                            </div>
                        </div>

                        <div class="pricing-total-block">
                            <span class="pricing-total-label">Total Payable Amount</span>
                            <span class="pricing-total-amount">&#8377;<?php echo number_format($finalPriceVal, 2); ?></span>
                        </div>

                        <?php if (!empty($payNowUrl)): ?>
                            <a href="<?php echo htmlspecialchars($payNowUrl); ?>" target="_blank" rel="noopener" class="btn-book-now">
                                <span>Click here to Book Now</span>
                            </a>
                        <?php endif; ?>

                        <div class="pricing-validity">
                            <i class="ri-time-line"></i> Quotation valid until <strong><?php echo $validUntilDate; ?></strong>
                        </div>
                    </div>

                    <?php
                    $hasAnyBank = !empty($companyBankAcNumber) || !empty($companyBankName) || !empty($companyBankDetails);
                    $hasAnyUpi  = !empty($companyUpiId);
                    if ($hasAnyBank || $hasAnyUpi):
                        $bankUpiClass = ($hasAnyBank && $hasAnyUpi) ? '' : 'single-col';
                    ?>
                        <div class="bank-upi-section <?php echo $bankUpiClass; ?>">
                            <?php if ($hasAnyBank): ?>
                                <div class="bank-details-box">
                                    <div class="section-micro-title">
                                        <i class="ri-bank-line"></i>
                                        <span>Official Banking Coordinates</span>
                                    </div>
                                    <div class="bank-details-stack">
                                        <?php if (!empty($companyBankAcName)): ?>
                                            <div class="bank-row">
                                                <span class="bank-label">A/C Name</span>
                                                <span class="bank-value"><?php echo htmlspecialchars($companyBankAcName); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($companyBankAcNumber)): ?>
                                            <div class="bank-row">
                                                <span class="bank-label">Account No.</span>
                                                <span class="bank-value" style="color: var(--std-blue-500);"><?php echo htmlspecialchars($companyBankAcNumber); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($companyBankIfsc)): ?>
                                            <div class="bank-row">
                                                <span class="bank-label">IFSC Code</span>
                                                <span class="bank-value"><?php echo htmlspecialchars($companyBankIfsc); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($companyBankName)): ?>
                                            <div class="bank-row">
                                                <span class="bank-label">Bank Name</span>
                                                <span class="bank-value"><?php echo htmlspecialchars($companyBankName); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasAnyUpi): ?>
                                <div class="upi-qr-box">
                                    <div class="qr-box-wrapper">
                                        <?php if (!empty($upiQrUrl)): ?>
                                            <img src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="UPI QR Code" class="upi-qr-img" />
                                        <?php else: ?>
                                            <i class="ri-qr-code-line" style="font-size: 40px; color: var(--std-navy-900);"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="qr-title">Instant UPI Transfer</div>
                                    <div class="upi-address-text" title="<?php echo htmlspecialchars($companyUpiId); ?>">
                                        <?php echo htmlspecialchars($companyUpiId); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- INCLUSIONS PAGE -->
        <?php if (!empty($incList)): ?>
            <section class="page days-page" id="page-inclusions">
                <div class="exec-top-ledger">
                    <div class="exec-brand-left">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                        <?php else: ?>
                            <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="exec-meta-right">
                        <span class="exec-tag-pill" style="color: #16a34a; background: #f0fdf4; border-color: #bbf7d0;">Package Specifications</span>
                    </div>
                </div>

                <header class="hero">
                    <div class="default-day-image" style="background: linear-gradient(135deg, #064e3b, #047857);">
                        <h2>Included Services</h2>
                    </div>
                    <div class="hero-banner-line" style="background: linear-gradient(90deg, #10b981, #34d399, transparent);"></div>
                </header>

                <div class="container">
                    <div class="climax-section">
                        <div class="tour_info_card tour_included">
                            <h3 class="tour_info_title">
                                <i class="ri-checkbox-circle-fill text-green-600"></i> What Is Explicitly Included In This Proposal:
                            </h3>
                            <ul class="tour_info_list">
                                <?php foreach ($incList as $inc): if (empty($inc)) continue; ?>
                                    <li>
                                        <div class="tour_check"><i class="ri-check-line"></i></div>
                                        <span><?php echo htmlspecialchars(capitalizeFirstWord($inc)); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- EXCLUSIONS PAGE -->
        <?php if (!empty($excList)): ?>
            <section class="page days-page" id="page-exclusions">
                <div class="exec-top-ledger">
                    <div class="exec-brand-left">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                        <?php else: ?>
                            <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="exec-meta-right">
                        <span class="exec-tag-pill" style="color: #dc2626; background: #fef2f2; border-color: #fecaca;">Important Clarifications</span>
                    </div>
                </div>

                <header class="hero">
                    <div class="default-day-image" style="background: linear-gradient(135deg, #7f1d1d, #b91c1c);">
                        <h2>Excluded Items</h2>
                    </div>
                    <div class="hero-banner-line" style="background: linear-gradient(90deg, #ef4444, #f87171, transparent);"></div>
                </header>

                <div class="container">
                    <div class="climax-section">
                        <div class="tour_info_card tour_excluded">
                            <h3 class="tour_info_title">
                                <i class="ri-close-circle-fill text-red-600"></i> What Is Not Included In This Proposal:
                            </h3>
                            <ul class="tour_info_list">
                                <?php foreach ($excList as $exc): if (empty($exc)) continue; ?>
                                    <li>
                                        <div class="tour_cross"><i class="ri-close-line"></i></div>
                                        <span><?php echo htmlspecialchars(capitalizeFirstWord($exc)); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- COMPANY & CONTACT DETAILS PAGE -->
        <section class="page days-page" id="page-contact">
            <div class="exec-top-ledger">
                <div class="exec-brand-left">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                    <?php else: ?>
                        <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </div>
                <div class="exec-meta-right">
                    <span class="exec-tag-pill">Partner Credentials &amp; Desk</span>
                </div>
            </div>

            <header class="hero">
                <div class="default-day-image" style="background: linear-gradient(135deg, var(--std-navy-900), #1e293b);">
                    <h2>Reservation Support</h2>
                </div>
                <div class="hero-banner-line"></div>
            </header>

            <div class="container">
                <div class="top-heading">
                    <span class="bar"><i class="ri-building-line"></i></span>
                    <h2>Authorized Agency Contact Desk</h2>
                </div>

                <div class="company-contact-card">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--std-navy-900);">
                        <?php echo htmlspecialchars($siteName); ?>
                    </h3>
                    <p style="font-size: 12px; color: var(--std-text-soft); margin-bottom: 8px;">
                        Certified Destination Management Partner &amp; Travel Operator
                    </p>

                    <div class="company-contact-grid">
                        <?php if (defined('SITE_MOBILE') && !empty(SITE_MOBILE)): ?>
                            <div class="contact-item">
                                <i class="ri-phone-fill"></i>
                                <span><strong>Direct Line:</strong> <?php echo htmlspecialchars(SITE_MOBILE); ?></span>
                            </div>
                        <?php elseif (!empty($sitePhone)): ?>
                            <div class="contact-item">
                                <i class="ri-phone-fill"></i>
                                <span><strong>Direct Line:</strong> <?php echo htmlspecialchars($sitePhone); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_WHATSAPP_NUMBER') && !empty(COMPANY_WHATSAPP_NUMBER)): ?>
                            <div class="contact-item">
                                <i class="ri-whatsapp-fill" style="color: #16a34a;"></i>
                                <span><strong>WhatsApp:</strong> <?php echo htmlspecialchars(COMPANY_WHATSAPP_NUMBER); ?></span>
                            </div>
                        <?php elseif (!empty($siteWhatsapp)): ?>
                            <div class="contact-item">
                                <i class="ri-whatsapp-fill" style="color: #16a34a;"></i>
                                <span><strong>WhatsApp:</strong> <?php echo htmlspecialchars($siteWhatsapp); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('SITE_EMAIL') && !empty(SITE_EMAIL)): ?>
                            <div class="contact-item">
                                <i class="ri-mail-fill"></i>
                                <span><strong>Email:</strong> <?php echo htmlspecialchars(SITE_EMAIL); ?></span>
                            </div>
                        <?php elseif (!empty($siteEmail)): ?>
                            <div class="contact-item">
                                <i class="ri-mail-fill"></i>
                                <span><strong>Email:</strong> <?php echo htmlspecialchars($siteEmail); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_SITE_DOMAIN') && !empty(COMPANY_SITE_DOMAIN)): ?>
                            <div class="contact-item">
                                <i class="ri-global-fill"></i>
                                <span><strong>Portal:</strong> <?php echo htmlspecialchars(COMPANY_SITE_DOMAIN); ?></span>
                            </div>
                        <?php elseif (!empty($siteUrl) || !empty($siteDomain)): ?>
                            <div class="contact-item">
                                <i class="ri-global-fill"></i>
                                <span><strong>Portal:</strong> <?php echo htmlspecialchars($siteDomain); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (defined('COMPANY_ADDRESS') && !empty(COMPANY_ADDRESS)): ?>
                            <div class="contact-item contact-item-full">
                                <i class="ri-map-pin-2-fill"></i>
                                <span><strong>Office:</strong> <?php echo htmlspecialchars(COMPANY_ADDRESS); ?></span>
                            </div>
                        <?php elseif (!empty($siteAddress)): ?>
                            <div class="contact-item contact-item-full">
                                <i class="ri-map-pin-2-fill"></i>
                                <span><strong>Office:</strong> <?php echo htmlspecialchars($siteAddress); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $socialIcons = [
                        'facebook'  => ['icon' => 'ri-facebook-circle-fill', 'label' => 'Facebook'],
                        'instagram' => ['icon' => 'ri-instagram-fill',       'label' => 'Instagram'],
                        'x'         => ['icon' => 'ri-twitter-x-fill',       'label' => 'X'],
                        'twitter'   => ['icon' => 'ri-twitter-x-fill',       'label' => 'Twitter'],
                        'linkedin'  => ['icon' => 'ri-linkedin-box-fill',    'label' => 'LinkedIn'],
                        'youtube'   => ['icon' => 'ri-youtube-fill',         'label' => 'YouTube'],
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
                            <h4 class="social-section-title">Verified Corporate Channels</h4>
                            <div class="company-social-links">
                                <?php foreach ($socialMediaList as $platform => $url):
                                    if (empty($url) || !is_string($url) || trim($url) === '') continue;
                                    $pKey = strtolower(trim($platform));
                                    $meta = $socialIcons[$pKey] ?? ['icon' => 'ri-link-m', 'label' => ucfirst($pKey)];
                                    $rawUrl = trim($url);
                                    $cleanUrl = preg_replace('#^https?://(www\.)?#i', '', rtrim($rawUrl, '/'));
                                ?>
                                    <a href="<?php echo htmlspecialchars($rawUrl); ?>" target="_blank" rel="noopener" class="social-badge-btn" title="<?php echo htmlspecialchars($rawUrl); ?>">
                                        <i class="<?php echo $meta['icon']; ?>"></i>
                                        <span><?php echo htmlspecialchars($cleanUrl); ?></span>
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
                $spDesig  = $salesPersonDesig  ?? 'Senior Travel Consultant';

                if (empty($spName) && isset($connection) && $connection) {
                    $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
                    $targetUserId = 0;

                    if (!empty($postData['agent_id']) && (int)$postData['agent_id'] > 0) {
                        $targetUserId = (int)$postData['agent_id'];
                    } elseif (!empty($postData['assigned_agent']) && (int)$postData['assigned_agent'] > 0) {
                        $targetUserId = (int)$postData['assigned_agent'];
                    }

                    $qId = (int)($quotationId ?? ($quoteId ?? 0));
                    if ($targetUserId === 0 && $qId > 0) {
                        $qRes = @mysqli_query($connection, "
                            SELECT q.quotation_sent_by, q.quotation_ref_id, e.enquiry_agent 
                            FROM {$tblPrefix}quotation q 
                            LEFT JOIN {$tblPrefix}enquiry e ON e.enquiry_id = q.quotation_ref_id 
                            WHERE q.quotation_id = {$qId} 
                            LIMIT 1
                        ");
                        if ($qRes && $qRow = mysqli_fetch_assoc($qRes)) {
                            if (!empty($qRow['enquiry_agent']) && (int)$qRow['enquiry_agent'] > 0) {
                                $targetUserId = (int)$qRow['enquiry_agent'];
                            } elseif (!empty($qRow['quotation_sent_by']) && (int)$qRow['quotation_sent_by'] > 0) {
                                $targetUserId = (int)$qRow['quotation_sent_by'];
                            }
                        }
                    }

                    if ($targetUserId > 0) {
                        $spRes = @mysqli_query($connection, "
                            SELECT profile_display_name, profile_first_name, profile_last_name,
                                   profile_email, profile_mobile, profile_image
                            FROM {$tblPrefix}profile
                            WHERE profile_user_id = {$targetUserId}
                            LIMIT 1
                        ");
                        if ($spRes && $spRow = mysqli_fetch_assoc($spRes)) {
                            $spName   = trim($spRow['profile_display_name'] ?? trim(($spRow['profile_first_name'] ?? '') . ' ' . ($spRow['profile_last_name'] ?? '')));
                            $spEmail  = trim($spRow['profile_email'] ?? '');
                            $spMobile = trim($spRow['profile_mobile'] ?? '');
                            $rawImg   = $spRow['profile_image'] ?? '';
                            if (!empty($rawImg)) {
                                $siteBase = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
                                $spImage  = filter_var($rawImg, FILTER_VALIDATE_URL) ? $rawImg : $siteBase . '/' . ltrim($rawImg, '/');
                            }
                        }
                    }
                }

                if (empty($spName)) {
                    $spName   = !empty($siteName) ? $siteName . ' Travel Specialist' : 'Executive Travel Desk';
                    $spDesig  = 'Senior Destination Consultant';
                    $spMobile = !empty($siteWhatsapp) ? $siteWhatsapp : (!empty($sitePhone) ? $sitePhone : '');
                    $spEmail  = !empty($siteEmail) ? $siteEmail : '';
                    $spImage  = !empty($siteLogo) ? $siteLogo : '';
                }

                $spWhatsappDisplay = !empty($salesPersonWhatsapp) ? $salesPersonWhatsapp : $spMobile;
                $cleanSpMobileDigits = preg_replace('/[^\d]/', '', (string)$spMobile);
                $cleanSpWhatsappDigits = preg_replace('/[^\d]/', '', (string)$spWhatsappDisplay);
                $spWords    = preg_split('/\s+/', trim($spName));
                $spInitials = strtoupper(mb_substr($spWords[0], 0, 1) . (count($spWords) > 1 ? mb_substr(end($spWords), 0, 1) : ''));
                ?>
                <div class="sp-card">
                    <div class="sp-card-top">
                        <div class="sp-avatar-col">
                            <?php if (!empty($spImage)): ?>
                                <img src="<?php echo htmlspecialchars($spImage); ?>" alt="<?php echo htmlspecialchars($spName); ?>" class="sp-avatar">
                            <?php else: ?>
                                <div class="sp-avatar-initials"><?php echo htmlspecialchars($spInitials); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="sp-identity-col">
                            <div class="sp-badge">
                                <i class="ri-user-star-line"></i> Dedicated Itinerary Specialist
                            </div>
                            <div class="sp-name"><?php echo htmlspecialchars($spName); ?></div>
                            <div class="sp-desig"><?php echo htmlspecialchars($spDesig); ?> &bull; Assigned Proposal Desk</div>
                        </div>
                    </div>

                    <div class="sp-contacts-large-grid">
                        <?php if (!empty($spMobile)): ?>
                            <a href="tel:<?php echo htmlspecialchars($cleanSpMobileDigits); ?>" class="sp-contact-box" title="Direct Phone">
                                <div class="sp-icon-circle">
                                    <i class="ri-phone-line"></i>
                                </div>
                                <div>
                                    <span class="sp-field-label">Direct Calling</span>
                                    <span class="sp-field-value"><?php echo htmlspecialchars($spMobile); ?></span>
                                </div>
                            </a>

                            <?php if (!empty($cleanSpWhatsappDigits)): ?>
                                <a href="https://wa.me/<?php echo htmlspecialchars($cleanSpWhatsappDigits); ?>?text=<?php echo rawurlencode("Hello " . $spName . ", I have a query regarding proposal " . $cleanQuoteRef); ?>" target="_blank" rel="noopener" class="sp-contact-box" title="WhatsApp">
                                    <div class="sp-icon-circle" style="color: #16a34a;">
                                        <i class="ri-whatsapp-line"></i>
                                    </div>
                                    <div>
                                        <span class="sp-field-label">WhatsApp Channel</span>
                                        <span class="sp-field-value"><?php echo htmlspecialchars($spWhatsappDisplay); ?></span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($spEmail)): ?>
                            <a href="mailto:<?php echo htmlspecialchars($spEmail); ?>" class="sp-contact-box sp-contact-box-full" title="Official Email">
                                <div class="sp-icon-circle" style="color: #0284c7;">
                                    <i class="ri-mail-line"></i>
                                </div>
                                <div>
                                    <span class="sp-field-label">Electronic Mail</span>
                                    <span class="sp-field-value"><?php echo htmlspecialchars($spEmail); ?></span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- BOOKING POLICIES, CANCELLATION & TERMS PAGE (LAST PAGE) -->
        <section class="page days-page" id="page-policies">
            <div class="exec-top-ledger">
                <div class="exec-brand-left">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="exec-brand-logo">
                    <?php else: ?>
                        <span class="exec-brand-title"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </div>
                <div class="exec-meta-right">
                    <span class="exec-tag-pill">Contractual Policies</span>
                </div>
            </div>

            <header class="hero">
                <div class="default-day-image" style="background: linear-gradient(135deg, #0a192f, #1e293b);">
                    <h2>Terms &amp; Policies</h2>
                </div>
                <div class="hero-banner-line"></div>
            </header>

            <div class="container">
                <div class="legal-doc-body">
                    <?php if (!empty($rawItineraryPolicy)): ?>
                        <div class="legal-doc-section">
                            <?php renderPolicyContent($rawItineraryPolicy); ?>
                        </div>
                    <?php else: ?>
                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">1. Payment &amp; Confirmation Policy</h3>
                            <?php
                            $fallbackPayment = implode('', [
                                '<ul>',
                                '<li><strong>Booking Deposit:</strong> A 50% non-refundable advance remittance is required upon confirmation to secure accommodations, private vehicle allocation, and transit reservations.</li>',
                                '<li><strong>Balance Clearance:</strong> The remaining 50% balance must be settled upon arrival at the initial destination check-in.</li>',
                                '<li><strong>Remittance Mode:</strong> Transfers must be executed solely through verified corporate bank accounts, authorized UPI, or formal invoice payment gateway links.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawPaymentPolicy, $fallbackPayment);
                            ?>
                        </div>

                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">2. Cancellation &amp; Refund Schedule</h3>
                            <?php
                            $fallbackCancel = implode('', [
                                '<ul>',
                                '<li><strong>Over 15 Days Notice:</strong> 20% administrative and file processing charge is retained.</li>',
                                '<li><strong>7 to 14 Days Notice:</strong> 50% of the total quotation value is non-refundable.</li>',
                                '<li><strong>Under 7 Days / Non-Arrival:</strong> 100% forfeiture of total package amount.</li>',
                                '<li><strong>Refund Timeline:</strong> Authorized refunds are disbursed to the originating account within 5 to 7 corporate banking business days.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawCancellationPolicy, $fallbackCancel);
                            ?>
                        </div>

                        <div class="legal-doc-section">
                            <h3 class="legal-doc-section-title">3. General Booking Conditions</h3>
                            <?php
                            $fallbackTerms = implode('', [
                                '<ul>',
                                '<li><strong>Check-In Norms:</strong> Standard hotel check-in is 12:00 PM to 2:00 PM; checkout is 11:00 AM. Early arrivals or delayed departures depend strictly on inventory availability.</li>',
                                '<li><strong>Vehicle Operations:</strong> Assigned private transport operates strictly on an itinerary point-to-point basis. 24-hour disposal or unmapped diversions are not part of agreed scope.</li>',
                                '<li><strong>Identification:</strong> All traveling guests must maintain government-issued physical photo identification throughout the duration of the tour.</li>',
                                '</ul>'
                            ]);
                            renderPolicyContent($rawTermsConditions, $fallbackTerms);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </div>

    <!-- Script for Dynamic Pagination, Footers & PDF Exporter -->
    <script>
        function paginatePolicyPages() {
            const initialPage = document.getElementById('page-policies');
            if (!initialPage) return;

            initialPage.classList.add('page-policies-page');

            const container = initialPage.querySelector('.container');
            const docBody = initialPage.querySelector('.legal-doc-body');
            if (!container || !docBody) return;

            if (!initialPage.hasAttribute('data-original-policies-html')) {
                initialPage.setAttribute('data-original-policies-html', docBody.innerHTML);
            }
            const sourceHtml = initialPage.getAttribute('data-original-policies-html');
            if (!sourceHtml || !sourceHtml.trim()) return;

            document.querySelectorAll('.page-policies-page').forEach(p => {
                if (p !== initialPage) p.remove();
            });

            document.querySelectorAll('.page-footer-decor').forEach(f => f.remove());

            const sandbox = document.createElement('div');
            sandbox.innerHTML = sourceHtml;

            function extractBlockItems(node) {
                const blocks = [];
                Array.from(node.children).forEach(child => {
                    if (child.tagName === 'DIV' && child.children.length > 0 && !child.classList.contains('sp-card')) {
                        blocks.push(...extractBlockItems(child));
                    } else {
                        blocks.push(child);
                    }
                });
                return blocks;
            }

            function isHeadingOrTitle(el) {
                if (!el || !el.tagName) return false;
                const tag = el.tagName.toUpperCase();
                if (/^H[1-6]$/.test(tag)) return true;
                if (el.classList.contains('legal-doc-section-title') || el.classList.contains('title') || el.classList.contains('heading')) return true;

                const text = el.textContent.trim();
                if (text.length > 0 && text.length < 100) {
                    if (/^(\d+[\.\:\)]|[A-Z][\.\:\)]|[IVXLCDM]+[\.\:\)])\s+/i.test(text)) return true;
                    if (el.querySelector('strong, b') && el.textContent.trim() === el.querySelector('strong, b').textContent.trim()) return true;
                }
                return false;
            }

            const allItems = extractBlockItems(sandbox);
            if (allItems.length === 0) return;

            docBody.innerHTML = '';
            let currentSection = document.createElement('div');
            currentSection.className = 'legal-doc-section';
            docBody.appendChild(currentSection);

            let currentPage = initialPage;
            let currentDocBody = docBody;
            let pageCounter = 1;
            const MAX_DOC_HEIGHT = 740;

            function appendNewPolicyPage() {
                pageCounter++;
                const newPage = document.createElement('section');
                newPage.className = 'page days-page page-policies-page';
                newPage.id = 'page-policies-' + pageCounter;

                const ledgerClone = initialPage.querySelector('.exec-top-ledger').cloneNode(true);
                newPage.appendChild(ledgerClone);

                const heroClone = initialPage.querySelector('.hero').cloneNode(true);
                newPage.appendChild(heroClone);

                const newContainer = document.createElement('div');
                newContainer.className = 'container';

                const newBody = document.createElement('div');
                newBody.className = 'legal-doc-body';

                const newSec = document.createElement('div');
                newSec.className = 'legal-doc-section';
                newBody.appendChild(newSec);

                newContainer.appendChild(newBody);
                newPage.appendChild(newContainer);

                currentPage.parentNode.insertBefore(newPage, currentPage.nextSibling);

                currentPage = newPage;
                currentDocBody = newBody;
                currentSection = newSec;
            }

            for (let i = 0; i < allItems.length; i++) {
                const item = allItems[i];

                if (isHeadingOrTitle(item) && currentSection.children.length > 0) {
                    if (currentDocBody.offsetHeight + 70 > MAX_DOC_HEIGHT) {
                        appendNewPolicyPage();
                        currentSection.appendChild(item);
                        continue;
                    }
                }

                currentSection.appendChild(item);

                if (currentDocBody.offsetHeight > MAX_DOC_HEIGHT) {
                    if ((item.tagName === 'UL' || item.tagName === 'OL') && item.children.length > 0) {
                        const allLis = Array.from(item.children);
                        item.innerHTML = '';

                        let splitIndex = 0;
                        for (let j = 0; j < allLis.length; j++) {
                            item.appendChild(allLis[j]);
                            if (currentDocBody.offsetHeight > MAX_DOC_HEIGHT) {
                                allLis[j].remove();
                                splitIndex = j;
                                break;
                            }
                            splitIndex = j + 1;
                        }

                        if (splitIndex > 0 && splitIndex < allLis.length) {
                            appendNewPolicyPage();
                            const nextList = document.createElement(item.tagName);
                            if (item.tagName === 'OL') {
                                nextList.setAttribute('start', splitIndex + 1);
                            }
                            for (let k = splitIndex; k < allLis.length; k++) {
                                nextList.appendChild(allLis[k]);
                            }
                            currentSection.appendChild(nextList);
                            continue;
                        } else if (splitIndex === 0) {
                            for (let k = 0; k < allLis.length; k++) {
                                item.appendChild(allLis[k]);
                            }
                            item.remove();
                        }
                    } else {
                        item.remove();
                    }

                    const lastEl = currentSection.lastElementChild;
                    let orphanHeading = null;
                    if (lastEl && isHeadingOrTitle(lastEl)) {
                        orphanHeading = lastEl;
                        orphanHeading.remove();
                    }

                    appendNewPolicyPage();

                    if (orphanHeading) {
                        currentSection.appendChild(orphanHeading);
                    }
                    currentSection.appendChild(item);
                }
            }
        }

        function initTemplateEffects() {
            paginatePolicyPages();

            const pages = document.querySelectorAll('.page');
            const totalPages = pages.length;

            pages.forEach((page, index) => {
                if (page.querySelector('.page-footer-decor')) return;
                const footer = document.createElement('div');
                footer.className = 'page-footer-decor';
                const pageNumber = "Page " + (index + 1) + " of " + totalPages;

                footer.innerHTML = `
                    <div class="bottom-decor">
                        <span class="bottom-domain"><?php echo htmlspecialchars($siteDomain); ?></span>
                    </div>
                    <div class="bottom-meta-row">
                        <span>Powered by Aleya.in</span>
                        <span class="meta-page-num" data-page-calc="true">${pageNumber}</span>
                    </div>
                `;
                page.appendChild(footer);
            });

            document.querySelectorAll('.hero img').forEach((img) => {
                const handleImgError = function() {
                    const hero = img.closest('.hero');
                    if (!hero || hero.querySelector('.default-day-image')) return;
                    const altText = img.getAttribute('alt') || 'Tour Day';
                    const fallback = document.createElement('div');
                    fallback.className = 'default-day-image';
                    const h2 = document.createElement('h2');
                    h2.textContent = altText;
                    fallback.appendChild(h2);
                    img.replaceWith(fallback);
                };
                if (img.complete && img.naturalWidth === 0) {
                    handleImgError();
                } else {
                    img.addEventListener('error', handleImgError, {
                        once: true
                    });
                }
            });

            hidePageLoader();
        }

        function hidePageLoader() {
            const loader = document.getElementById('itinerary-page-loader');
            if (loader) {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
                setTimeout(() => {
                    if (loader.parentNode) loader.style.display = 'none';
                }, 380);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener("DOMContentLoaded", initTemplateEffects);
        } else {
            initTemplateEffects();
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                initTemplateEffects();
                hidePageLoader();
            });
        }
        setTimeout(hidePageLoader, 2500);

        async function downloadItineraryPDF() {
            const btn = document.getElementById('btnDownloadPDF');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Preparing PDF...';
            }

            const loader = document.getElementById('itinerary-page-loader');
            const msg = document.getElementById('loaderStatusMsg');
            if (loader) {
                loader.style.display = 'flex';
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
                if (msg) msg.textContent = 'Generating PDF Document...';
            }

            try {
                await new Promise(resolve => setTimeout(resolve, 350));
                const pages = document.querySelectorAll(".page");
                if (!pages || pages.length === 0) {
                    throw new Error("No itinerary pages found to export.");
                }

                const jspdfModule = window.jspdf || (typeof jsPDF !== 'undefined' ? {
                    jsPDF
                } : null);
                if (!jspdfModule || !jspdfModule.jsPDF) {
                    throw new Error("jsPDF library is not loaded.");
                }
                const pdf = new jspdfModule.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                for (let i = 0; i < pages.length; i++) {
                    if (msg) msg.textContent = `Rendering page ${i + 1} of ${pages.length}...`;

                    const canvas = await html2canvas(pages[i], {
                        scale: 2,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: '#ffffff',
                        logging: false,
                        imageTimeout: 10000,
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

                if (msg) msg.textContent = 'Saving PDF file...';
                <?php
                $safePdfTitle = preg_replace('/_+/', '_', preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace('&', 'and', cleanItineraryUnicode($pkgTitle))));
                $safePdfTitle = trim($safePdfTitle, '_');
                if (empty($safePdfTitle)) $safePdfTitle = 'Tour_Package';
                ?>
                pdf.save("<?php echo $safePdfTitle; ?>_Proposal.pdf");
                await new Promise(resolve => setTimeout(resolve, 400));
                return true;
            } catch (err) {
                console.error('PDF export error:', err);
                if (msg) msg.textContent = 'PDF generation encountered an error.';
                window.print();
                throw err;
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-pdf-line"></i> Download PDF';
                }
                hidePageLoader();
            }
        }
        window.downloadItineraryPDF = downloadItineraryPDF;

        const btnPdf = document.getElementById('btnDownloadPDF');
        if (btnPdf) {
            btnPdf.addEventListener('click', function() {
                downloadItineraryPDF().catch(function(e) {
                    console.error(e);
                });
            });
        }

        const btnPrint = document.getElementById('btnPrintPage');
        if (btnPrint) {
            btnPrint.addEventListener('click', function() {
                window.print();
            });
        }

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