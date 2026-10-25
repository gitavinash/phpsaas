<?php

/**
 * Senior QA Engineer Automated Integration & Functional Test Suite
 * Tests all Control Panel V3 Endpoints via HTTP against http://ay.local
 */

$baseUrl = 'http://ay.local/api/v3/control_panel/';

function http_req($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
    $headers = [
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) {
            $payload = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($payload);
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $cleanJson = $response;
    $startBrace = strpos($response, '{');
    $endBrace = strrpos($response, '}');
    if ($startBrace !== false && $endBrace !== false && $endBrace > $startBrace) {
        $cleanJson = substr($response, $startBrace, $endBrace - $startBrace + 1);
    }
    $json = json_decode($cleanJson, true);
    return ['code' => $httpCode, 'raw' => $response, 'json' => $json];
}

$passed = 0;
$failed = 0;

function run_test($name, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $name\n";
        $passed++;
    } else {
        echo "  [FAIL] $name" . ($details ? " -> $details" : "") . "\n";
        $failed++;
    }
}

echo "=================================================================\n";
echo "SENIOR QA ENGINEER: CONTROL PANEL V3 COMPREHENSIVE TEST SUITE\n";
echo "Target Base URL: $baseUrl\n";
echo "=================================================================\n\n";

// 1. Overview API
echo "--- 1. Testing Overview API ---\n";
$resOverview = http_req($baseUrl . 'overview/');
run_test("Overview API returns HTTP 200", $resOverview['code'] === 200);
run_test("Overview API status is success", ($resOverview['json']['status'] ?? '') === 'success');
run_test("Apps KPI total count >= 33", ($resOverview['json']['data']['apps']['total'] ?? 0) >= 33);
run_test("Sites KPI total count >= 360", ($resOverview['json']['data']['sites']['total'] ?? 0) >= 360);
run_test("Users KPI total count >= 5000", ($resOverview['json']['data']['users']['total'] ?? 0) >= 5000);
run_test("Financials summary structure exists", isset($resOverview['json']['data']['financials']));
run_test("Commissions summary structure exists", isset($resOverview['json']['data']['commissions']));
run_test("Recent sites list populated", !empty($resOverview['json']['data']['recent_sites']));

// 2. Apps API
echo "\n--- 2. Testing Apps API ---\n";
$resApps = http_req($baseUrl . 'apps/?status=1');
run_test("Apps List returns HTTP 200", $resApps['code'] === 200);
run_test("Apps List contains active apps", count($resApps['json']['data'] ?? []) >= 30);

// Create Test App
$newAppPath = 'qa_test_app_' . time();
$resCreateApp = http_req($baseUrl . 'apps/', 'POST', [
    'action' => 'save',
    'app_title' => 'QA Automated App',
    'app_path' => $newAppPath,
    'app_description' => 'Created via Senior QA automated test runner',
    'app_primary_color' => '#10b981'
]);
run_test("Create App returns HTTP 200 & success", ($resCreateApp['json']['status'] ?? '') === 'success');

// 3. Themes API
echo "\n--- 3. Testing Themes API ---\n";
$resThemes = http_req($baseUrl . 'themes/?app_id=1');
run_test("Themes List returns HTTP 200", $resThemes['code'] === 200);
run_test("Themes List for App 1 contains default theme", count($resThemes['json']['data'] ?? []) >= 1);

// 4. Site License Quotas & Feature Toggles API
echo "\n--- 4. Testing Site License Quotas & Feature Toggles API ---\n";
$resGetLic = http_req($baseUrl . 'sites/?action=get_license&site_id=1');
run_test("Get License for Site 1 returns HTTP 200", $resGetLic['code'] === 200);
run_test("Get License returns site owner and domain", ($resGetLic['json']['data']['site_domain'] ?? '') === 'ay.local');
run_test("License features include whatsapp_enable toggle", isset($resGetLic['json']['data']['features']['whatsapp_enable']));

// Update Site 1 License limits (user limit, whatsapp device limit, module feature flags)
$resSaveLic = http_req($baseUrl . 'sites/', 'POST', [
    'action' => 'save_license',
    'site_id' => 1,
    'user_limit' => 25,
    'wa_device_limit' => 5,
    'subscription_plan' => 'Enterprise Cloud',
    'subscription_expiry' => '2028-12-31',
    'site_status' => 1,
    'features' => [
        'whatsapp_enable' => 1,
        'feature_leads' => 1,
        'feature_accounts' => 1,
        'feature_transporters' => 1,
        'feature_packages' => 1,
        'feature_api_access' => 1,
        'site_custom_domain' => 1
    ]
]);
run_test("Save License returns HTTP 200 & success", ($resSaveLic['json']['status'] ?? '') === 'success');

// Verify updated values persisted
$resVerifyLic = http_req($baseUrl . 'sites/?action=get_license&site_id=1');
run_test("Verified user_limit updated to 25", ($resVerifyLic['json']['data']['user_limit'] ?? 0) == 25);
run_test("Verified wa_device_limit updated to 5", ($resVerifyLic['json']['data']['wa_device_limit'] ?? 0) == 5);
run_test("Verified subscription_plan updated to Enterprise Cloud", ($resVerifyLic['json']['data']['subscription_plan'] ?? '') === 'Enterprise Cloud');

// 5. Users & RBAC API (5 User Types)
echo "\n--- 5. Testing Users & RBAC API (5 Roles) ---\n";
$resUsers = http_req($baseUrl . 'users/?role=administrator');
run_test("Users List returns HTTP 200", $resUsers['code'] === 200);
run_test("Users List contains administrator accounts", count($resUsers['json']['data'] ?? []) >= 1);

// Create Partner User
$partnerEmail = 'qa_partner_' . time() . '_' . rand(100, 999) . '@test.com';
$partnerMobile = '9' . rand(100000000, 999999999);
$resPartner = http_req($baseUrl . 'users/', 'POST', [
    'email' => $partnerEmail,
    'display_name' => 'QA Referral Partner',
    'mobile' => $partnerMobile,
    'role' => 'partner',
    'password' => 'Partner@123'
]);
run_test("Create Partner User returns success", ($resPartner['json']['status'] ?? '') === 'success');
$partnerUserId = $resPartner['json']['user_id'] ?? 0;

// Create On Ground Sales User
$groundEmail = 'qa_ground_' . time() . '_' . rand(100, 999) . '@test.com';
$groundMobile = '9' . rand(100000000, 999999999);
$resGround = http_req($baseUrl . 'users/', 'POST', [
    'email' => $groundEmail,
    'display_name' => 'QA On Ground Rep',
    'mobile' => $groundMobile,
    'role' => 'on_ground_sales',
    'password' => 'Ground@123'
]);
run_test("Create On Ground Sales User returns success", ($resGround['json']['status'] ?? '') === 'success');

// 6. Invoices & Billing API
echo "\n--- 6. Testing Invoices & Billing API ---\n";
$resCreateInv = http_req($baseUrl . 'invoices/', 'POST', [
    'action' => 'create',
    'site_id' => 1,
    'plan' => 'Annual SaaS Subscription',
    'amount' => 50000.00,
    'tax' => 9000.00,
    'due_date' => date('Y-m-d', strtotime('+30 days')),
    'partner_id' => $partnerUserId
]);
run_test("Create Invoice returns success", ($resCreateInv['json']['status'] ?? '') === 'success');
$invoiceId = $resCreateInv['json']['invoice_id'] ?? 0;
$invoiceNum = $resCreateInv['json']['invoice_number'] ?? '';
run_test("Invoice ID and Number generated", $invoiceId > 0 && !empty($invoiceNum));

// View Invoice Details
$resViewInv = http_req($baseUrl . 'invoices/?invoice_id=' . $invoiceId);
run_test("View Invoice details returns correct amount", ($resViewInv['json']['data']['invoice_amount'] ?? 0) == 50000.00);

// Mark Invoice Paid -> Triggers Automatic Partner Commission
echo "\n--- 7. Testing Invoice Payment & Partner Commission Lifecycle ---\n";
$resMarkPaid = http_req($baseUrl . 'invoices/', 'POST', [
    'action' => 'mark_paid',
    'invoice_id' => $invoiceId
]);
run_test("Mark Invoice Paid returns success", ($resMarkPaid['json']['status'] ?? '') === 'success');

// Check Commissions Table
$resComm = http_req($baseUrl . 'commissions/?status=pending');
run_test("Commissions list returns pending record", count($resComm['json']['data'] ?? []) >= 1);
$targetComm = null;
foreach ($resComm['json']['data'] ?? [] as $c) {
    if ($c['invoice_id'] == $invoiceId) {
        $targetComm = $c;
        break;
    }
}
run_test("Commission auto-calculated 10% on 50000 = 5000", ($targetComm['amount'] ?? 0) == 5000.00);
$commId = $targetComm['id'] ?? 0;

// Approve Commission
$resApprove = http_req($baseUrl . 'commissions/', 'POST', [
    'action' => 'approve',
    'commission_id' => $commId
]);
run_test("Approve Commission returns success", ($resApprove['json']['status'] ?? '') === 'success');

// Record Commission Payout
$resPayout = http_req($baseUrl . 'commissions/', 'POST', [
    'action' => 'payout',
    'commission_id' => $commId,
    'payout_ref' => 'UTR_BANK_TXN_' . time()
]);
run_test("Record Commission Payout returns success", ($resPayout['json']['status'] ?? '') === 'success');

// 8. Configurations API
echo "\n--- 8. Testing Configurations API ---\n";
$testCfgKey = 'qa_test_cfg_' . time();
$resSaveCfg = http_req($baseUrl . 'configurations/', 'POST', [
    'action' => 'save',
    'key' => $testCfgKey,
    'value' => 'Enterprise-V3-Tested',
    'global' => 1
]);
run_test("Save Global Config parameter returns success", ($resSaveCfg['json']['status'] ?? '') === 'success');

$resGetCfg = http_req($baseUrl . 'configurations/?search=' . $testCfgKey);
run_test("Configuration retrieved matching saved key", count($resGetCfg['json']['data'] ?? []) >= 1);

echo "\n=================================================================\n";
echo "QA TEST RESULTS: $passed PASSED, $failed FAILED\n";
echo "=================================================================\n";

if ($failed > 0) {
    exit(1);
} else {
    exit(0);
}
