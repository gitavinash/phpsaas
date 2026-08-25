<?php

/**
 * V3 Public Contact Info & Socials API Endpoint
 * Path: /api/v3/setting/contact.php
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
// 1. GET: Fetch Current Contact Details & Site Company Info
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $configKeys = [
        'company_name',
        'company_address',
        'company_email',
        'company_email_support',
        'company_mobile',
        'company_mobile_helpline',
        'company_whatsapp',
        'company_google_map',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'social_linkedin',
        'social_twitter'
    ];

    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) {
        return mysqli_real_escape_string($connection, $k);
    }, $configKeys)) . "'";

    $cfgRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
                                         FROM " . TABLE_PREFIX . "site_configuration 
                                         WHERE site_configuration_site_id = {$siteId} 
                                           AND site_configuration_key IN ({$escKeys}) 
                                           AND site_configuration_status = 1");

    $configMap = [];
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $configMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $siteRes = mysqli_query($connection, "SELECT site_name, site_email, site_mobile, site_address FROM " . TABLE_PREFIX . "site WHERE site_id = {$siteId} LIMIT 1");
    $siteRow = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    $data = [
        'company_name'            => $configMap['company_name'] ?? ($siteRow['site_name'] ?? ''),
        'company_address'         => $configMap['company_address'] ?? ($siteRow['site_address'] ?? ''),
        'company_email'           => $configMap['company_email'] ?? ($siteRow['site_email'] ?? ''),
        'company_email_support'   => $configMap['company_email_support'] ?? '',
        'company_mobile'          => $configMap['company_mobile'] ?? ($siteRow['site_mobile'] ?? ''),
        'company_mobile_helpline' => $configMap['company_mobile_helpline'] ?? '',
        'company_whatsapp'        => $configMap['company_whatsapp'] ?? '',
        'company_google_map'      => $configMap['company_google_map'] ?? '',
        'social_facebook'         => $configMap['social_facebook'] ?? '',
        'social_instagram'        => $configMap['social_instagram'] ?? '',
        'social_youtube'          => $configMap['social_youtube'] ?? '',
        'social_linkedin'         => $configMap['social_linkedin'] ?? '',
        'social_twitter'          => $configMap['social_twitter'] ?? ''
    ];

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Public Contact Info & Social Links
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $fields = [
        'company_name',
        'company_address',
        'company_email',
        'company_email_support',
        'company_mobile',
        'company_mobile_helpline',
        'company_whatsapp',
        'company_google_map',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'social_linkedin',
        'social_twitter'
    ];

    $updatedKeys = [];
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $val = trim((string)$data[$field]);
            $escVal = mysqli_real_escape_string($connection, $val);
            $escKey = mysqli_real_escape_string($connection, $field);

            $upsertSql = "INSERT INTO " . TABLE_PREFIX . "site_configuration 
                          (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                          VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)
                          ON DUPLICATE KEY UPDATE site_configuration_value = '{$escVal}', site_configuration_status = 1";
            mysqli_query($connection, $upsertSql);
            $updatedKeys[] = $field;
        }
    }

    $syncSiteName = isset($data['company_name']) ? trim((string)$data['company_name']) : '';
    $syncSiteEmail = isset($data['company_email']) ? trim((string)$data['company_email']) : '';
    $syncSiteMobile = isset($data['company_mobile']) ? trim((string)$data['company_mobile']) : '';
    $syncSiteAddress = isset($data['company_address']) ? trim((string)$data['company_address']) : '';

    $siteUpdates = [];
    if ($syncSiteName !== '') {
        $siteUpdates[] = "site_name = '" . mysqli_real_escape_string($connection, $syncSiteName) . "'";
    }
    if ($syncSiteEmail !== '') {
        $siteUpdates[] = "site_email = '" . mysqli_real_escape_string($connection, $syncSiteEmail) . "'";
    }
    if ($syncSiteMobile !== '') {
        $siteUpdates[] = "site_mobile = '" . mysqli_real_escape_string($connection, $syncSiteMobile) . "'";
    }
    if ($syncSiteAddress !== '') {
        $siteUpdates[] = "site_address = '" . mysqli_real_escape_string($connection, $syncSiteAddress) . "'";
    }

    if (!empty($siteUpdates)) {
        $siteUpdateSql = "UPDATE " . TABLE_PREFIX . "site SET " . implode(', ', $siteUpdates) . " WHERE site_id = {$siteId}";
        mysqli_query($connection, $siteUpdateSql);
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'Public contact information and social handles saved successfully.',
        'keys'   => $updatedKeys
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
