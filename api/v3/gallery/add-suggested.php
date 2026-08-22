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
            $whereClause = "gallery_id = '{$galleryId}' AND gallery_site_id = {$siteId}";
        } else {
            $whereClause = "gallery_category = '{$packageId}' AND gallery_type = 'tour_package' AND gallery_site_id = {$siteId}";
        }

        $gId = $galleryId;
        $existingList = [];
        $currentFeaturedImg = '';

        $query = "SELECT gallery_id, gallery_image, gallery_image_list FROM {$tblPrefix}gallery WHERE {$whereClause} LIMIT 1";
        if ($res = mysqli_query($connection, $query)) {
            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $gId = (int)$row['gallery_id'];
                $currentFeaturedImg = trim($row['gallery_image'] ?? '');
                $existingList = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];
            }
        }

        if (!function_exists('fetchTopPexelsPhotoForTermV3')) {
            function fetchTopPexelsPhotoForTermV3($searchTerm) {
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
        }

        $itemsToAdd = [];

        if ($autoAssignMode) {
            $pkgQuery = "SELECT tour_package_title, tour_package_city, tour_package_country, tour_package_theme, tour_package_duration, tour_package_itinerary FROM {$tblPrefix}tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId}";
            if ($pkgRes = mysqli_query($connection, $pkgQuery)) {
                if (mysqli_num_rows($pkgRes) > 0) {
                    $pkgData = mysqli_fetch_assoc($pkgRes);
                    $pkgCity = trim($pkgData['tour_package_city'] ?? '');
                    $pkgCountry = trim($pkgData['tour_package_country'] ?? '');
                    $pkgTitle = trim($pkgData['tour_package_title'] ?? '');
                    $pkgTheme = trim($pkgData['tour_package_theme'] ?? '');
                    $pkgDuration = (int)$pkgData['tour_package_duration'] ?? 0;
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

                        $cleanText = preg_replace('/^Day\s*\d+\s*:?\s*/i', '', strip_tags($rawDayText));
                        $cleanText = trim(preg_replace('/[^\w\s]/', '', $cleanText));

                        $words = array_filter(explode(' ', $cleanText));
                        $dayLandmark = implode(' ', array_slice($words, 0, 3));

                        $searchTerm = $baseDestination;
                        if (!empty($dayLandmark)) {
                            $searchTerm = $baseDestination . ' ' . $dayLandmark;
                        }

                        $remoteUrl = fetchTopPexelsPhotoForTermV3($searchTerm);
                        if (empty($remoteUrl)) {
                            $remoteUrl = fetchTopPexelsPhotoForTermV3($baseDestination . ' landmark');
                        }

                        if (!empty($remoteUrl)) {
                            $itemsToAdd[] = [
                                'url' => $remoteUrl,
                                'day' => $d,
                                'name' => "Day {$d} - " . ($dayLandmark ?: $baseDestination)
                            ];
                        }
                    }
                }
            }
        } elseif (!empty($rawItems)) {
            $parsed = is_array($rawItems) ? $rawItems : (json_decode($rawItems, true) ?? []);
            foreach ($parsed as $it) {
                if (!empty($it['url'])) {
                    $itemsToAdd[] = [
                        'url' => trim($it['url']),
                        'day' => isset($it['day']) ? (int)$it['day'] : 0,
                        'name' => isset($it['name']) ? trim($it['name']) : 'Stock Photo'
                    ];
                }
            }
        } elseif (!empty($imagePath)) {
            $itemsToAdd[] = [
                'url' => $imagePath,
                'day' => $assignedDay,
                'name' => 'Stock Photo'
            ];
        }

        if (count($itemsToAdd) === 0) {
            $httpStatus = 400;
            $msg = 'No images provided for assignment';
        } else {
            $uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $addedImages = [];

            foreach ($itemsToAdd as $item) {
                $remoteUrl = $item['url'];
                $assignDay = (int)$item['day'];
                $imageName = $item['name'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $remoteUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 12);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $imageData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && !empty($imageData)) {
                    $fileExtension = 'jpg';
                    $cleanPackageSlug = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower((string)$packageId));
                    $filename = "pexels_" . $cleanPackageSlug . "_d" . $assignDay . "_" . time() . "." . $fileExtension;

                    $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
                    $cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $filename));
                    $uploadFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
                    $uploadMediaPath = str_replace(ROOT, '', $uploadFile);
                    $uploadMediaLink = '/attachment' . str_replace('.' . $fileExtension, '', $uploadMediaPath) . '/';
                    $uploadFileName = ucwords(preg_replace('/[_]+/', ' ', str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));

                    if (file_put_contents($uploadFile, $imageData)) {
                        if ($assignDay > 0) {
                            foreach ($existingList as &$ex) {
                                if (isset($ex['day']) && (int)$ex['day'] === $assignDay) {
                                    $ex['day'] = 0;
                                }
                            }
                            unset($ex);
                        }

                        $existingList[] = [
                            'path' => $uploadMediaPath,
                            'name' => (!empty($imageName) && $imageName !== 'Stock Photo') ? $imageName : $uploadFileName,
                            'day'  => $assignDay,
                            'link' => $uploadMediaLink
                        ];

                        $addedImages[] = $uploadMediaPath;

                        if (empty($currentFeaturedImg) && $assignDay === 1) {
                            $currentFeaturedImg = $uploadMediaPath;
                        }
                    }
                }
            }

            if (empty($currentFeaturedImg) && count($existingList) > 0) {
                $currentFeaturedImg = $existingList[0]['path'] ?? '';
            }

            // Sort: assigned days first
            usort($existingList, function ($a, $b) {
                $dayA = isset($a['day']) && (int)$a['day'] > 0 ? (int)$a['day'] : 999999;
                $dayB = isset($b['day']) && (int)$b['day'] > 0 ? (int)$b['day'] : 999999;
                if ($dayA === $dayB) return 0;
                return ($dayA < $dayB) ? -1 : 1;
            });

            $jsonEncoded = mysqli_real_escape_string($connection, json_encode($existingList, JSON_UNESCAPED_SLASHES));
            $featuredEscaped = mysqli_real_escape_string($connection, $currentFeaturedImg);

            if ($gId > 0) {
                $updateQuery = "UPDATE {$tblPrefix}gallery SET gallery_image_list = '{$jsonEncoded}', gallery_image = '{$featuredEscaped}', gallery_status = 1 WHERE gallery_id = '{$gId}' AND gallery_site_id = {$siteId}";
                $dbSuccess = mysqli_query($connection, $updateQuery);
            } else {
                $insertQuery = "INSERT INTO {$tblPrefix}gallery (gallery_title, gallery_category, gallery_type, gallery_image, gallery_image_list, gallery_site_id, gallery_status, gallery_created_at) VALUES ('Tour Package Gallery', '{$packageId}', 'tour_package', '{$featuredEscaped}', '{$jsonEncoded}', {$siteId}, 1, NOW())";
                $dbSuccess = mysqli_query($connection, $insertQuery);
                if ($dbSuccess) {
                    $gId = mysqli_insert_id($connection);
                }
            }

            if ($dbSuccess) {
                $httpStatus = 200;
                $status = 'success';
                $msg = count($addedImages) > 1 ? "Added & assigned " . count($addedImages) . " photos successfully!" : "Photo added & assigned successfully!";
                $galleryImages = $existingList;
            } else {
                $httpStatus = 500;
                $status = 'error';
                $msg = 'Database error saving gallery: ' . mysqli_error($connection);
            }
        }
    } else {
        $httpStatus = 400;
        $msg = 'Invalid Package ID';
    }
}

http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg' => $msg,
    'images' => $galleryImages
], JSON_UNESCAPED_SLASHES);
exit;
