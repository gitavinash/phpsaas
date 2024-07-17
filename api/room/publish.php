<?php

require_once LOGIN_CHECK;
$responseCode = 500;
$submitForm = false;
$status = 'warning';
$requiredFields = [
    'propertyId' => 'Invalid request received',
    'roomType' => 'Room type is required',
    'priceMRP' => 'EP mrp is required',
    'priceB2C' => 'EP B2C price is required',
    'priceB2B' => 'EP B2B is required',
    'priceCP' => 'CP mrp is required',
    'priceCPB2C' => 'CP B2C price is required',
    'priceCPB2B' => 'CP B2B is required',
    'priceMAP' => 'MAP mrp is required',
    'priceMAPB2C' => 'MAP B2C price is required',
    'priceMAPB2B' => 'MAP B2B is required',
    'priceAP' => 'AP mrp is required',
    'priceAPB2C' => 'AP B2C price is required',
    'priceAPB2B' => 'AP B2B is required'
];

$numericFields = [
    'priceMRP' => 'EP mrp price must be a real number',
    'priceB2C' => 'EP B2C price must be a real number',
    'priceB2B' => 'EP B2B must be a real number',
    'priceCP' => 'CP mrp must be a real number',
    'priceCPB2C' => 'CP B2C price must be a real number',
    'priceCPB2B' => 'CP B2B must be a real number',
    'priceMAP' => 'MAP mrp must be a real number',
    'priceMAPB2C' => 'MAP B2C price must be a real number',
    'priceMAPB2B' => 'MAP B2B must be a real number',
    'priceAP' => 'AP mrp must be a real number',
    'priceAPB2C' => 'AP B2C price must be a real number',
    'priceAPB2B' => 'AP B2B must be a real number'
];

// Check required fields
foreach ($requiredFields as $field => $errorMsg) {
    if (empty($$field)) {
        $msg = $errorMsg;
        break;
    }
    $msg = '';
}

// Check numeric fields
if (empty($msg)) {
    foreach ($numericFields as $field => $errorMsg) {
        if (!is_numeric($$field)) {
            $msg = $errorMsg;
            break;
        }
    }
}

// If all required fields are set, proceed to submit form
if (empty($msg)) {
    $submitForm = true;
}

// Handle form submission
if ($submitForm) {

    $id = base64_decode($propertyId);

    require_once MODULES . 'imageUploader.php';

    // Set default values for images if not set
    $featureImage = $featureImage ?? '';
    $thumbnailImage = $thumbnailImage ?? '';

    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
    }

    mysqli_query($connection, "START TRANSACTION");

    if ($checkProperty = mysqli_query($connection, "SELECT property_starting_mrp, property_starting_price FROM " . TABLE_PREFIX . "property WHERE property_id={$id} AND property_status=1 AND property_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkProperty) != 0) {
            extract(mysqli_fetch_assoc($checkProperty));

            if ($checkRoomType = mysqli_query($connection, "SELECT room_type FROM " . TABLE_PREFIX . "room WHERE room_type='{$roomType}' AND room_status=1 AND room_property_id='{$id}' AND room_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkRoomType) == 0) {
                    if ($property_starting_mrp != 0) {
                        if ($property_starting_mrp < $priceMRP) {
                            $priceMRP = $property_starting_mrp;
                        }
                    }
                    if ($property_starting_price != 0) {
                        if ($property_starting_price < $priceB2C) {
                            $priceB2C = $property_starting_price;
                        }
                    }


                    $updateProperty = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "property SET property_starting_mrp='{$priceMRP}', property_starting_price='{$priceB2C}', property_total_rooms=property_total_rooms+1 WHERE property_id='{$id}' AND property_status=1 AND property_site_id=" . SITE_ID);

                    $addRoom = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "room(room_property_id, room_site_id, room_feature_image, room_gallery, room_description, room_type, room_ep_mrp, room_ep_b2c, room_ep_b2b, room_cp_mrp, room_cp_b2c, room_cp_b2b, room_map_mrp, room_map_b2c, room_map_b2b, room_ap_mrp, room_ap_b2b, room_ap_b2c, room_status, room_create) VALUES('{$id}', " . SITE_ID . ", '{$featureImage}', '{$gallery}', '{$comment}', '{$roomType}', '{$priceMRP}', '{$priceB2C}', '{$priceB2B}', '{$priceCP}', '{$priceCPB2C}', '{$priceCPB2B}', '{$priceMAP}', '{$priceMAPB2C}', '{$priceMAPB2B}', '{$priceAP}', '{$priceAPB2C}', '{$priceAPB2B}', 1, NOW())");

                    if ($updateProperty && $addRoom) {
                        $status = 'success';
                        $responseCode = 201;
                        $msg = 'New room added successfully';
                        mysqli_query($connection, "COMMIT");
                    } else {
                        mysqli_query($connection, "ROLLBACK");
                        $status = 'error';
                        if (DEVELOPMENT_MODE == TRUE) {
                            $msg = mysqli_error($connection);
                        } else {
                            $msg = "Something went wrong while fetching property details";
                        }
                    }
                } else {
                    $msg = 'Room type is already exists in this property';
                }
            }
        } else {
            $responseCode = 404;
            $msg = 'Invalid request received';
        }
    } else {
        if (DEVELOPMENT_MODE == TRUE) {
            $msg = mysqli_error($connection);
        } else {
            $msg = "Something went wrong while fetching property details";
        }
    }
}

// Set HTTP response code
http_response_code($responseCode);

// Prepare result array
$result = [
    'status' => $status ? $status : 'error',
    'propertyId' => $propertyId ?? '0',
    'msg' => $msg ?? 'Invalid response',
];

// Output JSON result
echo json_encode($result);
exit();