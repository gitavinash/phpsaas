<?php

/**
 * V3 SEO Metadata & Search Indexing API Endpoint
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

require_once LOGIN_CHECK;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
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
    echo json_encode([
        'status' => 'error',
        'msg' => 'Active site identifier could not be resolved.'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 1. GET: Fetch SEO & Indexing Metadata
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $sql = "SELECT site_id, site_name, site_description, site_keyword, site_index, site_domain
            FROM " . TABLE_PREFIX . "site
            WHERE site_id = {$siteId}
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Site configuration not found.'
        ]);
        exit;
    }

    $site = mysqli_fetch_assoc($res);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'site_name'        => $site['site_name'] ?? '',
            'site_description' => $site['site_description'] ?? '',
            'site_keyword'     => $site['site_keyword'] ?? '',
            'site_index'       => $site['site_index'] ?: 'index, follow',
            'site_domain'      => $site['site_domain'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : '')
        ]
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Save SEO & Indexing Metadata
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $siteName        = trim($data['site_name'] ?? $data['site_meta_title'] ?? '');
    $siteDescription = trim($data['site_description'] ?? $data['site_meta_description'] ?? '');
    $siteKeyword     = trim($data['site_keyword'] ?? $data['site_meta_keywords'] ?? '');
    $siteIndex       = trim($data['site_index'] ?? 'index, follow');

    $escName = mysqli_real_escape_string($connection, $siteName);
    $escDesc = mysqli_real_escape_string($connection, $siteDescription);
    $escKw   = mysqli_real_escape_string($connection, $siteKeyword);
    $escIdx  = mysqli_real_escape_string($connection, $siteIndex);

    $updateSql = "UPDATE " . TABLE_PREFIX . "site 
                  SET site_description = '{$escDesc}',
                      site_keyword     = '{$escKw}',
                      site_index       = '{$escIdx}'";

    if (!empty($siteName)) {
        $updateSql .= ", site_name = '{$escName}'";
    }

    $updateSql .= " WHERE site_id = {$siteId}";

    $ok = mysqli_query($connection, $updateSql);

    if ($ok) {
        echo json_encode([
            'status' => 'success',
            'msg'    => 'SEO metadata and search indexing preferences saved successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Failed to update SEO metadata.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
