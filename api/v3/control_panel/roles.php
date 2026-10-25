<?php

/**
 * Control Panel: Roles & Permissions Dictionary API Endpoint
 * Path: /api/v3/control_panel/roles.php
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

$roles = [];
$res = mysqli_query($connection, "SELECT user_type_id, user_type_name, user_type_code 
    FROM {$tblPrefix}user_type 
    WHERE user_type_app_id = 33 AND user_type_status = 1 
    ORDER BY user_type_id ASC");

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $roles[] = [
            'id' => (int)$row['user_type_id'],
            'name' => trim($row['user_type_name']),
            'code' => strtolower(trim($row['user_type_code']))
        ];
    }
}

// Fallback to standard 5 roles if not yet returned
if (empty($roles)) {
    $roles = [
        ['id' => 1, 'name' => 'System Administrator', 'code' => 'administrator'],
        ['id' => 2, 'name' => 'Partner / Referral', 'code' => 'partner'],
        ['id' => 3, 'name' => 'Sales Executive', 'code' => 'sales'],
        ['id' => 4, 'name' => 'Customer Support', 'code' => 'support'],
        ['id' => 5, 'name' => 'On Ground Sales', 'code' => 'on_ground_sales']
    ];
}

$roleCapabilities = [
    'administrator' => [
        'badge' => 'Admin (Full Access)',
        'color' => 'indigo',
        'can_manage_apps' => true,
        'can_manage_themes' => true,
        'can_manage_sites' => true,
        'can_manage_users' => true,
        'can_manage_billing' => true,
        'can_manage_commissions' => true,
        'can_manage_configs' => true
    ],
    'partner' => [
        'badge' => 'Partner / Referral',
        'color' => 'amber',
        'can_manage_apps' => false,
        'can_manage_themes' => false,
        'can_manage_sites' => false,
        'can_manage_users' => false,
        'can_manage_billing' => false,
        'can_manage_commissions' => true,
        'can_manage_configs' => false
    ],
    'sales' => [
        'badge' => 'Sales Executive',
        'color' => 'emerald',
        'can_manage_apps' => false,
        'can_manage_themes' => false,
        'can_manage_sites' => true,
        'can_manage_users' => false,
        'can_manage_billing' => true,
        'can_manage_commissions' => false,
        'can_manage_configs' => false
    ],
    'support' => [
        'badge' => 'Customer Support',
        'color' => 'sky',
        'can_manage_apps' => false,
        'can_manage_themes' => false,
        'can_manage_sites' => true,
        'can_manage_users' => true,
        'can_manage_billing' => false,
        'can_manage_commissions' => false,
        'can_manage_configs' => true
    ],
    'on_ground_sales' => [
        'badge' => 'On Ground Sales',
        'color' => 'purple',
        'can_manage_apps' => false,
        'can_manage_themes' => false,
        'can_manage_sites' => true,
        'can_manage_users' => false,
        'can_manage_billing' => true,
        'can_manage_commissions' => false,
        'can_manage_configs' => false
    ]
];

echo json_encode([
    'status' => 'success',
    'roles' => $roles,
    'capabilities' => $roleCapabilities
]);
exit();
