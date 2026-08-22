<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$httpStatus = 200;
$status = 'success';
$msg = '';
$suggestions = [];

$packageIdRaw = $_REQUEST['packageId'] ?? 0;
$packageId = is_numeric($packageIdRaw) ? (int)$packageIdRaw : (int)base64_decode($packageIdRaw);
$customQuery = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';
$page = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;

if ($packageId > 0) {
    $query = "SELECT tour_package_title, tour_package_city, tour_package_country, tour_package_theme, tour_package_duration, tour_package_itinerary FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId}";
    
    if ($res = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($res) > 0) {
            $pkg = mysqli_fetch_assoc($res);
            $city = trim($pkg['tour_package_city'] ?? '');
            $country = trim($pkg['tour_package_country'] ?? '');
            $theme = trim($pkg['tour_package_theme'] ?? '');
            $title = trim($pkg['tour_package_title'] ?? '');
            $duration = (int)($pkg['tour_package_duration'] ?? 0);
            $itineraryRaw = $pkg['tour_package_itinerary'] ?? '';

            $itineraryList = [];
            if (!empty($itineraryRaw)) {
                $itineraryList = json_decode($itineraryRaw, true) ?? [];
            }
            $totalDays = max(count($itineraryList), $duration + 1, 1);

            $destination = !empty($city) ? $city : (!empty($country) ? $country : $title);

            if (!function_exists('fetchPexelsPhotosV3')) {
                function fetchPexelsPhotosV3($searchTerm, $perPage = 6, $page = 1) {
                    $apiKey = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : 's8pSS2kcCDM4A59jeh6fNx1XL5eHLBdBcOU0gFgZPpiYOnwthaTI1bat';
                    $url = "https://api.pexels.com/v1/search?query=" . urlencode($searchTerm) . "&orientation=landscape&page=" . (int)$page . "&per_page=" . (int)$perPage;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Authorization: {$apiKey}"
                    ]);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    $photos = [];
                    if ($httpCode === 200 && !empty($response)) {
                        $data = json_decode($response, true);
                        if (isset($data['photos']) && is_array($data['photos'])) {
                            foreach ($data['photos'] as $p) {
                                $photos[] = [
                                    'url' => $p['src']['large2x'] ?? ($p['src']['large'] ?? ($p['src']['original'] ?? '')),
                                    'thumb' => $p['src']['large'] ?? ($p['src']['medium'] ?? ''),
                                    'photographer' => $p['photographer'] ?? 'Pexels Creator',
                                    'alt' => $p['alt'] ?? $searchTerm
                                ];
                            }
                        }
                    }
                    return $photos;
                }
            }

            if (!empty($customQuery)) {
                $searchFilter = $customQuery . ' travel';
                $photos = fetchPexelsPhotosV3($searchFilter, 12, $page);
                if (empty($photos)) {
                    $photos = fetchPexelsPhotosV3($customQuery, 12, $page);
                }
                foreach ($photos as $p) {
                    $suggestions[] = [
                        'day' => 0,
                        'dayTitle' => 'Search Result',
                        'searchTerm' => $customQuery,
                        'url' => $p['url'],
                        'thumb' => $p['thumb'],
                        'photographer' => $p['photographer'],
                        'alt' => $p['alt']
                    ];
                }
            } else {
                for ($d = 1; $d <= $totalDays; $d++) {
                    $dayInfo = $itineraryList[$d - 1] ?? [];
                    $rawDayTitle = $dayInfo['day'] ?? ("Day " . $d);
                    $rawOverview = $dayInfo['overview'] ?? '';

                    $cleanTitle = preg_replace('/^Day\s*\d+\s*:?\s*/i', '', strip_tags($rawDayTitle));
                    $cleanTitle = trim(preg_replace('/[^\w\s]/', '', $cleanTitle));

                    $cleanOverview = trim(preg_replace('/[^\w\s]/', '', strip_tags($rawOverview)));
                    $overviewWords = array_slice(explode(' ', $cleanOverview), 0, 3);
                    $overviewSnippet = implode(' ', array_filter($overviewWords));

                    $searchTerm = $destination;
                    if (!empty($cleanTitle) && stripos($cleanTitle, 'day') === false) {
                        $searchTerm = $destination . ' ' . $cleanTitle;
                    } elseif (!empty($overviewSnippet)) {
                        $searchTerm = $destination . ' ' . $overviewSnippet;
                    }

                    $photos = fetchPexelsPhotosV3($searchTerm, 2, $page);
                    if (empty($photos)) {
                        $photos = fetchPexelsPhotosV3($destination . ' landmark', 2, $page);
                    }

                    foreach ($photos as $p) {
                        $suggestions[] = [
                            'day' => $d,
                            'dayTitle' => !empty($cleanTitle) ? ("Day {$d}: " . $cleanTitle) : ("Day {$d} Itinerary"),
                            'searchTerm' => $searchTerm,
                            'url' => $p['url'],
                            'thumb' => $p['thumb'],
                            'photographer' => $p['photographer'],
                            'alt' => $p['alt']
                        ];
                    }
                }

                if (count($suggestions) < 6) {
                    $extraPhotos = fetchPexelsPhotosV3($destination . ' scenic landscape', 6, $page);
                    foreach ($extraPhotos as $p) {
                        $suggestions[] = [
                            'day' => 0,
                            'dayTitle' => 'General Destination Highlight',
                            'searchTerm' => $destination,
                            'url' => $p['url'],
                            'thumb' => $p['thumb'],
                            'photographer' => $p['photographer'],
                            'alt' => $p['alt']
                        ];
                    }
                }
            }

            $msg = count($suggestions) > 0 ? "Found " . count($suggestions) . " high quality stock photo suggestions" : "No stock photos found for this search term.";
        } else {
            $httpStatus = 404;
            $status = 'error';
            $msg = 'Tour Package not found';
        }
    } else {
        $httpStatus = 500;
        $status = 'error';
        $msg = 'Database query failed';
    }
} else {
    $httpStatus = 400;
    $status = 'error';
    $msg = 'Invalid Package ID';
}

http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg' => $msg,
    'suggestions' => $suggestions,
    'total' => count($suggestions),
    'page' => $page,
    'hasMore' => count($suggestions) >= 6
], JSON_UNESCAPED_SLASHES);
exit;
