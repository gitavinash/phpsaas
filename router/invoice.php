<?php

/**
 * Public Proforma & Tax Invoice Router (V3)
 * Endpoints:
 *   /invoice/{encoded_id}/
 *   /view-invoice/{encoded_id}/
 *   /download-invoice/{encoded_id}/
 */

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT') ? ROOT : realpath(__DIR__ . '/../'));

$rawId = '';
if (!empty($urlStructure[1])) {
    $rawId = $urlStructure[1];
} elseif (!empty($_GET['id'])) {
    $rawId = $_GET['id'];
} elseif (!empty($_GET['lead_id'])) {
    $rawId = $_GET['lead_id'];
}

$isDownload = false;
if (isset($urlStructure[0]) && in_array($urlStructure[0], ['download-invoice'])) {
    $isDownload = true;
}
if (isset($_GET['download'])) {
    $dlVal = strtolower(trim((string)$_GET['download']));
    $isDownload = ($dlVal === 'true' || $dlVal === '1' || $dlVal === 'yes');
}

$_GET['id'] = $rawId;
$_GET['download'] = $isDownload ? 'true' : 'false';

require_once $docRoot . '/api/v3/operations/invoice-show.php';
