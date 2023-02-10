<?php

function slider($connection, $data)
{
    print_r($data);
    extract($data);
    require_once LOGIN_CHECK;

    if (!isset($sliderId)) {
        $featureImage = '';
    }

    if ($_FILES['featureImage']['error'] == 0) {

        require_once IMAGE_UPLOADER;
    }

    if (isset($sliderId)) {

        $query = "UPDATE " . TABLE_PREFIX . "slide SET slide_image='{$featureImage}', slide_title='{$title}', slide_description = '{$description}' WHERE slide_id={$sliderId} AND slide_site_id=" . APP_SITE_ID;
    } else {
        $query = "INSERT INTO " . TABLE_PREFIX . "slide(slide_site_id, slide_image, slide_title, slide_description, slide_status) VALUES(" . APP_SITE_ID . ", '{$featureImage}', '{$title}', '{$description}', 1)";
    }

    if (mysqli_query($connection, $query)) {
        if (!isset($sliderId)) {
            $sliderId = mysqli_insert_id($connection);
        }

        $sliderId = base64_encode($sliderId);

        $queryString = "&sliderId=" . $sliderId . "&status=error&responseMsg=" . urlencode('Slider add/update successfully');
    } else {
        $queryString = "&status=error&responseMsg=" . urlencode(mysqli_error($connection));
    }

    header("Location: /dashboard/setting/?type=sliders" . $queryString);
    exit();
}