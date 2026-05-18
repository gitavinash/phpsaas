<?php

if (isset($sliderId)) {

    $sliderId = base64_decode($sliderId);

    if ($checkSlider = mysqli_query($connection, "SELECT slide_image as featureImage, slider_device, slide_title as title, slide_subtitle, slide_description as description, slide_link, slide_link2, slider_cta, slider_cta2, slide_alignment, slide_overlay, cta1_bg_color, cta1_text_color, cta2_style, cta2_bg_color, cta2_text_color FROM " . TABLE_PREFIX . "slide WHERE slide_id='{$sliderId}' AND slide_site_id=" . APP_SITE_ID)) {

        if (mysqli_num_rows($checkSlider) == 1) {
            extract(mysqli_fetch_assoc($checkSlider));
        } else {
            echo "
            <script>
            alert('Invalid slider Id');
            window.location = '/dashboard/appearance/?type=sliders';
            </script>
            ";
        }
    }
} else {

    $featureImage = '';
    $title = '';
    $description = '';
    $slide_link = '';
    $slide_link2 = '';
    $slider_cta = '';
    $slider_cta2 = '';
    $slider_device = '';
    $slide_alignment = 'center';
    $slide_overlay = 1;
    $cta1_bg_color = '#F5D154';
    $cta1_text_color = '#1E293B';
    $cta2_style = 'outline';
    $slide_subtitle = '';
    $cta2_bg_color = '';
    $cta2_text_color = '';
}

?>
<h2><?php echo ((isset($sliderId)) ? "Update slide <span><a href='/dashboard/appearance/?type=sliders' class='btn btn-blue btn-sm'>Add new</a></span>" : "Add new slide") ?>
</h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <style>
        .section-card {
            float: left;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }
        .form-control.inside-shadow {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: #0f172a;
            background: #fff;
            box-shadow: none;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .form-control.inside-shadow:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .modern-switch-group {
            display: inline-flex;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 8px;
            gap: 2px;
        }
        .modern-switch-label {
            margin: 0;
            cursor: pointer;
            position: relative;
        }
        .modern-switch-label input[type="radio"] {
            display: none;
        }
        .modern-switch-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px;
            border-radius: 6px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .modern-switch-text i {
            color: inherit !important;
        }
        .modern-switch-text:hover {
            color: #1e293b;
        }
        .modern-switch-label input[type="radio"]:checked + .modern-switch-text {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .color-picker-input {
            padding: 2px !important;
            height: 42px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }
        .modern-toggle {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
            margin: 0;
        }
        .modern-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .modern-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 22px;
        }
        .modern-toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .modern-toggle input:checked + .modern-toggle-slider {
            background-color: #0f172a;
        }
        .modern-toggle input:checked + .modern-toggle-slider:before {
            transform: translateX(18px);
        }

        .device-selector-group {
            display: flex;
            gap: 15px;
            width: 100%;
        }
        .device-selector-label {
            flex: 1;
            cursor: pointer;
            margin: 0;
        }
        .device-selector-label input[type="radio"] {
            display: none;
        }
        .device-selector-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .device-selector-label input[type="radio"]:checked + .device-selector-content {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
        }
        .device-selector-content i {
            font-size: 24px;
        }
        </style>
        <div class="left-box">
            
            <div class="section-card">
                <div class="section-title">Slider Content</div>
                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Slider Subtitle (Kicker)</label>
                        <input type="text" id="slide_subtitle" value="<?php echo isset($slide_subtitle) ? $slide_subtitle : '' ?>" class="form-control inside-shadow"
                            name="slide_subtitle" placeholder="e.g. New Collection">
                    </div>
                    <div style="flex: 2;">
                        <label>Slider Title</label>
                        <input type="text" id="title" value="<?php echo $title ?>" class="form-control inside-shadow"
                            name="title" placeholder="Title" autofocus>
                    </div>
                </div>
                <div class="form-group fullscreen-editor" style="margin-bottom: 0;">
                    <label>Slider Description</label>
                    <textarea name="description" class="form-control inside-shadow"><?php echo $description ?></textarea>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Primary Action (CTA 1)</div>
                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Button Text</label>
                        <input type="text" id="cta" value="<?php echo $slider_cta ?>" class="form-control inside-shadow"
                            name="cta" placeholder="e.g. Shop Now">
                    </div>
                    <div style="flex: 1;">
                        <label>Link URL</label>
                        <input type="text" id="link" value="<?php echo $slide_link ?>" class="form-control inside-shadow"
                            name="link" placeholder="e.g. https://...">
                    </div>
                </div>
                <div class="form-group" style="display: flex; gap: 15px; margin-bottom: 0;">
                    <div style="flex: 1;">
                        <label>Background Color</label>
                        <input type="color" name="cta1_bg_color" value="<?php echo $cta1_bg_color ?>" class="form-control inside-shadow color-picker-input">
                    </div>
                    <div style="flex: 1;">
                        <label>Text Color</label>
                        <input type="color" name="cta1_text_color" value="<?php echo $cta1_text_color ?>" class="form-control inside-shadow color-picker-input">
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Secondary Action (CTA 2)</div>
                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Button Text</label>
                        <input type="text" id="cta2" value="<?php echo $slider_cta2 ?>" class="form-control inside-shadow"
                            name="cta2" placeholder="e.g. Learn More">
                    </div>
                    <div style="flex: 1;">
                        <label>Link URL</label>
                        <input type="text" id="link2" value="<?php echo $slide_link2 ?>" class="form-control inside-shadow"
                            name="link2" placeholder="e.g. https://...">
                    </div>
                </div>
                <div class="form-group" style="display: flex; gap: 15px; margin-bottom: 0;">
                    <div style="flex: 1;">
                        <label>Background Color</label>
                        <input type="color" name="cta2_bg_color" value="<?php echo isset($cta2_bg_color) ? $cta2_bg_color : '' ?>" class="form-control inside-shadow color-picker-input">
                    </div>
                    <div style="flex: 1;">
                        <label>Text Color</label>
                        <input type="color" name="cta2_text_color" value="<?php echo isset($cta2_text_color) ? $cta2_text_color : '' ?>" class="form-control inside-shadow color-picker-input">
                    </div>
                    <div style="flex: 1;">
                        <label>Button Style</label>
                        <select name="cta2_style" class="form-control inside-shadow" style='height:40px'>
                            <option value="outline" <?php echo ($cta2_style == 'outline' ? 'selected' : '') ?>>Outline (Transparent)</option>
                            <option value="solid" <?php echo ($cta2_style == 'solid' ? 'selected' : '') ?>>Solid</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Layout & Display Settings</div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="margin-bottom: 12px; font-size: 14px; display: block;">Select Slider Device</label>
                    <div class="device-selector-group">
                        <label class="device-selector-label">
                            <input type="radio" name="device" value="desktop" <?php echo (strtolower($slider_device) == 'desktop' || empty($slider_device)) ? 'checked' : ''; ?>>
                            <div class="device-selector-content">
                                <i class="fas fa-desktop"></i>
                                <span>Desktop</span>
                            </div>
                        </label>
                        <label class="device-selector-label">
                            <input type="radio" name="device" value="mobile" <?php echo (strtolower($slider_device) == 'mobile') ? 'checked' : ''; ?>>
                            <div class="device-selector-content">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="form-group" style="display: flex; gap: 20px; margin-bottom: 0;">
                    <div style="flex: 1;">
                        <label style="margin-bottom: 8px; display: block;">Text Alignment</label>
                        <select name="slide_alignment" class="form-control inside-shadow" style="height: 44px;">
                            <option value="center" <?php echo ($slide_alignment == 'center' ? 'selected' : '') ?>>Center</option>
                            <option value="left" <?php echo ($slide_alignment == 'left' ? 'selected' : '') ?>>Left</option>
                            <option value="right" <?php echo ($slide_alignment == 'right' ? 'selected' : '') ?>>Right</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="margin-bottom: 8px; display: block;">Dark Background Overlay</label>
                        <div class="modern-switch-group" style="width: 100%; height: 44px; display: flex; padding: 4px; box-sizing: border-box;">
                            <label class="modern-switch-label" style="flex: 1;">
                                <input type="radio" name="slide_overlay" value="1" <?php echo ($slide_overlay == 1 ? 'checked' : '') ?>>
                                <span class="modern-switch-text" style="width: 100%; height: 100%; box-sizing: border-box;">On</span>
                            </label>
                            <label class="modern-switch-label" style="flex: 1;">
                                <input type="radio" name="slide_overlay" value="0" <?php echo ($slide_overlay != 1 ? 'checked' : '') ?>>
                                <span class="modern-switch-text" style="width: 100%; height: 100%; box-sizing: border-box;">Off</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="submit" id="publishPost" class="btn btn-green btn-block"
                    value="<?php echo ((isset($sliderId)) ? 'Update ' : 'Add new ') ?>  slide">
                <?php

                if (isset($sliderId)) {
                    echo "<input type='hidden' name='sliderId' value='" . $sliderId . "'>";
                    echo "<input type='hidden' name='featureImage' value='" . $featureImage . "'>";
                }

                ?>
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="slider">
            </div>
            <h2>Slider Image</h2>
            <div class="form-group featureImageUploadBox upload-btn">
                <span>Set slider image</span>
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg"
                    <?php echo ((isset($sliderId)) ? '' : 'required') ?>>
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                        file size maximum 500kb</p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box"
                style="display: <?php echo (($featureImage != '') ? 'block' : 'none') ?>;">
                <img src="//<?php echo SITE_DOMAIN . $featureImage ?>" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>

<h2>Slides list</h2>
<div class="form-box">
    <table id="table">
        <thead>
            <tr>
                <th style="width: 400px">Image</th>
                <th style="width: 150px">Title</th>
                <th style="width: 150px">Description</th>
                <th style="width: 100px">Device</th>
                <th style="width: 100px"></th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($sliderList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slide_site_id=" . APP_SITE_ID)) {

                if (mysqli_num_rows($sliderList) > 0) {

                    while ($sliderData = mysqli_fetch_assoc($sliderList)) {

                        extract($sliderData); ?>
            <tr>
                <td>
                    <img src="<?php echo $slide_image ?>" width="100px" alt=""><br /><a
                        href="<?php echo $slide_image ?>" target="_blank">View Full Size</a>
                </td>
                <td><?php echo $slide_title ?></td>
                <td><?php echo $slide_description ?></td>
                <td><?php echo $slider_device ?></td>
                <td>
                    <a href="/dashboard/appearance/?type=sliders&sliderId=<?php echo base64_encode($slide_id); ?>"
                        class="text-btn"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($slide_id) ?>_slide" class="deleteBtn text-btn"><i
                            class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php

                    }
                }
            } else {

                echo mysqli_error($connection);
            }

            ?>
        </tbody>
    </table>
</div>
</div>

<script>
$(document).ready(function() {
    $('#formData').on('submit', function(e) {
        e.preventDefault();
        
        var form = this;
        var formData = new FormData(form);
        formData.append('ajax', '1');
        
        var originalBtnVal = $('#publishPost').val();
        $('#publishPost').prop('disabled', true).val('Please wait...');
        
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(data) {
                $('#publishPost').prop('disabled', false).val(originalBtnVal);
                if(data.status == 200) {
                    Swal.fire({
                        title: 'Success', 
                        text: data.msg, 
                        type: 'success',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error', 
                        text: data.msg, 
                        type: 'error',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                $('#publishPost').prop('disabled', false).val(originalBtnVal);
                Swal.fire({
                    title: 'Error', 
                    text: 'Something went wrong while submitting.', 
                    type: 'error',
                    icon: 'error'
                });
            }
        });
    });
});
</script>