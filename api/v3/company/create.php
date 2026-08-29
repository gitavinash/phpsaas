<?php
/**
 * V3 Create Company API
 * Endpoint: /api/v3/company/create.php
 * Method: POST (multipart/form-data or json)
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
$siteId    = defined('SITE_ID') && (int)SITE_ID > 0 ? (int) SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
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

$currentUserId = defined('USER_ID') ? (int)USER_ID : (int)($_SESSION['user_id'] ?? 0);
if ($currentUserId <= 0 && isset($_COOKIE['user_id'])) {
    $currentUserId = (int)base64_decode($_COOKIE['user_id']);
}

$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?: [];

// Gather Inputs (check $_POST, $_REQUEST, and JSON)
$name        = trim($_POST['name'] ?? ($_REQUEST['name'] ?? ($jsonData['name'] ?? ($jsonData['company_name'] ?? ''))));
$description = trim($_POST['description'] ?? ($_REQUEST['description'] ?? ($jsonData['description'] ?? '')));
$email       = trim($_POST['email'] ?? ($_REQUEST['email'] ?? ($jsonData['email'] ?? '')));
$mobile      = trim($_POST['mobile'] ?? ($_REQUEST['mobile'] ?? ($jsonData['mobile'] ?? '')));
$companyType = trim($_POST['companyType'] ?? ($_POST['company_type'] ?? ($_REQUEST['companyType'] ?? ($jsonData['companyType'] ?? ($jsonData['company_type'] ?? 'private')))));
$companyCode = trim($_POST['companyCode'] ?? ($_POST['company_code'] ?? ($_REQUEST['companyCode'] ?? ($jsonData['companyCode'] ?? ($jsonData['company_code'] ?? '')))));
$gst         = trim($_POST['gst'] ?? ($_REQUEST['gst'] ?? ($jsonData['gst'] ?? '')));
$pan         = trim($_POST['pan'] ?? ($_REQUEST['pan'] ?? ($jsonData['pan'] ?? '')));
$cin         = trim($_POST['cin'] ?? ($_REQUEST['cin'] ?? ($jsonData['cin'] ?? '')));

// Address Inputs
$address     = trim($_POST['address'] ?? ($_REQUEST['address'] ?? ($jsonData['address'] ?? '')));
$stateId     = (int)($_POST['state'] ?? ($_POST['state_id'] ?? ($_REQUEST['state'] ?? ($jsonData['state'] ?? ($jsonData['state_id'] ?? 0)))));
$cityId      = (int)($_POST['city'] ?? ($_POST['city_id'] ?? ($_REQUEST['city'] ?? ($jsonData['city'] ?? ($jsonData['city_id'] ?? 0)))));
$pinCode     = trim($_POST['pinCode'] ?? ($_POST['pincode'] ?? ($_REQUEST['pinCode'] ?? ($jsonData['pinCode'] ?? ($jsonData['pincode'] ?? '')))));
$countryId   = 101; // India

// Validations
if (empty($name)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => 'Company legal name is required.', 'field' => 'name']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => 'Valid official email address is required.', 'field' => 'email']);
    exit;
}

if (empty($mobile)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => 'Contact mobile number is required.', 'field' => 'mobile']);
    exit;
}

if (empty($companyCode)) {
    $companyCode = 'COMP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
}

// Handle Logo Upload via imageUploader.php if present
$featureImage = '';
if (!empty($_FILES['featureImage']['tmp_name']) && is_uploaded_file($_FILES['featureImage']['tmp_name'])) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);

    if (!defined('ROOT')) define('ROOT', $docRoot);
    if (!defined('MODULES')) define('MODULES', $docRoot . '/lib/modules/');
    if (!defined('LOGIN_CHECK')) define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
    if (!defined('PNG_IMAGE')) define('PNG_IMAGE', 'image/png');
    if (!defined('JPEG_IMAGE')) define('JPEG_IMAGE', 'image/jpeg');
    if (!defined('JPG_IMAGE')) define('JPG_IMAGE', 'image/jpg');
    if (!defined('GIF_IMAGE')) define('GIF_IMAGE', 'image/gif');
    if (!defined('IMAGE_COMPRESSION_QUALITY')) define('IMAGE_COMPRESSION_QUALITY', 80);
    if (!defined('IMAGE_THUMBNAIL_WIDTH')) define('IMAGE_THUMBNAIL_WIDTH', 300);
    if (!defined('IMAGE_THUMBNAIL_HEIGHT')) define('IMAGE_THUMBNAIL_HEIGHT', 300);
    if (!defined('USER_ID')) define('USER_ID', $currentUserId);
    if (!defined('SITE_ID')) define('SITE_ID', $siteId);
    if (!defined('SITE_DOMAIN')) {
        $siteDomain = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : 'default';
        define('SITE_DOMAIN', $siteDomain);
    }

    if (file_exists(MODULES . 'imageUploader.php')) {
        require_once MODULES . 'imageUploader.php';
        if (!empty($image['featureImage'])) {
            $featureImage = $image['featureImage'];
        }
    }
}

// Escapes
$escName        = mysqli_real_escape_string($connection, $name);
$escDesc        = mysqli_real_escape_string($connection, $description);
$escEmail       = mysqli_real_escape_string($connection, $email);
$escMobile      = mysqli_real_escape_string($connection, $mobile);
$escType        = mysqli_real_escape_string($connection, $companyType);
$escCode        = mysqli_real_escape_string($connection, $companyCode);
$escGst         = mysqli_real_escape_string($connection, $gst);
$escPan         = mysqli_real_escape_string($connection, $pan);
$escCin         = mysqli_real_escape_string($connection, $cin);
$escLogo        = mysqli_real_escape_string($connection, $featureImage);
$escAddress     = mysqli_real_escape_string($connection, $address);
$escPin         = mysqli_real_escape_string($connection, $pinCode);

mysqli_begin_transaction($connection);

try {
    // 1. Insert Address if provided
    $addressId = 0;
    if (!empty($address) || $stateId > 0 || $cityId > 0 || !empty($pinCode)) {
        $addrQuery = "
            INSERT INTO {$tblPrefix}address (
                address_site_id, address_user_id, address_name, address_area,
                address_city_id, address_state_id, address_country_id, address_pin_code,
                address_type, address_status
            ) VALUES (
                {$siteId}, {$currentUserId}, '{$escName}', '{$escAddress}',
                {$cityId}, {$stateId}, {$countryId}, '{$escPin}',
                'work', 1
            )
        ";
        if (mysqli_query($connection, $addrQuery)) {
            $addressId = mysqli_insert_id($connection);
        }
    }

    // 2. Insert Company Record
    $compQuery = "
        INSERT INTO {$tblPrefix}company (
            company_code, company_site_id, company_user_id, company_address_id,
            company_logo, company_title, company_description, company_gst,
            company_pan, company_cin, company_email, company_mobile,
            company_type, company_status
        ) VALUES (
            '{$escCode}', {$siteId}, {$currentUserId}, {$addressId},
            '{$escLogo}', '{$escName}', '{$escDesc}', '{$escGst}',
            '{$escPan}', '{$escCin}', '{$escEmail}', '{$escMobile}',
            '{$escType}', 1
        )
    ";

    if (!mysqli_query($connection, $compQuery)) {
        throw new Exception(mysqli_error($connection));
    }

    $companyId = mysqli_insert_id($connection);
    mysqli_commit($connection);

    http_response_code(201);
    echo json_encode([
        'status'     => 'success',
        'msg'        => 'Company registered successfully!',
        'company_id' => $companyId,
        'encoded_id' => base64_encode((string)$companyId)
    ]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to register company: ' . $e->getMessage()
    ]);
}
