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
    echo json_encode(['status' => 'error', 'msg' => 'Active site identifier could not be resolved.']);
    exit;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$userId = defined('USER_ID') ? (int)USER_ID : 0;

if ($userId <= 0 && isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Admin Profile
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $userQuery = "SELECT u.user_id, u.user_email, u.user_mobile, u.user_status,
                         p.profile_first_name, p.profile_last_name, p.profile_display_name,
                         p.profile_avatar, p.profile_gender, p.profile_address, p.profile_city, p.profile_country,
                         sa.site_access_type
                  FROM " . TABLE_PREFIX . "user u
                  LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                  LEFT JOIN " . TABLE_PREFIX . "site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
                  WHERE u.user_id = {$userId} LIMIT 1";

    $res = mysqli_query($connection, $userQuery);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $data = [
            'user_id'              => (int)$row['user_id'],
            'profile_first_name'   => $row['profile_first_name'] ?? '',
            'profile_last_name'    => $row['profile_last_name'] ?? '',
            'profile_display_name' => $row['profile_display_name'] ?? '',
            'user_email'           => $row['user_email'] ?? '',
            'user_mobile'          => $row['user_mobile'] ?? '',
            'profile_gender'       => $row['profile_gender'] ?? '',
            'profile_address'      => $row['profile_address'] ?? '',
            'profile_city'         => $row['profile_city'] ?? '',
            'profile_country'      => $row['profile_country'] ?? '',
            'profile_avatar'       => $row['profile_avatar'] ?? '',
            'role'                 => $row['site_access_type'] ?? 'Administrator'
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
                'profile_first_name'   => '',
                'profile_last_name'    => '',
                'profile_display_name' => 'Administrator',
                'user_email'           => '',
                'user_mobile'          => '',
                'profile_gender'       => '',
                'profile_address'      => '',
                'profile_city'         => '',
                'profile_country'      => '',
                'profile_avatar'       => '',
                'role'                 => 'Administrator'
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

    $firstName   = trim($data['profile_first_name'] ?? '');
    $lastName    = trim($data['profile_last_name'] ?? '');
    $displayName = trim($data['profile_display_name'] ?? '');
    $email       = trim($data['user_email'] ?? '');
    $mobile      = trim($data['user_mobile'] ?? '');
    $gender      = trim($data['profile_gender'] ?? '');
    $address     = trim($data['profile_address'] ?? '');
    $city        = trim($data['profile_city'] ?? '');
    $avatar      = trim($data['profile_avatar'] ?? '');

    if ($displayName === '') {
        $displayName = trim($firstName . ' ' . $lastName);
    }

    if ($userId > 0) {
        $escEmail = mysqli_real_escape_string($connection, $email);
        $escMobile = mysqli_real_escape_string($connection, $mobile);

        $userSql = "UPDATE " . TABLE_PREFIX . "user 
                    SET user_email = '{$escEmail}', user_mobile = '{$escMobile}' 
                    WHERE user_id = {$userId}";
        mysqli_query($connection, $userSql);

        $escFirst = mysqli_real_escape_string($connection, $firstName);
        $escLast = mysqli_real_escape_string($connection, $lastName);
        $escDisplay = mysqli_real_escape_string($connection, $displayName);
        $escGender = mysqli_real_escape_string($connection, $gender);
        $escAddress = mysqli_real_escape_string($connection, $address);
        $escCity = mysqli_real_escape_string($connection, $city);
        $escAvatar = mysqli_real_escape_string($connection, $avatar);

        $profSql = "INSERT INTO " . TABLE_PREFIX . "profile 
                    (profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_gender, profile_address, profile_city, profile_avatar)
                    VALUES ({$userId}, '{$escFirst}', '{$escLast}', '{$escDisplay}', '{$escGender}', '{$escAddress}', '{$escCity}', '{$escAvatar}')
                    ON DUPLICATE KEY UPDATE 
                        profile_first_name = '{$escFirst}',
                        profile_last_name = '{$escLast}',
                        profile_display_name = '{$escDisplay}',
                        profile_gender = '{$escGender}',
                        profile_address = '{$escAddress}',
                        profile_city = '{$escCity}',
                        profile_avatar = IF('{$escAvatar}' != '', '{$escAvatar}', profile_avatar)";
        mysqli_query($connection, $profSql);
    }

    echo json_encode([
        'status' => 'success',
        'msg'    => 'Personal profile and avatar updated successfully.'
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
