<?php

require_once LOGIN_CHECK;

function fetchRemoteContent($url, $options = [])
{
    $ch = curl_init($url);
    $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => $options['userAgent'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/137.0 Safari/537.36',
        CURLOPT_HTTPHEADER => $options['headers'] ?? [],
        CURLOPT_NOPROGRESS => false,
        CURLOPT_PROGRESSFUNCTION => function ($resource, $downloadSize, $downloaded) {
            return ($downloadSize > 5242880 || $downloaded > 5242880) ? 1 : 0;
        }
    ];

    if (array_key_exists('postData', $options)) {
        $curlOptions[CURLOPT_POST] = true;
        $curlOptions[CURLOPT_POSTFIELDS] = $options['postData'];
    }

    curl_setopt_array($ch, $curlOptions);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($content === false || $httpCode < 200 || $httpCode >= 400) {
        return '';
    }

    return $content;
}

function decodeEscapedUrl($url)
{
    for ($i = 0; $i < 2; $i++) {
        $decoded = json_decode('"' . $url . '"');
        if (!is_string($decoded)) {
            break;
        }
        $url = $decoded;
    }

    return html_entity_decode(str_replace(['\\/', '\\u0026', '\\u0025'], ['/', '&', '%'], $url), ENT_QUOTES, 'UTF-8');
}

function getInstagramVideoSource($url)
{
    if (!preg_match('~instagram\.com/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)~i', $url, $matches)) {
        return '';
    }

    $embedUrl = 'https://www.instagram.com/reel/' . $matches[1] . '/embed/captioned/';
    $html = fetchRemoteContent($embedUrl, [
        'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Instagram 333.0.0.0.0'
    ]);

    if ($html !== '') {
        if (preg_match('/"video_url"\s*:\s*"((?:\\\\.|[^"])*)"/i', $html, $videoMatch)) {
            return decodeEscapedUrl($videoMatch[1]);
        }

        $marker = 'video_url\\":\\"';
        $start = strpos($html, $marker);
        if ($start !== false) {
            $start += strlen($marker);
            $end = strpos($html, '\\"', $start);
            if ($end !== false) {
                return decodeEscapedUrl(substr($html, $start, $end - $start));
            }
        }
    }

    return '';
}

function getYoutubeVideoSource($url)
{
    if (!preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $matches)) {
        return '';
    }

    $videoId = $matches[1];
    $pageUrl = 'https://www.youtube.com/watch?v=' . $videoId;
    $html = fetchRemoteContent($pageUrl);

    if (
        $html === ''
        || !preg_match('/"INNERTUBE_API_KEY":"([^"]+)"/', $html, $apiKeyMatch)
        || !preg_match('/"INNERTUBE_CLIENT_VERSION":"([^"]+)"/', $html, $clientVersionMatch)
    ) {
        return '';
    }

    $payload = json_encode([
        'context' => [
            'client' => [
                'clientName' => 'ANDROID',
                'clientVersion' => '20.10.38',
                'androidSdkVersion' => 34,
                'hl' => 'en',
                'gl' => 'IN'
            ]
        ],
        'videoId' => $videoId,
        'contentCheckOk' => true,
        'racyCheckOk' => true
    ]);

    $playerResponse = fetchRemoteContent(
        'https://www.youtube.com/youtubei/v1/player?key=' . rawurlencode($apiKeyMatch[1]),
        [
            'userAgent' => 'com.google.android.youtube/20.10.38 (Linux; U; Android 14)',
            'headers' => ['Content-Type: application/json'],
            'postData' => $payload
        ]
    );
    $playerData = json_decode($playerResponse, true);

    if (($playerData['playabilityStatus']['status'] ?? '') !== 'OK') {
        return '';
    }

    foreach ($playerData['streamingData']['formats'] ?? [] as $format) {
        if (
            isset($format['url'])
            && strpos($format['mimeType'] ?? '', 'video/mp4') === 0
            && strpos($format['mimeType'] ?? '', 'mp4a') !== false
        ) {
            return $format['url'];
        }
    }

    return '';
}

function getRemoteVideoSource($url)
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));

    if (strpos($host, 'instagram.com') !== false) {
        return getInstagramVideoSource($url);
    }

    if (strpos($host, 'youtube.com') !== false || $host === 'youtu.be') {
        return getYoutubeVideoSource($url);
    }

    $html = fetchRemoteContent($url);
    if ($html === '') {
        return '';
    }

    $patterns = [
        '/<meta[^>]+property=["\']og:video(?::url|:secure_url)?["\'][^>]+content=["\']([^"\']+)["\']/i',
        '/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:video(?::url|:secure_url)?["\']/i',
        '/"video_url"\s*:\s*"((?:\\\\.|[^"])*)"/i'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            return decodeEscapedUrl($matches[1]);
        }
    }

    return '';
}

function downloadSocialProofVideo($videoUrl)
{
    $videoUrl = trim($videoUrl);
    $scheme = strtolower((string) parse_url($videoUrl, PHP_URL_SCHEME));

    if (!filter_var($videoUrl, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
        return false;
    }

    $uploadDir = ROOT . '/upload/' . SITE_DOMAIN . '/social_proof/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        return false;
    }

    $sourceUrl = $videoUrl;
    $sourcePath = (string) parse_url($videoUrl, PHP_URL_PATH);
    $sourceExtension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

    if (!in_array($sourceExtension, ['mp4', 'webm', 'mov', 'm4v'], true)) {
        $remoteVideoSource = getRemoteVideoSource($videoUrl);
        if ($remoteVideoSource !== '') {
            $sourceUrl = $remoteVideoSource;
            $sourcePath = (string) parse_url($sourceUrl, PHP_URL_PATH);
            $sourceExtension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        }
    }

    $tempFile = tempnam($uploadDir, 'video_');
    if ($tempFile === false) {
        return false;
    }

    $fileHandle = fopen($tempFile, 'wb');
    if ($fileHandle === false) {
        @unlink($tempFile);
        return false;
    }

    $ch = curl_init($sourceUrl);
    $sourceHost = strtolower((string) parse_url($sourceUrl, PHP_URL_HOST));
    $requestHeaders = [];
    if (strpos($sourceHost, 'cdninstagram.com') !== false || strpos($sourceHost, 'fbcdn.net') !== false) {
        $requestHeaders[] = 'Referer: https://www.instagram.com/';
    } elseif (strpos($sourceHost, 'googlevideo.com') !== false) {
        $requestHeaders[] = 'Referer: https://www.youtube.com/';
    }

    curl_setopt_array($ch, [
        CURLOPT_FILE => $fileHandle,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/137.0 Safari/537.36',
        CURLOPT_HTTPHEADER => $requestHeaders,
        CURLOPT_NOPROGRESS => false,
        CURLOPT_PROGRESSFUNCTION => function ($resource, $downloadSize, $downloaded) {
            return ($downloadSize > 209715200 || $downloaded > 209715200) ? 1 : 0;
        }
    ]);

    $downloaded = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = strtolower((string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
    curl_close($ch);
    fclose($fileHandle);

    $fileSize = file_exists($tempFile) ? filesize($tempFile) : 0;
    $detectedType = function_exists('mime_content_type') ? strtolower((string) mime_content_type($tempFile)) : '';
    $isVideo = strpos($contentType, 'video/') === 0 || strpos($detectedType, 'video/') === 0;

    if ($downloaded === false || $httpCode < 200 || $httpCode >= 400 || $fileSize === 0 || !$isVideo) {
        @unlink($tempFile);
        return false;
    }

    $extensionByType = [
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/quicktime' => 'mov',
        'video/x-m4v' => 'm4v'
    ];
    $mimeType = explode(';', $detectedType ?: $contentType)[0];
    $extension = $extensionByType[$mimeType] ?? $sourceExtension;
    if (!in_array($extension, ['mp4', 'webm', 'mov', 'm4v'], true)) {
        $extension = 'mp4';
    }

    if (preg_match('~(?:reel|reels|p|shorts)/([A-Za-z0-9_-]+)~i', $videoUrl, $matches)) {
        $baseName = $matches[1];
    } else {
        $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $baseName);
        $baseName = trim((string) $baseName, '-_');
    }

    if ($baseName === '') {
        $baseName = 'video-' . substr(hash('sha256', $videoUrl), 0, 16);
    }

    $fileName = $baseName . '.' . $extension;
    $filePath = $uploadDir . $fileName;

    if (file_exists($filePath)) {
        $fileName = $baseName . '-' . substr(hash('sha256', $videoUrl . microtime(true)), 0, 8) . '.' . $extension;
        $filePath = $uploadDir . $fileName;
    }

    if (!rename($tempFile, $filePath)) {
        @unlink($tempFile);
        return false;
    }

    return [
        'url' => SITE_URL . '/upload/' . SITE_DOMAIN . '/social_proof/' . $fileName,
        'file' => $filePath
    ];
}

function publish($connection, $data)
{

    extract($data);
    if ($title != '') {

        if (isset($category)) {

            $skipUpload = false;

            if (isset($remote_image)) {
                if ($remote_image != '') {
                    $skipUpload = true;
                }
            }

            if ($skipUpload == false) {
                require_once MODULES . 'imageUploader.php';
            } else {
                $image['featureImage'] = $remote_image;
                $featureImage = $remote_image;
                $thumbnailImage = $remote_image;
            }

            if (isset($image['featureImage'])) {

                // Extract the single upload image string to variables

                $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
                $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

                /** Category */

                $category = explode('_', $category);
                $categoryURL = $category[1];
                $category = $category[0];

                /** social proof page url */

                $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

                /* define facebook and twitter meta desciption */

                $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
                $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

                if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                    if (mysqli_num_rows($checkURL) == 0) {

                        require_once EDITOR_CONTENT_FILTER;

                        $link_status = 200;

                        $mediaRelation = explode('_', $link_url_ref);
                        if (count($mediaRelation) >= 2) {
                            $mediaRelationId = $mediaRelation[0];
                            $mediaRelationType = strtolower($mediaRelation[1]);
                        } else {
                            $mediaRelationId = 0;
                            $mediaRelationType = '';
                        }

                        $video_url = trim($video_url ?? '');
                        $mediaPlatform = '';
                        $downloadedVideoFile = '';
                        $videoDownloadFailed = false;

                        if ($video_url !== '') {
                            $videoHost = preg_replace('/^www\./', '', (string) parse_url($video_url, PHP_URL_HOST));
                            $mediaPlatform = explode('.', $videoHost)[0];
                            $downloadedVideo = downloadSocialProofVideo($video_url);

                            if ($downloadedVideo === false) {
                                $videoDownloadFailed = true;
                                $msg = 'Unable to download the video. Please use a direct or publicly accessible video URL.';
                            } else {
                                $video_url = mysqli_real_escape_string($connection, $downloadedVideo['url']);
                                $downloadedVideoFile = $downloadedVideo['file'];
                            }
                        }

                        /** Start uploading the social proof */

                        if (!$videoDownloadFailed) {
                            mysqli_query($connection, "START TRANSACTION");

                            /* Insert data into link table */

                            $addNewSocialProof = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "social_proof(sp_site_id, sp_image, sp_thumbnail, sp_title, sp_description, sp_video_url, sp_ref_id, sp_ref_type, sp_category, sp_owner_id, sp_status, sp_type) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$video_url}', '{$mediaRelationId}', '{$mediaRelationType}', '{$category}', '" . USER_ID . "', '1', '{$mediaPlatform}')");
                            $socialProofId = mysqli_insert_id($connection);

                            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$socialProofId}', '{$link_status}', 'social_proof', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                            $linkID = mysqli_insert_id($connection);

                            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'social_proof', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                            /* Insert tags into tag table */

                            if ($tags != '') {

                                $tags = explode(',', $tags);

                                for ($i = 0; $i < sizeof($tags); $i++) {

                                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                    $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$socialProofId}', '" . USER_ID . "', '" . SITE_ID . "', 'social_proof')");
                                }
                            } else {

                                $addNewTags = TRUE;
                            }

                            if ($addNewSocialProof && $addNewLink && $addNewMeta && $addNewTags) {

                                $status = 200;
                                $msg = 'Social Proof add successfully';
                                $redirect = '/dashboard/social_proof/?type=edit&editor=true&id=' . base64_encode($socialProofId);
                                mysqli_query($connection, "COMMIT");
                            } else {

                                $msg = 'Something is wrong while adding new social proof';

                                mysqli_query($connection, "ROLLBACK");
                                if ($downloadedVideoFile !== '' && file_exists($downloadedVideoFile)) {
                                    @unlink($downloadedVideoFile);
                                }
                            }
                        }
                    } else {

                        $msg = 'Please change url';
                    }
                } else {

                    $msg = 'Technical errro';
                }
            } else {

                $msg = 'Please select social proof feature image.';
            }
        } else {

            $msg = 'Please select category';
        }
    } else {

        $msg = 'Please enter social proof name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
