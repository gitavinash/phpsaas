<?php

// Set the content type to application/json
header("Content-type: application/json; charset=utf-8");

function respondJson($payload, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

$rawTemplate = isset($site_mainfest_json) ? (string) $site_mainfest_json : '';

if ($rawTemplate === '') {
    respondJson([
        'error' => true,
        'message' => 'Manifest template is empty.'
    ], 500);
}

// Normalize template storage format:
// - If DB value is a JSON-encoded string, decode once to raw JSON text.
// - If DB value is already JSON text, keep as-is.
$template = $rawTemplate;
$decodedTemplate = json_decode($rawTemplate, true);
if (json_last_error() === JSON_ERROR_NONE) {
    if (is_string($decodedTemplate)) {
        $template = $decodedTemplate;
    } elseif (is_array($decodedTemplate)) {
        $template = json_encode($decodedTemplate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

$replacements = [
    '%SITE_NAME%' => SITE_NAME,
    '%SITE_DESC%' => SITE_DESC,
    '%SITE_THEME_COLOUR%' => SITE_THEME_COLOUR,
    '%SITE_LOGO%' => SITE_LOGO,
    '%SITE_ICON%' => SITE_ICON,
];

foreach ($replacements as $placeholder => $value) {
    $jsonValue = json_encode((string) $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($jsonValue === false) {
        respondJson([
            'error' => true,
            'message' => 'Failed to encode replacement value.',
            'placeholder' => $placeholder,
        ], 500);
    }

    // Remove surrounding quotes so the replacement remains valid for quoted placeholders.
    $escapedValue = substr($jsonValue, 1, -1);

    $template = str_replace(
        ['"' . $placeholder . '"', $placeholder],
        ['"' . $escapedValue . '"', $escapedValue],
        $template
    );
}

$manifest = json_decode($template, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($manifest)) {
    respondJson([
        'error' => true,
        'message' => 'Invalid manifest JSON after replacements.',
        'json_error' => json_last_error_msg(),
    ], 500);
}

respondJson($manifest);