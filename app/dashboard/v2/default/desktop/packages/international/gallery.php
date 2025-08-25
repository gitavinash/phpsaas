<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

$query = "
    SELECT tour_package_title, gallery_image_list
    FROM " . TABLE_PREFIX . "tour_package AS tp
    LEFT JOIN " . TABLE_PREFIX . "gallery AS g
        ON g.gallery_category = tp.tour_package_id
        AND g.gallery_site_id = " . SITE_ID . "
        AND g.gallery_type = 'tour_package'
        AND g.gallery_status = 1
    WHERE tp.tour_package_id = '{$id}'
    AND tp.tour_package_site_id = " . SITE_ID;

if ($galleryData = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($galleryData) != 0) {
        extract(mysqli_fetch_assoc($galleryData));
        if ($gallery_image_list != '') {
            $galleryImagesList = json_decode($gallery_image_list, TRUE);
        } else {
            $galleryImagesList = [];
        }
    }
}

?>
<h2 class="heading"><?php echo ucwords($tour_package_title) ?>'s Gallery<span>Tour package image gallery</span>
</h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                foreach ($galleryImagesList as $galleyImages => $imageData) {
                    extract($imageData);
                    echo "
                    <div class='editGallery galleryImageBox' >
                        <img src='" . (($path != '') ?
                        $path : ADMIN_IMAGES . 'default-picture.png') . "' >
                    </div>
                    ";
                }

                ?>
                <div class="shortcut">
                    <a href="#" class="addNewGallery"><i class="fi fi-rr-plus"></i></a>
                </div>
            </section>
        </div>
    </div>

    <div class="popUp galleryBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addGalleryForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new gallery</h4>
                                </div>
                                <div class="form-group">
                                    <div id="dropZone" style="height: 500px;">
                                        <span class="defaultText">Drag & Drop files here or click to upload</span>
                                    </div>
                                    <input type="file" id="fileInput" name="gallery[]" accept=".jpg, .jpeg, .png"
                                        multiple />
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addGallery"><i class="fi-rr-check"></i> Add new gallery</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>
</div>