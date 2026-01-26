<?php

require_once LOGIN_CHECK;

// Cashfree credentials
$cashfreeClientId = PG_APP_ID;
$cashfreeClientSecret = PG_SECRET_KEY;
if (DEVELOPMENT_MODE == TRUE) {
    $cashfreeBaseUrl = 'https://sandbox.cashfree.com';
} else {
    $cashfreeBaseUrl = 'https://api.cashfree.com';
}
$cashfreeApiVersion = '2025-01-01';

// Pricing tiers
function get_price_per_seat(int $seats): int
{
    if ($seats >= 51) {
        return 1799;
    }
    if ($seats >= 26) {
        return 2299;
    }
    return 2699; // 10 - 25
}

$minSeats = 10;
$requestedSeats = max($minSeats, (int)($_GET['num_users'] ?? $minSeats));
$pricePerSeat = get_price_per_seat($requestedSeats);
$calculatedTotal = $requestedSeats * $pricePerSeat;

$errors = [];

// Get customer details from session/globals
$customerName = $logginUserName ?? 'Corporate Customer';
$customerEmail = $user_email ?? '';
$customerPhone = $user_mobile ?? '';

// Validate
if (!$cashfreeClientId || !$cashfreeClientSecret) {
    $errors[] = 'Cashfree credentials are missing.';
}
if (!$customerEmail || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email required. Please login first.';
}
if (!$customerPhone) {
    $errors[] = 'Phone number required. Please update your profile.';
}

if (empty($errors)) {
    // First, generate a unique reference ID to track this transaction
    $referenceId = uniqid('corp_sub_', true);

    $payload = [
        'customer_details' => [
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
        ],
        'link_amount' => $calculatedTotal,
        'link_currency' => 'INR',
        'link_purpose' => 'Corporate subscription - ' . $requestedSeats . ' seats',
        'link_id' => $referenceId,
        'link_auto_reminders' => true,
        'link_partial_payments' => false,
        'link_notify' => [
            'send_sms' => true,
            'send_email' => true,
        ],
        'link_notes' => [
            'num_users' => (string)$requestedSeats,
            'price_per_seat' => (string)$pricePerSeat,
            'reference_id' => $referenceId,
        ],
        'link_meta' => [
            'return_url' => (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/corporate-subscription-status/?ref_id=' . urlencode($referenceId) : null),
        ],
    ];

    $ch = curl_init($cashfreeBaseUrl . '/pg/links');
    $headers = [
        'Content-Type: application/json',
        'x-api-version: ' . $cashfreeApiVersion,
        'x-client-id: ' . $cashfreeClientId,
        'x-client-secret: ' . $cashfreeClientSecret,
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
    ]);

    $responseBody = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlErr) {
        $errors[] = 'Cashfree request failed: ' . $curlErr;
    } else {
        $responseData = json_decode($responseBody, true);
        if ($httpStatus === 200 && isset($responseData['link_url'])) {

            $linkUrl = $responseData['link_url'];
            header('Location: ' . $linkUrl);
            exit;
        } else {
            $errors[] = 'Cashfree responded with status ' . $httpStatus . '. ' . ($responseData['message'] ?? json_encode($responseData));
        }
    }
}

// If we reach here, there was an error
if (!empty($errors)) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family: sans-serif; padding: 40px; max-width: 600px; margin: 0 auto;">';
    echo '<h2>Error</h2>';
    foreach ($errors as $err) {
        echo '<p>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '</body></html>';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Redirecting</title></head><body style="font-family: sans-serif; padding: 40px; text-align: center;"><p>Redirecting...</p></body></html>';
}