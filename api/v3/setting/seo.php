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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    $siteId = 1;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch Current SEO Metadata
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $siteQuery = "
        SELECT site_name, site_description, site_keyword, site_index, site_domain 
        FROM {$tblPrefix}site 
        WHERE site_id = {$siteId} 
        LIMIT 1
    ";
    $siteRes = mysqli_query($connection, $siteQuery);
    $siteData = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    // Also check site_configuration for site_title / site_keywords
    $cfgRes = mysqli_query($connection, "
        SELECT site_configuration_key, site_configuration_value 
        FROM {$tblPrefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key IN ('site_title', 'site_keywords', 'site_keyword', 'site_description', 'site_index')
          AND site_configuration_status = 1
    ");
    $cfgMap = [];
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $cfgMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $siteName = $siteData['site_name'] ?? '';
    $siteTitle = $cfgMap['site_title'] ?? $siteName;
    $siteDescription = !empty($cfgMap['site_description']) ? $cfgMap['site_description'] : ($siteData['site_description'] ?? '');
    $siteKeywords = !empty($cfgMap['site_keywords']) ? $cfgMap['site_keywords'] : (!empty($cfgMap['site_keyword']) ? $cfgMap['site_keyword'] : ($siteData['site_keyword'] ?? ''));
    $siteIndex = !empty($cfgMap['site_index']) ? $cfgMap['site_index'] : (string)($siteData['site_index'] ?? 'index, follow');

    $data = [
        'site_title'       => $siteTitle,
        'site_name'        => $siteName,
        'site_domain'      => $siteData['site_domain'] ?? '',
        'site_description' => $siteDescription,
        'site_keywords'    => $siteKeywords,
        'site_keyword'     => $siteKeywords,
        'site_index'       => $siteIndex
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

    $title       = trim($data['site_title'] ?? ($data['site_name'] ?? ''));
    $description = trim($data['site_description'] ?? '');
    $keywords    = trim($data['site_keywords'] ?? ($data['site_keyword'] ?? ''));
    $indexVal    = trim((string)($data['site_index'] ?? 'index, follow'));

    $escDesc     = mysqli_real_escape_string($connection, $description);
    $escKeywords = mysqli_real_escape_string($connection, $keywords);
    $escIndex    = mysqli_real_escape_string($connection, $indexVal);

    // Update site table
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site 
        SET site_description = '{$escDesc}', 
            site_keyword = '{$escKeywords}', 
            site_index = '{$escIndex}' 
        WHERE site_id = {$siteId}
    ");

    // Also sync to site_configuration
    $cfgUpdates = [
        'site_title'       => $title,
        'site_description' => $description,
        'site_keywords'    => $keywords,
        'site_keyword'     => $keywords,
        'site_index'       => $indexVal
    ];

    foreach ($cfgUpdates as $k => $v) {
        $escVal = mysqli_real_escape_string($connection, (string)$v);
        $escKey = mysqli_real_escape_string($connection, (string)$k);
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES 
                ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ON DUPLICATE KEY UPDATE 
                site_configuration_value = '{$escVal}', 
                site_configuration_status = 1
        ");
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'SEO metadata and Google search indexing preferences updated successfully.'
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
