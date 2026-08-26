<?php

/**
 * V3 Auth Master Router Bridge
 * Path: /api/v3/auth.php
 */

header('Content-Type: application/json; charset=utf-8');

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($requestUri, '/auth/send-otp') !== false) {
    require_once __DIR__ . '/auth/send-otp.php';
} elseif (strpos($requestUri, '/auth/verify-otp') !== false) {
    require_once __DIR__ . '/auth/verify-otp.php';
} elseif (strpos($requestUri, '/auth/google') !== false) {
    require_once __DIR__ . '/auth/google.php';
} elseif (strpos($requestUri, '/auth/register') !== false) {
    require_once __DIR__ . '/auth/register.php';
} elseif (strpos($requestUri, '/auth/onboarding') !== false) {
    require_once __DIR__ . '/auth/onboarding.php';
} elseif (strpos($requestUri, '/auth/login') !== false || strpos($requestUri, '/auth/') !== false) {
    require_once __DIR__ . '/auth/login.php';
} else {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Auth endpoint not found.']);
}
