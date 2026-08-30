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
    'company_contact_person',
    'site_mobile',
    'site_email',
    'company_whatsapp_number',
    'company_intro',
    'company_address',
    'company_google_map',
    'company_social_media',
    'company_site_domain',
    // Fallback legacy keys
    'owner_name',
    'mobile',
    'company_mobile',
    'email',
    'company_email',
    'whatsapp',
    'intro',
    'address',
    'maps_url',
    'company_google_map',
    'site_domain',
    'facebook',
    'social_facebook',
    'instagram',
    'social_instagram',
    'x',
    'social_twitter',
    'linkedin',
    'social_linkedin',
    'youtube',
    'social_youtube'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Contact Details & Site Company Info
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $escKeys = "'" . implode("','", array_map(function ($k) use ($connection) {
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

    $siteRes = mysqli_query($connection, "SELECT site_name, site_description, site_domain FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
    $siteRow = $siteRes ? mysqli_fetch_assoc($siteRes) : [];

    $siteDomainDefault = $siteRow['site_domain'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : '');
    $companySiteDomain = !empty($configMap['company_site_domain']) ? $configMap['company_site_domain'] : (!empty($configMap['site_domain']) ? $configMap['site_domain'] : $siteDomainDefault);

    $companyName    = $configMap['company_name'] ?? ($siteRow['site_name'] ?? '');
    $contactPerson  = $configMap['company_contact_person'] ?? ($configMap['owner_name'] ?? '');
    $siteMobile     = $configMap['site_mobile'] ?? ($configMap['mobile'] ?? ($configMap['company_mobile'] ?? ''));
    $siteEmail      = $configMap['site_email'] ?? ($configMap['email'] ?? ($configMap['company_email'] ?? ''));
    $whatsappNumber = $configMap['company_whatsapp_number'] ?? ($configMap['whatsapp'] ?? ($configMap['company_whatsapp_number'] ?? $siteMobile));
    $intro          = $configMap['company_intro'] ?? ($configMap['intro'] ?? ($siteRow['site_description'] ?? ''));
    $address        = $configMap['company_address'] ?? ($configMap['address'] ?? '');
    $gmap           = $configMap['company_google_map'] ?? ($configMap['maps_url'] ?? ($configMap['company_google_map'] ?? ''));

    // Social media JSON
    $socialMedia = [];
    if (!empty($configMap['company_social_media'])) {
        $decoded = json_decode($configMap['company_social_media'], true);
        if (is_array($decoded)) {
            $socialMedia = $decoded;
        }
    }

    $facebook  = $socialMedia['facebook'] ?? ($configMap['facebook'] ?? ($configMap['social_facebook'] ?? ''));
    $instagram = $socialMedia['instagram'] ?? ($configMap['instagram'] ?? ($configMap['social_instagram'] ?? ''));
    $x         = $socialMedia['x'] ?? ($configMap['x'] ?? ($configMap['social_twitter'] ?? ''));
    $linkedin  = $socialMedia['linkedin'] ?? ($configMap['linkedin'] ?? ($configMap['social_linkedin'] ?? ''));
    $youtube   = $socialMedia['youtube'] ?? ($configMap['youtube'] ?? ($configMap['social_youtube'] ?? ''));

    $socialMedia = [
        'facebook'  => $facebook,
        'instagram' => $instagram,
        'x'         => $x,
        'linkedin'  => $linkedin,
        'youtube'   => $youtube
    ];

    $data = [
        'company_name'            => $companyName,
        'company_contact_person'  => $contactPerson,
        'site_mobile'             => $siteMobile,
        'site_email'              => $siteEmail,
        'company_whatsapp_number' => $whatsappNumber,
        'company_intro'           => $intro,
        'company_address'         => $address,
        'company_google_map'            => $gmap,
        'company_site_domain'     => $companySiteDomain,
        'company_social_media'    => $socialMedia,
        // Direct keys for convenience
        'facebook'                => $facebook,
        'instagram'               => $instagram,
        'x'                       => $x,
        'linkedin'                => $linkedin,
        'youtube'                 => $youtube,
        // Aliases for compatibility
        'owner_name'              => $contactPerson,
        'mobile'                  => $siteMobile,
        'email'                   => $siteEmail,
        'whatsapp'                => $whatsappNumber,
        'intro'                   => $intro,
        'address'                 => $address,
        'maps_url'                => $gmap,
        'site_domain'             => $companySiteDomain
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

    // Social media JSON object assembly
    $socialInput = $data['company_social_media'] ?? [];
    if (!is_array($socialInput)) {
        $socialInput = json_decode((string)$socialInput, true) ?: [];
    }

    $socialMedia = [
        'facebook'  => trim((string)($data['facebook'] ?? ($socialInput['facebook'] ?? ''))),
        'instagram' => trim((string)($data['instagram'] ?? ($socialInput['instagram'] ?? ''))),
        'x'         => trim((string)($data['x'] ?? ($socialInput['x'] ?? ''))),
        'linkedin'  => trim((string)($data['linkedin'] ?? ($socialInput['linkedin'] ?? ''))),
        'youtube'   => trim((string)($data['youtube'] ?? ($socialInput['youtube'] ?? ''))),
    ];
    $companySocialMediaJson = json_encode($socialMedia, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Target predefined keys
    $targetKeys = [
        'company_name'            => trim((string)($data['company_name'] ?? '')),
        'company_contact_person'  => trim((string)($data['company_contact_person'] ?? ($data['owner_name'] ?? ''))),
        'site_mobile'             => trim((string)($data['site_mobile'] ?? ($data['mobile'] ?? ''))),
        'site_email'              => trim((string)($data['site_email'] ?? ($data['email'] ?? ''))),
        'company_whatsapp_number' => trim((string)($data['company_whatsapp_number'] ?? ($data['whatsapp'] ?? ''))),
        'company_site_domain'     => trim((string)($data['company_site_domain'] ?? ($data['site_domain'] ?? ''))),
        'company_intro'           => trim((string)($data['company_intro'] ?? ($data['intro'] ?? ''))),
        'company_address'         => trim((string)($data['company_address'] ?? ($data['address'] ?? ''))),
        'company_google_map'            => trim((string)($data['company_google_map'] ?? ($data['maps_url'] ?? ''))),
        'company_social_media'    => $companySocialMediaJson
    ];

    $updatedKeys = [];
    foreach ($targetKeys as $key => $val) {
        $escKey = mysqli_real_escape_string($connection, $key);
        $escVal = mysqli_real_escape_string($connection, $val);

        $checkQuery = mysqli_query($connection, "
            SELECT site_configuration_id 
            FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key = '{$escKey}' 
            LIMIT 1
        ");

        if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
            $row = mysqli_fetch_assoc($checkQuery);
            $cfgId = (int)$row['site_configuration_id'];
            mysqli_query($connection, "
                UPDATE {$tblPrefix}site_configuration 
                SET site_configuration_value = '{$escVal}', 
                    site_configuration_status = 1 
                WHERE site_configuration_id = {$cfgId}
            ");
        } else {
            mysqli_query($connection, "
                INSERT INTO {$tblPrefix}site_configuration 
                    (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
                VALUES 
                    ({$siteId}, '{$escKey}', '{$escVal}', 1)
            ");
        }
        $updatedKeys[] = $key;
    }

    // Optionally update site_name in site table if company_name was provided
    if (!empty($targetKeys['company_name'])) {
        $escSiteName = mysqli_real_escape_string($connection, $targetKeys['company_name']);
        mysqli_query($connection, "UPDATE {$tblPrefix}site SET site_name = '{$escSiteName}' WHERE site_id = {$siteId}");
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'Public contact information and business profile saved successfully.',
        'keys'   => $updatedKeys
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
