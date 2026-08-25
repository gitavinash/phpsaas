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
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$siteAppId = defined('SITE_APP_ID')  ? (int) SITE_APP_ID : (int)($_SESSION['site_app_id'] ?? 0);
$currentUserId = defined('USER_ID')  ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

// Host fallback if SITE_ID is not defined yet
if ($siteId <= 0 && isset($connection)) {
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    if (!empty($currentHost)) {
        $hostSafe = mysqli_real_escape_string($connection, $currentHost);
        $sQuery = mysqli_query($connection, "SELECT site_id, site_app_id FROM {$tblPrefix}site WHERE site_url = '{$hostSafe}' OR site_domain = '{$hostSafe}' LIMIT 1");
        if ($sQuery && $sRow = mysqli_fetch_assoc($sQuery)) {
            $siteId = (int)$sRow['site_id'];
            if ($siteAppId <= 0) {
                $siteAppId = (int)$sRow['site_app_id'];
            }
        }
    }
}

if (!$connection || $siteId <= 0) {
    echo json_encode([
        'status'  => 'success',
        'msg'     => 'No active site found for current domain.',
        'total'   => 0,
        'data'    => [],
        'users'   => [],
        'metrics' => [
            'total'      => 0,
            'admins'     => 0,
            'agents'     => 0,
            'operations' => 0
        ],
        'roles'   => [],
        'current_user_id' => $currentUserId
    ]);
    exit();
}

// Parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
$role   = isset($_GET['role'])   ? mysqli_real_escape_string($connection, trim($_GET['role'])) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Single User ID filter (numeric or base64)
$rawId = $_GET['id'] ?? ($_GET['user_id'] ?? 0);
$targetUserId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $targetUserId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $targetUserId = (int)$decoded;
    }
}

// Fetch available user roles for this app
$rolesList = [];
$roleLabels = [
    'administrator' => 'Administrator',
    'agent'         => 'Sales Agent',
    'operations'    => 'Operations Manager',
    'accountant'    => 'Accounts & Finance',
    'support'       => 'Customer Support',
    'driver'        => 'Driver / Chauffeur',
    'vendor'        => 'Vendor',
    'member'        => 'Team Member'
];

$roleQuery = "SELECT user_type_name, user_type_code FROM {$tblPrefix}user_type WHERE user_type_app_id = '{$siteAppId}' AND user_type_status = 1";
$roleRes = mysqli_query($connection, $roleQuery);
if ($roleRes && mysqli_num_rows($roleRes) > 0) {
    while ($rRow = mysqli_fetch_assoc($roleRes)) {
        $code = strtolower(trim($rRow['user_type_code']));
        $name = trim($rRow['user_type_name']);
        if (!in_array($code, ['customer', 'subscriber'])) {
            $rolesList[] = [
                'code' => $code,
                'name' => $name
            ];
            $roleLabels[$code] = $name;
        }
    }
}

// Strictly filter by site_access table's site_access_site_id
$whereConditions = [
    "sa.site_access_site_id = {$siteId}",
    "sa.site_access_type NOT IN ('customer', 'subscriber')"
];

// If requesting specific user
if ($targetUserId > 0) {
    $whereConditions[] = "u.user_id = {$targetUserId}";
}

// Status filter on site_access_status
if ($status === 'inactive' || $status === '2' || $status === '0') {
    $whereConditions[] = "sa.site_access_status = 2";
} else if ($status === 'all') {
    $whereConditions[] = "sa.site_access_status IN (1, 2)";
} else {
    // Default: show active site team members
    $whereConditions[] = "sa.site_access_status = 1";
}

// Role filter
if (!empty($role) && $role !== 'all') {
    $whereConditions[] = "sa.site_access_type = '{$role}'";
}

// Search filter
if (!empty($search)) {
    $whereConditions[] = "(
        p.profile_display_name LIKE '%{$search}%' OR 
        p.profile_first_name LIKE '%{$search}%' OR 
        p.profile_last_name LIKE '%{$search}%' OR 
        u.user_username LIKE '%{$search}%' OR 
        u.user_email LIKE '%{$search}%' OR 
        u.user_mobile LIKE '%{$search}%' OR 
        sa.site_access_type LIKE '%{$search}%'
    )";
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

$sql = "
    SELECT 
        u.user_id,
        u.user_username,
        u.user_email,
        u.user_mobile,
        u.user_status,
        u.user_reg_time,
        p.profile_first_name,
        p.profile_last_name,
        p.profile_display_name,
        p.profile_mobile,
        p.profile_email,
        p.profile_image,
        sa.site_access_id,
        sa.site_access_type,
        sa.site_access_status,
        sa.site_access_meta
    FROM {$tblPrefix}user u
    INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
    {$whereClause}
    ORDER BY 
        CASE WHEN sa.site_access_type = 'administrator' THEN 0 
             WHEN sa.site_access_type = 'agent' THEN 1 
             WHEN sa.site_access_type = 'operations' THEN 2 
             ELSE 3 END, 
        u.user_id DESC
";

$res = mysqli_query($connection, $sql);

if (!$res) {
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to retrieve users: ' . mysqli_error($connection),
        'data'   => [],
        'users'  => []
    ]);
    exit();
}

$users = [];
while ($row = mysqli_fetch_assoc($res)) {
    $uId = (int)$row['user_id'];
    $roleCode = strtolower(trim($row['site_access_type'] ?? 'member'));
    $roleLabel = $roleLabels[$roleCode] ?? ucwords(str_replace('_', ' ', $roleCode));

    $firstName = trim($row['profile_first_name'] ?? '');
    $lastName  = trim($row['profile_last_name'] ?? '');
    $displayName = trim($row['profile_display_name'] ?? '');
    if (empty($displayName)) {
        $displayName = trim($firstName . ' ' . $lastName);
    }
    if (empty($displayName)) {
        $displayName = $row['user_username'] ?? 'User #' . $uId;
    }
    if (empty($firstName) && !empty($displayName)) {
        $nameParts = explode(' ', $displayName);
        $firstName = $nameParts[0] ?? '';
        if (count($nameParts) > 1) {
            $lastName = implode(' ', array_slice($nameParts, 1));
        }
    }

    $email = !empty($row['profile_email']) ? $row['profile_email'] : ($row['user_email'] ?? '');
    $mobile = !empty($row['profile_mobile']) ? $row['profile_mobile'] : ($row['user_mobile'] ?? '');

    $meta = !empty($row['site_access_meta']) ? json_decode($row['site_access_meta'], true) : [];

    // Initials for avatar
    $initials = '';
    $parts = explode(' ', $displayName);
    foreach ($parts as $p) {
        if (!empty($p)) {
            $initials .= strtoupper(substr($p, 0, 1));
            if (strlen($initials) >= 2) break;
        }
    }
    if (empty($initials)) {
        $initials = 'U';
    }

    $users[] = [
        'id'           => $uId,
        'encoded_id'   => base64_encode((string)$uId),
        'username'     => $row['user_username'] ?? '',
        'first_name'   => $firstName,
        'last_name'    => $lastName,
        'name'         => ucwords(strtolower($displayName)),
        'email'        => $email,
        'mobile'       => $mobile,
        'role'         => $roleCode,
        'role_label'   => $roleLabel,
        'status'       => (int)($row['site_access_status'] ?? 1),
        'status_label' => ((int)($row['site_access_status'] ?? 1) === 1) ? 'Active' : 'Inactive',
        'initials'     => $initials,
        'avatar'       => !empty($row['profile_image']) ? $row['profile_image'] : '',
        'meta'         => $meta,
        'is_self'      => ($uId === $currentUserId),
        'is_deletable' => ($uId !== $currentUserId),
        'created_at'   => $row['user_reg_time'] ?? ''
    ];
}

// Fetch Metrics KPI strictly for current site
$metricsSql = "
    SELECT 
        COUNT(DISTINCT u.user_id) AS total_users,
        SUM(CASE WHEN sa.site_access_type = 'administrator' THEN 1 ELSE 0 END) AS total_admins,
        SUM(CASE WHEN sa.site_access_type = 'agent' THEN 1 ELSE 0 END) AS total_agents,
        SUM(CASE WHEN sa.site_access_type IN ('operations', 'accountant', 'support') THEN 1 ELSE 0 END) AS total_operations
    FROM {$tblPrefix}user u
    INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
    WHERE sa.site_access_site_id = {$siteId} 
      AND sa.site_access_status = 1 
      AND sa.site_access_type NOT IN ('customer', 'subscriber')
";
$metricsRes = mysqli_query($connection, $metricsSql);
$metrics = [
    'total'      => count($users),
    'admins'     => 0,
    'agents'     => 0,
    'operations' => 0
];
if ($metricsRes && $mRow = mysqli_fetch_assoc($metricsRes)) {
    $metrics['total']      = (int)($mRow['total_users'] ?? 0);
    $metrics['admins']     = (int)($mRow['total_admins'] ?? 0);
    $metrics['agents']     = (int)($mRow['total_agents'] ?? 0);
    $metrics['operations'] = (int)($mRow['total_operations'] ?? 0);
}

echo json_encode([
    'status'          => 'success',
    'msg'             => 'Users retrieved successfully.',
    'site_id'         => $siteId,
    'total'           => count($users),
    'data'            => $users,
    'users'           => $users,
    'metrics'         => $metrics,
    'roles'           => $rolesList,
    'current_user_id' => $currentUserId
]);
