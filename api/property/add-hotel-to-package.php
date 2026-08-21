<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
    $tour_id = isset($_POST['tour_id']) ? (int)$_POST['tour_id'] : 0;
    $night = isset($_POST['night']) ? (int)$_POST['night'] : 0;
    $roomType = isset($_POST['roomType']) ? trim($_POST['roomType']) : '';

    if ($property_id > 0 && $tour_id > 0) {
        // Verify property & tour package
        $checkProperty = mysqli_query($connection, "SELECT property_id FROM " . TABLE_PREFIX . "property WHERE property_id = '{$property_id}' AND property_site_id = " . SITE_ID);
        $checkPackage = mysqli_query($connection, "SELECT tour_package_id FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id = '{$tour_id}' AND tour_package_site_id = " . SITE_ID);

        if ($checkProperty && mysqli_num_rows($checkProperty) > 0 && $checkPackage && mysqli_num_rows($checkPackage) > 0) {
            
            if ($night === 0) {
                // Unassign hotel from this package
                $deleteQuery = "DELETE FROM " . TABLE_PREFIX . "tour_package_hotel WHERE tph_package_id = '{$tour_id}' AND tph_hotel_id = '{$property_id}' AND tph_site_id = " . SITE_ID;
                if (mysqli_query($connection, $deleteQuery)) {
                    $httpStatus = 200;
                    $status = 'success';
                    $msg = 'Hotel unassigned from package';
                } else {
                    $msg = 'Database error unassigning hotel';
                }
            } else {
                $roomId = 0;
                $roomTypeName = '';

                if (!empty($roomType) && $roomType !== '#') {
                    $roomTypeExt = explode(':', $roomType);
                    $roomId = (int)($roomTypeExt[0] ?? 0);
                    $roomTypeName = trim($roomTypeExt[1] ?? '');
                }

                // Delete any previous assignment for this night OR for this hotel in this package (Enforces 1 hotel per night rule)
                $clearQuery = "DELETE FROM " . TABLE_PREFIX . "tour_package_hotel WHERE tph_package_id = '{$tour_id}' AND (tph_night = '{$night}' OR tph_hotel_id = '{$property_id}') AND tph_site_id = " . SITE_ID;
                mysqli_query($connection, $clearQuery);

                // Insert new assignment
                $escapedRoomType = mysqli_real_escape_string($connection, $roomTypeName);
                $insertQuery = "INSERT INTO " . TABLE_PREFIX . "tour_package_hotel (tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) VALUES ('{$property_id}', '{$tour_id}', '{$roomId}', '{$escapedRoomType}', '{$night}', 1, " . SITE_ID . ")";

                if (mysqli_query($connection, $insertQuery)) {
                    $httpStatus = 200;
                    $status = 'success';
                    $msg = 'Hotel successfully assigned to Night ' . $night;
                } else {
                    $msg = 'Database error assigning hotel: ' . mysqli_error($connection);
                }
            }
        } else {
            $msg = 'Property or Tour Package not found';
        }
    } else {
        $msg = 'Invalid Property or Tour Package ID';
    }
}

// Response
$result = array(
    'status' => $status,
    'msg' => $msg,
);

http_response_code($httpStatus);
echo json_encode($result, true);