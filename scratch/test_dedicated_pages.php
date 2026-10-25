<?php

/**
 * Senior QA Engineer: Dedicated Pages & Site-Wise Configurations QA Test Suite
 * Path: scratch/test_dedicated_pages.php
 */

$baseUrl = 'http://ay.local';
$cookieFile = __DIR__ . '/ay_cookie.txt';

function test_http($url, $method = 'GET', $data = null) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)');
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $headers = [];
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) {
            $payload = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Type: application/json';
        }
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
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

function assert_test($title, $condition, $info = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $title\n";
        $passed++;
    } else {
        echo "  [FAIL] $title" . ($info ? " -> $info" : "") . "\n";
        $failed++;
    }
}

echo "=================================================================\n";
echo "SENIOR QA TEST: DEDICATED PAGES & SITE-WISE CONFIGURATION SUITE\n";
echo "=================================================================\n\n";

// 1. Test Dedicated Page HTTP Routes
echo "--- 1. Testing Dedicated Page HTTP Routes ---\n";
$pages = [
    'Overview' => ['uri' => '/dashboard/', 'expected' => 'id="controlPanelApp"'],
    'Apps Hub' => ['uri' => '/dashboard/apps/', 'expected' => 'data-page="apps"'],
    'Themes Matrix' => ['uri' => '/dashboard/themes/', 'expected' => 'data-page="themes"'],
    'Client Sites' => ['uri' => '/dashboard/sites/', 'expected' => 'data-page="sites"'],
    'Team & Roles (users)' => ['uri' => '/dashboard/users/', 'expected' => 'data-page="users"'],
    'Team & Roles (team)' => ['uri' => '/dashboard/team/', 'expected' => 'data-page="users"'],
    'Invoices & Billing' => ['uri' => '/dashboard/invoices/', 'expected' => 'data-page="invoices"'],
    'Partner Commissions' => ['uri' => '/dashboard/commissions/', 'expected' => 'data-page="commissions"'],
    'Configurations Studio' => ['uri' => '/dashboard/configurations/', 'expected' => 'id="configurationsApp"']
];

foreach ($pages as $label => $spec) {
    $uri = $spec['uri'];
    $expected = $spec['expected'];
    $res = test_http($baseUrl . $uri);
    assert_test("$label ($uri) loads with HTTP 200", $res['code'] === 200, "HTTP Code: {$res['code']}");
    assert_test("$label page contains expected identifier ($expected)", 
        strpos($res['raw'], $expected) !== false,
        "Identifier not found in HTML response"
    );
}

// 2. Test Site-Wise License & Billing API
echo "\n--- 2. Testing Site-Wise License & Operational Control API ---\n";
$siteLicenseGet = test_http("$baseUrl/api/v3/control_panel/sites/?action=get_license&site_id=1");
assert_test("GET /api/v3/control_panel/sites/?action=get_license&site_id=1 returns HTTP 200", $siteLicenseGet['code'] === 200);
assert_test("License response contains subscription_plan and site_status", 
    isset($siteLicenseGet['json']['data']['subscription_plan']) && isset($siteLicenseGet['json']['data']['site_status'])
);

// 3. Save updated billing, expiry & status
echo "\n--- 3. Testing Site-Wise Save License & Billing Plan ---\n";
$testPlan = 'Enterprise Suite ' . date('Y');
$testExpiry = date('Y-m-d', strtotime('+1 year'));
$savePayload = [
    'action' => 'save_license',
    'site_id' => 1,
    'subscription_plan' => $testPlan,
    'subscription_expiry' => $testExpiry,
    'site_status' => 1,
    'user_limit' => 25,
    'whatsapp_device_limit' => 5,
    'storage_limit_mb' => 10240,
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

$saveRes = test_http("$baseUrl/api/v3/control_panel/sites/", 'POST', $savePayload);
assert_test("POST save_license returns HTTP 200", $saveRes['code'] === 200);
assert_test("POST save_license status is success", ($saveRes['json']['status'] ?? '') === 'success');

// Re-verify that the updated license details were stored and returned correctly
$reVerify = test_http("$baseUrl/api/v3/control_panel/sites/?action=get_license&site_id=1");
assert_test("Re-verified subscription_plan matches updated value ($testPlan)", 
    ($reVerify['json']['data']['subscription_plan'] ?? '') === $testPlan,
    "Got: " . ($reVerify['json']['data']['subscription_plan'] ?? 'null')
);
assert_test("Re-verified user_limit is 25", 
    ($reVerify['json']['data']['user_limit'] ?? 0) == 25,
    "Got: " . ($reVerify['json']['data']['user_limit'] ?? 'null')
);
assert_test("Re-verified wa_device_limit is 5", 
    ($reVerify['json']['data']['wa_device_limit'] ?? 0) == 5,
    "Got: " . ($reVerify['json']['data']['wa_device_limit'] ?? 'null')
);

// 4. Test Site-Wise Parameter Listing
echo "\n--- 4. Testing Site-Wise Parameter API ---\n";
$siteParams = test_http("$baseUrl/api/v3/control_panel/configurations/?scope=site&site_id=1");
assert_test("GET site configurations returns HTTP 200", $siteParams['code'] === 200);
assert_test("GET site configurations status is success", ($siteParams['json']['status'] ?? '') === 'success');

// 5. Test Manual Commission Record API
echo "\n--- 5. Testing Manual Commission Record API ---\n";
$commPayload = [
    'action' => 'create',
    'partner_id' => 1,
    'site_id' => 1,
    'amount' => 7500.00,
    'rate_percent' => 15.00,
    'notes' => 'Q4 Dedicated Page QA Commission Test'
];
$commRes = test_http("$baseUrl/api/v3/control_panel/commissions/", 'POST', $commPayload);
assert_test("POST commissions create returns HTTP 200", $commRes['code'] === 200);
assert_test("POST commissions create status is success", ($commRes['json']['status'] ?? '') === 'success');

echo "\n=================================================================\n";
echo "SUMMARY: Passed: $passed, Failed: $failed\n";
echo "=================================================================\n";

if ($failed > 0) {
    exit(1);
} else {
    exit(0);
}
