<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$agents = [];
$siteCondition = ($siteId > 0) ? " AND site_access_site_id = {$siteId}" : "";

$sql = "SELECT user_id, user_email, profile_display_name, site_access_type
        FROM {$tblPrefix}user
        INNER JOIN {$tblPrefix}profile ON profile_user_id = user_id
        INNER JOIN {$tblPrefix}site_access ON site_access_user_id = user_id
        WHERE site_access_status = 1{$siteCondition}
        ORDER BY profile_display_name ASC";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $name = !empty($row['profile_display_name']) ? trim($row['profile_display_name']) : $row['user_email'];
        $role = !empty($row['site_access_type']) ? ucwords(str_replace('_', ' ', $row['site_access_type'])) : 'Team Member';
        $agents[] = [
            'id'    => (int)$row['user_id'],
            'name'  => $name,
            'email' => $row['user_email'],
            'role'  => $role,
        ];
    }
}

echo json_encode([
    'status' => 200,
    'agents' => $agents,
]);
exit();
