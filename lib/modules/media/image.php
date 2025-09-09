<?php
// /viewmedia/index.php

ini_set('display_errors', '0');
error_reporting(0);

// -------- Params --------
$srcUrl = isset($_GET['url']) ? trim($_GET['url']) : '';
$tw = isset($_GET['width'])  ? max(1, min(3000, (int)$_GET['width']))  : 512;
$th = isset($_GET['height']) ? max(1, min(3000, (int)$_GET['height'])) : 512;

// Modes:
// - contain (default): keep aspect ratio, NO padding — output is the resized size
// - cover: fill box and crop to exactly width x height
$mode = isset($_GET['mode']) ? strtolower($_GET['mode']) : 'contain';

// Optional: output format and quality
$fmt = isset($_GET['fmt']) ? strtolower($_GET['fmt']) : 'auto';     // auto|jpg|png|webp
$q   = isset($_GET['q'])   ? max(1, min(100, (int)$_GET['q'])) : 85;

if (!$srcUrl) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Bad Request: url is required.";
    exit;
}

// -------- Fetch bytes safely --------
function fetch_bytes($url, $timeout = 8, $maxBytes = 20 * 1024 * 1024)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 4,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_USERAGENT      => 'ImageProxy/1.1',
        CURLOPT_SSL_VERIFYPEER => false, // set true with proper CA in prod
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HEADER         => false,
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

// -------- Decode & inspect --------
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
    echo "Unsupported image data.";
    exit;
}

$extMap = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp', IMAGETYPE_BMP => 'bmp'];
$srcExt = isset($extMap[$stype]) ? $extMap[$stype] : 'jpg';

$acceptsWebP = isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
if ($fmt === 'auto') {
    if ($acceptsWebP && function_exists('imagewebp')) {
        $outFmt = 'webp';
    } else {
        // preserve alpha-capable formats as PNG, others JPG
        $outFmt = in_array($srcExt, ['png', 'gif', 'webp'], true) ? 'png' : 'jpg';
    }
} else {
    $outFmt = in_array($fmt, ['jpg', 'jpeg', 'png', 'webp'], true) ? ($fmt === 'jpeg' ? 'jpg' : $fmt) : 'jpg';
}

// -------- Geometry (no background on contain) --------
$srcAspect = $sw / $sh;
$dstAspect = $tw / $th;

if ($mode === 'cover') {
    // Fill exact WxH and crop excess
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
    $dh = $th;       // destination size (exact)
    $dx = 0;
    $dy = 0;
    $copySrcW = $cropW;
    $copySrcH = $cropH;
    $canvasW = $tw;
    $canvasH = $th;
} else {
    // contain: scale to fit, NO padding — output is the resized size
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
    $canvasH = $dh;   // <— KEY: no letterbox/canvas color
}

// -------- Create destination (preserve alpha if possible) --------
$dst = imagecreatetruecolor($canvasW, $canvasH);
$useAlpha = ($outFmt === 'png' || $outFmt === 'webp');

if ($useAlpha) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $canvasW, $canvasH, $transparent);
} else {
    // For JPG we also avoid background bars by resizing canvas to exact image size.
    // So no fill is needed; still, initialize to black to be safe.
    $bg = imagecolorallocate($dst, 0, 0, 0);
    imagefilledrectangle($dst, 0, 0, $canvasW, $canvasH, $bg);
}

// High-quality resample
imagecopyresampled($dst, $src, $dx, $dy, $sx, $sy, $dw, $dh, $copySrcW, $copySrcH);

// -------- Caching --------
$etag = '"' . md5($srcUrl . '|' . $tw . '|' . $th . '|' . $mode . '|' . $q . '|' . $outFmt) . '""';
header('ETag: ' . $etag);
header('Cache-Control: public, max-age=604800, immutable'); // 7 days
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    imagedestroy($src);
    imagedestroy($dst);
    http_response_code(304);
    exit;
}

// -------- Output --------
ob_clean();
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
        $compression = (int)round((100 - $q) * 9 / 100); // 0 best/large, 9 smallest
        imagepng($dst, null, $compression);
        break;
    default:
        header('Content-Type: image/jpeg');
        imagejpeg($dst, null, $q);
        break;
}

imagedestroy($src);
imagedestroy($dst);