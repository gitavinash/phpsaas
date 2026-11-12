<?php
/**
 * V3 Settings Master Router Bridge
 * Path: /api/v3/setting.php
 */

header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 2);
if (!isset($connection)) {
    if (file_exists($docRoot . '/lib/modules/siteStatus.php')) {
        require_once $docRoot . '/lib/modules/siteStatus.php';
    } elseif (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
        if (file_exists($docRoot . '/lib/modules/connection.php')) {
            require_once $docRoot . '/lib/modules/connection.php';
        }
    }
}
if (!defined('LOGIN_CHECK') && file_exists($docRoot . '/lib/configuration/index.php')) {
    require_once $docRoot . '/lib/configuration/index.php';
}

// Enforce authentication guard before routing to settings handlers
require_once LOGIN_CHECK;

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
} elseif (strpos($requestUri, '/setting/redirection') !== false) {
    require_once __DIR__ . '/setting/redirection.php';
} elseif (strpos($requestUri, '/setting/app') !== false) {
    require_once __DIR__ . '/setting/app.php';
} elseif (strpos($requestUri, '/setting/migration') !== false) {
    require_once __DIR__ . '/master/index.php';
} else {
    require_once __DIR__ . '/setting/configuration.php';
}
