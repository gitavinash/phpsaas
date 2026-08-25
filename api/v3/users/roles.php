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
$siteAppId = defined('SITE_APP_ID') && (int)SITE_APP_ID > 0 ? (int) SITE_APP_ID : (isset($_SESSION['site_app_id']) ? (int)$_SESSION['site_app_id'] : 0);

$roles = [];
$roleQuery = "SELECT user_type_id, user_type_name, user_type_code FROM {$tblPrefix}user_type WHERE user_type_app_id = '{$siteAppId}' AND user_type_status = 1 ORDER BY user_type_id ASC";
$res = mysqli_query($connection, $roleQuery);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $roles[] = [
            'id'   => (int)$row['user_type_id'],
            'name' => trim($row['user_type_name']),
            'code' => strtolower(trim($row['user_type_code']))
        ];
    }
}

// Fallback if no roles configured in DB
if (empty($roles)) {
    $roles = [
        ['id' => 1, 'name' => 'Administrator', 'code' => 'administrator'],
        ['id' => 2, 'name' => 'Sales Agent', 'code' => 'agent'],
        ['id' => 3, 'name' => 'Operations Manager', 'code' => 'operations'],
        ['id' => 4, 'name' => 'Accounts & Finance', 'code' => 'accountant'],
        ['id' => 5, 'name' => 'Customer Support', 'code' => 'support']
    ];
}

echo json_encode([
    'status' => 'success',
    'roles'  => $roles,
    'data'   => $roles
]);
