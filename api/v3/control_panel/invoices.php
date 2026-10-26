<?php

/**
 * Control Panel: Invoice & Billing API Endpoint
 * Path: /api/v3/control_panel/invoices.php
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
// 1. GET: List Invoices or Single Invoice Details
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;

    // Single invoice lookup
    if ($invoiceId > 0) {
        $sql = "SELECT i.*, s.site_name, s.site_domain, s.site_url, a.app_title,
                       u.user_email, u.user_mobile, p.profile_display_name,
                       partner.user_email as partner_email, partner_p.profile_display_name as partner_name
                FROM {$tblPrefix}control_panel_invoice i
                LEFT JOIN {$tblPrefix}site s ON s.site_id = i.invoice_site_id
                LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
                LEFT JOIN {$tblPrefix}user u ON u.user_id = i.invoice_user_id
                LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
                LEFT JOIN {$tblPrefix}user partner ON partner.user_id = i.invoice_partner_id
                LEFT JOIN {$tblPrefix}profile partner_p ON partner_p.profile_user_id = partner.user_id
                WHERE i.invoice_id = {$invoiceId} LIMIT 1";
        $res = mysqli_query($connection, $sql);
        if ($res && $inv = mysqli_fetch_assoc($res)) {
            echo json_encode(['status' => 'success', 'data' => $inv]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Invoice not found.']);
        }
        exit();
    }

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $status = isset($_GET['status']) ? mysqli_real_escape_string($connection, trim($_GET['status'])) : '';
    $siteId = isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0;

    $where = [];
    if (!empty($search)) {
        $where[] = "(i.invoice_number LIKE '%{$search}%' OR s.site_name LIKE '%{$search}%' OR s.site_domain LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%')";
    }
    if (!empty($status)) {
        $where[] = "i.invoice_status = '{$status}'";
    }
    if ($siteId > 0) {
        $where[] = "i.invoice_site_id = {$siteId}";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $countRes = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}control_panel_invoice i 
        LEFT JOIN {$tblPrefix}site s ON s.site_id = i.invoice_site_id 
        LEFT JOIN {$tblPrefix}user u ON u.user_id = i.invoice_user_id 
        {$whereSql}");
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    $sql = "SELECT i.*, s.site_name, s.site_domain, a.app_title, u.user_email, p.profile_display_name
            FROM {$tblPrefix}control_panel_invoice i
            LEFT JOIN {$tblPrefix}site s ON s.site_id = i.invoice_site_id
            LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
            LEFT JOIN {$tblPrefix}user u ON u.user_id = i.invoice_user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            {$whereSql}
            ORDER BY i.invoice_id DESC
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $invoices = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $invoices[] = [
                'id' => (int)$row['invoice_id'],
                'invoice_number' => htmlspecialchars($row['invoice_number']),
                'site_id' => (int)$row['invoice_site_id'],
                'site_name' => htmlspecialchars($row['site_name'] ?? 'N/A'),
                'site_domain' => htmlspecialchars($row['site_domain'] ?? ''),
                'app_title' => htmlspecialchars($row['app_title'] ?? 'Generic'),
                'client_name' => htmlspecialchars($row['profile_display_name'] ?? $row['user_email'] ?? 'Client'),
                'client_email' => htmlspecialchars($row['user_email'] ?? ''),
                'plan' => htmlspecialchars($row['invoice_plan']),
                'amount' => (float)$row['invoice_amount'],
                'tax' => (float)$row['invoice_tax'],
                'total' => (float)$row['invoice_total'],
                'currency' => htmlspecialchars($row['invoice_currency']),
                'status' => htmlspecialchars($row['invoice_status']),
                'due_date' => $row['invoice_due_date'],
                'payment_date' => $row['invoice_payment_date'],
                'created_at' => $row['invoice_created_at']
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $invoices,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create Invoice or Update Payment Status
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'create';
    $invoiceId = isset($payload['invoice_id']) ? (int)$payload['invoice_id'] : 0;

    // Mark as Paid
    if ($action === 'mark_paid') {
        if ($invoiceId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Invoice ID']);
            exit();
        }

        // Fetch invoice details
        $invQ = mysqli_query($connection, "SELECT * FROM {$tblPrefix}control_panel_invoice WHERE invoice_id = {$invoiceId} LIMIT 1");
        if (!$invQ || mysqli_num_rows($invQ) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Invoice not found.']);
            exit();
        }
        $inv = mysqli_fetch_assoc($invQ);

        // Update invoice status
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}control_panel_invoice SET invoice_status = 'paid', invoice_payment_date = NOW() WHERE invoice_id = {$invoiceId}");
        if ($up) {
            // Check if there is an associated partner to trigger commission
            $partnerId = (int)$inv['invoice_partner_id'];
            if ($partnerId > 0) {
                $commRate = 10.00; // 10% default referral cut
                $commAmount = round(((float)$inv['invoice_amount'] * $commRate) / 100, 2);
                $siteId = (int)$inv['invoice_site_id'];

                mysqli_query($connection, "INSERT INTO {$tblPrefix}control_panel_commission 
                    (commission_partner_id, commission_site_id, commission_invoice_id, commission_rate_percent, commission_amount, commission_status, commission_notes)
                    VALUES ({$partnerId}, {$siteId}, {$invoiceId}, {$commRate}, {$commAmount}, 'pending', 'Auto-commission from paid invoice {$inv['invoice_number']}')");
            }

            echo json_encode(['status' => 'success', 'msg' => 'Invoice marked as paid and partner commission credited!']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Create New Invoice
    $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : 0;
    $plan = trim($payload['plan'] ?? 'Professional SaaS');
    $amount = isset($payload['amount']) ? (float)$payload['amount'] : 0.00;
    $tax = isset($payload['tax']) ? (float)$payload['tax'] : 0.00;
    $currency = trim($payload['currency'] ?? 'INR');
    $dueDate = !empty($payload['due_date']) ? date('Y-m-d 23:59:59', strtotime($payload['due_date'])) : date('Y-m-d 23:59:59', strtotime('+15 days'));
    $partnerId = isset($payload['partner_id']) ? (int)$payload['partner_id'] : 0;
    $notes = trim($payload['notes'] ?? '');

    if ($siteId <= 0 || $amount <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid Site ID and Amount are required.']);
        exit();
    }

    // Get site owner
    $sQ = mysqli_query($connection, "SELECT site_owner_id FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
    $ownerId = ($sQ && $r = mysqli_fetch_assoc($sQ)) ? (int)$r['site_owner_id'] : 1;

    $total = $amount + $tax;
    $invoiceNumber = 'BLX-INV-' . date('Y') . '-' . str_pad((string)rand(100, 99999), 5, '0', STR_PAD_LEFT);

    $escNum = mysqli_real_escape_string($connection, $invoiceNumber);
    $escPlan = mysqli_real_escape_string($connection, $plan);
    $escCurr = mysqli_real_escape_string($connection, $currency);
    $escNotes = mysqli_real_escape_string($connection, $notes);

    $insertSql = "INSERT INTO {$tblPrefix}control_panel_invoice 
        (invoice_number, invoice_site_id, invoice_user_id, invoice_partner_id, invoice_plan, invoice_amount, invoice_tax, invoice_total, invoice_currency, invoice_status, invoice_due_date, invoice_notes)
        VALUES ('{$escNum}', {$siteId}, {$ownerId}, {$partnerId}, '{$escPlan}', {$amount}, {$tax}, {$total}, '{$escCurr}', 'unpaid', '{$dueDate}', '{$escNotes}')";

    if (mysqli_query($connection, $insertSql)) {
        $newInvId = mysqli_insert_id($connection);
        echo json_encode([
            'status' => 'success',
            'msg' => 'Invoice created successfully!',
            'invoice_id' => $newInvId,
            'invoice_number' => $invoiceNumber
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
    }
    exit();
}
