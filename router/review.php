<?php

/**
 * Public Traveler Smart Review Router (V3)
 * Endpoints:
 *   /review/{encoded_id}/
 *   /feedback/{encoded_id}/
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

$_GET['id'] = $rawId;

require_once $docRoot . '/api/v3/operations/review-show.php';
