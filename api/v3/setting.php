<?php
/**
 * V3 Settings Master Router Bridge
 * Path: /api/v3/setting.php
 */

header('Content-Type: application/json');

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($requestUri, '/setting/profile') !== false) {
    require_once __DIR__ . '/setting/profile.php';
} else {
    require_once __DIR__ . '/setting/configuration.php';
}
