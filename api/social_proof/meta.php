<?php

require_once LOGIN_CHECK;

header('Content-Type: application/json; charset=utf-8');

$url = trim($_REQUEST['url'] ?? '');

if (empty($url)) {
    echo json_encode([
        'status' => 400,
        'msg'    => 'Video URL is required.'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/

define('DEFAULT_INSTAGRAM_THUMBNAIL', SITE_URL . '/assets/images/default-instagram.jpg');

/*
|--------------------------------------------------------------------------
| CURL
|--------------------------------------------------------------------------
*/

function fetchUrl($url, $userAgent = '')
{
    $ch = curl_init();

    if ($userAgent === '') {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0 Safari/537.36';
    }

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => $userAgent,
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9'
        ]
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response ?: '';
}

function decodeInstagramValue($value)
{
    for ($i = 0; $i < 2; $i++) {
        $decoded = json_decode('"' . $value . '"');
        if (!is_string($decoded)) {
            break;
        }
        $value = $decoded;
    }

    return html_entity_decode(
        str_replace(['\\/', '\\u0026', '\\u0025'], ['/', '&', '%'], $value),
        ENT_QUOTES,
        'UTF-8'
    );
}

function getInstagramEmbeddedValue($html, $marker, $offset = 0)
{
    $start = strpos($html, $marker, $offset);
    if ($start === false) {
        return '';
    }

    $start += strlen($marker);
    $length = strlen($html);

    for ($i = $start; $i < $length; $i++) {
        if ($html[$i] !== '"') {
            continue;
        }

        $slashCount = 0;
        for ($j = $i - 1; $j >= $start && $html[$j] === '\\'; $j--) {
            $slashCount++;
        }

        if ($slashCount === 1) {
            return decodeInstagramValue(substr($html, $start, $i - $start - 1));
        }
    }

    return '';
}

function getInstagramEmbedMetadata($shortcode, $type)
{
    $embedType = ($type === 'p') ? 'p' : 'reel';
    $embedUrl = "https://www.instagram.com/{$embedType}/{$shortcode}/embed/captioned/";
    $html = fetchUrl(
        $embedUrl,
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Instagram 333.0.0.0.0'
    );

    if ($html === '') {
        return [];
    }

    $captionMarker = 'edge_media_to_caption\\":{\\"edges\\":[{\\"node\\":{\\"text\\":\\"';
    $displayUrlMarker = 'display_url\\":\\"';
    $ownerMarker = 'owner\\":{';
    $ownerPosition = strpos($html, $ownerMarker);

    return [
        'caption' => trim(getInstagramEmbeddedValue($html, $captionMarker)),
        'thumbnail' => getInstagramEmbeddedValue($html, $displayUrlMarker),
        'username' => $ownerPosition === false
            ? ''
            : trim(getInstagramEmbeddedValue($html, 'username\\":\\"', $ownerPosition))
    ];
}

function getInstagramTitleFromCaption($caption, $username, $defaultTitle)
{
    $caption = trim($caption);
    if ($caption !== '') {
        $lines = preg_split('/\R+/', $caption);
        $title = trim($lines[0] ?? '');
        if ($title !== '') {
            return function_exists('mb_substr') ? mb_substr($title, 0, 180) : substr($title, 0, 180);
        }
    }

    return $username !== '' ? '@' . $username . ' on Instagram' : $defaultTitle;
}

function getInstagramMediaUrl($shortcode)
{
    $url = "https://www.instagram.com/p/{$shortcode}/media/?size=l";
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0 Safari/537.36'
    ]);

    curl_exec($ch);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    return !empty($redirectUrl) ? $redirectUrl : $url;
}

function downloadInstagramThumbnail($url, $shortcode)
{
    if (empty($url) || $url === DEFAULT_INSTAGRAM_THUMBNAIL) {
        return $url;
    }

    $uploadDir = ROOT . '/upload/' . SITE_DOMAIN . '/social_proof/';
    $fileName = $shortcode . '.jpg';
    $filePath = $uploadDir . $fileName;

    // If file already exists locally, reuse it
    if (file_exists($filePath)) {
        return SITE_URL . '/upload/' . SITE_DOMAIN . '/social_proof/' . $fileName;
    }

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return $url;
        }
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0 Safari/537.36'
    ]);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($imageData && $httpCode === 200) {
        if (file_put_contents($filePath, $imageData) !== false) {
            return SITE_URL . '/upload/' . SITE_DOMAIN . '/social_proof/' . $fileName;
        }
    }

    return $url;
}


/*
|--------------------------------------------------------------------------
| META TAG PARSER
|--------------------------------------------------------------------------
*/

function getMetaContent($html, $property)
{
    if (!$html) {
        return '';
    }

    $patterns = [
        '/<meta[^>]+property=["\']' . preg_quote($property, '/') . '["\'][^>]+content=["\']([^"\']+)["\']/i',
        '/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']' . preg_quote($property, '/') . '["\']/i',
        '/<meta[^>]+name=["\']' . preg_quote($property, '/') . '["\'][^>]+content=["\']([^"\']+)["\']/i'
    ];

    foreach ($patterns as $pattern) {

        if (preg_match($pattern, $html, $matches)) {
            return html_entity_decode(
                $matches[1],
                ENT_QUOTES,
                'UTF-8'
            );
        }
    }

    return '';
}

/*
|--------------------------------------------------------------------------
| RESPONSE DEFAULT
|--------------------------------------------------------------------------
*/

$response = [
    'status'      => 400,
    'msg'         => 'Unsupported URL.',
    'platform'    => '',
    'title'       => '',
    'thumbnail'   => '',
    'description' => '',
    'embed'       => ''
];

/*
|--------------------------------------------------------------------------
| YOUTUBE
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '~(?:youtube\.com/(?:watch\?v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i',
        $url,
        $matches
    )
) {

    $videoId = $matches[1];

    $oembedUrl =
        'https://www.youtube.com/oembed?url=' .
        urlencode($url) .
        '&format=json';

    $json = fetchUrl($oembedUrl);

    $data = json_decode($json, true);

    $title = $data['title'] ?? 'YouTube Video';

    $thumbnail = "https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg";

    $response = [
        'status'      => 200,
        'msg'         => 'YouTube metadata fetched successfully.',
        'platform'    => 'youtube',
        'title'       => $title,
        'thumbnail'   => $thumbnail,
        'description' => $title,
        'embed'       =>
        '<iframe src="https://www.youtube.com/embed/' .
            $videoId .
            '" width="560" height="315" frameborder="0" allowfullscreen style="max-width:100%;"></iframe>'
    ];
}

/*
|--------------------------------------------------------------------------
| INSTAGRAM
|--------------------------------------------------------------------------
*/ elseif (
    preg_match(
        '~instagram\.com/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)~i',
        $url,
        $matches
    )
) {

    $shortcode = $matches[1];

    // Determine the type from the URL (reel, tv, or post)
    $type = 'p';
    if (preg_match('~instagram\.com/(reel|reels|p|tv)/~i', $url, $typeMatches)) {
        $matchedType = strtolower($typeMatches[1]);
        if ($matchedType === 'reels' || $matchedType === 'reel') {
            $type = 'reel';
        } elseif ($matchedType === 'tv') {
            $type = 'tv';
        }
    }

    // Determine default clean fallback title
    $defaultTitle = 'Instagram Post';
    if ($type === 'reel') {
        $defaultTitle = 'Instagram Reel';
    } elseif ($type === 'tv') {
        $defaultTitle = 'Instagram TV';
    }

    // Construct clean URL without query parameters to avoid cURL request issues
    $instagramUrl = "https://www.instagram.com/{$type}/{$shortcode}/";

    $title = $defaultTitle;
    $thumbnail = getInstagramMediaUrl($shortcode);
    $description = '';

    $embedMetadata = getInstagramEmbedMetadata($shortcode, $type);
    if (!empty($embedMetadata)) {
        $description = $embedMetadata['caption'] ?? '';
        $title = getInstagramTitleFromCaption(
            $description,
            $embedMetadata['username'] ?? '',
            $defaultTitle
        );

        if (!empty($embedMetadata['thumbnail'])) {
            $thumbnail = $embedMetadata['thumbnail'];
        }
    }

    $html = fetchUrl($instagramUrl);

    if (!empty($html)) {

        $metaTitle = getMetaContent($html, 'og:title');
        if (!empty($metaTitle) && $title === $defaultTitle) {
            $title = $metaTitle;
        }

        $metaDescription = getMetaContent($html, 'og:description');
        if (!empty($metaDescription) && $description === '') {
            $description = $metaDescription;
        }

        $metaImage = getMetaContent($html, 'og:image');
        if (!empty($metaImage) && empty($embedMetadata['thumbnail'])) {
            $thumbnail = $metaImage;
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK #1
        |--------------------------------------------------------------------------
        */

        if (
            empty($thumbnail) &&
            preg_match('/"thumbnail_url":"([^"]+)"/i', $html, $thumb)
        ) {
            $thumbnail = stripslashes($thumb[1]);
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK #2
        |--------------------------------------------------------------------------
        */

        if (
            empty($thumbnail) &&
            preg_match('/"display_url":"([^"]+)"/i', $html, $thumb)
        ) {
            $thumbnail = stripslashes($thumb[1]);
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK #3
        |--------------------------------------------------------------------------
        */

        if (
            ($title === $defaultTitle || empty($title)) &&
            preg_match('/<title>(.*?)<\/title>/is', $html, $titleMatch)
        ) {
            $title = trim(
                html_entity_decode(
                    strip_tags($titleMatch[1]),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }

    // Check if we were redirected to a login page or failed to fetch real post content
    $loginKeywords = ['login', 'welcome back to instagram', 'sign in', 'create an account', 'error • instagram'];
    $isLoginOrError = false;
    foreach ($loginKeywords as $keyword) {
        if (stripos($title, $keyword) !== false || stripos($description, $keyword) !== false) {
            $isLoginOrError = true;
            break;
        }
    }

    if ($isLoginOrError) {
        $title = $defaultTitle;
        $description = '';
    }

    // Ensure thumbnail is always set, resolving it to the direct CDN image URL
    if (empty($thumbnail) || $thumbnail === DEFAULT_INSTAGRAM_THUMBNAIL || strpos($thumbnail, 'instagram.com/p/') !== false) {
        $thumbnail = getInstagramMediaUrl($shortcode);
    }

    // Download the thumbnail locally to bypass CORP (cross-origin-resource-policy) and URL expiration
    $thumbnail = downloadInstagramThumbnail($thumbnail, $shortcode);

    $response = [
        'status'      => 200,
        'msg'         => 'Instagram URL processed.',
        'platform'    => 'instagram',
        'title'       => $title,
        'thumbnail'   => $thumbnail,
        'description' => $description,
        'embed'       =>
        '<blockquote class="instagram-media"
                data-instgrm-permalink="' .
            htmlspecialchars($instagramUrl) .
            '"
                data-instgrm-version="14">
            </blockquote>'
    ];
}

echo json_encode(
    $response,
    JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE
);

exit;
