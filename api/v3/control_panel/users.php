<?php

/**
 * Control Panel: Users & Role-Based Access Control API Endpoint
 * Path: /api/v3/control_panel/users.php
 * Strictly scoped to current site access context (sa.site_access_site_id)
 */

header("Content-Type: application/json; charset=utf-8");

if (!isset($connection) || !($connection instanceof mysqli)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/dbConfig.php')) {
        require_once $docRoot . '/dbConfig.php';
    }
    if (file_exists($docRoot . '/config.php')) {
        @require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        @require_once $docRoot . '/lib/modules/connection.php';
    }
    if ((!isset($connection) || !($connection instanceof mysqli)) && defined('DB_HOST')) {
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$controlPanelSiteId = defined('SITE_ID') ? (int)SITE_ID : 1;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Ensure required tables and columns exist on load
require_once __DIR__ . '/schema_helper.php';
ensureControlPanelTables($connection, $tblPrefix);

// --------------------------------------------------------------------------
// 1. GET: List Users for Active Site Access Context
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
    $offset = ($page - 1) * $limit;

    // Site context: scope strictly to site_access_site_id
    $currentSiteId = isset($_GET['site_id']) && is_numeric($_GET['site_id']) && (int)$_GET['site_id'] > 0
        ? (int)$_GET['site_id']
        : $controlPanelSiteId;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $role = isset($_GET['role']) ? mysqli_real_escape_string($connection, trim($_GET['role'])) : '';
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    // Fetch site information
    $siteInfo = [
        'id' => $currentSiteId,
        'name' => 'Current Workspace',
        'domain' => ''
    ];
    $siteQuery = mysqli_query($connection, "SELECT site_id, site_name, site_domain FROM {$tblPrefix}site WHERE site_id = {$currentSiteId} LIMIT 1");
    if ($siteQuery && $sRow = mysqli_fetch_assoc($siteQuery)) {
        $siteInfo['name'] = $sRow['site_name'];
        $siteInfo['domain'] = $sRow['site_domain'];
    }

    // Site access condition: MANDATORY multi-tenant site scoping
    $where = [];
    $where[] = "sa.site_access_site_id = {$currentSiteId}";

    if (!empty($search)) {
        $where[] = "(u.user_username LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR u.user_mobile LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
    }
    if (!empty($role)) {
        $where[] = "sa.site_access_type = '{$role}'";
    }
    if ($status !== null) {
        $where[] = "sa.site_access_status = {$status}";
    }
    $whereSql = "WHERE " . implode(' AND ', $where);

    // Site KPI Stats
    $statsSql = "SELECT 
                    COUNT(DISTINCT u.user_id) as total_users,
                    SUM(CASE WHEN sa.site_access_status = 1 AND u.user_status = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN sa.site_access_status = 0 OR u.user_status = 0 THEN 1 ELSE 0 END) as disabled_users,
                    SUM(CASE WHEN sa.site_access_type IN ('administrator', 'admin') THEN 1 ELSE 0 END) as admin_users,
                    SUM(CASE WHEN sa.site_access_type NOT IN ('administrator', 'admin') THEN 1 ELSE 0 END) as staff_users
                 FROM {$tblPrefix}user u
                 INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
                 WHERE sa.site_access_site_id = {$currentSiteId}";
    $statsRes = mysqli_query($connection, $statsSql);
    $stats = ($statsRes && $stRow = mysqli_fetch_assoc($statsRes)) ? [
        'total' => (int)($stRow['total_users'] ?? 0),
        'active' => (int)($stRow['active_users'] ?? 0),
        'disabled' => (int)($stRow['disabled_users'] ?? 0),
        'admins' => (int)($stRow['admin_users'] ?? 0),
        'staff' => (int)($stRow['staff_users'] ?? 0),
    ] : [
        'total' => 0,
        'active' => 0,
        'disabled' => 0,
        'admins' => 0,
        'staff' => 0
    ];

    // Available roles on this site
    $rolesSql = "SELECT sa.site_access_type as role_code, COUNT(*) as cnt 
                 FROM {$tblPrefix}site_access sa 
                 WHERE sa.site_access_site_id = {$currentSiteId} AND sa.site_access_type != '' 
                 GROUP BY sa.site_access_type ORDER BY cnt DESC";
    $rolesRes = mysqli_query($connection, $rolesSql);
    $availableRoles = [];
    if ($rolesRes) {
        while ($rRow = mysqli_fetch_assoc($rolesRes)) {
            $availableRoles[] = [
                'role' => $rRow['role_code'],
                'count' => (int)$rRow['cnt']
            ];
        }
    }

    // Filtered count
    $countSql = "SELECT COUNT(DISTINCT u.user_id) as total 
                 FROM {$tblPrefix}user u
                 INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 {$whereSql}";
    $countRes = mysqli_query($connection, $countSql);
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    // Users list
    $sql = "SELECT u.user_id, u.user_username, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                   p.profile_display_name, p.profile_image,
                   sa.site_access_id, sa.site_access_type, sa.site_access_site_id, sa.site_access_status,
                   s.site_name, s.site_domain
            FROM {$tblPrefix}user u
            INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            LEFT JOIN {$tblPrefix}site s ON s.site_id = sa.site_access_site_id
            {$whereSql}
            ORDER BY u.user_id DESC
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $users = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $isActive = ((int)$row['site_access_status'] === 1 && (int)$row['user_status'] === 1) ? 1 : 0;
            $users[] = [
                'id' => (int)$row['user_id'],
                'site_access_id' => (int)$row['site_access_id'],
                'username' => htmlspecialchars($row['user_username'] ?? ''),
                'email' => htmlspecialchars($row['user_email'] ?? ''),
                'mobile' => htmlspecialchars($row['user_mobile'] ?? ''),
                'display_name' => htmlspecialchars($row['profile_display_name'] ?? $row['user_username']),
                'role' => htmlspecialchars($row['site_access_type'] ?? 'member'),
                'site_id' => (int)($row['site_access_site_id'] ?? $currentSiteId),
                'site_name' => htmlspecialchars($row['site_name'] ?? $siteInfo['name']),
                'site_domain' => htmlspecialchars($row['site_domain'] ?? $siteInfo['domain']),
                'site_access_status' => (int)$row['site_access_status'],
                'user_status' => (int)$row['user_status'],
                'status' => $isActive,
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
        'total_pages' => ceil($total / $limit),
        'stats' => $stats,
        'site' => $siteInfo,
        'available_roles' => $availableRoles
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create, Update Role, Toggle Status, or Reset Password
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'create';
    $userId = isset($payload['user_id']) ? (int)$payload['user_id'] : 0;
    $targetSiteId = isset($payload['site_id']) && (int)$payload['site_id'] > 0 ? (int)$payload['site_id'] : $controlPanelSiteId;

    // Action 1: Toggle Site Access Status for user
    if ($action === 'toggle_status') {
        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid User ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}site_access 
                                         SET site_access_status = {$newStatus} 
                                         WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        if ($up && mysqli_affected_rows($connection) >= 0) {
            echo json_encode([
                'status' => 'success',
                'msg' => $newStatus ? 'User site access enabled successfully.' : 'User site access disabled successfully.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection) ?: 'Failed to update user status.']);
        }
        exit();
    }

    // Action 2: Update User Role on Current Site
    if ($action === 'update_role') {
        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid User ID']);
            exit();
        }
        $role = trim($payload['role'] ?? '');
        if (empty($role)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Role is required.']);
            exit();
        }
        $escRole = mysqli_real_escape_string($connection, $role);
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}site_access 
                                         SET site_access_type = '{$escRole}' 
                                         WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => "User role on this site updated to '{$role}' successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Action 3: Reset Password
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

    // Action 4: Create New User / Assign User to Current Site Access
    $email = trim($payload['email'] ?? '');
    $username = trim($payload['username'] ?? '');
    $mobile = trim($payload['mobile'] ?? '');
    $displayName = trim($payload['display_name'] ?? '');
    $password = trim($payload['password'] ?? 'Blumox@2026');
    $role = trim($payload['role'] ?? 'sales_executive');

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

    $validRoles = ['administrator', 'agent', 'team_lead', 'operations', 'digital_marketing', 'sales_executive', 'partner', 'support', 'on_ground_sales', 'member', 'subscriber'];
    if (!in_array($role, $validRoles)) {
        $role = 'sales_executive';
    }

    $escEmail = mysqli_real_escape_string($connection, $email);
    $escUser = mysqli_real_escape_string($connection, $username);
    $escMobile = mysqli_real_escape_string($connection, $mobile);
    $escName = mysqli_real_escape_string($connection, $displayName);
    $escRole = mysqli_real_escape_string($connection, $role);
    $regIp = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

    if ($userId > 0) {
        // Update existing user profile & site access
        mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_email = '{$escEmail}', user_mobile = '{$escMobile}' WHERE user_id = {$userId}");
        mysqli_query($connection, "UPDATE {$tblPrefix}profile SET profile_display_name = '{$escName}' WHERE profile_user_id = {$userId}");
        
        $accQ = mysqli_query($connection, "SELECT site_access_id FROM {$tblPrefix}site_access WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        if (mysqli_num_rows($accQ) > 0) {
            mysqli_query($connection, "UPDATE {$tblPrefix}site_access SET site_access_type = '{$escRole}', site_access_status = 1 WHERE site_access_user_id = {$userId} AND site_access_site_id = {$targetSiteId}");
        } else {
            mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status)
                                       VALUES ({$targetSiteId}, {$userId}, '{$escRole}', 1)");
        }
        echo json_encode(['status' => 'success', 'msg' => 'User profile and site access updated successfully.']);
        exit();
    } else {
        // Check if user already exists in global user directory
        $chkUser = mysqli_query($connection, "SELECT user_id, user_email FROM {$tblPrefix}user WHERE user_email = '{$escEmail}' LIMIT 1");
        if ($chkUser && $uRow = mysqli_fetch_assoc($chkUser)) {
            $existingUserId = (int)$uRow['user_id'];
            
            // Check if they already have site access for this specific site
            $chkAcc = mysqli_query($connection, "SELECT site_access_id FROM {$tblPrefix}site_access 
                                                 WHERE site_access_user_id = {$existingUserId} AND site_access_site_id = {$targetSiteId}");
            if ($chkAcc && mysqli_num_rows($chkAcc) > 0) {
                // Already assigned: update role and ensure active
                mysqli_query($connection, "UPDATE {$tblPrefix}site_access 
                                           SET site_access_type = '{$escRole}', site_access_status = 1 
                                           WHERE site_access_user_id = {$existingUserId} AND site_access_site_id = {$targetSiteId}");
                echo json_encode([
                    'status' => 'success',
                    'msg' => "User '{$email}' already existed in system. Updated access to role '{$role}' on this site.",
                    'user_id' => $existingUserId
                ]);
                exit();
            } else {
                // Grant access to this site
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status)
                                           VALUES ({$targetSiteId}, {$existingUserId}, '{$escRole}', 1)");
                echo json_encode([
                    'status' => 'success',
                    'msg' => "Existing user '{$email}' granted access to this site as '{$role}'.",
                    'user_id' => $existingUserId
                ]);
                exit();
            }
        }

        // Check mobile uniqueness if provided
        if (!empty($mobile)) {
            $mChk = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_mobile = '{$escMobile}' LIMIT 1");
            if ($mChk && mysqli_num_rows($mChk) > 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'msg' => "A user with mobile number '{$mobile}' is already registered."]);
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
                    'msg' => 'New user created and assigned site access successfully.',
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
