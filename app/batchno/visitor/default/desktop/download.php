<?php

ob_start();

if (isset($downloadUrl)) {

    $downloadUrl = SITE_URL . $downloadUrl;

    // Get image content
    $imageData = file_get_contents($downloadUrl);

    if ($imageData === false) {
        die('Failed to fetch image.');
    }

    // Extract filename from URL
    $fileName = basename(parse_url($downloadUrl, PHP_URL_PATH));

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($imageData));

    // Output the image data
    echo $imageData;
    exit;
} else {
    echo "<p>Image URL is missing</p>";
}

ob_end_flush();
