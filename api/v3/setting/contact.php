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

$contactKeys = [
    'company_name',
    'owner_name',
    'intro',
    'company_address',
    'address',
    'company_email',
    'email',
    'email_notify',
    'company_email_support',
    'company_mobile',
    'mobile',
    'mobile_notify',
    'company_mobile_helpline',
    'company_whatsapp',
    'whatsapp',
    'whatsapp_notify',
    'company_google_map',
    'maps_url',
    'social_facebook',
    'facebook',
    'social_instagram',
    'instagram',
    'social_youtube',
    'youtube',
    'social_linkedin',
    'linkedin',
    'social_twitter',
    'x'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Contact Details & Site Company Info
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $escKeys = "'" . implode("','", array_map(function($k) use ($connection) {
        return mysqli_real_escape_string($connection, $k);
    }, $contactKeys)) . "'";

    $cfgRes = mysqli_query($connection, "
        SELECT site_configuration_key, site_configuration_value 
        FROM {$tblPrefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key IN ({$escKeys}) 
          AND site_configuration_status = 1
    ");

    $configMap = [];
    if ($cfgRes) {
        while ($r = mysqli_fetch_assoc($cfgRes)) {
            $configMap[$r['site_configuration_key']] = $r['site_configuration_value'];
        }
    }

    $siteRes = mysqli_query($connection, "SELECT site_name, site_description FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
    $siteRow = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    $companyName = $configMap['company_name'] ?? ($siteRow['site_name'] ?? '');
    $intro       = $configMap['intro'] ?? ($siteRow['site_description'] ?? '');
    $email       = $configMap['email'] ?? ($configMap['company_email'] ?? '');
    $mobile      = $configMap['mobile'] ?? ($configMap['company_mobile'] ?? '');
    $whatsapp    = $configMap['whatsapp'] ?? ($configMap['company_whatsapp'] ?? ($mobile));
    $address     = $configMap['address'] ?? ($configMap['company_address'] ?? '');
    $mapsUrl     = $configMap['maps_url'] ?? ($configMap['company_google_map'] ?? '');
    $facebook    = $configMap['facebook'] ?? ($configMap['social_facebook'] ?? '');
    $instagram   = $configMap['instagram'] ?? ($configMap['social_instagram'] ?? '');
    $x           = $configMap['x'] ?? ($configMap['social_twitter'] ?? '');
    $linkedin    = $configMap['linkedin'] ?? ($configMap['social_linkedin'] ?? '');
    $youtube     = $configMap['youtube'] ?? ($configMap['social_youtube'] ?? '');

    $data = [
        'company_name'            => $companyName,
        'owner_name'               => $configMap['owner_name'] ?? '',
        'intro'                    => $intro,
        'email'                    => $email,
        'email_notify'             => $configMap['email_notify'] ?? '',
        'company_email_support'    => $configMap['company_email_support'] ?? '',
        'mobile'                   => $mobile,
        'mobile_notify'            => $configMap['mobile_notify'] ?? '',
        'company_mobile_helpline'  => $configMap['company_mobile_helpline'] ?? '',
        'whatsapp'                 => $whatsapp,
        'whatsapp_notify'          => $configMap['whatsapp_notify'] ?? '',
        'address'                  => $address,
        'maps_url'                 => $mapsUrl,
        'facebook'                 => $facebook,
        'instagram'                => $instagram,
        'x'                        => $x,
        'linkedin'                 => $linkedin,
        'youtube'                  => $youtube,
        // Aliases for compatibility
        'company_address'          => $address,
        'company_email'            => $email,
        'company_mobile'           => $mobile,
        'company_whatsapp'         => $whatsapp,
        'company_google_map'       => $mapsUrl,
        'social_facebook'          => $facebook,
        'social_instagram'         => $instagram,
        'social_twitter'           => $x,
        'social_linkedin'          => $linkedin,
        'social_youtube'           => $youtube
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

    $updatedKeys = [];
    foreach ($data as $key => $value) {
        $val = trim((string)$value);
        $escVal = mysqli_real_escape_string($connection, $val);
        $escKey = mysqli_real_escape_string($connection, $key);

        $upsertSql = "
            INSERT INTO {$tblPrefix}site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
            VALUES 
                ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ON DUPLICATE KEY UPDATE 
                site_configuration_value = '{$escVal}', 
                site_configuration_status = 1
        ";
        mysqli_query($connection, $upsertSql);
        $updatedKeys[] = $key;
    }

    // Also mirror specific fields for standard compatibility
    $syncedAliases = [
        'company_name'    => $data['company_name'] ?? '',
        'company_address' => $data['address'] ?? ($data['company_address'] ?? ''),
        'company_email'   => $data['email'] ?? ($data['company_email'] ?? ''),
        'company_mobile'  => $data['mobile'] ?? ($data['company_mobile'] ?? ''),
        'company_whatsapp'=> $data['whatsapp'] ?? ($data['company_whatsapp'] ?? ''),
        'company_google_map' => $data['maps_url'] ?? ($data['company_google_map'] ?? ''),
        'social_facebook' => $data['facebook'] ?? ($data['social_facebook'] ?? ''),
        'social_instagram'=> $data['instagram'] ?? ($data['social_instagram'] ?? ''),
        'social_twitter'  => $data['x'] ?? ($data['social_twitter'] ?? ''),
        'social_linkedin' => $data['linkedin'] ?? ($data['social_linkedin'] ?? ''),
        'social_youtube'  => $data['youtube'] ?? ($data['social_youtube'] ?? '')
    ];

    foreach ($syncedAliases as $aliasKey => $aliasVal) {
        if ($aliasVal !== '') {
            $escVal = mysqli_real_escape_string($connection, (string)$aliasVal);
            $escKey = mysqli_real_escape_string($connection, $aliasKey);
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
    }

    // Optionally update site_name in site table if company_name was provided
    if (!empty($data['company_name'])) {
        $escSiteName = mysqli_real_escape_string($connection, trim($data['company_name']));
        mysqli_query($connection, "UPDATE {$tblPrefix}site SET site_name = '{$escSiteName}' WHERE site_id = {$siteId}");
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
