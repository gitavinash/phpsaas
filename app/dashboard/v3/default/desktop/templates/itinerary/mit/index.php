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
    /**
     * Renders policy content in Word-document style.
     * - If DB content exists: outputs it directly (HTML stripped of inline styles, or plain text as <p>).
     * - If empty: outputs the $fallbackHtml string as-is.
     */
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
            // Strip only inline style attributes to keep HTML structure
            $cleaned = preg_replace('/\s*style="[^"]*"/i', '', $trimmed);
            echo capitalizeParagraphs($cleaned);
        } else {
            // Plain text: split into lines and render as <p> blocks
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $trimmed)));
            foreach ($lines as $line) {
                echo '<p>' . htmlspecialchars(capitalizeFirstWord($line)) . '</p>';
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

$gstPercentVal  = defined('GST_PERCENTAGE') ? (float)GST_PERCENTAGE : 5.0;
$basePriceVal   = $totalPriceVal;
$gstAmountVal   = $totalPriceVal > 0 ? round($totalPriceVal * ($gstPercentVal / 100), 2) : 0.0;
$finalPriceVal  = $totalPriceVal + $gstAmountVal;

$advance30Percent = round($finalPriceVal * 0.30, 2);
$balance70Percent = $finalPriceVal - $advance30Percent;

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
    $payNowUrl = "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I would like to make payment for Quotation " . $cleanQuoteRef . " (" . ($tour_package_title ?? 'Tour Package') . ") - Amount: INR " . number_format($finalPriceVal > 0 ? $finalPriceVal : $totalPriceVal, 2));
} else {
    $payNowUrl = "tel:" . preg_replace('/[^\d\+]/', '', (string)$sitePhone);
}

$waPayUrl = !empty($cleanWhatsappDigits)
    ? "https://wa.me/" . $cleanWhatsappDigits . "?text=" . rawurlencode("Hello " . $siteName . ", I am confirming quotation " . $cleanQuoteRef . " (" . ($tour_package_title ?? 'Tour Package') . "). Please share payment link / QR code for INR " . number_format($finalPriceVal > 0 ? $finalPriceVal : $totalPriceVal, 2))
    : '';

// Package Variables
$pkgTitle       = capitalizeFirstWord(cleanItineraryUnicode($tour_package_title ?? ($itinerary_title ?? ($title ?? 'Tour Package Itinerary'))));
$pkgCity        = cleanItineraryUnicode($tour_package_city ?? ($city ?? 'Destination'));
$pkgState       = cleanItineraryUnicode($tour_package_state ?? '');
$pkgCountry     = cleanItineraryUnicode($tour_package_country ?? '');
$pkgDuration    = $tour_package_duration ?? ($numberOfDays ?? ($duration ?? 0));
$pkgTheme       = cleanItineraryUnicode($tour_package_theme ?? ($travelStyle ?? 'Standard'));
$pkgHotel       = cleanItineraryUnicode($tour_package_hotel ?? ($hotel ?? ''));
$pkgPriceAdult  = $tour_package_price_selling_adult ?? ($selling_price ?? ($price ?? 0));
$pkgOutVehicle  = cleanItineraryUnicode($tour_package_outstation_vehicle ?? ($outstationVehicle ?? ''));
$pkgLocVehicle  = cleanItineraryUnicode($tour_package_localsightseen_vehicle ?? ($localSightseeingVehicle ?? ''));
$pkgOverview    = cleanItineraryUnicode($tour_package_overview ?? ($itinerary_overview ?? ($overview ?? '')));
$pkgInclusions  = cleanItineraryUnicode($tour_package_inclusions ?? ($inclusions ?? ''));
$pkgExclusions  = cleanItineraryUnicode($tour_package_exclusions ?? ($exclusions ?? ''));
$pkgDays        = cleanItineraryUnicode($itineraries ?? ($itinerary ?? ($days ?? [])));
$quoteRef       = $quoteId ?? ($quotationId ?? ($tour_package_id ?? rand(1000, 9999)));

// Company Bank & UPI Details
$companyBankName      = defined('COMPANY_BANK_NAME') ? COMPANY_BANK_NAME : ($bank_name ?? ($bankDetailsMap['bank_name'] ?? ''));
$companyBankAcName    = defined('COMPANY_BANK_AC_NAME') ? COMPANY_BANK_AC_NAME : ($bank_ac_name ?? ($bankDetailsMap['bank_ac_name'] ?? ''));
$companyBankAcNumber  = defined('COMPANY_BANK_AC_NUMBER') ? COMPANY_BANK_AC_NUMBER : ($bank_ac_number ?? ($bankDetailsMap['bank_ac_number'] ?? ''));
$companyBankIfsc      = defined('COMPANY_BANK_IFSC') ? COMPANY_BANK_IFSC : ($bank_ifsc ?? ($bankDetailsMap['bank_ifsc'] ?? ''));
$companyBankAddress   = defined('COMPANY_BANK_ADDRESS') ? COMPANY_BANK_ADDRESS : ($bank_address ?? ($bankDetailsMap['bank_address'] ?? ''));
$companyBankDetails   = defined('COMPANY_BANK_DETAILS') ? COMPANY_BANK_DETAILS : ($bank_details ?? ($bankDetailsMap['bank_details'] ?? ''));
$companyUpiId         = defined('COMPANY_UPI_ID') ? COMPANY_UPI_ID : ($upi_id ?? ($bankDetailsMap['upi_id'] ?? ($upi_details ?? ($bankDetailsMap['upi_details'] ?? ''))));
$hasRealBankDetails   = !empty($companyBankAcNumber) || !empty($companyBankName) || !empty($companyBankDetails);
$hasUpiDetails        = !empty($companyUpiId);
$hasBankDetails       = $hasRealBankDetails;

// Dynamic UPI QR Code URL Generation - ONLY when company UPI ID is configured
$upiPayeeName = !empty($companyBankAcName) ? $companyBankAcName : $siteName;
$upiPayAmt    = ($advance30Percent > 0) ? $advance30Percent : ($finalPriceVal > 0 ? $finalPriceVal : $totalPriceVal);
$upiQrUrl     = '';
if ($hasUpiDetails) {
    $upiPayload = "upi://pay?pa=" . rawurlencode($companyUpiId) . "&pn=" . rawurlencode($upiPayeeName) . "&am=" . number_format($upiPayAmt, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Quote " . $cleanQuoteRef);
    $upiQrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=" . urlencode($upiPayload);
}

// Quote Validity & Pax Summary for Pricing Card
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
        return '3 Star Hotel';
    }
    $catLower = strtolower($cat);
    $catMap = [
        '3_star_hotel' => '3 Star Hotel',
        '4_star_hotel' => '4 Star Hotel',
        '5_star_hotel' => '5 Star Hotel',
        'budget_hotel' => 'Budget Hotel',
        'deluxe_hotel' => 'Deluxe Hotel',
        'luxury_hotel' => 'Luxury Hotel',
        'resort'       => 'Resort / Retreat',
        'houseboat'    => 'Deluxe Houseboat',
        'camp'         => 'Luxury Camp / Tent',
        'home_stay'    => 'Homestay / Cottage',
        'hotel'        => 'Standard Hotel'
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
        'CP'  => 'CP (Breakfast Included)',
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

// Helper: Transit or trip end detection
$isTransitOrTripEnd = function ($dayTitle, $dayOverview = '', $stayCity = '') {
    $combined = strtolower(trim((string)$dayTitle . ' ' . (string)$dayOverview . ' ' . (string)$stayCity));
    if (
        str_contains($combined, 'transit') ||
        str_contains($combined, 'overnight journey') ||
        str_contains($combined, 'overnight travel') ||
        str_contains($combined, 'night journey') ||
        str_contains($combined, 'no hotel') ||
        str_contains($combined, 'no stay') ||
        str_contains($combined, 'trip end') ||
        str_contains($combined, 'tour end') ||
        str_contains($combined, 'departure') ||
        str_contains($combined, 'drop') ||
        str_contains($combined, 'return') ||
        str_contains($combined, 'fly back') ||
        str_contains($combined, 'back home')
    ) {
        return true;
    }
    return false;
};

if (is_string($pkgDays)) {
    $pkgDays = json_decode($pkgDays, true) ?: [];
}

// Resolve Stay Category for Hero Section
$allHotelsHidden = false;
if (!empty($pkgDays) && is_array($pkgDays)) {
    $allHotelsHidden = true;
    foreach ($pkgDays as $d) {
        $hsHidden = (isset($d['show_hotel']) && ($d['show_hotel'] === 0 || $d['show_hotel'] === '0' || $d['show_hotel'] === false)) || !empty($d['hide_hotel']) || !empty($d['is_hotel_hidden']);
        if (!$hsHidden) {
            $allHotelsHidden = false;
            break;
        }
    }
}

$stayCategory = '';
if ($allHotelsHidden) {
    $pkgHotel = '';
} else {
    if (!empty($tour_package_hotel_category)) {
        $stayCategory = $formatCategoryName($tour_package_hotel_category);
    }
    if (empty($stayCategory) && !empty($pkgDays) && is_array($pkgDays)) {
        foreach ($pkgDays as $d) {
            $hsHidden = (isset($d['show_hotel']) && ($d['show_hotel'] === 0 || $d['show_hotel'] === '0' || $d['show_hotel'] === false)) || !empty($d['hide_hotel']) || !empty($d['is_hotel_hidden']);
            if ($hsHidden) continue;
            $hs = trim((string)($d['hotel_stars'] ?? ''));
            if (!empty($hs) && strtolower($hs) !== 'all' && strtolower($hs) !== 'no_hotel') {
                $stayCategory = $formatCategoryName($hs);
                break;
            }
        }
    }
    if (empty($stayCategory) && !empty($tour_package_hotel)) {
        $cleanH = strtolower(trim((string)$tour_package_hotel));
        if (str_contains($cleanH, 'star') || str_contains($cleanH, 'hotel') || str_contains($cleanH, 'resort') || str_contains($cleanH, 'stay') || str_contains($cleanH, 'camp')) {
            $stayCategory = $formatCategoryName($tour_package_hotel);
        }
    }
    if (empty($stayCategory)) {
        $stayCategory = '3 Star Hotel';
    }
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
if (!empty($cleanOverview)) {
    if (strpos($cleanOverview, '<') === false) {
        $cleanOverview = '<p>' . nl2br(htmlspecialchars($cleanOverview, ENT_QUOTES, 'UTF-8', false)) . '</p>';
    }
    $cleanOverview = capitalizeParagraphs($cleanOverview);
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
        .hero .default-day-image,
        .days-page .hero .default-day-image {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            text-align: center;
            color: #fff;
            padding: 20px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        .hero .default-day-image h2,
        .days-page .hero .default-day-image h2 {
            font-size: 3.5em;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 6px 20px rgba(0, 0, 0, 0.35), 0 2px 6px rgba(0, 0, 0, 0.2);
            padding: 0 30px;
            box-sizing: border-box;
            max-width: 95%;
            word-break: break-word;
            line-height: 1.2;
            margin: 0;
        }

        #page-1 .hero .default-day-image {
            background: linear-gradient(135deg, var(--primary-color, #e76317), var(--secondary-color, #26679c));
        }

        #page-1 .hero .default-day-image h2 {
            font-size: clamp(1.8rem, 3.2vw, 2.75rem);
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: 0.5px;
            max-width: 90%;
            padding: 0 20px;
            text-transform: uppercase;
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
            white-space: nowrap;
            flex-shrink: 0;
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

        /* ── Day Stay & Property Information (Plain Layout) ── */
        .day-stay-plain-wrap {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* First Row: Stay City */
        .stay-city-plain-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #334155;
            margin-bottom: 2px;
        }

        .stay-city-plain-row i {
            font-size: 15px;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .stay-city-title {
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        .stay-city-val {
            font-weight: 800;
            color: #0f172a;
            font-size: 13.5px;
            font-family: "Mulish", sans-serif;
        }

        /* Second Row: Property & Transportation Information (Increased Card Height) */
        .stay-property-plain-card {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 16px 20px;
            min-height: 104px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-sizing: border-box;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        }

        .stay-property-thumb-wrap {
            width: 124px;
            height: 84px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .transport-thumb-wrap {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
            border-color: #bae6fd;
        }

        .stay-property-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .stay-property-details {
            flex: 1;
            min-width: 0;
        }

        .stay-property-top {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 7px;
        }

        .stay-property-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            font-family: "Mulish", sans-serif;
            line-height: 1.25;
        }

        .stay-property-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
        }

        .category-tag {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .category-tag i {
            font-size: 11px;
            color: #f59e0b;
        }

        .stay-property-meta-row {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 12px;
            color: #475569;
            line-height: 1.4;
        }

        .stay-property-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .stay-property-meta-item i {
            font-size: 14px;
            color: var(--primary-color);
        }

        .stay-transport-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 700;
            color: #0369a1;
            background: #f0f9ff;
            padding: 3px 9px;
            border-radius: 5px;
            border: 1px solid #bae6fd;
        }

        .stay-transport-meta-item i {
            color: #0284c7;
            font-size: 14px;
        }

        .stay-transfer-notes-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #64748b;
            font-style: italic;
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

        .bank-upi-section.single-col {
            grid-template-columns: 1fr;
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
        #page-policies .hero,
        .page-policies-page .hero,
        #page-contact .hero {
            height: 42mm !important;
            min-height: 42mm !important;
            max-height: 42mm !important;
        }

        #page-policies .hero .default-day-image h2,
        .page-policies-page .hero .default-day-image h2,
        #page-contact .hero .default-day-image h2 {
            font-size: 2.2em !important;
            padding: 0 20px !important;
        }

        #page-policies .container,
        .page-policies-page .container,
        #page-contact .container {
            padding: 8px 50px 10px 50px;
            max-height: calc(297mm - 42mm - 28mm);
            overflow: visible;
        }

        #page-policies .top-heading,
        .page-policies-page .top-heading,
        #page-contact .top-heading {
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
            border-radius: 12px;
            padding: 14px 20px;
            margin-top: 10px;
        }

        .company-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 14px;
            margin-top: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #334155;
        }

        .contact-item i {
            font-size: 16px;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .contact-item-full {
            grid-column: 1 / -1;
        }

        /* Company Social Section */
        .company-social-section {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .social-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .company-social-links {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .social-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .social-badge-btn i {
            font-size: 14px;
            color: var(--badge-accent, #0284c7);
            flex-shrink: 0;
        }

        .social-url-text {
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .social-badge-btn:hover {
            border-color: var(--badge-accent, #0284c7);
            background: #f8fafc;
            transform: translateY(-1px);
        }

        /* ── Large Executive Salesperson Card ── */
        .sp-card {
            margin-top: 18px;
            padding: 20px 24px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4.5px solid var(--primary-color);
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            box-sizing: border-box;
        }

        .sp-card-top {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sp-avatar-col {
            flex-shrink: 0;
        }

        .sp-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            display: block;
        }

        .sp-avatar-initials {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            font-family: "Mulish", sans-serif;
            flex-shrink: 0;
        }

        .sp-identity-col {
            flex: 1;
            min-width: 0;
        }

        .sp-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--primary-color);
            margin-bottom: 3px;
        }

        .sp-badge i {
            font-size: 13px;
        }

        .sp-name {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            font-family: "Mulish", sans-serif;
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .sp-desig {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        /* 2-Column Large Contact Grid with Full-Width Email */
        .sp-contacts-large-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .sp-contact-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            text-decoration: none;
            color: inherit;
            box-sizing: border-box;
            min-width: 0;
            transition: all 0.15s ease;
        }

        .sp-contact-box-full {
            grid-column: 1 / -1;
        }

        .sp-contact-box:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .sp-icon-circle {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sp-phone-circle {
            background: #eff6ff;
            color: #2563eb;
        }

        .sp-wa-circle {
            background: #f0fdf4;
            color: #16a34a;
        }

        .sp-email-circle {
            background: #fef2f2;
            color: #dc2626;
        }

        .sp-contact-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }

        .sp-field-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .sp-field-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            white-space: normal;
            word-break: break-word;
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
        /* Full-screen Loading Animation Overlay */
        .itinerary-loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(241, 245, 249, 0.96);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 9999999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        .itinerary-loader-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 32px 36px;
            max-width: 360px;
            width: 88%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
        }

        .loader-brand-logo {
            max-height: 44px;
            width: auto;
            object-fit: contain;
            margin-bottom: 16px;
        }

        .loader-spinner-ring {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3.5px solid #e2e8f0;
            border-top-color: var(--primary-color);
            animation: ringSpin 0.8s linear infinite;
            margin-bottom: 14px;
        }

        @keyframes ringSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .loader-heading {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        .loader-subtext {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 14px;
        }

        .loader-progress-track {
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 12px;
            position: relative;
        }

        .loader-progress-bar {
            position: absolute;
            height: 100%;
            width: 40%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 999px;
            animation: indeterminateSlide 1.4s ease-in-out infinite;
        }

        @keyframes indeterminateSlide {
            0% {
                left: -40%;
                width: 30%;
            }

            50% {
                left: 30%;
                width: 50%;
            }

            100% {
                left: 100%;
                width: 30%;
            }
        }

        .loader-tagline {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media print {
            .itinerary-loader-overlay {
                display: none !important;
            }

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
                max-height: 297mm;
                margin: 0;
                padding: 0;
                page-break-after: always;
                break-after: page;
                page-break-inside: avoid;
                break-inside: avoid;
                box-shadow: none;
                border-radius: 0;
            }

            .page:last-child {
                page-break-after: auto;
                break-after: auto;
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

            #page-policies .hero,
            .page-policies-page .hero,
            #page-contact .hero {
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

    <!-- Full-Screen Initial Loading Overlay -->
    <div id="itinerary-page-loader" class="itinerary-loader-overlay">
        <div class="itinerary-loader-card">
            <?php if (!empty($siteLogo)): ?>
                <img src="<?php echo htmlspecialchars($siteLogo); ?>" class="loader-brand-logo" alt="<?php echo htmlspecialchars($siteName); ?>">
            <?php endif; ?>
            <div class="loader-spinner-ring"></div>
            <div class="loader-heading">Preparing Your Itinerary</div>
            <div class="loader-subtext" id="loaderStatusMsg">Formatting pages &amp; travel details...</div>
            <div class="loader-progress-track">
                <div class="loader-progress-bar" id="loaderProgressBar"></div>
            </div>
            <div class="loader-tagline">Powered by Aleya.in</div>
        </div>
    </div>

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
                        <h2><?php echo htmlspecialchars(!empty($pkgTitle) ? $pkgTitle : (!empty($pkgCity) ? $pkgCity : 'Tour Package Proposal')); ?></h2>
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

                    <?php if (!empty($formattedTravelDate)): ?>
                        <div class="box">
                            <p>Travel Date</p>
                            <p><strong><?php echo htmlspecialchars($formattedTravelDate); ?></strong></p>
                        </div>
                    <?php endif; ?>

                    <div class="box">
                        <p>Location</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgCity)); ?></strong></p>
                    </div>

                    <div class="box">
                        <p>Theme / Style</p>
                        <p><strong><?php echo htmlspecialchars(cleanProposalLabel($pkgTheme)); ?></strong></p>
                    </div>

                    <?php if (!empty($stayCategory)): ?>
                        <div class="box">
                            <p>Stay Option</p>
                            <p><strong><?php echo htmlspecialchars($stayCategory); ?></strong></p>
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
        // Preload assigned package hotels if available in DB
        $dayWiseHotels = [];
        $packageIdForHotels = (int)($quotationData['quotation_type_id'] ?? ($tour_package_id ?? ($postData['package_id'] ?? ($postData['tour_package_id'] ?? 0))));
        if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX') && $packageIdForHotels > 0) {
            // Preload gallery_image_list if empty
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

        // Check if day represents transit / journey / trip conclusion
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
                'proceed to your onward journey',
                'overnight journey',
                'overnight bus',
                'overnight volvo',
                'overnight train',
                'board the volvo',
                'board the bus',
                'board the train',
                'board your bus',
                'board your volvo',
                'bus stand. proceed',
                'bus stand',
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

            // If last day and mentions departure / drop / return
            if ($idx === $total) {
                if (
                    str_contains($combined, 'departure') ||
                    str_contains($combined, 'depart') ||
                    str_contains($combined, 'drop') ||
                    str_contains($combined, 'return') ||
                    str_contains($combined, 'fly back') ||
                    str_contains($combined, 'back home') ||
                    str_contains($combined, 'delhi')
                ) {
                    return true;
                }
            }

            return false;
        };

        // Format hotel category
        $formatCategoryName = function ($rawCategory, $stars = '') {
            $cat = trim((string)$rawCategory);
            if (empty($cat) && !empty($stars)) {
                $cat = $stars;
            }
            if (empty($cat)) {
                return 'Standard Hotel';
            }
            $catLower = strtolower($cat);
            $catMap = [
                '3_star_hotel' => '3 Star Hotel',
                '4_star_hotel' => '4 Star Hotel',
                '5_star_hotel' => '5 Star Hotel',
                'budget_hotel' => 'Budget Hotel',
                'deluxe_hotel' => 'Deluxe Hotel',
                'luxury_hotel' => 'Luxury Hotel',
                'resort'       => 'Resort / Retreat',
                'houseboat'    => 'Deluxe Houseboat',
                'camp'         => 'Luxury Camp / Tent',
                'hotel'        => 'Hotel'
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

        // Format meal plan
        $formatMealPlanName = function ($rawMeal) {
            $m = strtoupper(trim((string)$rawMeal));
            if (empty($m)) return '';
            $mealMap = [
                'CP'  => 'CP (Breakfast Included)',
                'MAP' => 'MAP (Breakfast & Dinner)',
                'AP'  => 'AP (All Meals Included)',
                'EP'  => 'EP (Room Only)'
            ];
            return $mealMap[$m] ?? $rawMeal;
        };

        // Category default thumbnail
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
            // Strip out "Day X - " prefix if present
            $titleParts = explode(' - ', $dayTitle, 2);
            $cleanTitle = capitalizeFirstWord(cleanItineraryUnicode(isset($titleParts[1]) ? trim($titleParts[1]) : trim($dayTitle)));

            $dayImg = fixImageUrl($day['feature_image'] ?? ($day['image'] ?? ($day['day_image'] ?? '')));
            if (empty($dayImg) && !empty($gallery_image_list)) {
                $galList = is_string($gallery_image_list) ? json_decode($gallery_image_list, true) : $gallery_image_list;
                if (is_array($galList)) {
                    // Try to match by explicit day field first
                    foreach ($galList as $gItem) {
                        if (isset($gItem['day']) && (int)$gItem['day'] === $dayIndex && !empty($gItem['path'])) {
                            $dayImg = fixImageUrl($gItem['path']);
                            break;
                        }
                    }
                    // Positional fallback
                    if (empty($dayImg) && isset($galList[$dayIndex - 1]['path'])) {
                        $dayImg = fixImageUrl($galList[$dayIndex - 1]['path']);
                    }
                }
            }
            $dayOverview = capitalizeParagraphs(cleanItineraryUnicode($day['overview'] ?? ($day['description'] ?? ($day['desc'] ?? ''))));

            // Stay City from Itinerary Builder
            $rawStayCity = trim((string)($day['stay_city'] ?? ($day['city'] ?? '')));
            $stayCityClean = cleanProposalLabel($rawStayCity);
            $stayCityLower = strtolower($rawStayCity);

            // Hotel from Itinerary Builder
            $builderHotelId = (int)($day['hotel_id'] ?? ($day['property_id'] ?? 0));
            $rawBuilderHotel = trim((string)($day['hotel_title'] ?? ($day['hotel'] ?? ($day['stay'] ?? ($day['stay_option'] ?? ($day['hotel_name'] ?? ''))))));
            $builderHotelClean = cleanProposalLabel($rawBuilderHotel);
            $builderHotelLower = strtolower($rawBuilderHotel);

            // Check if hotel is explicitly hidden in Itinerary Builder
            $isHotelExplicitlyHidden = (
                (isset($day['show_hotel']) && ($day['show_hotel'] === 0 || $day['show_hotel'] === '0' || $day['show_hotel'] === false)) ||
                !empty($day['hide_hotel']) ||
                !empty($day['is_hotel_hidden'])
            );

            // Check if stay is explicitly in transit or no hotel
            $isTransitStay = (
                str_contains($stayCityLower, 'transit') ||
                str_contains($stayCityLower, 'overnight') ||
                str_contains($stayCityLower, 'no stay') ||
                str_contains($stayCityLower, 'no hotel') ||
                str_contains($stayCityLower, 'trip end') ||
                str_contains($stayCityLower, 'tour end') ||
                in_array($builderHotelLower, ['no stay', 'no hotel', 'no_hotel', 'transit', 'in transit', 'overnight', 'overnight journey', 'overnight / in transit', 'tour end', 'trip end', 'self arranged', 'self / no hotel stay needed', 'overnight journey (in transit)', 'tour end / departure (no stay)']) ||
                str_contains($builderHotelLower, 'no hotel') ||
                str_contains($builderHotelLower, 'in transit')
            );

            // Resolve Property for the day
            $propTitle = '';
            $propImage = '';
            $propLocation = '';
            $propCategory = '';
            $propRoomType = '';
            $propMealPlan = '';

            if ($isHotelExplicitlyHidden) {
                // Hotel explicitly hidden by user in Itinerary Builder
                $propTitle = '';
                $builderHotelClean = '';
                $builderHotelId = 0;
            } else if ($isTransitStay) {
                // If stay is in transit, do not mention any hotel name!
                $isTransitOrTripEnd = true;
                if (str_contains($stayCityLower, 'end') || str_contains($stayCityLower, 'departure')) {
                    $stayCityClean = 'Tour End / Departure';
                } else {
                    $stayCityClean = 'Overnight / In Transit';
                }
            } else {
                // 1. Priority 1: Use Existing Itinerary Builder Data (day hotel_title, hotel, stay, hotel_id)
                if (!empty($builderHotelClean) || $builderHotelId > 0) {
                    $propTitle = $builderHotelClean;

                    // Lookup DB by ID or Title
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

                // 2. Priority 2: Fallback to Package Assigned Hotel (tour_package_hotel) ONLY if builder hotel is empty
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

                // 3. Priority 3: Fallback to General Package Hotel ($tour_package_hotel) ONLY if still empty
                if (empty($propTitle)) {
                    $isPotentialTransit = $checkTransitOrTripEnd($day, $dayIndex, count($pkgDays), $cleanTitle, $dayOverview);
                    if (!$isPotentialTransit && !empty($tour_package_hotel) && is_string($tour_package_hotel) && trim($tour_package_hotel) !== '') {
                        $propTitle = cleanProposalLabel($tour_package_hotel);
                        if (isset($connection) && defined('SITE_ID') && defined('TABLE_PREFIX')) {
                            $cacheKey = strtolower($propTitle);
                            if (!isset($propertyLookupCache[$cacheKey])) {
                                $cleanEscName = mysqli_real_escape_string($connection, $propTitle);
                                $pQuery = "SELECT property_id, property_title, property_thumbnail, property_address, property_city, property_type FROM " . TABLE_PREFIX . "property WHERE property_site_id = " . (int)SITE_ID . " AND (property_title = '{$cleanEscName}' OR property_title LIKE '{$cleanEscName}%') LIMIT 1";
                                $pRes = @mysqli_query($connection, $pQuery);
                                $propertyLookupCache[$cacheKey] = ($pRes && mysqli_num_rows($pRes) > 0) ? mysqli_fetch_assoc($pRes) : false;
                            }
                            $dbProp = !empty($propertyLookupCache[$cacheKey]) ? $propertyLookupCache[$cacheKey] : null;
                            if (!empty($dbProp)) {
                                $propTitle = $dbProp['property_title'];
                                if (empty($propImage) && !empty($dbProp['property_thumbnail'])) {
                                    $propImage = fixImageUrl($dbProp['property_thumbnail']);
                                }
                                if (empty($propLocation)) {
                                    $propLocation = implode(', ', array_filter([$dbProp['property_address'] ?? '', $dbProp['property_city'] ?? '']));
                                }
                                if (empty($propCategory) && !empty($dbProp['property_type'])) {
                                    $propCategory = $formatCategoryName($dbProp['property_type']);
                                }
                            }
                        }
                    }
                }

                // Determine if transit day without hotel
                $isTransitOrTripEnd = empty($propTitle) && $checkTransitOrTripEnd($day, $dayIndex, count($pkgDays), $cleanTitle, $dayOverview);

                if (empty($stayCityClean)) {
                    $stayCityClean = !$isTransitOrTripEnd ? $pkgCity : 'Overnight / In Transit';
                }

                // Populate remaining property metadata if property is resolved
                if (!empty($propTitle)) {
                    if (empty($propCategory)) {
                        $propCategory = $formatCategoryName($day['hotel_stars'] ?? ($pkgHotel ?? ''));
                    }
                    if (empty($propLocation)) {
                        $propLocation = $stayCityClean ?: $pkgCity;
                    }
                    if (empty($propRoomType) && !empty($day['room_type'])) {
                        $propRoomType = ucwords(str_replace('_', ' ', trim((string)$day['room_type'])));
                    }
                    if (empty($propMealPlan) && !empty($day['meal_plan'])) {
                        $propMealPlan = $formatMealPlanName($day['meal_plan']);
                    }
                    if (empty($propImage)) {
                        $propImage = $getCategoryThumbnail($propCategory, $propTitle);
                    }
                }
            }

            // Resolve day transportation option
            $dayVehicle = trim((string)($day['vehicle'] ?? ($day['transport'] ?? ($day['transportation'] ?? ''))));
            if (empty($dayVehicle) || strtolower($dayVehicle) === 'custom' || strtolower($dayVehicle) === 'private sedan') {
                $isFirstDay = ($dayIndex === 1);
                $isLastDay  = ($dayIndex === count($pkgDays));
                if ($isFirstDay || $isLastDay) {
                    $dayVehicle = !empty($pkgOutVehicle) ? $pkgOutVehicle : (!empty($pkgLocVehicle) ? $pkgLocVehicle : ($dayVehicle ?: 'Private Sedan'));
                } else {
                    $dayVehicle = !empty($pkgLocVehicle) ? $pkgLocVehicle : (!empty($pkgOutVehicle) ? $pkgOutVehicle : ($dayVehicle ?: 'Private Sedan'));
                }
            }
            $dayVehicleClean = cleanProposalLabel($dayVehicle);
            $dayTransferNotes = trim((string)($day['transfer_notes'] ?? ''));
        ?>
            <section class="page days-page" id="page-day-<?php echo $dayIndex; ?>">
                <header class="hero">
                    <?php if (!empty($dayImg)): ?>
                        <img src="<?php echo htmlspecialchars($dayImg); ?>" alt="<?php echo htmlspecialchars($cleanTitle); ?>">
                    <?php else: ?>
                        <div class="default-day-image">
                            <h2>Day <?php echo $dayNumStr; ?><?php if (!empty($dayDateStr)): ?> · <?php echo $dayDateStr; ?><?php endif; ?></h2>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="container">
                    <div class="day-header">
                        <div class="day-number-badge">Day <?php echo $dayNumStr; ?><?php if (!empty($dayDateWithDay)): ?> &bull; <?php echo $dayDateWithDay; ?><?php endif; ?></div>
                        <h2 class="day-title"><?php echo htmlspecialchars($cleanTitle); ?></h2>
                    </div>

                    <div class="day-content">
                        <?php echo !empty($dayOverview) ? (strpos($dayOverview, '<') !== false ? $dayOverview : nl2br(htmlspecialchars($dayOverview, ENT_QUOTES, 'UTF-8', false))) : '<p>Sightseeing, transfers, and exploration according to custom schedule.</p>'; ?>
                    </div>

                    <?php if (!empty($stayCityClean) || !empty($propTitle) || !empty($dayVehicleClean)): ?>
                        <div class="day-stay-plain-wrap">
                            <!-- First Row: Stay City Header -->
                            <?php if (!empty($stayCityClean) || $isTransitOrTripEnd): ?>
                                <div class="stay-city-plain-row">
                                    <i class="ri-map-pin-2-fill"></i>
                                    <span class="stay-city-title">Stay City:</span>
                                    <span class="stay-city-val"><?php echo htmlspecialchars($stayCityClean ?: ($isTransitOrTripEnd ? 'Overnight / In Transit' : $pkgCity)); ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Hotel Card (Separate Card) -->
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

                            <!-- Transport Card (Separate Card, No 'Daily Transport' badge) -->
                            <?php if (!empty($dayVehicleClean)): ?>
                                <div class="stay-property-plain-card stay-transport-plain-card">
                                    <div class="stay-property-thumb-wrap transport-thumb-wrap">
                                        <i class="ri-roadster-fill" style="font-size: 38px; color: #0284c7;"></i>
                                    </div>

                                    <div class="stay-property-details">
                                        <div class="stay-property-top">
                                            <h4 class="stay-property-title"><?php echo htmlspecialchars($dayVehicleClean); ?></h4>
                                        </div>

                                        <div class="stay-property-meta-row">
                                            <span class="stay-property-meta-item">
                                                <i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($stayCityClean ?: 'Route Journey / Transfer'); ?>
                                            </span>
                                            <?php if (!empty($dayTransferNotes)): ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-route-line"></i> <?php echo htmlspecialchars($dayTransferNotes); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="stay-property-meta-item">
                                                    <i class="ri-shield-check-line"></i> Dedicated Transport &amp; Transfers Included
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
                                    <?php if (!empty($stayCategory) || !empty($pkgHotel)): ?>
                                        <span>•</span>
                                        <span><i class="ri-hotel-line"></i> <?php echo htmlspecialchars($stayCategory ?: cleanProposalLabel($pkgHotel)); ?></span>
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
                            <span class="pricing-total-amount">&#8377;<?php echo number_format($finalPriceVal, 2); ?></span>
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

                    <!-- 6. Direct Bank Remittance & Dynamic UPI QR Section (Hidden if both empty) -->
                    <?php
                    $hasAnyBank = !empty($companyBankAcNumber) || !empty($companyBankName) || !empty($companyBankDetails);
                    $hasAnyUpi  = !empty($companyUpiId);
                    if ($hasAnyBank || $hasAnyUpi):
                        $bankUpiClass = ($hasAnyBank && $hasAnyUpi) ? '' : 'single-col';
                    ?>
                        <div class="bank-upi-section <?php echo $bankUpiClass; ?>">
                            <!-- Left Column: Bank Details -->
                            <?php if ($hasAnyBank): ?>
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
                            <?php endif; ?>

                            <!-- Right Column: QR Code + UPI Address below QR -->
                            <?php if ($hasAnyUpi): ?>
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
                                        <div class="upi-address-text" title="<?php echo htmlspecialchars($companyUpiId); ?>">
                                            <i class="ri-wallet-3-line"></i> <?php echo htmlspecialchars($companyUpiId); ?>
                                        </div>
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
                                            <span><?php echo htmlspecialchars(capitalizeFirstWord($inc)); ?></span>
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
                                            <span><?php echo htmlspecialchars(capitalizeFirstWord($exc)); ?></span>
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
                    <h2>Company Details &amp; Support</h2>
                </div>
            </header>

            <div class="container">
                <div class="top-heading">
                    <span class="bar">|</span>
                    <h2>Company Details &amp; Reservation Support</h2>
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
                                    $rawUrl = trim($url);
                                    $cleanUrl = preg_replace('#^https?://(www\.)?#i', '', rtrim($rawUrl, '/'));
                                ?>
                                    <a href="<?php echo htmlspecialchars($rawUrl); ?>" target="_blank" rel="noopener" class="social-badge-btn" style="--badge-accent: <?php echo $meta['color']; ?>;" title="<?php echo htmlspecialchars($rawUrl); ?>">
                                        <i class="<?php echo $meta['icon']; ?>"></i>
                                        <span class="social-url-text"><?php echo htmlspecialchars($cleanUrl); ?></span>
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

                // If salesperson details are not passed in directly, dynamically discover from quotation, enquiry, session, or site profile
                if (empty($spName) && isset($connection) && $connection) {
                    $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
                    $siteIdVal = defined('SITE_ID') ? (int)SITE_ID : 0;
                    $targetUserId = 0;

                    // 1. Check posted/previewed assigned agent ID
                    if (!empty($postData['agent_id']) && (int)$postData['agent_id'] > 0) {
                        $targetUserId = (int)$postData['agent_id'];
                    } elseif (!empty($postData['assigned_agent']) && (int)$postData['assigned_agent'] > 0) {
                        $targetUserId = (int)$postData['assigned_agent'];
                    }

                    // 2. Check quotation record for assigned enquiry agent FIRST
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
                            // Prioritize assigned enquiry agent over quotation creator
                            if (!empty($qRow['enquiry_agent']) && (int)$qRow['enquiry_agent'] > 0) {
                                $targetUserId = (int)$qRow['enquiry_agent'];
                            } elseif (!empty($qRow['quotation_sent_by']) && (int)$qRow['quotation_sent_by'] > 0) {
                                $targetUserId = (int)$qRow['quotation_sent_by'];
                            }
                        }
                    }

                    // 3. Check lead / enquiry ID from postData or params
                    $rawLeadRef = $postData['lead_id'] ?? ($postData['enquiry_id'] ?? ($postData['refId'] ?? ($postData['ref_id'] ?? 0)));
                    $leadIdVal = 0;
                    if (is_numeric($rawLeadRef) && (int)$rawLeadRef > 0) {
                        $leadIdVal = (int)$rawLeadRef;
                    } elseif (!empty($rawLeadRef)) {
                        $dec = @base64_decode((string)$rawLeadRef, true);
                        if ($dec !== false && is_numeric($dec)) {
                            $leadIdVal = (int)$dec;
                        }
                    }

                    if ($targetUserId === 0 && $leadIdVal > 0) {
                        $eRes = @mysqli_query($connection, "SELECT enquiry_agent FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadIdVal} LIMIT 1");
                        if ($eRes && $eRow = mysqli_fetch_assoc($eRes)) {
                            if (!empty($eRow['enquiry_agent']) && (int)$eRow['enquiry_agent'] > 0) {
                                $targetUserId = (int)$eRow['enquiry_agent'];
                            }
                        }
                    }

                    // 4. Check session / current user
                    if ($targetUserId === 0) {
                        if (!empty($_SESSION['user_id'])) {
                            $targetUserId = (int)$_SESSION['user_id'];
                        } elseif (defined('USER_ID') && (int)USER_ID > 0) {
                            $targetUserId = (int)USER_ID;
                        }
                    }

                    // 5. Query profile by user ID
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

                // 5. Fallback: company dedicated booking & itinerary consultant desk
                if (empty($spName)) {
                    $spName   = !empty($siteName) ? $siteName . ' Travel Consultant' : 'Dedicated Travel Consultant';
                    $spDesig  = 'Senior Destination Specialist';
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
                <!-- Salesperson / Dedicated Specialist Card -->
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
                                <i class="ri-user-star-fill"></i> Your Dedicated Travel Specialist
                            </div>
                            <div class="sp-name"><?php echo htmlspecialchars($spName); ?></div>
                            <div class="sp-desig"><?php echo htmlspecialchars($spDesig); ?> &bull; Direct Tour Coordinator</div>
                        </div>
                    </div>

                    <div class="sp-contacts-large-grid">
                        <?php if (!empty($spMobile)): ?>
                            <a href="tel:<?php echo htmlspecialchars($cleanSpMobileDigits); ?>" class="sp-contact-box" title="Call <?php echo htmlspecialchars($spName); ?>">
                                <div class="sp-icon-circle sp-phone-circle">
                                    <i class="ri-phone-fill"></i>
                                </div>
                                <div class="sp-contact-details">
                                    <span class="sp-field-label">Direct Calling</span>
                                    <span class="sp-field-value"><?php echo htmlspecialchars($spMobile); ?></span>
                                </div>
                            </a>

                            <?php if (!empty($cleanSpWhatsappDigits)): ?>
                                <a href="https://wa.me/<?php echo htmlspecialchars($cleanSpWhatsappDigits); ?>?text=<?php echo rawurlencode("Hello " . $spName . ", I have a query regarding itinerary " . $cleanQuoteRef); ?>" target="_blank" rel="noopener" class="sp-contact-box" title="Chat on WhatsApp">
                                    <div class="sp-icon-circle sp-wa-circle">
                                        <i class="ri-whatsapp-line"></i>
                                    </div>
                                    <div class="sp-contact-details">
                                        <span class="sp-field-label">WhatsApp Number</span>
                                        <span class="sp-field-value"><?php echo htmlspecialchars($spWhatsappDisplay); ?></span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($spEmail)): ?>
                            <a href="mailto:<?php echo htmlspecialchars($spEmail); ?>" class="sp-contact-box sp-contact-box-full" title="Email <?php echo htmlspecialchars($spName); ?>">
                                <div class="sp-icon-circle sp-email-circle">
                                    <i class="ri-mail-fill"></i>
                                </div>
                                <div class="sp-contact-details">
                                    <span class="sp-field-label">Email Address</span>
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
        // Auto-paginate policy content if it overflows the single A4 page
        function paginatePolicyPages() {
            const initialPage = document.getElementById('page-policies');
            if (!initialPage) return;

            initialPage.classList.add('page-policies-page');

            const container = initialPage.querySelector('.container');
            const docBody = initialPage.querySelector('.legal-doc-body');
            if (!container || !docBody) return;

            // 1. Cache pristine, original HTML of policies section so re-runs never lose content
            if (!initialPage.hasAttribute('data-original-policies-html')) {
                initialPage.setAttribute('data-original-policies-html', docBody.innerHTML);
            }
            const sourceHtml = initialPage.getAttribute('data-original-policies-html');
            if (!sourceHtml || !sourceHtml.trim()) return;

            // 2. Remove any previously created continuation pages on re-run
            document.querySelectorAll('.page-policies-page').forEach(p => {
                if (p !== initialPage) p.remove();
            });

            // 3. Remove existing footer decors before measuring
            document.querySelectorAll('.page-footer-decor').forEach(f => f.remove());

            // 4. Parse pristine sourceHtml in an off-DOM container and recursively extract all content blocks
            const sandbox = document.createElement('div');
            sandbox.innerHTML = sourceHtml;

            function extractBlockItems(node) {
                const blocks = [];
                Array.from(node.children).forEach(child => {
                    // Unpack generic wrapper divs or sections
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
                    // Starts with number/bullet like "8. Force..." or "1. Payment..."
                    if (/^(\d+[\.\:\)]|[A-Z][\.\:\)]|[IVXLCDM]+[\.\:\)])\s+/i.test(text)) return true;
                    // Or entire element is bold / strong
                    if (el.querySelector('strong, b') && el.textContent.trim() === el.querySelector('strong, b').textContent.trim()) return true;
                }
                return false;
            }

            const allItems = extractBlockItems(sandbox);
            if (allItems.length === 0) return;

            // 5. Reset initialPage docBody
            docBody.innerHTML = '';
            let currentSection = document.createElement('div');
            currentSection.className = 'legal-doc-section';
            docBody.appendChild(currentSection);

            let currentPage = initialPage;
            let currentDocBody = docBody;
            let pageCounter = 1;

            // Max content height in pixels allowed inside legal-doc-body
            // A4 page: 1122px. Hero: 159px. Footer decor: 90px. Padding: 18px.
            // A threshold of 740px leaves generous, elegant spacing above the company logo.
            const MAX_DOC_HEIGHT = 740;

            function appendNewPolicyPage() {
                pageCounter++;
                const newPage = document.createElement('section');
                newPage.className = 'page days-page page-policies-page';
                newPage.id = 'page-policies-' + pageCounter;

                // Clone hero banner from initialPage
                const heroClone = initialPage.querySelector('.hero').cloneNode(true);
                const oldWave = heroClone.querySelector('.hero-wave-container');
                if (oldWave) oldWave.remove();
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

                // Insert directly after currentPage
                currentPage.parentNode.insertBefore(newPage, currentPage.nextSibling);

                currentPage = newPage;
                currentDocBody = newBody;
                currentSection = newSec;
            }

            for (let i = 0; i < allItems.length; i++) {
                const item = allItems[i];

                // If this item is a section heading, check if there is room for it AND at least 70px of content
                if (isHeadingOrTitle(item) && currentSection.children.length > 0) {
                    if (currentDocBody.offsetHeight + 70 > MAX_DOC_HEIGHT) {
                        appendNewPolicyPage();
                        currentSection.appendChild(item);
                        continue;
                    }
                }

                currentSection.appendChild(item);

                // Check if currentDocBody has exceeded the printable page limit
                if (currentDocBody.offsetHeight > MAX_DOC_HEIGHT) {
                    // Check if this is a list with multiple <li> items that can be split
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
                            // Part of the list fits on currentPage. Move remaining items to new page
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
                            // NONE of the lis fit on current page: restore ALL lis back into item
                            for (let k = 0; k < allLis.length; k++) {
                                item.appendChild(allLis[k]);
                            }
                            item.remove();
                        }
                    } else {
                        // Whole non-list item cannot fit on current page
                        item.remove();
                    }

                    // Check for orphan heading (if the item above is a heading with no content below it)
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

        function initMitTemplateEffects() {
            // 0. Auto-paginate policies across multiple A4 pages if content overflows
            paginatePolicyPages();

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

            // Handle missing or broken hero images gracefully by showing styled fallback title
            document.querySelectorAll('.hero img').forEach((img) => {
                const handleImgError = function() {
                    const hero = img.closest('.hero');
                    if (!hero || hero.querySelector('.default-day-image')) return;
                    const altText = img.getAttribute('alt') || 'Tour Proposal';
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

            // Dismiss loader once effects, layout, and pagination are ready
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
            document.addEventListener("DOMContentLoaded", initMitTemplateEffects);
        } else {
            initMitTemplateEffects();
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                initMitTemplateEffects();
                hidePageLoader();
            });
        }

        // Safety fallback: ensure loader is always dismissed even on very slow assets
        setTimeout(hidePageLoader, 2500);

        // 3. Client-side PDF Exporter Function (High-Fidelity Page-by-Page)
        async function downloadItineraryPDF(shouldSave = true, onProgress = null) {
            const btn = document.getElementById('btnDownloadPDF');
            if (btn && shouldSave !== false) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Generating PDF...';
            }

            const loader = document.getElementById('itinerary-page-loader');
            const msg = document.getElementById('loaderStatusMsg');
            if (loader && shouldSave !== false) {
                loader.style.display = 'flex';
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
                if (msg) msg.textContent = 'Preparing PDF document...';
            }

            try {
                // Allow styles, fonts, and SVGs to finish layout
                await new Promise(resolve => setTimeout(resolve, shouldSave === false ? 60 : 250));

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
                const isBgRender = (shouldSave === false);

                for (let i = 0; i < pages.length; i++) {
                    if (msg && !isBgRender) msg.textContent = `Rendering page ${i + 1} of ${pages.length}...`;
                    if (typeof onProgress === 'function') {
                        try { onProgress(i + 1, pages.length); } catch (pe) {}
                    }

                    const canvas = await html2canvas(pages[i], {
                        scale: isBgRender ? 1.25 : 1.6,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: '#ffffff',
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

                if (msg && !isBgRender) msg.textContent = 'Saving PDF file...';
                <?php
                $safePdfTitle = preg_replace('/_+/', '_', preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace('&', 'and', cleanItineraryUnicode($pkgTitle))));
                $safePdfTitle = trim($safePdfTitle, '_');
                if (empty($safePdfTitle)) $safePdfTitle = 'Tour_Package';
                ?>
                const fileName = '<?php echo $safePdfTitle; ?>_Itinerary.pdf';
                if (!isBgRender) {
                    pdf.save(fileName);
                }

                const pdfDataUri = pdf.output('datauristring');
                return pdfDataUri;
            } catch (err) {
                console.error('PDF export error:', err);
                if (msg && shouldSave !== false) msg.textContent = 'PDF generation encountered an error.';
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

        // 4. Auto-Download on page load if requested (?download=true or ?pdf=1)
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