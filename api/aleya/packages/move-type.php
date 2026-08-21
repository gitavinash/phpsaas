<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

$packageIdRaw = $_REQUEST['packageId'] ?? $_REQUEST['id'] ?? 0;
$packageId = is_numeric($packageIdRaw) ? (int)$packageIdRaw : (int)base64_decode($packageIdRaw);
$targetType = isset($_REQUEST['targetType']) ? strtolower(trim($_REQUEST['targetType'])) : '';

if ($packageId > 0) {
    // Check existing tour package
    $checkQuery = "SELECT tour_package_id, tour_package_type, tour_package_title FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = " . SITE_ID;
    $res = mysqli_query($connection, $checkQuery);

    if ($res && mysqli_num_rows($res) > 0) {
        $pkg = mysqli_fetch_assoc($res);
        $currentType = strtolower($pkg['tour_package_type'] ?? 'domestic');
        
        if (empty($targetType)) {
            $targetType = ($currentType === 'domestic') ? 'international' : 'domestic';
        }

        if (!in_array($targetType, ['domestic', 'international'])) {
            $msg = 'Invalid target package type specified';
        } else {
            $updateQuery = "UPDATE " . TABLE_PREFIX . "tour_package 
                            SET tour_package_type = '{$targetType}' 
                            WHERE tour_package_id = '{$packageId}' 
                            AND tour_package_site_id = " . SITE_ID;

            if (mysqli_query($connection, $updateQuery)) {
                $httpStatus = 200;
                $status = 'success';
                $formattedType = ucfirst($targetType);
                $msg = "Tour package successfully moved to {$formattedType}!";
            } else {
                $httpStatus = 500;
                $msg = 'Database error: ' . mysqli_error($connection);
            }
        }
    } else {
        $httpStatus = 404;
        $msg = 'Tour package not found';
    }
} else {
    $msg = 'Invalid Package ID';
}

http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg' => $msg,
    'targetType' => $targetType
], JSON_UNESCAPED_SLASHES);
