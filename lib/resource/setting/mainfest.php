<?php

// Set the content type to application/json
header("Content-type: application/json; charset=utf-8");

// Assume $site_mainfest_json contains the raw manifest.json template with placeholders
$mainfest = str_replace(
    ['%SITE_NAME%', '%SITE_DESC%', '%SITE_THEME_COLOUR%', '%SITE_LOGO%', '%SITE_ICON%'],
    [SITE_NAME, SITE_DESC, SITE_THEME_COLOUR, SITE_LOGO, SITE_ICON],
    $site_mainfest_json
);

// Decode and re-encode to validate JSON and output it correctly
$decoded = json_decode($mainfest, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode(['error' => 'Invalid JSON after replacements']);
}
exit();