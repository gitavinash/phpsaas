<?php

/**
 * Control Panel: Executive Overview & KPI Metrics API Endpoint
 * Path: /api/v3/control_panel/overview.php
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

// Ensure tables exist across all environments
require_once __DIR__ . '/schema_helper.php';
ensureControlPanelTables($connection, $tblPrefix);

// 1. Total & Active Apps
$appStats = ['total' => 0, 'active' => 0];
$appRes = mysqli_query($connection, "SELECT COUNT(*) as total, SUM(CASE WHEN app_status = 1 THEN 1 ELSE 0 END) as active FROM {$tblPrefix}app");
if ($appRes && $row = mysqli_fetch_assoc($appRes)) {
    $appStats['total'] = (int)$row['total'];
    $appStats['active'] = (int)$row['active'];
}

// 2. Total & Active Themes
$themeStats = ['total' => 0, 'active' => 0];
$themeRes = mysqli_query($connection, "SELECT COUNT(*) as total, SUM(CASE WHEN theme_status = 1 THEN 1 ELSE 0 END) as active FROM {$tblPrefix}theme");
if ($themeRes && $row = mysqli_fetch_assoc($themeRes)) {
    $themeStats['total'] = (int)$row['total'];
    $themeStats['active'] = (int)$row['active'];
}

// 3. Total & Active Sites
$siteStats = ['total' => 0, 'active' => 0, 'suspended' => 0];
$siteRes = mysqli_query($connection, "SELECT COUNT(*) as total, SUM(CASE WHEN site_status = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN site_status = 0 THEN 1 ELSE 0 END) as suspended FROM {$tblPrefix}site");
if ($siteRes && $row = mysqli_fetch_assoc($siteRes)) {
    $siteStats['total'] = (int)$row['total'];
    $siteStats['active'] = (int)$row['active'];
    $siteStats['suspended'] = (int)$row['suspended'];
}

// 4. Users count broken down by role
$userStats = [
    'total' => 0,
    'administrator' => 0,
    'partner' => 0,
    'sales' => 0,
    'support' => 0,
    'on_ground_sales' => 0
];

$userCountRes = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}user WHERE user_status = 1");
if ($userCountRes && $row = mysqli_fetch_assoc($userCountRes)) {
    $userStats['total'] = (int)$row['total'];
}

$roleBreakdownRes = mysqli_query($connection, "SELECT site_access_type, COUNT(DISTINCT site_access_user_id) as role_count 
    FROM {$tblPrefix}site_access 
    WHERE site_access_type IN ('administrator', 'partner', 'sales', 'support', 'on_ground_sales')
    GROUP BY site_access_type");
if ($roleBreakdownRes) {
    while ($r = mysqli_fetch_assoc($roleBreakdownRes)) {
        $type = $r['site_access_type'];
        if (isset($userStats[$type])) {
            $userStats[$type] = (int)$r['role_count'];
        }
    }
}

// 5. Financials: Invoices & Revenue
$financialStats = [
    'total_billed' => 0.00,
    'total_paid' => 0.00,
    'total_unpaid' => 0.00,
    'paid_invoices' => 0,
    'unpaid_invoices' => 0
];

try {
    $invRes = mysqli_query($connection, "SELECT 
        COALESCE(SUM(invoice_total), 0) as total_billed,
        COALESCE(SUM(CASE WHEN invoice_status = 'paid' THEN invoice_total ELSE 0 END), 0) as total_paid,
        COALESCE(SUM(CASE WHEN invoice_status != 'paid' THEN invoice_total ELSE 0 END), 0) as total_unpaid,
        COUNT(CASE WHEN invoice_status = 'paid' THEN 1 END) as paid_invoices,
        COUNT(CASE WHEN invoice_status != 'paid' THEN 1 END) as unpaid_invoices
        FROM {$tblPrefix}control_panel_invoice");
    if ($invRes && $row = mysqli_fetch_assoc($invRes)) {
        $financialStats['total_billed'] = (float)$row['total_billed'];
        $financialStats['total_paid'] = (float)$row['total_paid'];
        $financialStats['total_unpaid'] = (float)$row['total_unpaid'];
        $financialStats['paid_invoices'] = (int)$row['paid_invoices'];
        $financialStats['unpaid_invoices'] = (int)$row['unpaid_invoices'];
    }
} catch (\Throwable $e) {
    // Graceful fallback if table is unavailable
}

// 6. Partner Commissions Summary
$commissionStats = [
    'total_commissions' => 0.00,
    'paid_commissions' => 0.00,
    'pending_commissions' => 0.00
];

try {
    $commRes = mysqli_query($connection, "SELECT 
        COALESCE(SUM(commission_amount), 0) as total_commissions,
        COALESCE(SUM(CASE WHEN commission_status = 'paid' THEN commission_amount ELSE 0 END), 0) as paid_commissions,
        COALESCE(SUM(CASE WHEN commission_status = 'pending' THEN commission_amount ELSE 0 END), 0) as pending_commissions
        FROM {$tblPrefix}control_panel_commission");
    if ($commRes && $row = mysqli_fetch_assoc($commRes)) {
        $commissionStats['total_commissions'] = (float)$row['total_commissions'];
        $commissionStats['paid_commissions'] = (float)$row['paid_commissions'];
        $commissionStats['pending_commissions'] = (float)$row['pending_commissions'];
    }
} catch (\Throwable $e) {
    // Graceful fallback if table is unavailable
}

// 7. Recent Sites Provisioned (Last 5)
$recentSites = [];
$recentSitesRes = mysqli_query($connection, "SELECT s.site_id, s.site_name, s.site_domain, s.site_url, s.site_status, a.app_title, s.site_dashboard_version 
    FROM {$tblPrefix}site s 
    LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id 
    ORDER BY s.site_id DESC LIMIT 5");
if ($recentSitesRes) {
    while ($s = mysqli_fetch_assoc($recentSitesRes)) {
        $recentSites[] = [
            'id' => (int)$s['site_id'],
            'name' => htmlspecialchars($s['site_name'] ?? ''),
            'domain' => htmlspecialchars($s['site_domain'] ?? ''),
            'url' => htmlspecialchars($s['site_url'] ?? ''),
            'status' => (int)$s['site_status'],
            'app_title' => htmlspecialchars($s['app_title'] ?? 'Custom'),
            'version' => (int)$s['site_dashboard_version']
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'data' => [
        'apps' => $appStats,
        'themes' => $themeStats,
        'sites' => $siteStats,
        'users' => $userStats,
        'financials' => $financialStats,
        'commissions' => $commissionStats,
        'recent_sites' => $recentSites
    ]
]);
exit();
