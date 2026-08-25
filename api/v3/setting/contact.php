<?php

/**
 * V3 Public Contact & Company Profile Settings API Endpoint
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

$cfgKey = 'setting_company_profile';

// --------------------------------------------------------------------------
// 1. GET: Fetch Public Contact & Company Information
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $sql = "SELECT site_configuration_value 
            FROM " . TABLE_PREFIX . "site_configuration 
            WHERE site_configuration_key = '{$cfgKey}' 
              AND site_configuration_site_id = {$siteId} 
              AND site_configuration_status = 1 
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    $data = [];

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $decoded = json_decode($row['site_configuration_value'] ?? '{}', true);
        if (is_array($decoded)) {
            $data = [
                'company_name'    => $decoded['companyName'] ?? '',
                'owner_name'      => $decoded['companyOwnerName'] ?? '',
                'intro'           => $decoded['companyIntro'] ?? '',
                'email'           => $decoded['companyEmail'] ?? '',
                'email_notify'    => $decoded['companyEmailNotify'] ?? '',
                'mobile'          => $decoded['companyMobile'] ?? '',
                'mobile_notify'   => $decoded['companyMobileNotify'] ?? '',
                'whatsapp'        => $decoded['companyWhatsapp'] ?? '',
                'whatsapp_notify' => $decoded['companyWhatsappNotify'] ?? '',
                'address'         => $decoded['companyAddress'] ?? '',
                'maps_url'        => $decoded['companyMapsUrl'] ?? '',
                'facebook'        => $decoded['companyFacebookProfile'] ?? '',
                'x'               => $decoded['companyTwitterProfile'] ?? ($decoded['companyTwitter'] ?? ''),
                'linkedin'        => $decoded['companyLinkedinProfile'] ?? '',
                'instagram'       => $decoded['companyInstagramProfile'] ?? '',
                'youtube'         => $decoded['companyYoutubeProfile'] ?? ''
            ];
        }
    }

    // Fallback company name from site_name if empty
    if (empty($data['company_name'])) {
        $siteRes = mysqli_query($connection, "SELECT site_name FROM " . TABLE_PREFIX . "site WHERE site_id = {$siteId} LIMIT 1");
        if ($siteRes && mysqli_num_rows($siteRes) > 0) {
            $siteRow = mysqli_fetch_assoc($siteRes);
            $data['company_name'] = $siteRow['site_name'] ?? '';
        }
    }

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Public Contact Information
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $companyName    = trim($data['company_name'] ?? '');
    $ownerName      = trim($data['owner_name'] ?? '');
    $intro          = trim($data['intro'] ?? '');
    $email          = trim($data['email'] ?? '');
    $emailNotify    = trim($data['email_notify'] ?? '');
    $mobile         = trim($data['mobile'] ?? '');
    $mobileNotify   = trim($data['mobile_notify'] ?? '');
    $whatsapp       = trim($data['whatsapp'] ?? '');
    $whatsappNotify = trim($data['whatsapp_notify'] ?? '');
    $address        = trim($data['address'] ?? '');
    $mapsUrl        = trim($data['maps_url'] ?? '');
    $facebook       = trim($data['facebook'] ?? '');
    $xTwitter       = trim($data['x'] ?? ($data['twitter'] ?? ''));
    $linkedin       = trim($data['linkedin'] ?? '');
    $instagram      = trim($data['instagram'] ?? '');
    $youtube        = trim($data['youtube'] ?? '');

    if (empty($companyName)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'warning',
            'msg'    => 'Business / Company Name is required.'
        ]);
        exit;
    }

    if (empty($mobile)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'warning',
            'msg'    => 'Primary Customer Helpline / Mobile number is required.'
        ]);
        exit;
    }

    $profileObject = [
        'companyName'             => $companyName,
        'companyOwnerName'        => $ownerName,
        'companyIntro'            => $intro,
        'companyEmail'            => $email,
        'companyEmailNotify'      => $emailNotify,
        'companyMobile'           => $mobile,
        'companyMobileNotify'     => $mobileNotify,
        'companyWhatsapp'         => $whatsapp,
        'companyWhatsappNotify'   => $whatsappNotify,
        'companyAddress'          => $address,
        'companyMapsUrl'          => $mapsUrl,
        'companyFacebookProfile'  => $facebook,
        'companyTwitterProfile'   => $xTwitter,
        'companyLinkedinProfile'  => $linkedin,
        'companyInstagramProfile' => $instagram,
        'companyYoutubeProfile'   => $youtube
    ];

    $jsonPayload = json_encode($profileObject, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $escapedJson = mysqli_real_escape_string($connection, $jsonPayload);

    $check = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key = '{$cfgKey}' AND site_configuration_site_id = {$siteId} LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $cfgId = (int)$row['site_configuration_id'];
        $saveSql = "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value = '{$escapedJson}', site_configuration_status = 1 WHERE site_configuration_id = {$cfgId} AND site_configuration_site_id = {$siteId}";
    } else {
        $saveSql = "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES ({$siteId}, '{$cfgKey}', '{$escapedJson}', 1, 0)";
    }

    if (mysqli_query($connection, $saveSql)) {
        // Also synchronize legacy discrete keys if needed
        $syncKeys = [
            'site_mobile'     => $mobile,
            'site_email'      => $email,
            'site_address'    => $address,
            'site_admin_name' => $ownerName
        ];

        foreach ($syncKeys as $sKey => $sVal) {
            if ($sVal !== '') {
                $escVal = mysqli_real_escape_string($connection, $sVal);
                $chkKey = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key = '{$sKey}' AND site_configuration_site_id = {$siteId} LIMIT 1");
                if ($chkKey && mysqli_num_rows($chkKey) > 0) {
                    $kRow = mysqli_fetch_assoc($chkKey);
                    $kId = (int)$kRow['site_configuration_id'];
                    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value = '{$escVal}', site_configuration_status = 1 WHERE site_configuration_id = {$kId} AND site_configuration_site_id = {$siteId}");
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'msg'    => 'Public contact information and business profile updated successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Database error while saving contact settings: ' . mysqli_error($connection)
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'error',
    'msg'    => 'Method not allowed.'
]);
