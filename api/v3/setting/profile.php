<?php

/**
 * V3 Admin User Profile Settings API Endpoint
 * Path: /api/v3/setting/profile.php
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

// Resolve authenticated user ID safely
$userId = defined('USER_ID') && (int)USER_ID > 0 ? (int)USER_ID : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);

if ($userId <= 0 && isset($_COOKIE['user_data'])) {
    $saltKey = base64_decode($_COOKIE['user_data']);
    if (!empty($saltKey)) {
        $escSalt = mysqli_real_escape_string($connection, $saltKey);
        $q = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_salt_key = '{$escSalt}' LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $uRow = mysqli_fetch_assoc($q);
            $userId = (int)$uRow['user_id'];
        }
    }
}

if ($userId <= 0) {
    // Fallback to site owner ID
    $ownerQuery = mysqli_query($connection, "SELECT site_owner_id FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
    if ($ownerQuery && mysqli_num_rows($ownerQuery) > 0) {
        $oRow = mysqli_fetch_assoc($ownerQuery);
        $userId = (int)($oRow['site_owner_id'] ?? 1);
    } else {
        $userId = 1;
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Admin Profile
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $userQuery = "
        SELECT 
            u.user_id, 
            u.user_username, 
            u.user_email, 
            u.user_mobile, 
            u.user_status,
            p.profile_first_name, 
            p.profile_last_name, 
            p.profile_display_name,
            p.profile_image, 
            p.profile_mobile, 
            p.profile_alt_mobile, 
            p.profile_email, 
            p.profile_alt_email,
            sa.site_access_type,
            sa.site_access_meta
        FROM {$tblPrefix}user u
        LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
        LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
        WHERE u.user_id = {$userId} 
        LIMIT 1
    ";

    $res = mysqli_query($connection, $userQuery);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        $bio = '';
        if (!empty($row['site_access_meta'])) {
            $meta = json_decode($row['site_access_meta'], true);
            if (is_array($meta) && !empty($meta['bio'])) {
                $bio = $meta['bio'];
            }
        }

        $fName = trim($row['profile_first_name'] ?? '');
        $lName = trim($row['profile_last_name'] ?? '');
        $dName = trim($row['profile_display_name'] ?? '');
        if (empty($dName)) {
            $dName = trim($fName . ' ' . $lName);
        }
        if (empty($dName)) {
            $dName = $row['user_username'] ?? 'Administrator';
        }

        $primaryMobile = !empty($row['profile_mobile']) ? $row['profile_mobile'] : ($row['user_mobile'] ?? '');
        $primaryEmail  = !empty($row['profile_email']) ? $row['profile_email'] : ($row['user_email'] ?? '');
        $altMobile     = trim($row['profile_alt_mobile'] ?? '');
        $altEmail      = trim($row['profile_alt_email'] ?? '');
        $profileImage  = trim($row['profile_image'] ?? '');

        $data = [
            'user_id'              => (int)$row['user_id'],
            'first_name'           => $fName,
            'last_name'            => $lName,
            'display_name'         => $dName,
            'username'             => $row['user_username'] ?? '',
            'mobile'               => $primaryMobile,
            'alt_mobile'           => $altMobile,
            'email'                => $primaryEmail,
            'alt_email'            => $altEmail,
            'bio'                  => $bio,
            'role'                 => $row['site_access_type'] ?? 'administrator',
            'profile_image'        => $profileImage,
            'avatar'               => $profileImage,
            // Aliases for compatibility
            'profile_first_name'   => $fName,
            'profile_last_name'    => $lName,
            'profile_display_name' => $dName,
            'user_email'           => $primaryEmail,
            'user_mobile'          => $primaryMobile
        ];

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'data'   => [
                'user_id'              => $userId,
                'first_name'           => '',
                'last_name'            => '',
                'display_name'         => 'Administrator',
                'username'             => '',
                'mobile'               => '',
                'alt_mobile'           => '',
                'email'                => '',
                'alt_email'            => '',
                'bio'                  => '',
                'role'                 => 'administrator',
                'profile_image'        => '',
                'avatar'               => '',
                'profile_first_name'   => '',
                'profile_last_name'    => '',
                'profile_display_name' => 'Administrator',
                'user_email'           => '',
                'user_mobile'          => ''
            ]
        ]);
    }
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Profile Information
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $firstName   = trim($data['first_name'] ?? ($data['profile_first_name'] ?? ''));
    $lastName    = trim($data['last_name'] ?? ($data['profile_last_name'] ?? ''));
    $displayName = trim($data['display_name'] ?? ($data['profile_display_name'] ?? ''));
    $bio         = trim($data['bio'] ?? '');
    $mobile      = trim($data['mobile'] ?? ($data['user_mobile'] ?? ''));
    $altMobile   = trim($data['alt_mobile'] ?? ($data['profile_alt_mobile'] ?? ''));
    $altEmail    = trim($data['alt_email'] ?? ($data['profile_alt_email'] ?? ''));
    $existingAvatar = trim($data['profile_image'] ?? ($data['avatar'] ?? ''));

    if ($displayName === '') {
        $displayName = trim($firstName . ' ' . $lastName);
    }

    if (empty($firstName)) {
        http_response_code(400);
        echo json_encode(['status' => 'warning', 'msg' => 'First Name is required.']);
        exit;
    }

    if (empty($mobile) || strlen($mobile) < 10) {
        http_response_code(400);
        echo json_encode(['status' => 'warning', 'msg' => 'Valid 10-digit primary mobile number is required.']);
        exit;
    }

    // Handle Profile Photo Upload if present in $_FILES
    $uploadedAvatarUrl = '';
    if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($ext, $allowedExts)) {
            $rootDir    = defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3));
            $siteDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : 'default';
            $uploadSubDir = '/upload/' . date('Y/m/');
            $targetDir = $rootDir . $uploadSubDir;

            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $newFileName = time() . '_user_' . $userId . '_' . uniqid() . '.' . $ext;
            $destination = $targetDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $uploadedAvatarUrl = $uploadSubDir . $newFileName;
            }
        }
    }

    if (!empty($uploadedAvatarUrl)) {
        $finalAvatar = $uploadedAvatarUrl;
    } else {
        $finalAvatar = $existingAvatar;
    }

    if ($userId > 0) {
        $escMobile = mysqli_real_escape_string($connection, $mobile);

        // 1. Update user primary mobile
        $userSql = "UPDATE {$tblPrefix}user 
                    SET user_mobile = '{$escMobile}' 
                    WHERE user_id = {$userId}";
        mysqli_query($connection, $userSql);

        // 2. Insert or update profile
        $escFirst     = mysqli_real_escape_string($connection, $firstName);
        $escLast      = mysqli_real_escape_string($connection, $lastName);
        $escDisplay   = mysqli_real_escape_string($connection, $displayName);
        $escAltMobile = mysqli_real_escape_string($connection, $altMobile);
        $escAltEmail  = mysqli_real_escape_string($connection, $altEmail);
        $escAvatar    = mysqli_real_escape_string($connection, $finalAvatar);

        $profSql = "
            INSERT INTO {$tblPrefix}profile 
                (profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_alt_mobile, profile_alt_email, profile_image)
            VALUES 
                ({$userId}, '{$escFirst}', '{$escLast}', '{$escDisplay}', '{$escMobile}', '{$escAltMobile}', '{$escAltEmail}', '{$escAvatar}')
            ON DUPLICATE KEY UPDATE 
                profile_first_name   = '{$escFirst}',
                profile_last_name    = '{$escLast}',
                profile_display_name = '{$escDisplay}',
                profile_mobile       = '{$escMobile}',
                profile_alt_mobile   = '{$escAltMobile}',
                profile_alt_email    = '{$escAltEmail}',
                profile_image        = IF('{$escAvatar}' != '', '{$escAvatar}', profile_image)
        ";
        mysqli_query($connection, $profSql);

        // 3. Update bio in site_access_meta
        $saRes = mysqli_query($connection, "SELECT site_access_meta FROM {$tblPrefix}site_access WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId} LIMIT 1");
        $metaObj = [];
        if ($saRes && mysqli_num_rows($saRes) > 0) {
            $saRow = mysqli_fetch_assoc($saRes);
            if (!empty($saRow['site_access_meta'])) {
                $metaObj = json_decode($saRow['site_access_meta'], true) ?: [];
            }
        }
        $metaObj['bio'] = $bio;
        $metaJson = json_encode($metaObj);
        $escMeta = mysqli_real_escape_string($connection, $metaJson);

        mysqli_query($connection, "
            UPDATE {$tblPrefix}site_access 
            SET site_access_meta = '{$escMeta}' 
            WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId}
        ");
    }

    echo json_encode([
        'status'        => 'success',
        'msg'           => 'Personal profile updated successfully.',
        'profile_image' => $finalAvatar
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
