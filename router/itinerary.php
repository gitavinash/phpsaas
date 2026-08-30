<?php

/**
 * Public Itinerary Router (V3)
 * Handles public viewing & downloading of tour itineraries / proposals.
 * Endpoints:
 *   /itinerary/{encoded_id}/
 *   /view-itinerary/{encoded_id}/
 *   /download-itinerary/{encoded_id}/
 * Parameters:
 *   ?download=true|false (or ?download=1|0 or ?pdf=1)
 *   ?template=mit|aleya|modern|standard
 */

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT') ? ROOT : realpath(__DIR__ . '/../'));

// Determine ID from URL or query
$rawId = '';
if (!empty($urlStructure[1])) {
    $rawId = $urlStructure[1];
} elseif (!empty($_GET['id'])) {
    $rawId = $_GET['id'];
} elseif (!empty($_GET['quotation_id'])) {
    $rawId = $_GET['quotation_id'];
} elseif (!empty($_GET['quotationId'])) {
    $rawId = $_GET['quotationId'];
}

// Determine if download is requested
$isDownload = false;
if (isset($urlStructure[0]) && $urlStructure[0] === 'download-itinerary') {
    $isDownload = true;
}
if (isset($_GET['download'])) {
    $dlVal = strtolower(trim((string)$_GET['download']));
    $isDownload = ($dlVal === 'true' || $dlVal === '1' || $dlVal === 'yes');
}
if (isset($_GET['pdf'])) {
    $pdfVal = strtolower(trim((string)$_GET['pdf']));
    if ($pdfVal === 'true' || $pdfVal === '1' || $pdfVal === 'yes') {
        $isDownload = true;
    }
}

// Pass parameters into $_GET / environment for the show endpoint
$_GET['id'] = $rawId;
$_GET['download'] = $isDownload ? 'true' : 'false';
if ($isDownload) {
    $_GET['auto_download'] = 'true';
}

// Delegate to API show endpoint
require_once $docRoot . '/api/v3/itinerary/show.php';
