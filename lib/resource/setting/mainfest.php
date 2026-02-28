<?php

// Set the content type to application/json
header("Content-type: application/json; charset=utf-8");

$manifestPath = MANIFEST . strtolower(SITE_APP) . '.php';

if (file_exists($manifestPath)) {

    $manifestData = (string) file_get_contents($manifestPath);
} else {

    $manifestData = (string) DEFAULT_MAINFEST_JSON;
}

if ($manifestData === '') {
    $manifestData = (string) DEFAULT_MAINFEST_JSON;
}

// Normalize storage format:
// - JSON encoded string  => decode once to JSON text
// - JSON object/array    => keep by re-encoding
$decodedManifest = json_decode($manifestData, true);
if (json_last_error() === JSON_ERROR_NONE) {
    if (is_string($decodedManifest)) {
        $manifestData = $decodedManifest;
    } elseif (is_array($decodedManifest)) {
        $manifestData = json_encode($decodedManifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}

$replacements = [
    '%SITE_NAME%' => SITE_NAME,
    '%SITE_DESC%' => SITE_DESC,
    '%SITE_THEME_COLOUR%' => SITE_THEME_COLOUR,
    '%SITE_LOGO%' => SITE_LOGO,
    '%SITE_ICON%' => SITE_ICON,
];

// Replace placeholders in the raw manifest string
$manifestData = str_replace(
    array_keys($replacements),
    array_values($replacements),
    $manifestData
);

// If replaced JSON is invalid, fallback to DEFAULT_MAINFEST_JSON
$manifestArray = json_decode($manifestData, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($manifestArray)) {
    $defaultRaw = (string) DEFAULT_MAINFEST_JSON;
    $defaultDecoded = json_decode($defaultRaw, true);

    if (json_last_error() === JSON_ERROR_NONE && is_string($defaultDecoded)) {
        $manifestArray = json_decode($defaultDecoded, true);
    } elseif (is_array($defaultDecoded)) {
        $manifestArray = $defaultDecoded;
    }
}

if (!is_array($manifestArray)) {
    $manifestArray = [];
}

$output = json_encode(
    $manifestArray,
    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
);

if ($output === false) {
    echo '{}';
    exit();
}

echo $output;

exit();