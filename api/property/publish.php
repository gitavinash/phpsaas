<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    // require_once MODULES . 'imageUploader.php';

    // if (!isset($featureImage)) {

    //     /** if feature image is not upload then set featureImage as blank */

    //     $featureImage = '';
    //     $thumbnailImage = '';
    // }

    $featureImage = '';

    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
        if (!empty($gallery) && isset($gallery[0])) {
            $uploadedgallery = json_decode($gallery, true);
            $featureImage = $uploadedgallery[0]['path'];
        }
    } else {
        $gallery = '';
    }

    $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
    $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

    /** Property Property url */

    $url = '/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $name . '-' . $city)) . '-' . time() . '/';
    $canonical = $url;

    $meta_title = $name . ' in ' . ucwords($city);
    $meta_desc = $description;
    $meta_key = "{$meta_title}, {$name}, hotels in {$city}";
    $robots = '';
    $fb_title = $meta_title;
    $tw_title = $meta_title;

    /* define facebook and twitter meta desciption */

    $fb_desc = ((isset($fb_desc)) ? $fb_desc : $meta_desc);
    $tw_desc = ((isset($tw_desc)) ? $tw_desc : $meta_desc);

    mysqli_query($connection, "START TRANSACTION");

    if ($checkURL = mysqli_query($connection, "SELECT property_id FROM " . TABLE_PREFIX . "property WHERE property_site_id=" . SITE_ID . " AND property_title='{$name}' AND property_pincode='{$pinCode}'")) {

        if (mysqli_num_rows($checkURL) == 0) {

            require_once EDITOR_CONTENT_FILTER;

            $link_status = 200;

            /** Start uploading the Property */

            /* Insert data into link table */

            $propertyDetails = array_map('trim', explode(',', $city));
            $propertyCountry = end($propertyDetails);
            $propertyState = prev($propertyDetails);
            $propertyCity = $city;

            $addNewProperty = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "property(property_site_id, property_thumbnail, property_gallery, property_title, property_email, property_mobile, property_description, property_address, property_city, property_state, property_country, property_pincode, property_map_url, property_type, property_support_email, property_support_mobile, property_created_by, property_status) VALUES(" . SITE_ID . ", '{$featureImage}', '{$gallery}', '{$name}', '{$email}', '{$mobile}', '{$description}', '{$address}', '{$propertyCity}', '{$propertyState}', '{$propertyCountry}', '{$pinCode}', '{$mapUrl}', '{$type}', '{$supportEmail}', '{$supportMobile}', " . USER_ID . ", 1)");
            $propertyId = mysqli_insert_id($connection);

            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$propertyId}', '{$link_status}', 'property', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
            $linkID = mysqli_insert_id($connection);

            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . APP_SITE_ID . ", '{$meta_title}', 'Property', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

            for ($i = 0; $i < sizeof($_POST['roomType']); $i++) {
                $insertRoom = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "room(room_property_id, room_site_id, room_type, room_status, room_create) VALUES('{$propertyId}', " . SITE_ID . ", '{$_POST['roomType'][$i]}', 0, NOW())");
            }

            if ($addNewProperty && $addNewLink && $addNewMeta && $insertRoom) {
                $responseCode = 201;
                $status = 'success';
                $msg = 'New property added successfully';
                mysqli_query($connection, "COMMIT");
            } else {

                $msg = 'Something is wrong while adding new Property';
                mysqli_query($connection, "ROLLBACK");
            }
        } else {

            $msg = 'Property is already exits';
        }
    } else {

        $msg = 'Technical errro';
    }
} else {

    $msg = 'Please enter Property name';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'propertyId' => ((isset($propertyId)) ? $propertyId : '0'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
exit();