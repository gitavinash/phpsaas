<?php

/**
 * V3 SEO Metadata & Search Visibility API Endpoint
 * Path: /api/v3/setting/seo.php
 */

header('Content-Type: application/json');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Active site identifier could not be resolved.']);
    exit;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch Current SEO Metadata
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $siteQuery = "SELECT site_name, site_title, site_description, site_keywords, site_index, site_domain 
                  FROM " . TABLE_PREFIX . "site 
                  WHERE site_id = {$siteId} LIMIT 1";
    $siteRes = mysqli_query($connection, $siteQuery);
    $siteData = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    $data = [
        'site_title'       => $siteData['site_title'] ?? ($siteData['site_name'] ?? ''),
        'site_name'        => $siteData['site_name'] ?? '',
        'site_domain'      => $siteData['site_domain'] ?? '',
        'site_description' => $siteData['site_description'] ?? '',
        'site_keywords'    => $siteData['site_keywords'] ?? '',
        'site_index'       => (string)($siteData['site_index'] ?? '1')
    ];

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update SEO Metadata
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $title       = trim($data['site_title'] ?? '');
    $description = trim($data['site_description'] ?? '');
    $keywords    = trim($data['site_keywords'] ?? '');
    $index       = isset($data['site_index']) ? (int)$data['site_index'] : 1;

    $escTitle = mysqli_real_escape_string($connection, $title);
    $escDesc = mysqli_real_escape_string($connection, $description);
    $escKeywords = mysqli_real_escape_string($connection, $keywords);

    $sql = "UPDATE " . TABLE_PREFIX . "site 
            SET site_title = '{$escTitle}', 
                site_description = '{$escDesc}', 
                site_keywords = '{$escKeywords}', 
                site_index = {$index} 
            WHERE site_id = {$siteId}";

    $ok = mysqli_query($connection, $sql);

    if ($ok) {
        // Also sync to site_configuration for backward compatibility
        $cfgUpdates = [
            'site_title' => $title,
            'site_description' => $description,
            'site_keywords' => $keywords,
            'site_index' => (string)$index
        ];

        foreach ($cfgUpdates as $k => $v) {
            $escVal = mysqli_real_escape_string($connection, $v);
            $escKey = mysqli_real_escape_string($connection, $k);
            mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
                ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}', site_configuration_status = 1");
        }

        echo json_encode([
            'status' => 'success',
            'msg'    => 'SEO metadata and Google search indexing preferences updated successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Database error updating SEO configuration.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
