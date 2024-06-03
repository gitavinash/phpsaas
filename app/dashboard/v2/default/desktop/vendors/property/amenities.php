<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($amenitiesData = mysqli_query($connection, "SELECT amenitie_parent_id, amenitie_title, amenitie_description, amenitie_is_deletable FROM " . TABLE_PREFIX . "amenities")) {

    if (mysqli_num_rows($amenitiesData) != 0) {
        $amenitiesList = array();
        while ($data = mysqli_fetch_assoc($amenitiesData)) {
            $amenitiesList[] = $data;
        }
    } else {

        $amenities_title = '';
        $amenities_description = '';
        $amenities_parent_id = '';
        $link_url = '';
    }
}

?>
<h2 class="heading">Amenities list<span>Complete amenities list</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                for ($i = 0; $i < sizeof($amenitiesList); $i++) {
                    echo "
                    <div class='featureList updateAmenities' >
                        <img src='" . (($amenitiesList[$i]['amenitie_image'] != '') ?
                        $amenitiesList[$i]['amenitie_image'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$amenitiesList[$i]['amenitie_title']}</p>
                    </div>
                    ";
                }

                ?>
                <div class="featureList">
                    <a href="#" class="addNewAmenities addNewBtn"><i class="fi fi-rr-add"></i></a>
                </div>
            </section>
        </div>
    </div>

    <div class="popUp amenitiesBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addAmenitiesForm" action="#" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new amenities</h4>
                                </div>
                                <div class="form-group">
                                    <p>Name <sup>*</sup></p>
                                    <input id="name" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*" class="form-control" required></textarea>
                                    <label class="form-lable" id="noteMsgLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <p>Icon <sup>*</sup></p>
                                    <input id="icon" name="icon" type="file" accept="image/*" />
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class=" widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addAmenities"><i class="fi-rr-check"></i> Add new
                        amenities</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editAmenitiesBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit amenities</h4>
                                </div>
                                <div class="form-group">
                                    <p>Name <sup>*</sup></p>
                                    <input id="name" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*" class="form-control" required></textarea>
                                    <label class="form-lable" id="noteMsgLabel">Description</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateAmenities"><i class="fi-rr-check"></i> Update
                        amenities</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>
</div>