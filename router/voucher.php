<?php

/**
 * Public Travel Voucher Router (V3)
 * Handles public viewing & downloading of hotel, transport, and combined travel vouchers.
 * Endpoints:
 *   /voucher/{encoded_id}/
 *   /view-voucher/{encoded_id}/
 *   /download-voucher/{encoded_id}/
 * Parameters:
 *   ?download=true|false (or ?download=1|0 or ?pdf=1)
 */

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT') ? ROOT : realpath(__DIR__ . '/../'));

// Determine ID from URL or query
$rawId = '';
if (!empty($urlStructure[1])) {
    $rawId = $urlStructure[1];
} elseif (!empty($_GET['id'])) {
    $rawId = $_GET['id'];
} elseif (!empty($_GET['lead_id'])) {
    $rawId = $_GET['lead_id'];
} elseif (!empty($_GET['leadId'])) {
    $rawId = $_GET['leadId'];
}

// Determine if download/print is requested
$isDownload = false;
if (isset($urlStructure[0]) && in_array($urlStructure[0], ['download-voucher'])) {
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

// Pass parameters into $_GET
$_GET['id'] = $rawId;
$_GET['download'] = $isDownload ? 'true' : 'false';

// Delegate to API show endpoint
require_once $docRoot . '/api/v3/operations/voucher-show.php';
