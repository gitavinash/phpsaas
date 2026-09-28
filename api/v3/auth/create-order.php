<?php

/**
 * V3 Authentication API - Payment Order Initiation for Onboarding
 * Path: /api/v3/auth/create-order.php
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method not allowed. Use POST.']);
    exit;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?? (!empty($_POST) ? $_POST : []);

$plan = strtolower(trim($data['plan'] ?? 'erp'));
$billingCycle = strtolower(trim($data['billing_cycle'] ?? 'yearly'));
$email = strtolower(trim($data['email'] ?? ''));
$mobile = preg_replace('/\D/', '', (string)($data['mobile'] ?? ''));
$businessName = trim($data['business_name'] ?? 'Travel Agency');
$firstName = trim($data['first_name'] ?? '');
$lastName = trim($data['last_name'] ?? '');
$customerName = trim("{$firstName} {$lastName}") ?: $businessName;

// Pricing lookup
$pricingRates = [
    'crm' => [
        'monthly' => 1874,
        'yearly'  => 1499,
        'name'    => 'CRM Starter'
    ],
    'erp' => [
        'monthly' => 4999,
        'yearly'  => 3999,
        'name'    => 'ERP Complete'
    ]
];

$selectedPlanInfo = $pricingRates[$plan] ?? $pricingRates['erp'];
$planName = $selectedPlanInfo['name'];

$monthlyRate = (int)($selectedPlanInfo['monthly'] ?? 1874);
$yearlyMonthlyRate = (int)($selectedPlanInfo['yearly'] ?? 1499);

if ($billingCycle === 'monthly') {
    $planSubtotal = $monthlyRate;
    $setupFee = 5000;
} else {
    // Annual plan charges for complete year
    $planSubtotal = $yearlyMonthlyRate * 12;
    $setupFee = 0; // Waived on annual
}

$subtotal = $planSubtotal + $setupFee;
$gstAmount = round($subtotal * 0.18, 2);
$totalPayable = round($subtotal + $gstAmount, 2);

// Amount in rupees and in paise for Razorpay
$amountRupees = (float)$totalPayable;
$amountPaise = (int)round($amountRupees * 100);

// Resolve Razorpay Key from platform config or sandbox default
$keyId = '';
$keySecret = '';

$pgRes = mysqli_query($connection, "
    SELECT site_configuration_key, site_configuration_value 
    FROM {$tblPrefix}site_configuration 
    WHERE site_configuration_key IN ('pg_key_id', 'pg_key_secret', 'razorpay_key_id', 'razorpay_key_secret')
      AND site_configuration_status = 1
      AND site_configuration_value != ''
    ORDER BY site_configuration_id DESC
    LIMIT 2
");

if ($pgRes) {
    while ($row = mysqli_fetch_assoc($pgRes)) {
        if (strpos($row['site_configuration_key'], 'id') !== false && empty($keyId)) {
            $keyId = $row['site_configuration_value'];
        }
        if (strpos($row['site_configuration_key'], 'secret') !== false && empty($keySecret)) {
            $keySecret = $row['site_configuration_value'];
        }
    }
}

// Fallback to active sandbox key if not configured in DB
if (empty($keyId)) {
    $keyId = 'rzp_test_3XSILne3J1ag48';
}

$orderId = '';

// Attempt real Razorpay order creation if credentials and SDK are available
$razorpaySdkPath = dirname(__DIR__, 3) . '/lib/resource/payment-gateway/razorpay/razorpay.php';
if (!empty($keySecret) && file_exists($razorpaySdkPath)) {
    try {
        require_once $razorpaySdkPath;
        if (class_exists('\\Razorpay\\Api\\Api')) {
            $api = new \Razorpay\Api\Api($keyId, $keySecret);
            $rzpOrder = $api->order->create([
                'receipt'         => 'ALEYA_' . time(),
                'amount'          => $amountPaise,
                'currency'        => 'INR',
                'payment_capture' => 1
            ]);
            if (!empty($rzpOrder['id'])) {
                $orderId = $rzpOrder['id'];
            }
        }
    } catch (\Throwable $e) {
        // Fallback gracefully without order_id for standard direct checkout
        $orderId = '';
    }
}

$orderPayload = [
    'key'          => $keyId,
    'amount'       => $amountPaise,
    'currency'     => 'INR',
    'name'         => 'Aleya Travel CRM',
    'description'  => $planName . ' (' . ucfirst($billingCycle) . ' Plan)',
    'prefill'      => [
        'name'    => $customerName,
        'email'   => $email,
        'contact' => $mobile
    ],
    'theme'        => [
        'color'   => '#00647a'
    ]
];

if (!empty($orderId)) {
    $orderPayload['order_id'] = $orderId;
}

echo json_encode([
    'status'       => 'success',
    'msg'          => 'Payment order created.',
    'data'         => $orderPayload,
    'key'          => $keyId,
    'amount'       => $amountPaise,
    'currency'     => 'INR',
    'order_id'     => $orderId ?: null,
    'name'         => 'Aleya Travel CRM',
    'description'  => $planName . ' (' . ucfirst($billingCycle) . ' Plan)',
    'prefill'      => $orderPayload['prefill'],
    'theme'        => $orderPayload['theme']
]);
exit;
