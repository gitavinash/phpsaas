<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    if ($type != '') {

        $id = base64_decode($propertyId);

        $type = strtolower(str_replace(' ', '_', $type));

        if ($checkProperty = mysqli_query($connection, "SELECT property_gallery FROM " . TABLE_PREFIX . "property WHERE property_id={$id} AND property_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkProperty) != 0) {
                extract(mysqli_fetch_assoc($checkProperty));
                // require_once MODULES . 'imageUploader.php';

                // if (!isset($featureImage)) {
                //     /** if feature image is not upload then set featureImage as blank */
                //     $featureImage = $oldFeatureImage;
                // }

                $featureImage = '';
                if (isset($_FILES['gallery'])) {

                    require_once MODULES . 'galleryUploader.php';
                    $featureImage = $gallery[0]['path'];
                    if ($gallery != '') {
                        $isGalleryUpdate = TRUE;
                    } else {
                    }
                }

                $oldGallery = json_decode($property_gallery, TRUE);

                // Match old gallery db from from value
                $newPropertyGallery = array();

                if (isset($_POST['oldGallery'])) {

                    for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {

                        $j = $_POST['oldGallery'][$i] - 1;

                        $newPropertyGallery[] = $oldGallery[$j];
                    }
                }

                /** Update old gallery */

                if (isset($isGalleryUpdate)) {

                    $newPropertyGallery = array_merge($newPropertyGallery, $images);
                }

                $newPropertyGallery = json_encode($newPropertyGallery, TRUE);


                if ($checkProperty = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_id='{$id}'")) {
                    if (mysqli_num_rows($checkProperty) != 0) {


                        $propertyDetails = array_map('trim', explode(',', $city));
                        $propertyCountry = end($propertyDetails);
                        $propertyState = prev($propertyDetails);
                        $propertyCity = $city;

                        if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "property SET property_thumbnail='{$featureImage}', property_gallery='{$newPropertyGallery}', property_title='{$name}', property_email='{$email}', property_mobile='{$mobile}', property_description='{$description}', property_address='{$address}', property_city='{$propertyCity}', property_state='{$propertyState}', property_country='{$propertyCountry}', property_pincode='{$pinCode}', property_map_url='{$mapUrl}', property_type='{$type}', property_support_email='{$supportEmail}', property_support_mobile='{$supportMobile}' WHERE property_id='{$id}' AND property_site_id=" . SITE_ID)) {
                            $responseCode = 200;
                            $status = 'success';
                            $msg = 'Property update successfully';
                        } else {
                            $msg = 'Something went wrong while updating property';
                        }
                    } else {
                        $msg = 'Invalid property details found';
                    }
                }
            }
        } else {
            $msg = 'Invalid property id';
        }
    } else {
        $msg = 'Kindly select property type';
    }
} else {
    $msg = 'Property name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'propertyId' => ((isset($id)) ? $id : 0),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
exit();