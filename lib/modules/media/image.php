<?php
@ini_set('display_errors', '0');
@ini_set('zlib.output_compression', 'Off');
error_reporting(0);

$srcUrl = isset($_GET['url']) ? trim($_GET['url']) : '';
$tw = isset($_GET['width']) ? max(1, min(3000, (int)$_GET['width'])) : 512;
$th = isset($_GET['height']) ? max(1, min(3000, (int)$_GET['height'])) : 512;
$mode = isset($_GET['mode']) ? strtolower($_GET['mode']) : 'cover';
$fmt = isset($_GET['fmt']) ? strtolower($_GET['fmt']) : 'auto';
$q = isset($_GET['q']) ? max(1, min(100, (int)$_GET['q'])) : 85;
$disableCache = (isset($_GET['cache']) && $_GET['cache'] === 'off');

if (!$srcUrl) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Bad Request: url is required.";
    exit;
}

function fetch_bytes($url, $timeout = 10, $maxBytes = 20 * 1024 * 1024)
{
    // 1. Try local file read first if path is local
    $parsed = parse_url($url);
    $path = isset($parsed['path']) ? $parsed['path'] : $url;
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT'])
        ? rtrim($_SERVER['DOCUMENT_ROOT'], '/')
        : dirname(__DIR__, 2);
    
    if (!empty($path)) {
        $localFile = $docRoot . '/' . ltrim($path, '/');
        if (file_exists($localFile) && is_file($localFile) && filesize($localFile) <= $maxBytes) {
            return @file_get_contents($localFile);
        }
    }

    // 2. Fallback to cURL for remote URLs
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 4,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_USERAGENT => 'ImageProxy/1.3',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HEADER => false,
    ]);
    $data = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($err || $code >= 400 || !$data) return null;
    if (strlen($data) > $maxBytes) return null;
    return $data;
}

$bytes = fetch_bytes($srcUrl);
if (!$bytes) {
    http_response_code(502);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Failed to fetch source image.";
    exit;
}

$info = @getimagesizefromstring($bytes);
if (!$info) {
    http_response_code(415);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Unsupported or invalid image.";
    exit;
}
list($sw, $sh, $stype) = $info;

$src = @imagecreatefromstring($bytes);
if (!$src) {
    http_response_code(415);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Unsupported image data.";
    exit;
}

$extMap = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
    IMAGETYPE_GIF  => 'gif',
    IMAGETYPE_WEBP => 'webp',
    IMAGETYPE_BMP  => 'bmp'
];
$srcExt = isset($extMap[$stype]) ? $extMap[$stype] : 'jpg';

$acceptsWebP = isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
if ($fmt === 'auto') {
    if ($acceptsWebP && function_exists('imagewebp')) {
        $outFmt = 'webp';
    } else {
        $outFmt = in_array($srcExt, ['png', 'gif', 'webp'], true) ? 'png' : 'jpg';
    }
} else {
    $outFmt = in_array($fmt, ['jpg', 'jpeg', 'png', 'webp'], true) ? ($fmt === 'jpeg' ? 'jpg' : $fmt) : 'jpg';
}

$srcAspect = $sw / $sh;
$dstAspect = $tw / $th;

switch ($mode) {
    case 'pad':
        $ratio = min($tw / $sw, $th / $sh);
        $dw = max(1, (int)floor($sw * $ratio));
        $dh = max(1, (int)floor($sh * $ratio));
        $dx = (int)(($tw - $dw) / 2);
        $dy = (int)(($th - $dh) / 2);
        $sx = 0;
        $sy = 0;
        $copySrcW = $sw;
        $copySrcH = $sh;
        $canvasW = $tw;
        $canvasH = $th;
        break;
    case 'contain':
        $ratio = min($tw / $sw, $th / $sh);
        $dw = max(1, (int)floor($sw * $ratio));
        $dh = max(1, (int)floor($sh * $ratio));
        $dx = 0;
        $dy = 0;
        $sx = 0;
        $sy = 0;
        $copySrcW = $sw;
        $copySrcH = $sh;
        $canvasW = $dw;
        $canvasH = $dh;
        break;
    case 'cover':
    default:
        if ($srcAspect > $dstAspect) {
            $cropH = $sh;
            $cropW = (int)round($sh * $dstAspect);
            $sx = (int)(($sw - $cropW) / 2);
            $sy = 0;
        } else {
            $cropW = $sw;
            $cropH = (int)round($sw / $dstAspect);
            $sx = 0;
            $sy = (int)(($sh - $cropH) / 2);
        }
        $dw = $tw;
        $dh = $th;
        $dx = 0;
        $dy = 0;
        $copySrcW = $cropW;
        $copySrcH = $cropH;
        $canvasW = $tw;
        $canvasH = $th;
        break;
}

$dst = imagecreatetruecolor($canvasW, $canvasH);
$useAlpha = ($outFmt === 'png' || $outFmt === 'webp');

if ($useAlpha) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $canvasW, $canvasH, $transparent);
} else {
    $bg = imagecolorallocate($dst, 0, 0, 0);
    imagefilledrectangle($dst, 0, 0, $canvasW, $canvasH, $bg);
}

imagecopyresampled($dst, $src, $dx, $dy, $sx, $sy, $dw, $dh, $copySrcW, $copySrcH);

$etag = '"' . md5($srcUrl . '|' . $tw . '|' . $th . '|' . $mode . '|' . $q . '|' . $outFmt) . '"';
header('ETag: ' . $etag);
header('Vary: Accept');

if ($disableCache) {
    header('Cache-Control: no-store, max-age=0');
} else {
    header('Cache-Control: public, max-age=604800, immutable');
}

if (!$disableCache && isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    while (function_exists('ob_get_level') && ob_get_level() > 0) {
        @ob_end_clean();
    }
    imagedestroy($src);
    imagedestroy($dst);
    http_response_code(304);
    exit;
}

while (function_exists('ob_get_level') && ob_get_level() > 0) {
    @ob_end_clean();
}
ob_start();

switch ($outFmt) {
    case 'webp':
        header('Content-Type: image/webp');
        if (function_exists('imagewebp')) {
            imagewebp($dst, null, $q);
        } else {
            header_remove('Content-Type');
            header('Content-Type: image/jpeg');
            imagejpeg($dst, null, $q);
        }
        break;
    case 'png':
        header('Content-Type: image/png');
        $compression = (int)round((100 - $q) * 9 / 100);
        imagepng($dst, null, $compression);
        break;
    default:
        header('Content-Type: image/jpeg');
        imagejpeg($dst, null, $q);
        break;
}

$payload = ob_get_clean();
header('Content-Length: ' . strlen($payload));
echo $payload;

imagedestroy($src);
imagedestroy($dst);