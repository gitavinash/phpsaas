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

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input)) {
    $input = $_POST;
}

$rawId = $input['id'] ?? ($input['user_id'] ?? ($_GET['id'] ?? ($_GET['user_id'] ?? 0)));
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

// Guard against deleting own logged-in account
if ($userId === $loggedInUserId) {
    echo json_encode(['status' => 'warning', 'msg' => 'You cannot remove your own active account.']);
    exit();
}

// Check user on this site
$checkSql = "
    SELECT sa.site_access_id, sa.site_access_type, p.profile_display_name 
    FROM {$tblPrefix}site_access sa
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = sa.site_access_user_id
    WHERE sa.site_access_user_id = {$userId} AND sa.site_access_site_id = {$siteId}
    LIMIT 1
";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
    exit();
}

$userData = mysqli_fetch_assoc($checkRes);
$roleType = strtolower(trim($userData['site_access_type'] ?? ''));

// If administrator, check how many admins exist
if ($roleType === 'administrator') {
    $adminCountRes = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM {$tblPrefix}site_access WHERE site_access_site_id = {$siteId} AND site_access_type = 'administrator' AND site_access_status = 1");
    $adminCount = 1;
    if ($adminCountRes && $acRow = mysqli_fetch_assoc($adminCountRes)) {
        $adminCount = (int)$acRow['cnt'];
    }
    if ($adminCount <= 1) {
        echo json_encode(['status' => 'warning', 'msg' => 'Cannot remove the only administrator on this site.']);
        exit();
    }
}

// Soft delete site access
$updateSql = "UPDATE {$tblPrefix}site_access SET site_access_status = 2 WHERE site_access_user_id = {$userId} AND site_access_site_id = {$siteId}";
if (mysqli_query($connection, $updateSql)) {
    echo json_encode([
        'status' => 'success',
        'msg'    => 'User removed successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to remove user: ' . mysqli_error($connection)
    ]);
}
