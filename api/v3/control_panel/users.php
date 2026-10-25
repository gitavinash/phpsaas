<?php

/**
 * Control Panel: Users & Role-Based Access Control API Endpoint
 * Path: /api/v3/control_panel/users.php
 */

header("Content-Type: application/json; charset=utf-8");

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
$controlPanelSiteId = defined('SITE_ID') ? (int)SITE_ID : 1;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: List Users (with role filter & search)
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $role = isset($_GET['role']) ? mysqli_real_escape_string($connection, trim($_GET['role'])) : '';
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = [];
    if (!empty($search)) {
        $where[] = "(u.user_username LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR u.user_mobile LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
    }
    if (!empty($role)) {
        $where[] = "sa.site_access_type = '{$role}'";
    }
    if ($status !== null) {
        $where[] = "u.user_status = {$status}";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $countSql = "SELECT COUNT(DISTINCT u.user_id) as total 
                 FROM {$tblPrefix}user u
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
                 {$whereSql}";
    $countRes = mysqli_query($connection, $countSql);
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    $sql = "SELECT u.user_id, u.user_username, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                   p.profile_display_name, p.profile_image,
                   sa.site_access_type, sa.site_access_site_id, s.site_name, s.site_domain
            FROM {$tblPrefix}user u
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
            LEFT JOIN {$tblPrefix}site s ON s.site_id = sa.site_access_site_id
            {$whereSql}
            GROUP BY u.user_id
            ORDER BY u.user_id DESC
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $users = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $users[] = [
                'id' => (int)$row['user_id'],
                'username' => htmlspecialchars($row['user_username'] ?? ''),
                'email' => htmlspecialchars($row['user_email'] ?? ''),
                'mobile' => htmlspecialchars($row['user_mobile'] ?? ''),
                'display_name' => htmlspecialchars($row['profile_display_name'] ?? $row['user_username']),
                'role' => htmlspecialchars($row['site_access_type'] ?? 'administrator'),
                'site_id' => (int)($row['site_access_site_id'] ?? 1),
                'site_name' => htmlspecialchars($row['site_name'] ?? 'Main Control Panel'),
                'status' => (int)$row['user_status'],
                'reg_time' => $row['user_reg_time'],
                'image' => htmlspecialchars($row['profile_image'] ?? '')
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $users,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create, Update, or Reset Password
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'create';
    $userId = isset($payload['user_id']) ? (int)$payload['user_id'] : 0;

    // Toggle User Status
    if ($action === 'toggle_status') {
        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid User ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_status = {$newStatus} WHERE user_id = {$userId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'User status updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Reset Password
    if ($action === 'reset_password') {
        $newPass = trim($payload['new_password'] ?? '');
        if ($userId <= 0 || empty($newPass)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'User ID and New Password are required.']);
            exit();
        }
        $salt = hash('sha256', uniqid(mt_rand(), true) . time());
        $enc = hash('sha256', $salt . $newPass);
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_salt_key = '{$salt}', user_password = '{$enc}' WHERE user_id = {$userId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Password reset successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Create New User
    $email = trim($payload['email'] ?? '');
    $username = trim($payload['username'] ?? '');
    $mobile = trim($payload['mobile'] ?? '');
    $displayName = trim($payload['display_name'] ?? '');
    $password = trim($payload['password'] ?? 'Blumox@2026');
    $role = trim($payload['role'] ?? 'administrator');
    $targetSiteId = isset($payload['site_id']) ? (int)$payload['site_id'] : $controlPanelSiteId;

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Email address is required.']);
        exit();
    }

    if (empty($username)) {
        $cleanPrefix = substr(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]), 0, 14);
        $username = ($cleanPrefix ?: 'user') . '_' . rand(100, 999);
    } else {
        $username = substr(preg_replace('/[^a-zA-Z0-9_]/', '', $username), 0, 20);
    }
    if (empty($displayName)) {
        $displayName = ucwords(str_replace(['.', '_'], ' ', explode('@', $email)[0]));
    }

    // Supported roles check
    $validRoles = ['administrator', 'partner', 'sales', 'support', 'on_ground_sales'];
    if (!in_array($role, $validRoles)) {
        $role = 'sales';
    }

    $escEmail = mysqli_real_escape_string($connection, $email);
    $escUser = mysqli_real_escape_string($connection, $username);
    $escMobile = mysqli_real_escape_string($connection, $mobile);
    $escName = mysqli_real_escape_string($connection, $displayName);
    $escRole = mysqli_real_escape_string($connection, $role);
    $regIp = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

    if ($userId > 0) {
        // Update User
        mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_email = '{$escEmail}', user_mobile = '{$escMobile}' WHERE user_id = {$userId}");
        mysqli_query($connection, "UPDATE {$tblPrefix}profile SET profile_display_name = '{$escName}' WHERE profile_user_id = {$userId}");
        // Update or insert site access
        $accQ = mysqli_query($connection, "SELECT site_access_id FROM {$tblPrefix}site_access WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        if (mysqli_num_rows($accQ) > 0) {
            mysqli_query($connection, "UPDATE {$tblPrefix}site_access SET site_access_type = '{$escRole}' WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        } else {
            mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status)
                                       VALUES ({$targetSiteId}, {$userId}, '{$escRole}', 1)");
        }
        echo json_encode(['status' => 'success', 'msg' => 'User profile and role updated successfully.']);
        exit();
    } else {
        // Check uniqueness of email
        $chk = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_email = '{$escEmail}'");
        if ($chk && mysqli_num_rows($chk) > 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => "User with email '{$email}' already exists."]);
            exit();
        }

        // Check mobile uniqueness if provided
        if (!empty($mobile)) {
            $mChk = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_mobile = '{$escMobile}'");
            if ($mChk && mysqli_num_rows($mChk) > 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'msg' => "User with mobile number '{$mobile}' already exists."]);
                exit();
            }
        }

        $salt = hash('sha256', uniqid(mt_rand(), true) . time());
        $enc = hash('sha256', $salt . $password);

        try {
            $insUser = "INSERT INTO {$tblPrefix}user (user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip, user_refer_id)
                        VALUES (0, '{$escUser}', '{$escEmail}', '{$escMobile}', '{$enc}', '{$salt}', 1, NOW(), '{$regIp}', 0)";
            if (mysqli_query($connection, $insUser)) {
                $newUserId = mysqli_insert_id($connection);
                mysqli_query($connection, "INSERT INTO {$tblPrefix}profile (profile_user_id, profile_display_name, profile_email, profile_mobile) VALUES ({$newUserId}, '{$escName}', '{$escEmail}', '{$escMobile}')");
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status)
                                           VALUES ({$targetSiteId}, {$newUserId}, '{$escRole}', 1)");

                echo json_encode([
                    'status' => 'success',
                    'msg' => 'User account created and role assigned successfully.',
                    'user_id' => $newUserId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
            }
        } catch (Throwable $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
        exit();
    }
}
