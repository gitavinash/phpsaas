<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($galleryData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "gallery WHERE gallery_parent_id='" . base64_decode($id) . "' AND gallery_site_id=" . SITE_ID)) {
    $galleryList = array();
    if (mysqli_num_rows($galleryData) != 0) {
        while ($data = mysqli_fetch_assoc($galleryData)) {
            $galleryList[] = $data;
        }
    } else {

        $gallery_title = '';
        $gallery_description = '';
        $gallery_parent_id = '';
        $link_url = '';
    }
}

?>
<h2 class="heading">Gallery images<span>Tour package image gallery</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                if (count($galleryList) > 0) {
                    for ($i = 0; $i < sizeof($galleryList); $i++) {
                        echo "
                    <div class='featureList editGallery' id='" . $galleryList[$i]['gallery_id'] . "' data_name='" . $galleryList[$i]['gallery_title'] . "' data_seating='{$galleryList[$i]['gallery_seating']}' data_image='" . (($galleryList[$i]['gallery_thumbnail'] != '') ?
                            $galleryList[$i]['gallery_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' data_desc='" . $galleryList[$i]['gallery_description'] . "' data_type='" . $galleryList[$i]['gallery_type'] . "' >
                        <img src='" . (($galleryList[$i]['gallery_thumbnail'] != '') ?
                            $galleryList[$i]['gallery_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$galleryList[$i]['gallery_title']}</p>
                    </div>
                    ";
                    }
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
                                    <input type="file" id="fileInput" name="gallery[]" accept=".pdf, .jpg, .jpeg, .png"
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