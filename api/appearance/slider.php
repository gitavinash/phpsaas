<?php

function slider($connection, $data)
{
    extract($data);
    require_once LOGIN_CHECK;

    if (!isset($sliderId)) {
        $featureImage = '';
    }

    if ($_FILES['featureImage']['error'] == 0) {

        require_once IMAGE_UPLOADER;
    }

    $slide_alignment = isset($slide_alignment) ? $slide_alignment : 'center';
    $slide_overlay = (isset($slide_overlay) && $slide_overlay == 1) ? 1 : 0;
    $cta1_bg_color = isset($cta1_bg_color) ? $cta1_bg_color : '';
    $cta1_text_color = isset($cta1_text_color) ? $cta1_text_color : '';
    $cta1_style = isset($cta1_style) ? $cta1_style : 'solid';
    $cta2_style = isset($cta2_style) ? $cta2_style : 'outline';
    $slide_subtitle = isset($slide_subtitle) ? $slide_subtitle : '';
    $cta2_bg_color = isset($cta2_bg_color) ? $cta2_bg_color : '';
    $cta2_text_color = isset($cta2_text_color) ? $cta2_text_color : '';

    if (isset($sliderId)) {
        $query = "UPDATE " . TABLE_PREFIX . "slide SET slide_image='{$featureImage}', slide_title='{$title}', slide_subtitle='{$slide_subtitle}', slide_description = '{$description}', slide_link='{$link}', slide_link2='{$link2}', slider_device='{$device}', slider_cta='{$cta}', slider_cta2='{$cta2}', slide_alignment='{$slide_alignment}', slide_overlay={$slide_overlay}, cta1_bg_color='{$cta1_bg_color}', cta1_text_color='{$cta1_text_color}', cta1_style='{$cta1_style}', cta2_style='{$cta2_style}', cta2_bg_color='{$cta2_bg_color}', cta2_text_color='{$cta2_text_color}' WHERE slide_id={$sliderId} AND slide_site_id=" . APP_SITE_ID;
    } else {
        $query = "INSERT INTO " . TABLE_PREFIX . "slide(slide_site_id, slider_device, slide_image, slide_title, slide_subtitle, slide_description, slide_link, slide_link2, slider_cta, slider_cta2, slide_status, slide_alignment, slide_overlay, cta1_bg_color, cta1_text_color, cta1_style, cta2_style, cta2_bg_color, cta2_text_color) VALUES(" . APP_SITE_ID . ", '{$device}', '{$featureImage}', '{$title}', '{$slide_subtitle}', '{$description}', '{$link}', '{$link2}', '{$cta}', '{$cta2}', 1, '{$slide_alignment}', {$slide_overlay}, '{$cta1_bg_color}', '{$cta1_text_color}', '{$cta1_style}', '{$cta2_style}', '{$cta2_bg_color}', '{$cta2_text_color}')";
    }

    if (mysqli_query($connection, $query)) {
        if (!isset($sliderId)) {
            $sliderId = mysqli_insert_id($connection);
        }

        $sliderId = base64_encode($sliderId);

        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            echo json_encode(['status' => 200, 'msg' => 'Slider added/updated successfully']);
            exit();
        }

        $queryString = "&sliderId=" . $sliderId . "&status=error&responseMsg=" . urlencode('Slider add/update successfully');
    } else {
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            echo json_encode(['status' => 400, 'msg' => mysqli_error($connection)]);
            exit();
        }

        $queryString = "&status=error&responseMsg=" . urlencode(mysqli_error($connection));
    }

    header("Location: /dashboard/appearance/?type=sliders" . $queryString);
    exit();
}
