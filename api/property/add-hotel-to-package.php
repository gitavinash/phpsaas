<?php

require_once LOGIN_CHECK;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($checkProperty = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_id = '{$property_id}' AND property_site_id = " . SITE_ID)) {
        if (mysqli_num_rows($checkProperty) > 0) {
            if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id = '{$tour_id}' AND tour_package_site_id = " . SITE_ID)) {
                if (mysqli_num_rows($checkPackage) > 0) {

                    $roomTypeExt = explode(':', $roomType);
                    $roomId = $roomTypeExt[0];
                    $roomTypeName = $roomTypeExt[1];

                    if ($checkRoomAlloted = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package_hotel WHERE tph_night='{$night}' AND tph_package_id='{$tour_id}' AND tph_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($checkRoomAlloted) == 0) {
                            $insertQuery = "INSERT INTO " . TABLE_PREFIX . "tour_package_hotel(tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_status, tph_site_id) VALUES('{$property_id}', '{$tour_id}', '{$roomId}', '{$roomTypeName}', '{$night}', 1, " . SITE_ID . ")";

                            if (mysqli_query($connection, $insertQuery)) {
                                $httpStatus = 200;
                                $status = 'success';
                                $msg = 'Hotel assign to package';
                            } else {
                                $msg = 'Something went wrong';
                            }
                        } else {
                            $msg = 'Hotel is assinged for ' . $night . ' night';
                        }
                    }
                } else {
                    $msg = 'Invalid tour package not found';
                }
            } else {
                $msg = 'Invalid tour package not found';
            }
        } else {
            $msg = 'property not found';
        }
    } else {
        $msg = 'Invalid property ID';
    }
}

// Response
$result = array(
    'status' => $status,
    'msg' => $msg,
);

http_response_code($httpStatus);
echo json_encode($result, true);