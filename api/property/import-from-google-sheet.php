<?php
require_once LOGIN_CHECK;

$response = [
    'status' => 'error',
    'imported' => 0,
    'duplicates' => 0,
    'failed' => 0,
    'msg' => 'Something went wrong'
];
$httpStatus = 500;

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $response['msg'] = 'CSV file is required.';
    echo json_encode($response);
    exit;
}

$fileTmp = $_FILES['file']['tmp_name'];

// Open CSV
if (($handle = fopen($fileTmp, 'r')) !== false) {
    mysqli_query($connection, "START TRANSACTION");

    $rowCount = 0;

    while (($row = fgetcsv($handle, 1000, ",", '"', "\\")) !== false) {
        $rowCount++;
        if ($rowCount == 1 || $rowCount == 2) {
            continue;
        }

        print_r($row);

        $hotelName         = $row[0];
        $address           = $row[1];
        $city              = $row[2];
        $state             = $row[3];
        $pinCode           = $row[4];
        $reservationEmail  = $row[5];
        $reservationMobile = $row[6];
        $supportEmail      = $row[7];
        $supportMobile     = $row[8];
        $propertyType      = $row[9];
        $googleMapUrl      = $row[10];
        $roomTypes         = $row[11];

        $roomType1 = $row[12];
        $roomType1ep_mrp = $row[13];
        $roomType1ep_price = $row[14];
        $roomType1ep_price_b2b = $row[15];
        $roomType1cp_mrp = $row[16];
        $roomType1cp_price = $row[17];
        $roomType1cp_price_b2b = $row[18];
        $roomType1map_mrp = $row[19];
        $roomType1map_price = $row[20];
        $roomType1map_price_b2b = $row[21];
        $roomType1ap_mrp = $row[22];
        $roomType1ap_price = $row[23];
        $roomType1ap_price_b2b = $row[24];

        // if ($insertProperty && $insertLink && $insertMeta) {
        //     $response['imported']++;
        // } else {
        //     $response['failed']++;
        // }
    }

    fclose($handle);

    mysqli_query($connection, "COMMIT");

    $response['status'] = 'success';
    $response['msg'] = "Import finished. Imported: {$response['imported']}, Duplicates: {$response['duplicates']}, Failed: {$response['failed']}";
} else {
    $response['msg'] = 'Unable to read uploaded CSV file.';
}

http_response_code($httpStatus);
echo json_encode($response);
exit;