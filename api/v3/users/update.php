<?php

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
$loggedInUserId = defined('USER_ID') && (int)USER_ID > 0 ? (int) USER_ID : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input)) {
    $input = $_POST;
}

$rawId = $input['id'] ?? ($input['user_id'] ?? 0);
$userId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $userId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $userId = (int)$decoded;
    }
}

if ($userId <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Valid user ID is required.']);
    exit();
}

$firstName = trim($input['first_name'] ?? '');
$lastName  = trim($input['last_name'] ?? '');
$userRole  = strtolower(trim($input['userRole'] ?? ($input['role'] ?? '')));
$mobile    = preg_replace('/[^0-9]/', '', trim($input['mobile'] ?? ''));
$email     = strtolower(trim($input['email'] ?? ''));
$company   = $input['company'] ?? null;
$warehouse = $input['warehouse'] ?? null;
$bio       = trim($input['bio'] ?? '');

if (empty($firstName)) {
    echo json_encode(['status' => 'error', 'msg' => 'First name is required.']);
    exit();
}

if (empty($userRole)) {
    echo json_encode(['status' => 'error', 'msg' => 'Please select a user role.']);
    exit();
}

// Verify user has access to this site
$verifySql = "
    SELECT sa.site_access_id, sa.site_access_type, u.user_email, u.user_mobile 
    FROM {$tblPrefix}site_access sa
    INNER JOIN {$tblPrefix}user u ON u.user_id = sa.site_access_user_id
    WHERE sa.site_access_user_id = {$userId} AND sa.site_access_site_id = {$siteId}
    LIMIT 1
";
$verifyRes = mysqli_query($connection, $verifySql);
if (!$verifyRes || mysqli_num_rows($verifyRes) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'User not found on this site.']);
    exit();
}

$safeFirstName = mysqli_real_escape_string($connection, $firstName);
$safeLastName  = mysqli_real_escape_string($connection, $lastName);
$safeUserRole  = mysqli_real_escape_string($connection, $userRole);
$displayName   = trim($safeFirstName . ' ' . $safeLastName);

// Metadata
$siteAccessMeta = [];
if (!empty($company)) {
    $siteAccessMeta['company'] = $company;
    $siteAccessMeta['warehouse'] = !empty($warehouse) ? $warehouse : 'all';
}
$safeMetaJson = mysqli_real_escape_string($connection, json_encode($siteAccessMeta));

mysqli_begin_transaction($connection);

try {
    // 1. Update profile
    $updateProfileSql = "
        UPDATE {$tblPrefix}profile 
        SET profile_first_name = '{$safeFirstName}',
            profile_last_name = '{$safeLastName}',
            profile_display_name = '{$displayName}'
        WHERE profile_user_id = {$userId}
    ";
    if (!mysqli_query($connection, $updateProfileSql)) {
        throw new Exception("Failed to update profile: " . mysqli_error($connection));
    }

    // 2. Update site access role and metadata
    $updateSiteAccessSql = "
        UPDATE {$tblPrefix}site_access 
        SET site_access_type = '{$safeUserRole}',
            site_access_meta = '{$safeMetaJson}'
        WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId}
    ";
    if (!mysqli_query($connection, $updateSiteAccessSql)) {
        throw new Exception("Failed to update site access: " . mysqli_error($connection));
    }

    // 3. Optional bio in profile_meta
    if (!empty($bio)) {
        $safeBio = mysqli_real_escape_string($connection, $bio);
        $checkBio = mysqli_query($connection, "SELECT profile_meta_id FROM {$tblPrefix}profile_meta WHERE profile_meta_profile_id = '{$userId}' AND profile_meta_key = 'bio'");
        if ($checkBio && mysqli_num_rows($checkBio) > 0) {
            mysqli_query($connection, "UPDATE {$tblPrefix}profile_meta SET profile_meta_value = '{$safeBio}' WHERE profile_meta_profile_id = '{$userId}' AND profile_meta_key = 'bio'");
        } else {
            mysqli_query($connection, "INSERT INTO {$tblPrefix}profile_meta (profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES ('{$userId}', 'bio', '{$safeBio}')");
        }
    }

    mysqli_commit($connection);

    echo json_encode([
        'status'     => 'success',
        'msg'        => 'User details updated successfully!',
        'user_id'    => $userId,
        'encoded_id' => base64_encode((string)$userId)
    ]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Error updating user: ' . $e->getMessage()
    ]);
}
