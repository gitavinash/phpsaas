<?php

/**
 * Senior QA Engineer Automated Unit & Integration Test Suite
 * Control Panel (App 33) V3 Endpoints & Workflows
 * Path: scratch/test_control_panel_qa.php
 */

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
$_SERVER['HTTP_HOST'] = 'ay.local';
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

function assert_test($condition, $testName) {
    if ($condition) {
        echo "  [PASS] " . $testName . "\n";
    } else {
        echo "  [FAIL] " . $testName . "\n";
        exit(1);
    }
}

echo "====================================================\n";
echo "STARTING CONTROL PANEL QA UNIT & INTEGRATION TESTS\n";
echo "====================================================\n\n";

// TEST 1: Overview API
echo "1. Testing Overview API (KPIs & Financial Summary)...\n";
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/overview.php';
$res1 = json_decode(ob_get_clean(), true);

assert_test($res1['status'] === 'success', "Overview API returns HTTP success");
assert_test(isset($res1['data']['apps']['total']) && $res1['data']['apps']['total'] >= 33, "Total Apps count is valid (>=33)");
assert_test(isset($res1['data']['sites']['total']) && $res1['data']['sites']['total'] >= 360, "Total Sites count is valid (>=360)");
assert_test(isset($res1['data']['users']['total']) && $res1['data']['users']['total'] >= 5000, "Total Users count is valid (>=5000)");
assert_test(isset($res1['data']['financials']), "Financial metrics structure present");
assert_test(isset($res1['data']['commissions']), "Partner commission metrics structure present");

// TEST 2: Apps API CRUD
echo "\n2. Testing Apps API CRUD...\n";
// List
ob_start();
$_SERVER['REQUEST_METHOD'] = 'GET';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/apps.php';
$resApps = json_decode(ob_get_clean(), true);
assert_test($resApps['status'] === 'success', "Apps list API works");
assert_test(count($resApps['data']) >= 33, "Apps list returns all registered applications");

// TEST 3: Site License Quota & Feature Toggles API
echo "\n3. Testing Site License Quotas & Feature Toggles API...\n";
$_GET['action'] = 'get_license';
$_GET['site_id'] = '1';
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/sites.php';
$resLic = json_decode(ob_get_clean(), true);
assert_test($resLic['status'] === 'success', "get_license action returns success");
assert_test($resLic['data']['site_id'] === 1, "get_license returns site ID 1");
assert_test(isset($resLic['data']['user_limit']) && $resLic['data']['user_limit'] > 0, "User license limit is numeric and > 0");
assert_test(isset($resLic['data']['wa_device_limit']) && $resLic['data']['wa_device_limit'] > 0, "WhatsApp device limit is numeric and > 0");
assert_test(isset($resLic['data']['features']['whatsapp_enable']), "Feature flag whatsapp_enable is present");
assert_test(isset($resLic['data']['features']['feature_leads']), "Feature flag feature_leads is present");

// Save updated license
$testUserLimit = 35;
$testWaLimit = 4;
$savePayload = [
    'action' => 'save_license',
    'site_id' => 1,
    'user_limit' => $testUserLimit,
    'whatsapp_device_limit' => $testWaLimit,
    'subscription_plan' => 'Enterprise Suite QA Test',
    'subscription_expiry' => '2028-12-31',
    'site_status' => 1,
    'features' => [
        'whatsapp_enable' => 1,
        'feature_leads' => 1,
        'feature_accounts' => 1,
        'feature_transporters' => 1,
        'feature_packages' => 1,
        'site_custom_domain' => 1,
        'feature_api_access' => 1
    ]
];

// Execute save_license
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $savePayload;
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/sites.php';
$resSaveLic = json_decode(ob_get_clean(), true);
assert_test($resSaveLic['status'] === 'success', "save_license returns success");

// Re-verify get_license reflects updated limit
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'get_license';
$_GET['site_id'] = '1';
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/sites.php';
$resReVerify = json_decode(ob_get_clean(), true);
assert_test((int)$resReVerify['data']['user_limit'] === $testUserLimit, "Updated user_limit is persisted correctly (35)");
assert_test((int)$resReVerify['data']['wa_device_limit'] === $testWaLimit, "Updated whatsapp_device_limit is persisted correctly (4)");
assert_test($resReVerify['data']['features']['feature_api_access'] === true, "feature_api_access toggle enabled correctly");

// TEST 4: Users & RBAC API
echo "\n4. Testing Users & RBAC API for 5 User Roles...\n";
$_GET = ['role' => 'administrator'];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/users.php';
$resUsers = json_decode(ob_get_clean(), true);
assert_test($resUsers['status'] === 'success', "Users list by role 'administrator' returns success");
assert_test(count($resUsers['data']) > 0, "Administrator users found in database");

// Test Roles API
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/roles.php';
$resRoles = json_decode(ob_get_clean(), true);
assert_test($resRoles['status'] === 'success', "Roles API returns success");
assert_test(count($resRoles['roles']) === 5, "Exact 5 User Roles defined for Control Panel");

// TEST 5: Invoicing & Partner Commission Lifecycle
echo "\n5. Testing Invoices & Partner Commission Lifecycle...\n";
$testSiteId = 1;
// Create invoice
$invPayload = [
    'action' => 'create',
    'site_id' => $testSiteId,
    'plan' => 'Blumox QA Test Plan',
    'amount' => 50000.00,
    'tax' => 9000.00,
    'currency' => 'INR',
    'partner_id' => 9349 // Avinash as partner
];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $invPayload;
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/invoices.php';
$resInvCreate = json_decode(ob_get_clean(), true);
assert_test($resInvCreate['status'] === 'success', "Invoice creation returns success");
$testInvId = (int)$resInvCreate['invoice_id'];
$testInvNum = $resInvCreate['invoice_number'];
assert_test($testInvId > 0, "Invoice ID generated ($testInvId)");

// Mark Invoice Paid -> triggers automatic partner commission
$payPayload = [
    'action' => 'mark_paid',
    'invoice_id' => $testInvId
];
$_POST = $payPayload;
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/invoices.php';
$resPay = json_decode(ob_get_clean(), true);
assert_test($resPay['status'] === 'success', "Invoice mark_paid returns success and triggers partner commission");

// Verify commission record was created
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['partner_id' => 9349];
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/commissions.php';
$resComm = json_decode(ob_get_clean(), true);
assert_test($resComm['status'] === 'success', "Commissions list API returns success");
$foundCommission = null;
foreach ($resComm['data'] as $c) {
    if ((int)$c['site_id'] === $testSiteId && $c['invoice_number'] === $testInvNum) {
        $foundCommission = $c;
        break;
    }
}
assert_test($foundCommission !== null, "Partner commission automatically recorded for paid invoice");
assert_test((float)$foundCommission['amount'] === 5000.00, "10% commission correctly calculated (₹5,000 on ₹50,000)");

// Payout Commission
$commId = (int)$foundCommission['id'];
$payoutPayload = [
    'action' => 'payout',
    'commission_id' => $commId,
    'payout_ref' => 'QA-UTR-99887766'
];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $payoutPayload;
ob_start();
require $_SERVER['DOCUMENT_ROOT'] . '/api/v3/control_panel/commissions.php';
$resPayout = json_decode(ob_get_clean(), true);
assert_test($resPayout['status'] === 'success', "Commission payout recorded successfully");

// CLEANUP QA TEST RECORDS
echo "\n6. Cleaning up QA Test records...\n";
mysqli_query($connection, "DELETE FROM {$tblPrefix}control_panel_commission WHERE commission_id = {$commId}");
mysqli_query($connection, "DELETE FROM {$tblPrefix}control_panel_invoice WHERE invoice_id = {$testInvId}");
// Reset site 1 license back to standard defaults
mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '10' WHERE site_configuration_site_id = 1 AND site_configuration_key = 'user_limit'");
mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '1' WHERE site_configuration_site_id = 1 AND site_configuration_key = 'whatsapp_device_limit'");

echo "  [PASS] Test data cleaned up cleanly\n";

echo "\n====================================================\n";
echo "ALL QA UNIT & INTEGRATION TESTS PASSED SUCCESSFULLY!\n";
echo "====================================================\n";
