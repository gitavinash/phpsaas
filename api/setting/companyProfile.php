<?php


header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method Not Allowed'], JSON_UNESCAPED_SLASHES);
    exit;
}

require_once UTIL_FUNCTIONS;
require_once LOGIN_CHECK;

$SITE_ID  = defined('SITE_ID') ? SITE_ID : 0;
$TABLE    = TABLE_PREFIX . 'site_configuration';
$CFG_KEY  = 'setting_company_profile'; // maps to SETTING_COMPANY_PROFILE
$CFG_STAT = 1;

// -------- Parse body (supports form-encoded OR JSON) --------
$raw = file_get_contents('php://input');
$maybeJson = json_decode($raw, true);

if (json_last_error() === JSON_ERROR_NONE && is_array($maybeJson)) {
    // JSON body wins; merge in any $_POST fallback just in case
    $data = array_merge($_POST, $maybeJson);
} else {
    // standard form submit
    $data = $_POST;
}

// Helper getter
$get = function (string $key, string $default = '') use ($data): string {
    return isset($data[$key]) ? trim((string)$data[$key]) : $default;
};

// -------- Read fields (must match your HTML names) --------
$company_name     = $get('company_name');
$owner_name       = $get('owner_name');
$intro            = $get('intro');
$email_notify     = $get('email_notify');
$mobile           = $get('mobile');
$mobile_notify    = $get('mobile_notify');
$whatsapp         = $get('whatsapp');
$whatsapp_notify  = $get('whatsapp_notify');
$address          = $get('address');
$facebook         = $get('facebook');
$x                = $get('x'); // X (formerly Twitter)
$linkedin         = $get('linkedin');
$instagram        = $get('instagram');
$youtube          = $get('youtube');

// Optional legacy email slot (not in current form, harmless if empty)
$account_email    = $get('email');


// ---- Normalize Indian numbers BEFORE validation ----
$mobile          = normalizeIndianMobile($mobile);
$mobile_notify   = $mobile_notify !== '' ? normalizeIndianMobile($mobile_notify) : '';
$whatsapp        = normalizeIndianMobile($whatsapp);
$whatsapp_notify = $whatsapp_notify !== '' ? normalizeIndianMobile($whatsapp_notify) : '';

// -------- Validate --------
$errors = [];

if ($company_name === '') {
    $errors[] = 'Company name is required.';
}
if ($owner_name === '') {
    $errors[] = 'Owner / Proprietor name is required.';
}
if ($intro === '') {
    $errors[] = 'Short intro is required.';
}
if (mb_strlen($intro) > 300) {
    $errors[] = 'Intro must be 300 characters or fewer.';
}
if ($mobile === '') {
    $errors[] = 'Primary mobile is required.';
}
if ($whatsapp === '') {
    $errors[] = 'Primary WhatsApp is required.';
}
if ($address === '') {
    $errors[] = 'Registered address is required.';
}

if ($email_notify !== '' && !filter_var($email_notify, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Notification email is invalid.';
}

// Light URL checks for socials (skip empty)
$urls = [
    'facebook' => $facebook,
    'x'        => $x,
    'linkedin' => $linkedin,
    'instagram' => $instagram,
    'youtube'  => $youtube,
];
foreach ($urls as $label => $val) {
    if ($val !== '' && !filter_var($val, FILTER_VALIDATE_URL)) {
        $errors[] = ucfirst($label) . ' URL is invalid.';
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'warning',
        'msg'    => 'Please fix the highlighted errors.',
        'errors' => $errors
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// -------- Build profile object (exact schema) --------
$profile = [
    'companyName'     => $company_name,
    'companyOwnerName'       => $owner_name,
    'companyIntro'            => $intro,
    'companyEmail'            => $account_email, // optional/legacy
    'companyEmailNotify'     => $email_notify,
    'companyMobile'           => $mobile,
    'companyMobileNotify'    => $mobile_notify,
    'companyWhatsapp'         => $whatsapp,
    'companyWhatsappNotify'  => $whatsapp_notify,
    'companyAddress'          => $address,
    'companyFacebookProfile'         => $facebook,
    'companyTwitterProfile'                => $x,
    'companyLinkedinProfile'         => $linkedin,
    'companyInstagramProfile'        => $instagram,
    'companyYoutubeProfile'          => $youtube,
];

$json = json_encode($profile, JSON_UNESCAPED_SLASHES);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Failed to encode profile JSON.'], JSON_UNESCAPED_SLASHES);
    exit;
}

$sql = "
    INSERT INTO `$TABLE`
        (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status)
    VALUES
        (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        site_configuration_value = VALUES(site_configuration_value),
        site_configuration_status = VALUES(site_configuration_status),
        site_configuration_update = NOW()
";

if ($checkConfiguration = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key='{$CFG_KEY}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkConfiguration) != 0) {
        extract(mysqli_fetch_assoc($checkConfiguration));
        $query = "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value='{$json}', site_configuration_status=1 WHERE site_configuration_id='{$site_configuration_id}' AND site_configuration_site_id=" . SITE_ID;
    } else {
        $query = "INERT INTO " . TABLE_PREFIX . "site_configuration(site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES(" . SITE_ID . ", '{$CFG_KEY}',  '{$json}', 1, 0)";
    }
}

if (mysqli_query($connection, $query)) {
    $httpStatus = 201;
    $status = 'success';
    $msg = 'Company profile updated successfully.';
}


http_response_code(((isset($httpStatus)) ? $httpStatus : 500));
echo json_encode([
    'status' => ((isset($status)) ? $status : 'error'),
    'msg'    => ((isset($msg)) ? $msg : "Something went wrong while saving configuration"),
], JSON_UNESCAPED_SLASHES);