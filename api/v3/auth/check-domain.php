<?php

/**
 * V3 Authentication API - Real-time Workspace Domain & URL Availability Checker
 * Path: /api/v3/auth/check-domain.php
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');
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

$subdomain = trim($_GET['subdomain'] ?? ($_POST['subdomain'] ?? ''));

// Clean slug
$cleanSlug = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '', $subdomain));
$cleanSlug = trim($cleanSlug, '-');

if (empty($cleanSlug)) {
    echo json_encode([
        'status'    => 'error',
        'available' => false,
        'msg'       => 'Please enter a valid business name or subdomain.'
    ]);
    exit;
}

// Disallow reserved platform subdomains
$reservedSubdomains = [
    'www',
    'api',
    'admin',
    'administrator',
    'mail',
    'app',
    'dashboard',
    'auth',
    'login',
    'signup',
    'register',
    'checkout',
    'pay',
    'billing',
    'support',
    'help',
    'static',
    'cdn',
    'assets',
    'portal',
    'crm',
    'erp'
];

if (in_array($cleanSlug, $reservedSubdomains, true)) {
    echo json_encode([
        'status'    => 'success',
        'available' => false,
        'subdomain' => $cleanSlug,
        'msg'       => "The name '{$cleanSlug}' is reserved. Please choose another name."
    ]);
    exit;
}

$appParam = strtolower(trim($_GET['app'] ?? ($_POST['app'] ?? 'aleya')));
$domainSuffix = ($appParam === 'aleya') ? '.aleya.in' : '.blumox.com';
$displayDomain = $cleanSlug . $domainSuffix;

$mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : 'blumox.com';
$fullDomain = $cleanSlug . '.' . $mainDomain;
$escSlug    = mysqli_real_escape_string($connection, $cleanSlug);
$escFull    = mysqli_real_escape_string($connection, $fullDomain);

// Optionally exclude current user's newly initialized site
$excludeSiteClause = '';
$userEmail = strtolower(trim($_GET['setup_email'] ?? ($_GET['email'] ?? '')));
if (empty($userEmail) && !empty($_GET['setup_id'])) {
    $decoded = base64_decode($_GET['setup_id']);
    if ($decoded && filter_var($decoded, FILTER_VALIDATE_EMAIL)) {
        $userEmail = strtolower(trim($decoded));
    }
}
if (empty($userEmail) && !empty($_SESSION['user_email'])) {
    $userEmail = strtolower(trim($_SESSION['user_email']));
}

if (!empty($userEmail)) {
    $escUserEmail = mysqli_real_escape_string($connection, $userEmail);
    $userQ = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_email = '{$escUserEmail}' LIMIT 1");
    if ($userQ && $userRow = mysqli_fetch_assoc($userQ)) {
        $currUserId = (int)$userRow['user_id'];
        $userSiteQ = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_owner_id = {$currUserId} AND site_id > 1 LIMIT 1");
        if ($userSiteQ && $siteRow = mysqli_fetch_assoc($userSiteQ)) {
            $excludeSiteId = (int)$siteRow['site_id'];
            $excludeSiteClause = " AND site_id != {$excludeSiteId} ";
        }
    }
}

// Check availability in site table
$checkQuery = mysqli_query($connection, "
    SELECT site_id, site_name, site_url 
    FROM {$tblPrefix}site 
    WHERE (
        site_url LIKE '%//{$escSlug}.%' 
        OR site_url = 'https://{$escFull}'
        OR site_url = 'http://{$escFull}'
        OR site_domain = '{$escFull}'
        OR site_domain LIKE '{$escSlug}.%'
    ) {$excludeSiteClause}
    LIMIT 1
");

if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
    echo json_encode([
        'status'    => 'success',
        'available' => false,
        'subdomain' => $cleanSlug,
        'display'   => $displayDomain,
        'msg'       => "Workspace URL '{$displayDomain}' is already taken."
    ]);
} else {
    echo json_encode([
        'status'    => 'success',
        'available' => true,
        'subdomain' => $cleanSlug,
        'display'   => $displayDomain,
        'msg'       => "Workspace URL '{$displayDomain}' is available!"
    ]);
}
exit;
