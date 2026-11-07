<?php

/**
 * Control Panel: Partner & Referral Management API Endpoint
 * Path: /api/v3/control_panel/partners.php
 * Strictly V3 Standard: Secure, JSON envelope, multi-tenant aware
 */

header("Content-Type: application/json; charset=utf-8");

if (!isset($connection) || !($connection instanceof mysqli)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
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

// Ensure required tables exist
require_once __DIR__ . '/schema_helper.php';
ensureControlPanelTables($connection, $tblPrefix);

// --------------------------------------------------------------------------
// 1. GET: List Partners, Single Partner Details, or Referred Clients
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // ----------------------------------------------------------------------
    // A. Detailed Referred Clients for a specific Partner
    // ----------------------------------------------------------------------
    if ($action === 'clients') {
        $partnerId = isset($_GET['partner_id']) ? (int)$_GET['partner_id'] : 0;
        if ($partnerId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Partner ID is required.']);
            exit();
        }

        // Fetch partner details
        $pSql = "SELECT u.user_id, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                        p.profile_display_name, p.profile_image,
                        sa.site_access_id, sa.site_access_status, sa.site_access_meta
                 FROM {$tblPrefix}user u
                 INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_type = 'partner'
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 WHERE u.user_id = {$partnerId}
                 LIMIT 1";
        $pRes = mysqli_query($connection, $pSql);
        if (!$pRes || mysqli_num_rows($pRes) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Partner not found.']);
            exit();
        }

        $pRow = mysqli_fetch_assoc($pRes);
        $meta = !empty($pRow['site_access_meta']) ? @json_decode($pRow['site_access_meta'], true) : [];
        if (!is_array($meta)) $meta = [];

        $partnerData = [
            'id' => (int)$pRow['user_id'],
            'name' => htmlspecialchars($pRow['profile_display_name'] ?? 'Partner'),
            'email' => htmlspecialchars($pRow['user_email'] ?? ''),
            'mobile' => htmlspecialchars($pRow['user_mobile'] ?? ''),
            'status' => (int)$pRow['site_access_status'],
            'commission_rate' => isset($meta['commission_rate']) ? (float)$meta['commission_rate'] : 10.0,
            'bank_name' => htmlspecialchars($meta['bank_name'] ?? ''),
            'account_number' => htmlspecialchars($meta['account_number'] ?? ''),
            'ifsc_code' => htmlspecialchars($meta['ifsc_code'] ?? ''),
            'upi_id' => htmlspecialchars($meta['upi_id'] ?? ''),
            'notes' => htmlspecialchars($meta['notes'] ?? ''),
            'reg_time' => $pRow['user_reg_time']
        ];

        // Search within clients
        $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
        $whereSearch = "";
        if (!empty($search)) {
            $whereSearch = "AND (s.site_name LIKE '%{$search}%' OR s.site_domain LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
        }

        // Query referred clients
        $cSql = "SELECT s.site_id, s.site_name, s.site_domain, s.site_url, s.site_status,
                        a.app_title, a.app_path, t.theme_title,
                        u.user_id as owner_id, u.user_email as owner_email, u.user_mobile as owner_mobile,
                        p.profile_display_name as owner_name,
                        cpc.commission_id, cpc.commission_amount, cpc.commission_rate_percent, cpc.commission_status, cpc.commission_created_at, cpc.commission_payout_ref,
                        (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_plan' LIMIT 1) as subscription_plan,
                        (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_expiry' LIMIT 1) as subscription_expiry,
                        (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'user_limit' LIMIT 1) as user_limit
                 FROM {$tblPrefix}site s
                 LEFT JOIN {$tblPrefix}site_configuration sc ON sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'partner_id'
                 LEFT JOIN {$tblPrefix}control_panel_commission cpc ON cpc.commission_site_id = s.site_id AND cpc.commission_partner_id = {$partnerId}
                 LEFT JOIN {$tblPrefix}app a ON a.app_id = s.site_app_id
                 LEFT JOIN {$tblPrefix}theme t ON t.theme_id = s.site_theme_id
                 LEFT JOIN {$tblPrefix}user u ON u.user_id = s.site_owner_id
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 WHERE (sc.site_configuration_value = '{$partnerId}' OR cpc.commission_partner_id = {$partnerId})
                 {$whereSearch}
                 GROUP BY s.site_id
                 ORDER BY s.site_id DESC";

        $cRes = mysqli_query($connection, $cSql);
        $clients = [];
        $totalCommissionEarned = 0.00;
        $totalCommissionPaid = 0.00;

        if ($cRes) {
            while ($cRow = mysqli_fetch_assoc($cRes)) {
                $commAmt = (float)($cRow['commission_amount'] ?? 0);
                $totalCommissionEarned += $commAmt;
                if (($cRow['commission_status'] ?? '') === 'paid') {
                    $totalCommissionPaid += $commAmt;
                }

                $siteDomain = !empty($cRow['site_domain']) ? trim($cRow['site_domain']) : trim($cRow['site_url'] ?? '');
                $siteName = !empty($cRow['site_name']) ? trim($cRow['site_name']) : ($siteDomain ?: 'Site #' . $cRow['site_id']);

                $clients[] = [
                    'site_id' => (int)$cRow['site_id'],
                    'site_name' => htmlspecialchars($siteName),
                    'site_domain' => htmlspecialchars($siteDomain),
                    'site_status' => (int)$cRow['site_status'],
                    'app_title' => htmlspecialchars($cRow['app_title'] ?? 'Generic SaaS'),
                    'theme_title' => htmlspecialchars($cRow['theme_title'] ?? 'Default Theme'),
                    'owner_id' => (int)($cRow['owner_id'] ?? 0),
                    'owner_name' => htmlspecialchars($cRow['owner_name'] ?? 'Client Owner'),
                    'owner_email' => htmlspecialchars($cRow['owner_email'] ?? ''),
                    'owner_mobile' => htmlspecialchars($cRow['owner_mobile'] ?? ''),
                    'subscription_plan' => htmlspecialchars($cRow['subscription_plan'] ?? 'Professional SaaS'),
                    'subscription_expiry' => $cRow['subscription_expiry'] ?? null,
                    'user_limit' => (int)($cRow['user_limit'] ?? 10),
                    'commission_id' => (int)($cRow['commission_id'] ?? 0),
                    'commission_amount' => $commAmt,
                    'commission_rate' => (float)($cRow['commission_rate_percent'] ?? 10.0),
                    'commission_status' => htmlspecialchars($cRow['commission_status'] ?? 'pending'),
                    'payout_ref' => htmlspecialchars($cRow['commission_payout_ref'] ?? ''),
                    'assigned_date' => $cRow['commission_created_at'] ?? null
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'partner' => $partnerData,
            'clients' => $clients,
            'total_clients' => count($clients),
            'metrics' => [
                'total_clients' => count($clients),
                'total_commission' => $totalCommissionEarned,
                'paid_commission' => $totalCommissionPaid,
                'pending_commission' => max(0, $totalCommissionEarned - $totalCommissionPaid)
            ]
        ]);
        exit();
    }

    // ----------------------------------------------------------------------
    // B. Single Partner Details for Edit Modal
    // ----------------------------------------------------------------------
    if ($action === 'get_partner') {
        $partnerId = isset($_GET['partner_id']) ? (int)$_GET['partner_id'] : 0;
        if ($partnerId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Partner ID is required.']);
            exit();
        }

        $pSql = "SELECT u.user_id, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                        p.profile_display_name, p.profile_image,
                        sa.site_access_id, sa.site_access_status, sa.site_access_meta
                 FROM {$tblPrefix}user u
                 INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_type = 'partner'
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 WHERE u.user_id = {$partnerId}
                 LIMIT 1";
        $pRes = mysqli_query($connection, $pSql);
        if (!$pRes || mysqli_num_rows($pRes) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Partner not found.']);
            exit();
        }

        $pRow = mysqli_fetch_assoc($pRes);
        $meta = !empty($pRow['site_access_meta']) ? @json_decode($pRow['site_access_meta'], true) : [];
        if (!is_array($meta)) $meta = [];

        echo json_encode([
            'status' => 'success',
            'partner' => [
                'id' => (int)$pRow['user_id'],
                'display_name' => htmlspecialchars($pRow['profile_display_name'] ?? ''),
                'email' => htmlspecialchars($pRow['user_email'] ?? ''),
                'mobile' => htmlspecialchars($pRow['user_mobile'] ?? ''),
                'status' => (int)$pRow['site_access_status'],
                'commission_rate' => isset($meta['commission_rate']) ? (float)$meta['commission_rate'] : 10.0,
                'bank_name' => htmlspecialchars($meta['bank_name'] ?? ''),
                'account_number' => htmlspecialchars($meta['account_number'] ?? ''),
                'ifsc_code' => htmlspecialchars($meta['ifsc_code'] ?? ''),
                'upi_id' => htmlspecialchars($meta['upi_id'] ?? ''),
                'pan_gst' => htmlspecialchars($meta['pan_gst'] ?? ''),
                'notes' => htmlspecialchars($meta['notes'] ?? ''),
                'reg_time' => $pRow['user_reg_time']
            ]
        ]);
        exit();
    }

    // ----------------------------------------------------------------------
    // C. Default: List All Partners with Client Count & Commission Ledger
    // ----------------------------------------------------------------------
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = [];
    $where[] = "sa.site_access_type = 'partner'";
    if (!empty($search)) {
        $where[] = "(u.user_username LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR u.user_mobile LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
    }
    if ($statusFilter !== null) {
        $where[] = "sa.site_access_status = {$statusFilter}";
    }
    $whereSql = "WHERE " . implode(' AND ', $where);

    // Filtered count
    $countSql = "SELECT COUNT(DISTINCT u.user_id) as total
                 FROM {$tblPrefix}user u
                 INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                 {$whereSql}";
    $countRes = mysqli_query($connection, $countSql);
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    // Overall KPI Metrics for Partner Program
    $kpiSql = "SELECT 
                COUNT(DISTINCT sa.site_access_user_id) as total_partners,
                SUM(CASE WHEN sa.site_access_status = 1 THEN 1 ELSE 0 END) as active_partners
               FROM {$tblPrefix}site_access sa
               WHERE sa.site_access_type = 'partner'";
    $kpiRes = mysqli_query($connection, $kpiSql);
    $kpiRow = ($kpiRes && $kr = mysqli_fetch_assoc($kpiRes)) ? $kr : ['total_partners' => 0, 'active_partners' => 0];

    // Total distinct referred clients and commission financials
    $commKpiSql = "SELECT 
                    COUNT(DISTINCT commission_site_id) as total_referred_clients,
                    COALESCE(SUM(commission_amount), 0) as total_comm,
                    COALESCE(SUM(CASE WHEN commission_status = 'paid' THEN commission_amount ELSE 0 END), 0) as paid_comm,
                    COALESCE(SUM(CASE WHEN commission_status IN ('pending', 'approved') THEN commission_amount ELSE 0 END), 0) as pending_comm
                   FROM {$tblPrefix}control_panel_commission";
    $commKpiRes = mysqli_query($connection, $commKpiSql);
    $commKpiRow = ($commKpiRes && $ckr = mysqli_fetch_assoc($commKpiRes)) ? $ckr : [];

    $metrics = [
        'total_partners' => (int)($kpiRow['total_partners'] ?? 0),
        'active_partners' => (int)($kpiRow['active_partners'] ?? 0),
        'total_referred_clients' => (int)($commKpiRow['total_referred_clients'] ?? 0),
        'total_commission' => (float)($commKpiRow['total_comm'] ?? 0),
        'paid_commission' => (float)($commKpiRow['paid_comm'] ?? 0),
        'pending_commission' => (float)($commKpiRow['pending_comm'] ?? 0)
    ];

    // Partners list query
    $sql = "SELECT u.user_id, u.user_username, u.user_email, u.user_mobile, u.user_status, u.user_reg_time,
                   p.profile_display_name, p.profile_image,
                   sa.site_access_id, sa.site_access_status, sa.site_access_meta,
                   (SELECT COUNT(DISTINCT s.site_id) 
                    FROM {$tblPrefix}site s 
                    LEFT JOIN {$tblPrefix}site_configuration sc ON sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'partner_id'
                    LEFT JOIN {$tblPrefix}control_panel_commission cpc ON cpc.commission_site_id = s.site_id
                    WHERE sc.site_configuration_value = CAST(u.user_id AS CHAR) OR cpc.commission_partner_id = u.user_id) as referred_clients_count,
                   (SELECT COALESCE(SUM(commission_amount), 0) FROM {$tblPrefix}control_panel_commission WHERE commission_partner_id = u.user_id) as total_comm,
                   (SELECT COALESCE(SUM(CASE WHEN commission_status = 'paid' THEN commission_amount ELSE 0 END), 0) FROM {$tblPrefix}control_panel_commission WHERE commission_partner_id = u.user_id) as paid_comm,
                   (SELECT COALESCE(SUM(CASE WHEN commission_status IN ('pending', 'approved') THEN commission_amount ELSE 0 END), 0) FROM {$tblPrefix}control_panel_commission WHERE commission_partner_id = u.user_id) as pending_comm
            FROM {$tblPrefix}user u
            INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            {$whereSql}
            GROUP BY u.user_id
            ORDER BY u.user_id DESC
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $partners = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $meta = !empty($row['site_access_meta']) ? @json_decode($row['site_access_meta'], true) : [];
            if (!is_array($meta)) $meta = [];

            $partners[] = [
                'id' => (int)$row['user_id'],
                'username' => htmlspecialchars($row['user_username'] ?? ''),
                'email' => htmlspecialchars($row['user_email'] ?? ''),
                'mobile' => htmlspecialchars($row['user_mobile'] ?? ''),
                'display_name' => htmlspecialchars($row['profile_display_name'] ?? $row['user_username']),
                'status' => (int)$row['site_access_status'],
                'user_status' => (int)$row['user_status'],
                'clients_count' => (int)$row['referred_clients_count'],
                'commission_rate' => isset($meta['commission_rate']) ? (float)$meta['commission_rate'] : 10.0,
                'total_commission' => (float)$row['total_comm'],
                'paid_commission' => (float)$row['paid_comm'],
                'pending_commission' => (float)$row['pending_comm'],
                'bank_name' => htmlspecialchars($meta['bank_name'] ?? ''),
                'account_number' => htmlspecialchars($meta['account_number'] ?? ''),
                'ifsc_code' => htmlspecialchars($meta['ifsc_code'] ?? ''),
                'upi_id' => htmlspecialchars($meta['upi_id'] ?? ''),
                'notes' => htmlspecialchars($meta['notes'] ?? ''),
                'reg_time' => $row['user_reg_time'],
                'image' => htmlspecialchars($row['profile_image'] ?? '')
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'metrics' => $metrics,
        'data' => $partners,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create Partner, Edit Partner Details, or Toggle Status
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'update_partner';
    $partnerId = isset($payload['partner_id']) ? (int)$payload['partner_id'] : (isset($payload['user_id']) ? (int)$payload['user_id'] : 0);

    // Action 1: Toggle Partner Status
    if ($action === 'toggle_status') {
        if ($partnerId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Partner ID.']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        mysqli_query($connection, "UPDATE {$tblPrefix}site_access SET site_access_status = {$newStatus} WHERE site_access_user_id = {$partnerId} AND site_access_type = 'partner'");
        echo json_encode(['status' => 'success', 'msg' => $newStatus ? 'Partner activated successfully.' : 'Partner disabled successfully.']);
        exit();
    }

    // Action 2: Edit / Update Partner Details
    if ($action === 'update_partner') {
        if ($partnerId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Partner ID is required.']);
            exit();
        }

        $displayName = trim($payload['display_name'] ?? '');
        $email = trim($payload['email'] ?? '');
        $mobile = trim($payload['mobile'] ?? '');
        $commissionRate = isset($payload['commission_rate']) ? (float)$payload['commission_rate'] : 10.0;
        $status = isset($payload['status']) ? (int)$payload['status'] : 1;
        $bankName = trim($payload['bank_name'] ?? '');
        $accountNumber = trim($payload['account_number'] ?? '');
        $ifscCode = trim($payload['ifsc_code'] ?? '');
        $upiId = trim($payload['upi_id'] ?? '');
        $panGst = trim($payload['pan_gst'] ?? '');
        $notes = trim($payload['notes'] ?? '');

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Email address is required.']);
            exit();
        }

        // Check email uniqueness if changed
        $escEmail = mysqli_real_escape_string($connection, $email);
        $chkE = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_email = '{$escEmail}' AND user_id != {$partnerId} LIMIT 1");
        if ($chkE && mysqli_num_rows($chkE) > 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => "Email '{$email}' is already in use by another account."]);
            exit();
        }

        $escMobile = mysqli_real_escape_string($connection, $mobile);
        $escName = mysqli_real_escape_string($connection, $displayName);

        // Update user and profile
        mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_email = '{$escEmail}', user_mobile = '{$escMobile}' WHERE user_id = {$partnerId}");
        if (!empty($displayName)) {
            mysqli_query($connection, "UPDATE {$tblPrefix}profile SET profile_display_name = '{$escName}', profile_email = '{$escEmail}', profile_mobile = '{$escMobile}' WHERE profile_user_id = {$partnerId}");
        }

        // Prepare JSON meta for partner details
        $metaData = [
            'commission_rate' => $commissionRate,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'upi_id' => $upiId,
            'pan_gst' => $panGst,
            'notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $escMeta = mysqli_real_escape_string($connection, json_encode($metaData));

        mysqli_query($connection, "UPDATE {$tblPrefix}site_access 
                                   SET site_access_status = {$status}, site_access_meta = '{$escMeta}' 
                                   WHERE site_access_user_id = {$partnerId} AND site_access_type = 'partner'");

        echo json_encode([
            'status' => 'success',
            'msg' => 'Partner profile and commission details updated successfully!',
            'partner_id' => $partnerId
        ]);
        exit();
    }

    // Action 3: Create New Partner Account
    if ($action === 'create_partner') {
        $email = trim($payload['email'] ?? '');
        $displayName = trim($payload['display_name'] ?? '');
        $mobile = trim($payload['mobile'] ?? '');
        $password = trim($payload['password'] ?? 'Partner@2026');
        $commissionRate = isset($payload['commission_rate']) ? (float)$payload['commission_rate'] : 10.0;
        $bankName = trim($payload['bank_name'] ?? '');
        $accountNumber = trim($payload['account_number'] ?? '');
        $ifscCode = trim($payload['ifsc_code'] ?? '');
        $upiId = trim($payload['upi_id'] ?? '');
        $notes = trim($payload['notes'] ?? '');

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Email address is required.']);
            exit();
        }

        $escEmail = mysqli_real_escape_string($connection, $email);
        $escMobile = mysqli_real_escape_string($connection, $mobile);
        $escName = mysqli_real_escape_string($connection, !empty($displayName) ? $displayName : explode('@', $email)[0]);

        $metaData = [
            'commission_rate' => $commissionRate,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'upi_id' => $upiId,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $escMeta = mysqli_real_escape_string($connection, json_encode($metaData));

        // Check if user already exists
        $chkUser = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_email = '{$escEmail}' LIMIT 1");
        if ($chkUser && $uRow = mysqli_fetch_assoc($chkUser)) {
            $existingUserId = (int)$uRow['user_id'];
            // Grant partner site access on control panel
            $chkAcc = mysqli_query($connection, "SELECT site_access_id FROM {$tblPrefix}site_access WHERE site_access_user_id = {$existingUserId} AND site_access_type = 'partner'");
            if ($chkAcc && mysqli_num_rows($chkAcc) > 0) {
                mysqli_query($connection, "UPDATE {$tblPrefix}site_access SET site_access_status = 1, site_access_meta = '{$escMeta}' WHERE site_access_user_id = {$existingUserId} AND site_access_type = 'partner'");
            } else {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
                                           VALUES ({$controlPanelSiteId}, {$existingUserId}, 'partner', 1, '{$escMeta}')");
            }
            echo json_encode([
                'status' => 'success',
                'msg' => "Existing user '{$email}' registered as Referral Partner successfully!",
                'partner_id' => $existingUserId
            ]);
            exit();
        }

        // Create new user
        $cleanPrefix = substr(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]), 0, 14);
        $username = ($cleanPrefix ?: 'partner') . '_' . rand(100, 999);
        $salt = hash('sha256', uniqid(mt_rand(), true) . time());
        $enc = hash('sha256', $salt . $password);
        $regIp = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        $insUser = "INSERT INTO {$tblPrefix}user (user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip, user_refer_id)
                    VALUES (0, '{$username}', '{$escEmail}', '{$escMobile}', '{$enc}', '{$salt}', 1, NOW(), '{$regIp}', 0)";
        if (mysqli_query($connection, $insUser)) {
            $newUserId = mysqli_insert_id($connection);
            mysqli_query($connection, "INSERT INTO {$tblPrefix}profile (profile_user_id, profile_display_name, profile_email, profile_mobile) VALUES ({$newUserId}, '{$escName}', '{$escEmail}', '{$escMobile}')");
            mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
                                       VALUES ({$controlPanelSiteId}, {$newUserId}, 'partner', 1, '{$escMeta}')");

            echo json_encode([
                'status' => 'success',
                'msg' => 'New Referral Partner account created successfully!',
                'partner_id' => $newUserId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }
}
