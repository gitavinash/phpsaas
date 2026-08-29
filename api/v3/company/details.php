<?php
/**
 * V3 Company Details API
 * Endpoint: /api/v3/company/details.php
 * Method: GET
 * Params: id (encoded or numeric company_id)
 */

header("Content-type: application/json; charset=utf-8");

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
$siteId    = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
if ($siteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $siteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($siteId <= 0 && isset($connection)) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if (!empty($host)) {
        $escapedHost = mysqli_real_escape_string($connection, $host);
        $sQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
        if ($sQuery && $sRow = mysqli_fetch_assoc($sQuery)) {
            $siteId = (int)$sRow['site_id'];
        }
    }
}

$rawId = $_GET['id'] ?? ($_GET['company_id'] ?? '');
$companyId = 0;

if (is_numeric($rawId) && (int)$rawId > 0) {
    $companyId = (int)$rawId;
} elseif (!empty($rawId)) {
    $decoded = base64_decode($rawId, true);
    if ($decoded && is_numeric($decoded)) {
        $companyId = (int)$decoded;
    }
}

if ($companyId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'A valid company ID is required.'
    ]);
    exit;
}

$siteFilter = $siteId > 0 ? "AND c.company_site_id = {$siteId}" : "";

$query = "
    SELECT 
        c.company_id,
        c.company_code,
        c.company_title,
        c.company_description,
        c.company_email,
        c.company_mobile,
        c.company_gst,
        c.company_pan,
        c.company_cin,
        c.company_type,
        c.company_logo,
        c.company_status,
        c.company_address_id,
        a.address_area,
        a.address_pin_code,
        a.address_city_id,
        a.address_state_id,
        a.address_country_id,
        ct.city_name,
        st.state_name
    FROM {$tblPrefix}company c
    LEFT JOIN {$tblPrefix}address a ON c.company_address_id = a.address_id
    LEFT JOIN {$tblPrefix}city ct ON a.address_city_id = ct.city_id
    LEFT JOIN {$tblPrefix}state st ON a.address_state_id = st.state_id
    WHERE c.company_id = {$companyId} {$siteFilter}
    LIMIT 1
";

$res = mysqli_query($connection, $query);

if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Company entity not found.'
    ]);
    exit;
}

$row = mysqli_fetch_assoc($res);

// Format logo URL
$logoUrl = '';
if (!empty($row['company_logo'])) {
    if (filter_var($row['company_logo'], FILTER_VALIDATE_URL)) {
        $logoUrl = $row['company_logo'];
    } elseif (strpos($row['company_logo'], '/upload/') === 0) {
        $logoUrl = $row['company_logo'];
    } elseif (strpos($row['company_logo'], 'upload/') === 0) {
        $logoUrl = '/' . $row['company_logo'];
    } elseif (defined('IMG_LIBRARY')) {
        $logoUrl = IMG_LIBRARY . ltrim($row['company_logo'], '/');
    } else {
        $logoUrl = '/upload/' . ltrim($row['company_logo'], '/');
    }
}

$companyData = [
    'id' => (int)$row['company_id'],
    'encoded_id' => base64_encode($row['company_id']),
    'code' => $row['company_code'] ?? '',
    'name' => $row['company_title'] ?? '',
    'description' => $row['company_description'] ?? '',
    'email' => $row['company_email'] ?? '',
    'mobile' => $row['company_mobile'] ?? '',
    'gst' => $row['company_gst'] ?? '',
    'pan' => $row['company_pan'] ?? '',
    'cin' => $row['company_cin'] ?? '',
    'type' => !empty($row['company_type']) ? $row['company_type'] : 'private',
    'status' => (int)($row['company_status'] ?? 1),
    'logo' => $row['company_logo'] ?? '',
    'logo_url' => $logoUrl,
    'address' => [
        'id' => (int)($row['company_address_id'] ?? 0),
        'street' => $row['address_area'] ?? '',
        'state_id' => (int)($row['address_state_id'] ?? 0),
        'state_name' => $row['state_name'] ?? '',
        'city_id' => (int)($row['address_city_id'] ?? 0),
        'city_name' => $row['city_name'] ?? '',
        'pincode' => $row['address_pin_code'] ?? '',
        'country_id' => (int)($row['address_country_id'] ?? 101)
    ]
];

echo json_encode([
    'status' => 'success',
    'company' => $companyData
]);
