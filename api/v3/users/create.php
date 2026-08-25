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
$siteAppId = defined('SITE_APP_ID') && (int)SITE_APP_ID > 0 ? (int) SITE_APP_ID : (isset($_SESSION['site_app_id']) ? (int)$_SESSION['site_app_id'] : 0);
$loggedInUserId = defined('USER_ID') && (int)USER_ID > 0 ? (int) USER_ID : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method not allowed. Use POST.']);
    exit();
}

// Support raw JSON or form-data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input)) {
    $input = $_POST;
}

$firstName = trim($input['first_name'] ?? '');
$lastName  = trim($input['last_name'] ?? '');
$email     = strtolower(trim($input['email'] ?? ''));
$mobile    = preg_replace('/[^0-9]/', '', trim($input['mobile'] ?? ''));
$userRole  = strtolower(trim($input['userRole'] ?? ($input['role'] ?? '')));
$password  = trim($input['password'] ?? '');
$company   = $input['company'] ?? null;
$warehouse = $input['warehouse'] ?? null;

// Validation
if (empty($firstName)) {
    echo json_encode(['status' => 'error', 'msg' => 'First name is required.']);
    exit();
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'msg' => 'Please enter a valid email address.']);
    exit();
}

if (empty($mobile) || strlen($mobile) < 7 || strlen($mobile) > 15) {
    echo json_encode(['status' => 'error', 'msg' => 'Please enter a valid 10-digit mobile number.']);
    exit();
}

if (empty($userRole)) {
    echo json_encode(['status' => 'error', 'msg' => 'Please select a user role.']);
    exit();
}

$safeFirstName = mysqli_real_escape_string($connection, $firstName);
$safeLastName  = mysqli_real_escape_string($connection, $lastName);
$safeEmail     = mysqli_real_escape_string($connection, $email);
$safeMobile    = mysqli_real_escape_string($connection, $mobile);
$safeUserRole  = mysqli_real_escape_string($connection, $userRole);
$profileDisplayName = trim($safeFirstName . ' ' . $safeLastName);

// Metadata (e.g. for company/warehouse or additional fields)
$siteAccessMeta = [];
if (!empty($company)) {
    $siteAccessMeta['company'] = $company;
    $siteAccessMeta['warehouse'] = !empty($warehouse) ? $warehouse : 'all';
}
$safeMetaJson = mysqli_real_escape_string($connection, json_encode($siteAccessMeta));

// 1. Check if user already has access to this site
$checkSiteAccess = mysqli_query($connection, "
    SELECT u.user_id 
    FROM {$tblPrefix}user u
    INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
    WHERE (u.user_email = '{$safeEmail}' OR u.user_mobile = '{$safeMobile}')
      AND sa.site_access_site_id = {$siteId}
      AND sa.site_access_status = 1
    LIMIT 1
");

if ($checkSiteAccess && mysqli_num_rows($checkSiteAccess) > 0) {
    echo json_encode([
        'status' => 'warning',
        'msg'    => 'A team member with this email or mobile already exists on this site.'
    ]);
    exit();
}

mysqli_begin_transaction($connection);

try {
    // 2. Check if user already exists in global user table
    $checkGlobalUser = mysqli_query($connection, "SELECT user_id, user_email, user_mobile FROM {$tblPrefix}user WHERE user_email = '{$safeEmail}' OR user_mobile = '{$safeMobile}' LIMIT 1");
    
    $userId = 0;
    if ($checkGlobalUser && mysqli_num_rows($checkGlobalUser) > 0) {
        $existingUser = mysqli_fetch_assoc($checkGlobalUser);
        $userId = (int)$existingUser['user_id'];

        // Update profile details
        mysqli_query($connection, "
            UPDATE {$tblPrefix}profile 
            SET profile_first_name = '{$safeFirstName}', 
                profile_last_name = '{$safeLastName}', 
                profile_display_name = '{$profileDisplayName}'
            WHERE profile_user_id = {$userId}
        ");

        // Insert or activate site access
        $checkExistingAccess = mysqli_query($connection, "SELECT site_access_id FROM {$tblPrefix}site_access WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId} LIMIT 1");
        if ($checkExistingAccess && mysqli_num_rows($checkExistingAccess) > 0) {
            mysqli_query($connection, "
                UPDATE {$tblPrefix}site_access 
                SET site_access_type = '{$safeUserRole}', 
                    site_access_status = 1,
                    site_access_meta = '{$safeMetaJson}'
                WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId}
            ");
        } else {
            mysqli_query($connection, "
                INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
                VALUES ({$siteId}, {$userId}, '{$safeUserRole}', 1, '{$safeMetaJson}')
            ");
        }
    } else {
        // Create new user account
        if (empty($password)) {
            $password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%^&*'), 0, 10);
        }

        $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . time());
        $encPassword = hash('sha256', $saltKey . $password);
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . $lastName));
        if (empty($username)) {
            $username = 'user' . time();
        } else {
            // Check username uniqueness
            $checkUniq = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_username = '{$username}' LIMIT 1");
            if ($checkUniq && mysqli_num_rows($checkUniq) > 0) {
                $username = $username . rand(100, 999);
            }
        }

        $insertUserSql = "
            INSERT INTO {$tblPrefix}user (
                user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip
            ) VALUES (
                {$loggedInUserId}, '{$username}', '{$safeEmail}', '{$safeMobile}', '{$encPassword}', '{$saltKey}', 1, NOW(), '" . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') . "'
            )
        ";

        if (!mysqli_query($connection, $insertUserSql)) {
            throw new Exception("Failed to insert user: " . mysqli_error($connection));
        }

        $userId = mysqli_insert_id($connection);

        // Insert Profile
        $insertProfileSql = "
            INSERT INTO {$tblPrefix}profile (
                profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email
            ) VALUES (
                {$userId}, '{$safeFirstName}', '{$safeLastName}', '{$profileDisplayName}', '{$safeMobile}', '{$safeEmail}'
            )
        ";

        if (!mysqli_query($connection, $insertProfileSql)) {
            throw new Exception("Failed to insert profile: " . mysqli_error($connection));
        }

        // Insert Site Access
        $insertSiteAccessSql = "
            INSERT INTO {$tblPrefix}site_access (
                site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta
            ) VALUES (
                {$siteId}, {$userId}, '{$safeUserRole}', 1, '{$safeMetaJson}'
            )
        ";

        if (!mysqli_query($connection, $insertSiteAccessSql)) {
            throw new Exception("Failed to assign site access: " . mysqli_error($connection));
        }
    }

    mysqli_commit($connection);

    echo json_encode([
        'status'     => 'success',
        'msg'        => 'User added successfully!',
        'user_id'    => $userId,
        'encoded_id' => base64_encode((string)$userId)
    ]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Error creating user: ' . $e->getMessage()
    ]);
}
