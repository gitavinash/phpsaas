<?php
/**
 * V3 Create Transporter API
 * Path: /api/v3/transporters/create.php
 * Handles creating a new transporter vendor with full validation and metadata
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
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

// Read JSON or POST body
$input = file_get_contents('php://input');
$data  = json_decode($input, true) ?? $_POST;

$title         = trim($data['title'] ?? ($data['name'] ?? ''));
$contactPerson = trim($data['contact_person'] ?? '');
$email         = trim($data['email'] ?? '');
$mobile        = trim($data['mobile'] ?? ($data['phone'] ?? ''));
$altMobile     = trim($data['alt_mobile'] ?? '');
$description   = trim($data['description'] ?? '');
$address       = trim($data['address'] ?? '');
$city          = trim($data['city'] ?? '');
$pincode       = trim($data['pincode'] ?? ($data['pinCode'] ?? ''));
$serviceArea   = trim($data['service_state'] ?? ($data['serviceArea'] ?? ($data['service_area'] ?? '')));
$fleetTypes    = $data['fleet_types'] ?? [];

if (is_array($fleetTypes)) {
    $fleetTypesStr = implode(', ', array_filter(array_map('trim', $fleetTypes)));
} else {
    $fleetTypesStr = trim((string)$fleetTypes);
}

// Validation
$errors = [];

if (empty($title)) {
    $errors[] = 'Transporter / Vendor name is required.';
}

if (empty($mobile)) {
    $errors[] = 'Contact mobile number is required.';
}

if (empty($city)) {
    $errors[] = 'Transporter base city is required.';
}

if (empty($address)) {
    $errors[] = 'Operational address is required.';
}

if (empty($pincode)) {
    $errors[] = 'Postal / Pin code is required.';
}

if (empty($serviceArea)) {
    $errors[] = 'At least one service area / operational state is required.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => implode(' ', $errors),
        'errors' => $errors
    ]);
    exit;
}

// Build rich description if extra fleet/contact fields are provided
$fullDescription = $description;
$extraMeta = [];
if (!empty($contactPerson)) {
    $extraMeta[] = "Contact Person: " . $contactPerson;
}
if (!empty($altMobile)) {
    $extraMeta[] = "Alternate Contact: " . $altMobile;
}
if (!empty($fleetTypesStr)) {
    $extraMeta[] = "Fleet Types: " . $fleetTypesStr;
}

if (!empty($extraMeta)) {
    $metaNote = implode(' | ', $extraMeta);
    if (!empty($fullDescription)) {
        $fullDescription .= "\n[" . $metaNote . "]";
    } else {
        $fullDescription = $metaNote;
    }
}

// Sanitize inputs
$titleEsc       = mysqli_real_escape_string($connection, $title);
$emailEsc       = mysqli_real_escape_string($connection, $email);
$mobileEsc      = mysqli_real_escape_string($connection, $mobile);
$descriptionEsc = mysqli_real_escape_string($connection, $fullDescription);
$addressEsc     = mysqli_real_escape_string($connection, $address);
$cityEsc        = mysqli_real_escape_string($connection, $city);
$pincodeEsc     = mysqli_real_escape_string($connection, $pincode);
$serviceAreaEsc = mysqli_real_escape_string($connection, $serviceArea);

// Check duplicate transporter under same site_id
$dupCheckSql = "SELECT transporter_id FROM {$tblPrefix}transporter 
                WHERE transporter_title = '{$titleEsc}' 
                AND transporter_city = '{$cityEsc}' 
                AND transporter_site_id = {$siteId}
                LIMIT 1";
$dupRes = mysqli_query($connection, $dupCheckSql);

if ($dupRes && mysqli_num_rows($dupRes) > 0) {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'msg'    => "A transporter named '{$title}' in '{$city}' already exists in the directory."
    ]);
    exit;
}

// Insert into database
$insertSql = "INSERT INTO {$tblPrefix}transporter (
    transporter_site_id,
    transporter_title,
    transporter_email,
    transporter_mobile,
    transporter_description,
    transporter_address,
    transporter_city,
    transporter_pincode,
    transporter_service_state,
    transporter_created_by
) VALUES (
    {$siteId},
    '{$titleEsc}',
    '{$emailEsc}',
    '{$mobileEsc}',
    '{$descriptionEsc}',
    '{$addressEsc}',
    '{$cityEsc}',
    '{$pincodeEsc}',
    '{$serviceAreaEsc}',
    {$userId}
)";

if (mysqli_query($connection, $insertSql)) {
    $newId = mysqli_insert_id($connection);
    http_response_code(201);
    echo json_encode([
        'status'         => 'success',
        'msg'            => 'Transporter added to directory successfully!',
        'transporter_id' => $newId,
        'data'           => [
            'id'             => $newId,
            'title'          => $title,
            'contact_person' => $contactPerson,
            'email'          => $email,
            'mobile'         => $mobile,
            'alt_mobile'     => $altMobile,
            'city'           => $city,
            'pincode'        => $pincode,
            'service_state'  => $serviceArea,
            'fleet_types'    => $fleetTypesStr
        ]
    ]);
} else {
    $dbError = mysqli_error($connection);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to save transporter to database. Please try again.',
        'debug'  => $dbError
    ]);
}
