<?php

/**
 * Control Panel: Partner Commission Management API Endpoint
 * Path: /api/v3/control_panel/commissions.php
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
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Ensure tables exist across all environments
require_once __DIR__ . '/schema_helper.php';
ensureControlPanelTables($connection, $tblPrefix);

// --------------------------------------------------------------------------
// 1. GET: List Commissions with Filters & Metrics
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $partnerId = isset($_GET['partner_id']) ? (int)$_GET['partner_id'] : 0;
    $status = isset($_GET['status']) ? mysqli_real_escape_string($connection, trim($_GET['status'])) : '';
    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';

    $where = [];
    if ($partnerId > 0) {
        $where[] = "c.commission_partner_id = {$partnerId}";
    }
    if (!empty($status)) {
        $where[] = "c.commission_status = '{$status}'";
    }
    if (!empty($search)) {
        $where[] = "(s.site_name LIKE '%{$search}%' OR s.site_domain LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    // Financial Metrics for Commissions
    $summarySql = "SELECT 
        COALESCE(SUM(c.commission_amount), 0) as total_amount,
        COALESCE(SUM(CASE WHEN c.commission_status = 'paid' THEN c.commission_amount ELSE 0 END), 0) as total_paid,
        COALESCE(SUM(CASE WHEN c.commission_status = 'pending' THEN c.commission_amount ELSE 0 END), 0) as total_pending,
        COALESCE(SUM(CASE WHEN c.commission_status = 'approved' THEN c.commission_amount ELSE 0 END), 0) as total_approved
        FROM {$tblPrefix}control_panel_commission c
        LEFT JOIN {$tblPrefix}site s ON s.site_id = c.commission_site_id
        LEFT JOIN {$tblPrefix}user u ON u.user_id = c.commission_partner_id
        LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
        {$whereSql}";
    $sumRes = mysqli_query($connection, $summarySql);
    $metrics = ($sumRes && $r = mysqli_fetch_assoc($sumRes)) ? [
        'total' => (float)$r['total_amount'],
        'paid' => (float)$r['total_paid'],
        'pending' => (float)$r['total_pending'],
        'approved' => (float)$r['total_approved']
    ] : ['total' => 0, 'paid' => 0, 'pending' => 0, 'approved' => 0];

    // Commissions List
    $sql = "SELECT c.*, s.site_name, s.site_domain, u.user_email as partner_email, p.profile_display_name as partner_name,
                   i.invoice_number
            FROM {$tblPrefix}control_panel_commission c
            LEFT JOIN {$tblPrefix}site s ON s.site_id = c.commission_site_id
            LEFT JOIN {$tblPrefix}user u ON u.user_id = c.commission_partner_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            LEFT JOIN {$tblPrefix}control_panel_invoice i ON i.invoice_id = c.commission_invoice_id
            {$whereSql}
            ORDER BY c.commission_id DESC";

    $res = mysqli_query($connection, $sql);
    $commissions = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $commissions[] = [
                'id' => (int)$row['commission_id'],
                'partner_id' => (int)$row['commission_partner_id'],
                'partner_name' => htmlspecialchars($row['partner_name'] ?? $row['partner_email'] ?? 'Partner'),
                'partner_email' => htmlspecialchars($row['partner_email'] ?? ''),
                'site_id' => (int)$row['commission_site_id'],
                'site_name' => htmlspecialchars($row['site_name'] ?? 'N/A'),
                'site_domain' => htmlspecialchars($row['site_domain'] ?? ''),
                'invoice_id' => (int)$row['commission_invoice_id'],
                'invoice_number' => htmlspecialchars($row['invoice_number'] ?? 'N/A'),
                'rate_percent' => (float)$row['commission_rate_percent'],
                'amount' => (float)$row['commission_amount'],
                'status' => htmlspecialchars($row['commission_status']),
                'payout_ref' => htmlspecialchars($row['commission_payout_ref'] ?? ''),
                'payout_date' => $row['commission_payout_date'],
                'created_at' => $row['commission_created_at'],
                'notes' => htmlspecialchars($row['commission_notes'] ?? '')
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'metrics' => $metrics,
        'data' => $commissions,
        'total' => count($commissions)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Approve, Payout, or Create Commission
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'create';
    $commissionId = isset($payload['commission_id']) ? (int)$payload['commission_id'] : 0;

    // Approve Commission
    if ($action === 'approve') {
        if ($commissionId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Commission ID']);
            exit();
        }
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}control_panel_commission SET commission_status = 'approved' WHERE commission_id = {$commissionId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Commission approved successfully. Ready for payout.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Process Payout
    if ($action === 'payout') {
        $payoutRef = trim($payload['payout_ref'] ?? '');
        if ($commissionId <= 0 || empty($payoutRef)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Commission ID and Payout Reference (UTR / Txn ID) are required.']);
            exit();
        }
        $escRef = mysqli_real_escape_string($connection, $payoutRef);
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}control_panel_commission 
            SET commission_status = 'paid', commission_payout_ref = '{$escRef}', commission_payout_date = NOW() 
            WHERE commission_id = {$commissionId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Commission payout recorded successfully!']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Create Manual Commission
    $partnerId = isset($payload['partner_id']) ? (int)$payload['partner_id'] : 0;
    $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : 0;
    $amount = isset($payload['amount']) ? (float)$payload['amount'] : 0.00;
    $rate = isset($payload['rate_percent']) ? (float)$payload['rate_percent'] : 10.00;
    $notes = trim($payload['notes'] ?? '');

    if ($partnerId <= 0 || $siteId <= 0 || $amount <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Partner ID, Site ID, and Commission Amount are required.']);
        exit();
    }

    $escNotes = mysqli_real_escape_string($connection, $notes);
    $insertSql = "INSERT INTO {$tblPrefix}control_panel_commission 
        (commission_partner_id, commission_site_id, commission_rate_percent, commission_amount, commission_status, commission_notes)
        VALUES ({$partnerId}, {$siteId}, {$rate}, {$amount}, 'pending', '{$escNotes}')";

    if (mysqli_query($connection, $insertSql)) {
        echo json_encode(['status' => 'success', 'msg' => 'Commission record created successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
    }
    exit();
}
