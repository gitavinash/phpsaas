<?php

if (isset($sliderId)) {

    $sliderId = base64_decode($sliderId);

    if ($checkSlider = mysqli_query($connection, "SELECT slide_image as featureImage, slide_title as title, slide_description as description, slide_link FROM " . TABLE_PREFIX . "slide WHERE slide_id='{$sliderId}' AND slide_site_id=" . APP_SITE_ID)) {

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
}

?>
<h2><?php echo ((isset($sliderId)) ? "Update slide <span><a href='/dashboard/appearance/?type=sliders' class='btn btn-blue btn-sm'>Add new</a></span>" : "Add new slide") ?>
</h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <label>Slider Title</label>
                <input type="text" id="title" value="<?php echo $title ?>" class="form-control inside-shadow" name="title" placeholder="Title" autofocus>
            </div>

            <div class="form-group fullscreen-editor">
                <label>Slider Description</label>
                <textarea name="description" class="form-control inside-shadow"><?php echo $description ?></textarea>
            </div>

            <div class="form-group">
                <label>Slider Link</label>
                <input type="text" id="link" value="<?php echo $slide_link ?>" class="form-control inside-shadow" name="link" placeholder="Link" autofocus>
            </div>


        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="submit" id="publishPost" class="btn btn-green btn-block" value="<?php echo ((isset($sliderId)) ? 'Update ' : 'Add new ') ?>  slide">
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
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg" <?php echo ((isset($sliderId)) ? '' : 'required') ?>>
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                        file size maximum 500kb</p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo (($featureImage != '') ? 'block' : 'none') ?>;">
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
                                <img src="<?php echo $slide_image ?>" width="100px" alt=""><br /><a href="<?php echo $slide_image ?>" target="_blank">View Full Size</a>
                            </td>
                            <td><?php echo $slide_title ?></td>
                            <td><?php echo $slide_description ?></td>
                            <td>
                                <a href="/dashboard/appearance/?type=sliders&sliderId=<?php echo base64_encode($slide_id); ?>" class="text-btn"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="#" id="<?php echo base64_encode($slide_id) ?>_slide" class="deleteBtn text-btn"><i class="fas fa-trash"></i> Delete</a>
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