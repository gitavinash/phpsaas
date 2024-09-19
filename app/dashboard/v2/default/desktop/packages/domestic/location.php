<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($locationsData = mysqli_query($connection, "SELECT location_id, location_title, location_description, location_thumbnail, location_is_deletable, location_state, location_country FROM " . TABLE_PREFIX . "locations WHERE location_country=101")) {
    $locationsList = array();
    if (mysqli_num_rows($locationsData) != 0) {
        while ($data = mysqli_fetch_assoc($locationsData)) {
            $locationsList[] = $data;
        }
    } else {
        $locations_title = '';
        $locations_description = '';
        $locations_parent_id = '';
        $link_url = '';
    }

    $stateDropDown = "<option value=''></option>";

    if ($stateData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {
        if (mysqli_num_rows($stateData) != 0) {
            while ($stateInfo = mysqli_fetch_assoc($stateData)) {
                $stateDropDown .= "<option value='" . str_replace(' ', '_', strtolower($stateInfo['state_name'])) . "'>{$stateInfo['state_name']}</option>";
            }
        } else {
            $stateDropDown .= "<option value='No data'>No Data</option>";
        }
    } else {
        echo ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : "Something went wrong");
        exit();
    }

} else {
    echo ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : "Something went wrong");
    exit();
}

?>
<h2 class="heading">Locations list<span>Complete locations list</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

if (count($locationsList) > 0) {
    for ($i = 0; $i < sizeof($locationsList); $i++) {
        echo "
                    <div class='featureList editLocations' id='{$locationsList[$i]['location_id']}' data_name='{$locationsList[$i]['location_title']}' data_image='" . (($locationsList[$i]['location_thumbnail'] != '') ?
            $locationsList[$i]['location_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' data_desc='{$locationsList[$i]['location_description']}' data_type='{$locationsList[$i]['location_state']}' >
                        <img src='" . (($locationsList[$i]['location_thumbnail'] != '') ?
            $locationsList[$i]['location_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$locationsList[$i]['location_title']}</p>
                    </div>
                    ";
    }
}

?>
            </section>
        </div>
    </div>
    <div class="shortcut">
        <a href="#" class="addNewLocations"><i class="fi fi-rr-plus"></i></a>
    </div>

    <div class="popUp locationsBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addLocationsForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new locations</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" placeholder="Locations name" />
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
                                    <p>State</p>
                                    <select class="selectJS form-control" name="state"
                                        id="type"><?php echo $stateDropDown ?></select>
                                </div>
                                <input type="hidden" name="country" value="101">
                            </div>
                    </form>
                </div>
            </div>
            <div class=" widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addLocations"><i class="fi-rr-check"></i> Add new
                        locations</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editLocationsBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="updateLocationsForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit locations</h4>
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
                                    <p>State</p>
                                    <select class="selectJS form-control" name="state"
                                        id="editType"><?php echo $stateDropDown ?></select>
                                </div>
                                <input type="hidden" name="country" value="101">
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateLocations"><i class="fi-rr-check"></i> Update
                        locations</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                    <p>Current icon</p>
                    <img src="" id="locationsIcon" alt="">
                </div>
            </div>
        </div>
    </div>
</div>