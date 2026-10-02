<?php

/**
 * V3 Package API - Fetch Subscription / Product Packages for App
 * Path: /api/v3/packages/app-packages.php
 */

header('Content-Type: application/json; charset=utf-8');

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

// Map app string to app ID
$appIdMap = [
    'system'      => 1,
    'cms'         => 2,
    'ecom'        => 7,
    'ecommerce'   => 7,
    'gift'        => 9,
    'ecom_gift'   => 9,
    'e_com_gift'  => 9,
    'aleya'       => 20,
    'spa'         => 28,
    'travel'      => 26
];

// Determine target app ID
$rawApp = $_GET['app'] ?? ($_GET['app_type'] ?? ($_POST['app'] ?? ($_POST['app_type'] ?? '')));
$rawAppId = $_GET['app_id'] ?? ($_POST['app_id'] ?? 0);

$appId = 0;
if (!empty($rawAppId) && (int)$rawAppId > 0) {
    $appId = (int)$rawAppId;
} elseif (!empty($rawApp)) {
    $cleanApp = strtolower(trim((string)$rawApp));
    $appId = $appIdMap[$cleanApp] ?? 0;
    if ($appId <= 0) {
        $escAppName = mysqli_real_escape_string($connection, $cleanApp);
        $findAppQ = mysqli_query($connection, "SELECT app_id FROM {$tblPrefix}app WHERE app_path = '{$escAppName}' LIMIT 1");
        if ($findAppQ && $row = mysqli_fetch_assoc($findAppQ)) {
            $appId = (int)$row['app_id'];
        }
    }
}

if ($appId <= 0) {
    // Default to current domain site_app or Aleya (20)
    if (defined('SITE_APP')) {
        $appId = $appIdMap[strtolower(SITE_APP)] ?? 20;
    } else {
        $appId = 20; // Default Aleya app
    }
}

// Ensure default packages exist for Aleya (app 20) if missing
if ($appId === 20) {
    $checkQ = mysqli_query($connection, "SELECT package_id FROM {$tblPrefix}package WHERE package_app_id = 20 LIMIT 1");
    if (!$checkQ || mysqli_num_rows($checkQ) === 0) {
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}package 
                (package_app_id, package_title, package_description, package_image, package_price_inr, package_price_usd, package_status, package_launch_time, package_update_time) 
            VALUES 
                (20, 'CRM Starter', 'Lead auto-capture, smart follow-up pipeline, executive dashboard, 10 team seats included.', '/upload/others/crm-starter.png', '1499', '25', 1, NOW(), NOW()),
                (20, 'ERP Complete', 'Full travel agency OS, AI multi-day itinerary builder, supplier vouchers, invoicing & UPI links.', '/upload/others/erp-complete.png', '3999', '59', 1, NOW(), NOW()),
                (20, 'Enterprise Edition', 'Multi-branch operations, custom domain, API integrations, high-volume automation, priority SLA.', '/upload/others/enterprise.png', '9999', '149', 1, NOW(), NOW())
        ");
    }
}

$query = mysqli_query($connection, "
    SELECT 
        package_id, 
        package_app_id, 
        package_title, 
        package_description, 
        package_image, 
        package_price_inr, 
        package_price_usd, 
        package_status
    FROM {$tblPrefix}package
    WHERE package_app_id = {$appId} AND package_status = 1
    ORDER BY CAST(package_price_inr AS UNSIGNED) ASC
");

$packages = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $priceInr = (int)$row['package_price_inr'];
        $features = [];
        if ($priceInr <= 2000) {
            $features = [
                "Unlimited Leads & CRM Tracking",
                "Ready Tour Itinerary Builder",
                "WhatsApp Quotations & PDF Sharing",
                "Invoicing & UPI Payment Links",
                "10 Executive Team Seats"
            ];
        } elseif ($priceInr <= 5000) {
            $features = [
                "Everything in Starter",
                "Fleet & Driver Dispatching",
                "Hotel & Supplier Vouchers",
                "Automated GST & Invoicing",
                "Custom Workspace Subdomain"
            ];
        } else {
            $features = [
                "Everything in ERP Complete",
                "Multi-Branch & Unlimited Users",
                "Custom Domain Mapping (.com/.in)",
                "Dedicated Account Manager",
                "Priority 24/7 SLA Support"
            ];
        }

        $packages[] = [
            'id'            => (int)$row['package_id'],
            'package_id'    => (int)$row['package_id'],
            'app_id'        => (int)$row['package_app_id'],
            'title'         => $row['package_title'],
            'package_title' => $row['package_title'],
            'description'   => $row['package_description'] ?: '',
            'package_desc'  => $row['package_description'] ?: '',
            'image'         => $row['package_image'] ?: '',
            'price'         => $priceInr,
            'price_inr'     => $priceInr,
            'package_price' => $priceInr,
            'price_usd'     => (int)$row['package_price_usd'],
            'features'      => $features,
            'status'        => (int)$row['package_status']
        ];
    }
}

echo json_encode([
    'status'   => 'success',
    'app_id'   => $appId,
    'total'    => count($packages),
    'packages' => $packages
], JSON_UNESCAPED_UNICODE);
exit;
