<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($amenitiesData = mysqli_query($connection, "SELECT amenitie_id, amenitie_title, amenitie_description, amenitie_thumbnail, amenitie_is_deletable, amenitie_type FROM " . TABLE_PREFIX . "amenities")) {

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

$typeDropDown = '<option value="">Select amenities type</option>
                                        <option value="basic_facilities" >Basic Facilities</option>
                                        <option value="beauty_and_spa" >Beauty and Spa</option>
                                        <option value="food_and_drinks">Food and Drinks</option>
                                        <option value="payment_services">Payment Services</option>
                                        <option value="safety_andsecurity">Safety and Security</option>
                                        <option value="health_and_wellness">Health and wellness</option>
                                        <option value="general_services">General Services</option>
                                        <option value="outdoor_activities_and_sports">Outdoor Activities and Sports
                                        </option>
                                        <option value="indoor_activities and Sports">Indoor Activities and Sports
                                        </option>
                                        <option value="common_area">Common Area</option>
                                        <option value="other_facilities">Other Facilities</option>
                                        <option value="swimming_pool">Swimming Pool</option>
                                        <option value="kitchen">Kitchen</option>
                                        <option value="living_room">Living Room</option>
                                        <option value="parking">Parking</option>
                                        <option value="room_and_bathroom">Room and Bathroom</option>
                                        <option value="transfers">Transfers</option>
                                        <option value="media_and_technology">Media and technology</option>
                                        <option value="shopping">Shopping</option>
                                        <option value="business_center_and_conferences">Business Center and Conferences
                                        </option>
                                        <option value="terrace">Terrace</option>
                                        <option value="dining_area">Dining Area</option>
                                        <option value="entertainment">Entertainment</option>';

?>
<h2 class="heading">Amenities list<span>Complete amenities list</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                for ($i = 0; $i < sizeof($amenitiesList); $i++) {
                    echo "
                    <div class='featureList editAmenities' id='{$amenitiesList[$i]['amenitie_id']}' data_name='{$amenitiesList[$i]['amenitie_title']}'data_image='" . (($amenitiesList[$i]['amenitie_thumbnail'] != '') ?
                        $amenitiesList[$i]['amenitie_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' data_desc='{$amenitiesList[$i]['amenitie_description']}' data_type='{$amenitiesList[$i]['amenitie_type']}' >
                        <img src='" . (($amenitiesList[$i]['amenitie_thumbnail'] != '') ?
                        $amenitiesList[$i]['amenitie_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' >
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
                    <form method="POST" id="addAmenitiesForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new amenities</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" placeholder="Amenities name" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="description" placeholder="Description"
                                        class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <input type="file" id="icon" name="icon" accept="image/*" />
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category"
                                        id="type"><?php echo $typeDropDown ?></select>
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
                    <form method="POST" id="updateAmenitiesForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit amenities</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="editName" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="editDescription"
                                        placeholder="Description" class="form-control" required></textarea>
                                    <label class="form-lable" id="editDescriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <input type="file" id="editIcon" name="icon" accept="image/*" />
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category"
                                        id="editType"><?php echo $typeDropDown ?></select>
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
                    <p>Current icon</p>
                    <img src="" id="amenitiesIcon" alt="">
                </div>
            </div>
        </div>
    </div>
</div>