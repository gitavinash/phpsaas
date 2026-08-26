<?php
/**
 * V3 Settings Master Router Bridge
 * Path: /api/v3/setting.php
 */

header('Content-Type: application/json');

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($requestUri, '/setting/profile') !== false) {
    require_once __DIR__ . '/setting/profile.php';
} elseif (strpos($requestUri, '/setting/contact') !== false) {
    require_once __DIR__ . '/setting/contact.php';
} elseif (strpos($requestUri, '/setting/notification') !== false || strpos($requestUri, '/setting/notify') !== false) {
    require_once __DIR__ . '/setting/notifications.php';
} elseif (strpos($requestUri, '/setting/banking') !== false) {
    require_once __DIR__ . '/setting/banking.php';
} elseif (strpos($requestUri, '/setting/seo') !== false) {
    require_once __DIR__ . '/setting/seo.php';
} else {
    require_once __DIR__ . '/setting/configuration.php';
}
