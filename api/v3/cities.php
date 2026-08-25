<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 2);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$rawPkgId = $_GET['package_id'] ?? ($_GET['id'] ?? 0);
$packageId = 0;
if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
    $packageId = (int)$rawPkgId;
} else if (!empty($rawPkgId)) {
    $decoded = base64_decode((string)$rawPkgId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $packageId = (int)$decoded;
    }
}

$stateParam   = trim($_GET['state'] ?? '');
$countryParam = trim($_GET['country'] ?? '');
$searchParam  = trim($_GET['search'] ?? ($_GET['q'] ?? ''));

// Determine Package State if package_id is provided and no state was explicitly passed
$pkgState = $stateParam;
if ($packageId > 0 && empty($pkgState)) {
    $pkgSql = "SELECT tour_package_state FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId} LIMIT 1";
    if ($pkgRes = mysqli_query($connection, $pkgSql)) {
        if ($pkgRow = mysqli_fetch_assoc($pkgRes)) {
            $pkgState = trim($pkgRow['tour_package_state'] ?? '');
        }
    }
}

$escapedSearch  = mysqli_real_escape_string($connection, $searchParam);
$escapedState   = mysqli_real_escape_string($connection, $pkgState);
$escapedCountry = mysqli_real_escape_string($connection, $countryParam);

// Helper: Title case formatting
function formatCityTitle($str) {
    if (empty($str)) return '';
    return mb_convert_case(trim($str), MB_CASE_TITLE, "UTF-8");
}

// Build Query on isw_city joined with isw_state and isw_country
$whereClauses = ["c.city_name IS NOT NULL", "c.city_name <> ''"];
if (!empty($escapedSearch)) {
    $whereClauses[] = "(c.city_name LIKE '%{$escapedSearch}%' OR s.state_name LIKE '%{$escapedSearch}%' OR co.country_name LIKE '%{$escapedSearch}%')";
}
if (!empty($escapedCountry)) {
    $whereClauses[] = "(co.country_name = '{$escapedCountry}' OR co.country_id = '{$escapedCountry}')";
}

$whereSql = implode(' AND ', $whereClauses);

// Priority Ordering: 1. Current State first, 2. Name match position, 3. Alphabetical
$stateOrderSql = !empty($escapedState) ? "CASE WHEN UPPER(TRIM(s.state_name)) = UPPER('{$escapedState}') THEN 0 ELSE 1 END," : "";
$matchOrderSql = !empty($escapedSearch) ? "CASE WHEN c.city_name LIKE '{$escapedSearch}%' THEN 0 ELSE 1 END," : "";

$query = "
    SELECT 
        c.city_id,
        TRIM(c.city_name) AS city_name,
        TRIM(s.state_name) AS state_name,
        TRIM(co.country_name) AS country_name
    FROM isw_city c
    LEFT JOIN isw_state s ON c.city_state_id = s.state_id
    LEFT JOIN isw_country co ON s.state_country_id = co.country_id
    WHERE {$whereSql}
    ORDER BY {$stateOrderSql} {$matchOrderSql} c.city_name ASC
    LIMIT 50
";

// Common destination city pincode dictionary for fast accurate fallbacks
$knownPincodes = [
    'delhi' => '110001', 'new delhi' => '110001', 'mumbai' => '400001', 'bangalore' => '560001',
    'bengaluru' => '560001', 'kolkata' => '700001', 'chennai' => '600001', 'hyderabad' => '500001',
    'pune' => '411001', 'ahmedabad' => '380001', 'jaipur' => '302001', 'udaipur' => '313001',
    'jodhpur' => '342001', 'jaisalmer' => '345001', 'bikaner' => '334001', 'pushkar' => '305022',
    'ajmer' => '305001', 'mount abu' => '307501', 'shimla' => '171001', 'manali' => '175131',
    'kullu' => '175101', 'dharamshala' => '176215', 'mcleodganj' => '176219', 'dalhousie' => '176304',
    'kasauli' => '173204', 'jibhi' => '175119', 'spiti' => '172114', 'srinagar' => '190001',
    'gulmarg' => '193403', 'pahalgam' => '192126', 'leh' => '194101', 'ladakh' => '194101',
    'agra' => '282001', 'varanasi' => '221001', 'lucknow' => '226001', 'kanpur' => '208001',
    'amritsar' => '143001', 'chandigarh' => '160001', 'ludhiana' => '141001', 'jalandhar' => '144001',
    'dehradun' => '248001', 'haridwar' => '249401', 'rishikesh' => '249201', 'nainital' => '263001',
    'mussoorie' => '248179', 'ranikhet' => '263645', 'jim corbett' => '244715', 'almora' => '263601',
    'kausani' => '263639', 'auli' => '246443', 'goa' => '403001', 'panaji' => '403001',
    'arambol' => '403524', 'calangute' => '403516', 'candolim' => '403515', 'baga' => '403516',
    'margao' => '403601', 'kochi' => '682001', 'cochin' => '682001', 'munnar' => '685612',
    'alleppey' => '688001', 'alappuzha' => '688001', 'thekkady' => '685509', 'wayanad' => '673121',
    'kovalam' => '695527', 'varkala' => '695141', 'trivandrum' => '695001', 'thiruvananthapuram' => '695001',
    'ooty' => '643001', 'kodaikanal' => '624101', 'coimbatore' => '641001', 'madurai' => '625001',
    'rameswaram' => '623526', 'kanyakumari' => '629702', 'mysore' => '570001', 'mysuru' => '570001',
    'coorg' => '571201', 'hampi' => '583239', 'gokarna' => '581326', 'darjeeling' => '734101',
    'gangtok' => '737101', 'kalimpong' => '734301', 'pelling' => '737113', 'shillong' => '793001',
    'guwahati' => '781001', 'kaziranga' => '785609', 'port blair' => '744101', 'havelock' => '744211',
    'puri' => '752001', 'bhubaneswar' => '751001', 'patna' => '800001', 'gaya' => '823001',
    'bodhgaya' => '824231', 'ranchi' => '834001', 'jamshedpur' => '831001', 'indore' => '452001',
    'bhopal' => '462001', 'gwalior' => '474001', 'khajuraho' => '471606', 'ujjain' => '456001',
    'surat' => '395001', 'vadodara' => '390001', 'rajkot' => '360001', 'bhuj' => '370001',
    'rann of kutch' => '370510', 'somnath' => '362268', 'dwarka' => '361335'
];

$results = [];
$stateCities = [];
$otherCities = [];
$seen = [];

// If search term is present, also fetch matching States
if (!empty($escapedSearch)) {
    $stateQuery = "
        SELECT 
            s.state_id,
            TRIM(s.state_name) AS state_name,
            TRIM(co.country_name) AS country_name
        FROM isw_state s
        LEFT JOIN isw_country co ON s.state_country_id = co.country_id
        WHERE s.state_name LIKE '%{$escapedSearch}%'
        ORDER BY s.state_name ASC
        LIMIT 10
    ";
    if ($sRes = mysqli_query($connection, $stateQuery)) {
        while ($sRow = mysqli_fetch_assoc($sRes)) {
            $stName = formatCityTitle($sRow['state_name']);
            $coName = formatCityTitle($sRow['country_name'] ?? 'India');
            if (empty($stName)) continue;
            $sKey = 'state_' . strtolower($stName);
            if (!in_array($sKey, $seen, true)) {
                $seen[] = $sKey;
                $results[] = [
                    'id'      => $stName,
                    'text'    => $stName . ' (State, ' . $coName . ')',
                    'city'    => $stName,
                    'state'   => $stName,
                    'country' => $coName,
                    'pincode' => '',
                    'type'    => 'state'
                ];
            }
        }
    }
}

if ($res = mysqli_query($connection, $query)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cName = formatCityTitle($row['city_name']);
        $sName = formatCityTitle($row['state_name'] ?? '');
        $coName = formatCityTitle($row['country_name'] ?? 'India');
        if (empty($cName)) continue;

        $cKey = strtolower($cName . '_' . $sName . '_' . $coName);
        if (in_array($cKey, $seen, true)) {
            continue;
        }
        $seen[] = $cKey;

        // Determine Pincode
        $lowerCity = strtolower(trim($cName));
        $pincode = '';
        if (isset($knownPincodes[$lowerCity])) {
            $pincode = $knownPincodes[$lowerCity];
        } else {
            foreach ($knownPincodes as $k => $p) {
                if (strpos($lowerCity, $k) !== false || strpos($k, $lowerCity) !== false) {
                    $pincode = $p;
                    break;
                }
            }
        }

        // Label: "Manali (Himachal Pradesh, India)" or "Jaipur (Rajasthan, India)"
        $metaParts = array_filter([$sName, $coName]);
        $labelText = $cName . (!empty($metaParts) ? ' (' . implode(', ', $metaParts) . ')' : '');

        $cityItem = [
            'id'      => $cName,
            'text'    => $labelText,
            'city'    => $cName,
            'state'   => $sName,
            'country' => $coName,
            'pincode' => $pincode
        ];

        $results[] = $cityItem;

        if (!empty($pkgState) && strtoupper(trim($sName)) === strtoupper(trim($pkgState))) {
            $stateCities[] = $cName;
        } else {
            $otherCities[] = $cName;
        }
    }
}

echo json_encode([
    'status'       => 'success',
    'state'        => $pkgState,
    'query'        => $searchParam,
    'total'        => count($results),
    'results'      => $results,
    'state_cities' => array_values(array_unique($stateCities)),
    'other_cities' => array_values(array_unique($otherCities)),
    'cities'       => array_values(array_unique(array_map(function($i){ return $i['city']; }, $results)))
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
