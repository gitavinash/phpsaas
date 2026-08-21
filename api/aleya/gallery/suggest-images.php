<?php

require_once LOGIN_CHECK;

$httpStatus = 200;
$status = 'success';
$msg = '';
$suggestions = [];

$packageIdRaw = $_REQUEST['packageId'] ?? 0;
$packageId = is_numeric($packageIdRaw) ? (int)$packageIdRaw : (int)base64_decode($packageIdRaw);
$customQuery = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';
$page = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;

if ($packageId > 0) {
    $query = "SELECT tour_package_title, tour_package_city, tour_package_country, tour_package_theme, tour_package_duration, tour_package_itinerary FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = " . SITE_ID;
    
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

            // Fetch high-quality landscape photos via Pexels API with pagination
            function fetchPexelsPhotos($searchTerm, $perPage = 6, $page = 1) {
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

            if (!empty($customQuery)) {
                $searchFilter = $customQuery . ' travel';
                $photos = fetchPexelsPhotos($searchFilter, 12, $page);
                if (empty($photos)) {
                    $photos = fetchPexelsPhotos($customQuery, 12, $page);
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
                // Generate targeted day-wise suggested images based on City + Day Title / Itinerary
                for ($d = 1; $d <= $totalDays; $d++) {
                    $dayInfo = $itineraryList[$d - 1] ?? [];
                    $rawDayTitle = $dayInfo['day'] ?? ("Day " . $d);
                    $rawOverview = $dayInfo['overview'] ?? '';

                    // Clean day title
                    $cleanTitle = preg_replace('/^Day\s*\d+\s*:?\s*/i', '', strip_tags($rawDayTitle));
                    $cleanTitle = trim(preg_replace('/[^\w\s]/', '', $cleanTitle));

                    // Extract snippet from day overview if title is basic
                    $cleanOverview = trim(preg_replace('/[^\w\s]/', '', strip_tags($rawOverview)));
                    $overviewWords = array_slice(explode(' ', $cleanOverview), 0, 3);
                    $overviewSnippet = implode(' ', array_filter($overviewWords));

                    // Smart Day Search Query formulation: Destination + Landmark / Day Title
                    $queryTerms = [$destination];
                    if (!empty($cleanTitle) && strtolower($cleanTitle) !== "day {$d}") {
                        $queryTerms[] = $cleanTitle;
                    } else if (!empty($overviewSnippet)) {
                        $queryTerms[] = $overviewSnippet;
                    } else if (!empty($theme)) {
                        $queryTerms[] = $theme;
                    }

                    $daySearchQuery = implode(' ', array_unique($queryTerms));
                    $photos = fetchPexelsPhotos($daySearchQuery, 2, $page);
                    
                    // Fallback to City + travel landscape if day specific search returned no photos
                    if (empty($photos)) {
                        $photos = fetchPexelsPhotos($destination . ' travel landscape', 2, $page);
                    }

                    foreach ($photos as $p) {
                        $suggestions[] = [
                            'day' => $d,
                            'dayTitle' => $rawDayTitle,
                            'searchTerm' => $daySearchQuery,
                            'url' => $p['url'],
                            'thumb' => $p['thumb'],
                            'photographer' => $p['photographer'],
                            'alt' => $p['alt']
                        ];
                    }
                }
            }

            $msg = 'Day-wise stock image suggestions fetched successfully.';
        } else {
            $httpStatus = 404;
            $status = 'error';
            $msg = 'Tour package not found';
        }
    } else {
        $httpStatus = 500;
        $status = 'error';
        $msg = 'Database error: ' . mysqli_error($connection);
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
    'page' => $page,
    'hasMore' => !empty($suggestions),
    'suggestions' => $suggestions
], JSON_UNESCAPED_SLASHES);
