<?php

/**
 * V3 User Profile Settings API Endpoint
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

require_once LOGIN_CHECK;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$userId = defined('USER_ID') ? (int)USER_ID : 0;

if ($userId <= 0) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'msg' => 'User session is not authenticated.'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 1. GET: Fetch Current User Profile
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $sql = "SELECT u.user_id, u.user_username, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                   p.profile_first_name, p.profile_last_name, p.profile_display_name,
                   p.profile_alt_email, p.profile_alt_mobile, p.profile_image,
                   pm_bio.profile_meta_value AS profile_bio,
                   sa.site_access_type as role
            FROM " . TABLE_PREFIX . "user u
            INNER JOIN " . TABLE_PREFIX . "site_access sa ON sa.site_access_user_id = u.user_id
            LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
            LEFT JOIN " . TABLE_PREFIX . "profile_meta pm_bio ON pm_bio.profile_meta_profile_id = u.user_id AND pm_bio.profile_meta_key = 'bio'
            WHERE u.user_id = {$userId}
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'msg' => 'User profile not found.'
        ]);
        exit;
    }

    $u = mysqli_fetch_assoc($res);
    $fName = $u['profile_first_name'] ?? '';
    $lName = $u['profile_last_name'] ?? '';
    $dName = $u['profile_display_name'] ?? '';
    if (empty($dName)) {
        $dName = trim($fName . ' ' . $lName);
    }
    if (empty($dName)) {
        $dName = $u['user_username'] ?? 'User';
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'user_id' => (int)$u['user_id'],
            'username' => $u['user_username'] ?? '',
            'email' => $u['user_email'] ?? '',
            'mobile' => $u['user_mobile'] ?? '',
            'first_name' => $fName,
            'last_name' => $lName,
            'display_name' => $dName,
            'alt_email' => $u['profile_alt_email'] ?? '',
            'alt_mobile' => $u['profile_alt_mobile'] ?? '',
            'bio' => $u['profile_bio'] ?? '',
            'profile_image' => $u['profile_image'] ?? '',
            'role' => $u['role'] ?? 'administrator',
            'registered_time' => $u['user_reg_time'] ?? ''
        ]
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Profile
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $firstName   = trim($data['first_name'] ?? '');
    $lastName    = trim($data['last_name'] ?? '');
    $displayName = trim($data['display_name'] ?? '');
    $altEmail    = trim($data['alt_email'] ?? '');
    $mobile      = trim($data['mobile'] ?? '');
    $altMobile   = trim($data['alt_mobile'] ?? '');
    $bio         = trim($data['bio'] ?? '');

    if (empty($firstName)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'warning',
            'msg' => 'First Name is required.'
        ]);
        exit;
    }

    if (empty($displayName)) {
        $displayName = trim($firstName . ' ' . $lastName);
    }

    // Mobile uniqueness check if changing mobile
    if (!empty($mobile)) {
        $escapedMob = mysqli_real_escape_string($connection, $mobile);
        $mobQuery = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_mobile = '{$escapedMob}' AND user_id != {$userId} LIMIT 1");
        if ($mobQuery && mysqli_num_rows($mobQuery) > 0) {
            http_response_code(400);
            echo json_encode([
                'status' => 'warning',
                'msg' => 'This mobile number is already in use by another user account.'
            ]);
            exit;
        }
    }

    // Handle Profile Picture Upload (per lib/modules/imageUploader.php convention)
    $newProfileImage = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $rootDir    = defined('ROOT') ? ROOT : $_SERVER['DOCUMENT_ROOT'];
            $siteDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : 'default';
            $uploadPath = $rootDir . '/upload/' . $siteDomain . '/' . date('Y/m/');

            if (!is_dir($uploadPath)) {
                if (!@mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
                    // Fallback
                    $uploadPath = $rootDir . '/upload/' . date('Y/m/');
                    if (!is_dir($uploadPath)) {
                        @mkdir($uploadPath, 0755, true);
                    }
                }
            }

            $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
            $cleanName    = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $_FILES['profile_image']['name']));
            $filename     = 'profile_' . $userId . '_' . $uniquePrefix . '_' . $cleanName;
            $uploadFile   = $uploadPath . $filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                $newProfileImage = str_replace($rootDir, '', $uploadFile);
            }
        }
    }

    $escapedFName   = mysqli_real_escape_string($connection, $firstName);
    $escapedLName   = mysqli_real_escape_string($connection, $lastName);
    $escapedDName   = mysqli_real_escape_string($connection, $displayName);
    $escapedAltMail = mysqli_real_escape_string($connection, $altEmail);
    $escapedMob     = mysqli_real_escape_string($connection, $mobile);
    $escapedAltMob  = mysqli_real_escape_string($connection, $altMobile);
    $escapedBio     = mysqli_real_escape_string($connection, $bio);
    $escapedImg     = mysqli_real_escape_string($connection, $newProfileImage);

    mysqli_query($connection, "START TRANSACTION");

    // 1. Update isw_user mobile if provided
    $updateUser = true;
    if (!empty($mobile)) {
        $updateUser = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "user SET user_mobile = '{$escapedMob}' WHERE user_id = {$userId}");
    }

    // 2. Update isw_profile
    $checkProf = mysqli_query($connection, "SELECT profile_id, profile_image FROM " . TABLE_PREFIX . "profile WHERE profile_user_id = {$userId} LIMIT 1");
    if ($checkProf && mysqli_num_rows($checkProf) > 0) {
        $imgClause = !empty($newProfileImage) ? ", profile_image = '{$escapedImg}'" : "";
        $updateProf = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile SET
            profile_first_name = '{$escapedFName}',
            profile_last_name = '{$escapedLName}',
            profile_display_name = '{$escapedDName}',
            profile_alt_email = '{$escapedAltMail}',
            profile_mobile = '{$escapedMob}',
            profile_alt_mobile = '{$escapedAltMob}'
            {$imgClause}
            WHERE profile_user_id = {$userId}");
    } else {
        $updateProf = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile (
            profile_user_id, profile_first_name, profile_last_name, profile_display_name,
            profile_alt_email, profile_mobile, profile_alt_mobile, profile_image
        ) VALUES (
            {$userId}, '{$escapedFName}', '{$escapedLName}', '{$escapedDName}',
            '{$escapedAltMail}', '{$escapedMob}', '{$escapedAltMob}', '{$escapedImg}'
        )");
    }

    // 3. Update bio in profile_meta table (key = 'bio')
    $updateBio = true;
    $checkBio = mysqli_query($connection, "SELECT profile_meta_id FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id = {$userId} AND profile_meta_key = 'bio' LIMIT 1");
    if ($checkBio && mysqli_num_rows($checkBio) > 0) {
        $updateBio = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile_meta SET profile_meta_value = '{$escapedBio}' WHERE profile_meta_profile_id = {$userId} AND profile_meta_key = 'bio'");
    } else if (!empty($bio)) {
        $updateBio = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile_meta (profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES ({$userId}, 'bio', '{$escapedBio}')");
    }

    if ($updateUser && $updateProf && $updateBio) {
        mysqli_query($connection, "COMMIT");

        if (!empty($newProfileImage)) {
            setcookie('user_image', base64_encode($newProfileImage), time() + 30 * 24 * 60 * 60, '/');
        }

        echo json_encode([
            'status' => 'success',
            'msg' => 'Your profile information has been updated successfully.',
            'profile_image' => $newProfileImage
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Database error while updating profile: ' . mysqli_error($connection)
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'error',
    'msg' => 'Method not allowed.'
]);
