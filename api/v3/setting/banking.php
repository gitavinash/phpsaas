<?php

/**
 * V3 Bank Accounts, UPI & Billing Settings API Endpoint
 * Path: /api/v3/setting/banking.php
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

// Managed database keys from isw_site_configuration
$managedKeys = [
    'bank_name',
    'bank_ac_name',
    'bank_ac_number',
    'bank_ifsc',
    'bank_address',
    'bank_details',
    'upi_details',
    'pg_upi_address'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Bank & UPI Details
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $keyListStr = "'" . implode("','", $managedKeys) . "'";
    $sql = "SELECT site_configuration_key, site_configuration_value 
            FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key IN ({$keyListStr}) 
              AND site_configuration_status = 1";

    $res = mysqli_query($connection, $sql);
    $data = [];

    foreach ($managedKeys as $k) {
        $data[$k] = '';
    }

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }

    // Harmonize upi_details & pg_upi_address
    if (empty($data['upi_details']) && !empty($data['pg_upi_address'])) {
        $data['upi_details'] = $data['pg_upi_address'];
    }

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Save Bank & UPI Details
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $bankName      = trim($data['bank_name'] ?? '');
    $bankAcName    = trim($data['bank_ac_name'] ?? '');
    $bankAcNumber  = trim($data['bank_ac_number'] ?? '');
    $bankIfsc      = strtoupper(trim($data['bank_ifsc'] ?? ''));
    $bankAddress   = trim($data['bank_address'] ?? '');
    $bankDetails   = trim($data['bank_details'] ?? '');
    $upiDetails    = trim($data['upi_details'] ?? ($data['pg_upi_address'] ?? ''));

    $valuesToSave = [
        'bank_name'       => $bankName,
        'bank_ac_name'    => $bankAcName,
        'bank_ac_number'  => $bankAcNumber,
        'bank_ifsc'       => $bankIfsc,
        'bank_address'    => $bankAddress,
        'bank_details'    => $bankDetails,
        'upi_details'     => $upiDetails,
        'pg_upi_address'  => $upiDetails
    ];

    foreach ($valuesToSave as $cfgKey => $cfgVal) {
        $escVal = mysqli_real_escape_string($connection, (string)$cfgVal);
        $escKey = mysqli_real_escape_string($connection, (string)$cfgKey);
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
        'msg'    => 'Bank account and UPI details saved successfully.'
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
