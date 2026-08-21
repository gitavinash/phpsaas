<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';
$galleryImages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageIdRaw = $_POST['packageId'] ?? 0;
    $packageId = is_numeric($packageIdRaw) ? (int)$packageIdRaw : (int)base64_decode($packageIdRaw);
    $galleryId = isset($_POST['galleryId']) ? (int)$_POST['galleryId'] : 0;
    $imagePath = isset($_POST['imagePath']) ? trim($_POST['imagePath']) : '';
    $assignedDay = isset($_POST['day']) ? (int)$_POST['day'] : 0;
    $autoAssignMode = isset($_POST['autoAssign']) && (int)$_POST['autoAssign'] === 1;
    $rawItems = $_POST['items'] ?? '';

    if ($packageId > 0 || $galleryId > 0) {
        $whereClause = "";
        if ($galleryId > 0) {
            $whereClause = "gallery_id = '{$galleryId}' AND gallery_site_id = " . SITE_ID;
        } else {
            $whereClause = "gallery_category = '{$packageId}' AND gallery_type = 'tour_package' AND gallery_site_id = " . SITE_ID;
        }

        $gId = $galleryId;
        $existingList = [];
        $currentFeaturedImg = '';

        $query = "SELECT gallery_id, gallery_image, gallery_image_list FROM " . TABLE_PREFIX . "gallery WHERE " . $whereClause . " LIMIT 1";
        if ($res = mysqli_query($connection, $query)) {
            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $gId = (int)$row['gallery_id'];
                $currentFeaturedImg = trim($row['gallery_image'] ?? '');
                $existingList = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];
            }
        }

        // Helper function to query Pexels for top photo matching specific destination/landmark search term
        function fetchTopPexelsPhotoForTerm($searchTerm) {
            $apiKey = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : 's8pSS2kcCDM4A59jeh6fNx1XL5eHLBdBcOU0gFgZPpiYOnwthaTI1bat';
            $url = "https://api.pexels.com/v1/search?query=" . urlencode($searchTerm) . "&orientation=landscape&per_page=1";

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

            if ($httpCode === 200 && !empty($response)) {
                $data = json_decode($response, true);
                if (isset($data['photos'][0])) {
                    $p = $data['photos'][0];
                    return $p['src']['large2x'] ?? ($p['src']['large'] ?? ($p['src']['original'] ?? ''));
                }
            }
            return '';
        }

        $itemsToAdd = [];

        if ($autoAssignMode) {
            // Intelligent Day-Wise Destination Image Search & Auto Pick
            $pkgQuery = "SELECT tour_package_title, tour_package_city, tour_package_country, tour_package_theme, tour_package_duration, tour_package_itinerary FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = " . SITE_ID;
            if ($pkgRes = mysqli_query($connection, $pkgQuery)) {
                if (mysqli_num_rows($pkgRes) > 0) {
                    $pkgData = mysqli_fetch_assoc($pkgRes);
                    $pkgCity = trim($pkgData['tour_package_city'] ?? '');
                    $pkgCountry = trim($pkgData['tour_package_country'] ?? '');
                    $pkgTitle = trim($pkgData['tour_package_title'] ?? '');
                    $pkgTheme = trim($pkgData['tour_package_theme'] ?? '');
                    $pkgDuration = (int)($pkgData['tour_package_duration'] ?? 0);
                    $itineraryRaw = $pkgData['tour_package_itinerary'] ?? '';

                    $itineraryList = [];
                    if (!empty($itineraryRaw)) {
                        $itineraryList = json_decode($itineraryRaw, true) ?? [];
                    }
                    $totalDays = max(count($itineraryList), $pkgDuration + 1, 1);
                    $baseDestination = !empty($pkgCity) ? $pkgCity : (!empty($pkgCountry) ? $pkgCountry : $pkgTitle);

                    for ($d = 1; $d <= $totalDays; $d++) {
                        $dayInfo = $itineraryList[$d - 1] ?? [];
                        $rawDayText = ($dayInfo['day'] ?? '') . ' ' . ($dayInfo['title'] ?? '') . ' ' . ($dayInfo['overview'] ?? '');

                        // Clean day text to extract landmark or city name
                        $cleanText = preg_replace('/^Day\s*\d+\s*:?\s*/i', '', strip_tags($rawDayText));
                        $cleanText = trim(preg_replace('/[^\w\s]/', '', $cleanText));

                        // Extract clean words
                        $words = array_filter(explode(' ', $cleanText));
                        $dayLandmark = implode(' ', array_slice($words, 0, 3));

                        // Build targeted search query for this specific day
                        $daySearchQuery = "";
                        if (!empty($dayLandmark)) {
                            // Check if day landmark already includes city or stands alone
                            if (!empty($pkgCity) && stripos($dayLandmark, $pkgCity) === false) {
                                $daySearchQuery = $pkgCity . ' ' . $dayLandmark . ' travel';
                            } else {
                                $daySearchQuery = $dayLandmark . ' travel landscape';
                            }
                        } else {
                            $daySearchQuery = $baseDestination . ' Day ' . $d . ' travel';
                        }

                        $photoUrl = fetchTopPexelsPhotoForTerm($daySearchQuery);

                        // Fallback search if title specific search returned nothing
                        if (empty($photoUrl)) {
                            $photoUrl = fetchTopPexelsPhotoForTerm($baseDestination . ' travel landscape');
                        }

                        if (!empty($photoUrl)) {
                            $itemsToAdd[] = [
                                'path' => $photoUrl,
                                'day' => $d,
                                'name' => 'Stock Image - Day ' . $d
                            ];
                        }
                    }
                }
            }
        } else if (!empty($rawItems)) {
            $decoded = json_decode($rawItems, true);
            if (is_array($decoded)) {
                $itemsToAdd = $decoded;
            }
        } else if (!empty($imagePath)) {
            $itemsToAdd[] = [
                'path' => $imagePath,
                'name' => 'Stock Photo',
                'day' => $assignedDay > 0 ? $assignedDay : 0
            ];
        }

        if (!empty($itemsToAdd)) {
            // Process each item to add & enforce 1 image per day rule
            foreach ($itemsToAdd as $item) {
                $p = trim($item['path'] ?? '');
                $d = isset($item['day']) ? (int)$item['day'] : 0;

                if (!empty($p)) {
                    // Enforce max 1 image per day
                    if ($d > 0) {
                        foreach ($existingList as &$ex) {
                            if (isset($ex['day']) && (int)$ex['day'] === $d) {
                                $ex['day'] = 0;
                            }
                        }
                        unset($ex);
                    }

                    // Check if already in list
                    $exists = false;
                    foreach ($existingList as &$ex) {
                        if (($ex['path'] ?? '') === $p) {
                            $ex['day'] = $d;
                            $exists = true;
                            break;
                        }
                    }
                    unset($ex);

                    if (!$exists) {
                        $existingList[] = [
                            'path' => $p,
                            'name' => $item['name'] ?? 'Stock Photo',
                            'day' => $d
                        ];
                    }
                }
            }

            // Sort images: assigned days (1, 2, 3...) first, then unassigned
            usort($existingList, function ($a, $b) {
                $dayA = isset($a['day']) && (int)$a['day'] > 0 ? (int)$a['day'] : 999999;
                $dayB = isset($b['day']) && (int)$b['day'] > 0 ? (int)$b['day'] : 999999;
                if ($dayA === $dayB) {
                    return 0;
                }
                return ($dayA < $dayB) ? -1 : 1;
            });

            $jsonEncoded = mysqli_real_escape_string($connection, json_encode($existingList, JSON_UNESCAPED_SLASHES));
            
            // Determine featured image: if not set yet, automatically set Day 1 / first photo as featured
            $newFeaturedImg = $currentFeaturedImg;
            if (empty($newFeaturedImg) || $newFeaturedImg === '0') {
                $newFeaturedImg = $existingList[0]['path'] ?? '';
            }
            $escapedFeaturedImg = mysqli_real_escape_string($connection, $newFeaturedImg);

            if ($gId > 0) {
                $updateQuery = "UPDATE " . TABLE_PREFIX . "gallery SET gallery_image = '{$escapedFeaturedImg}', gallery_thumbnail = '{$escapedFeaturedImg}', gallery_image_list = '{$jsonEncoded}' WHERE gallery_id = '{$gId}' AND gallery_site_id = " . SITE_ID;
                $execute = mysqli_query($connection, $updateQuery);
            } else {
                // Create gallery record if it doesn't exist yet
                $insertQuery = "INSERT INTO " . TABLE_PREFIX . "gallery 
                    (gallery_image, gallery_thumbnail, gallery_title, gallery_category, gallery_site_id, gallery_type, gallery_image_list, gallery_owner_id, gallery_status)
                    VALUES
                    ('{$escapedFeaturedImg}', '{$escapedFeaturedImg}', 'Tour Package Gallery', '{$packageId}', " . SITE_ID . ", 'tour_package', '{$jsonEncoded}', '" . USER_ID . "', 1)";
                $execute = mysqli_query($connection, $insertQuery);
                $gId = mysqli_insert_id($connection);
            }

            // Also update tour_package_image in tbl_tour_package if empty or modified
            if (!empty($escapedFeaturedImg)) {
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "tour_package SET tour_package_image = '{$escapedFeaturedImg}' WHERE tour_package_id = '{$packageId}' AND (tour_package_image = '' OR tour_package_image IS NULL OR tour_package_image = '0') AND tour_package_site_id = " . SITE_ID);
            }

            if ($execute) {
                $httpStatus = 200;
                $status = 'success';
                $msg = 'Day-wise destination photos automatically searched & assigned for all days!';
                $galleryImages = $existingList;
            } else {
                $httpStatus = 200;
                $status = 'error';
                $msg = 'Database error: ' . mysqli_error($connection);
            }
        } else {
            $msg = 'No items to add';
        }
    } else {
        $msg = 'Invalid Package ID';
    }
}

$result = [
    'status' => $status,
    'msg' => $msg,
    'galleryImages' => $galleryImages
];

http_response_code($httpStatus);
echo json_encode($result, JSON_UNESCAPED_SLASHES);
