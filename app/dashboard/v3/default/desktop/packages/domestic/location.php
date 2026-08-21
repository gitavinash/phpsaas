<?php

$id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));

if ($locationsData = mysqli_query($connection, "SELECT location_id, location_title, location_description, location_thumbnail, location_is_deletable, location_state, location_country, location_best_from, location_best_to, state_name FROM " . TABLE_PREFIX . "locations INNER JOIN " . TABLE_PREFIX . "state ON location_state=state_id WHERE location_country=101")) {
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
                $stateDropDown .= "<option value='" . str_replace(' ', '_', strtolower($stateInfo['state_id'])) . "'>{$stateInfo['state_name']}</option>";
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
                            $locationsList[$i]['location_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' data_desc='{$locationsList[$i]['location_description']}' data_type='{$locationsList[$i]['location_state']}' data_best_from='{$locationsList[$i]['location_best_from']}' data_best_to='{$locationsList[$i]['location_best_to']}' >
                        <img src='" . (($locationsList[$i]['location_thumbnail'] != '') ?
                            $locationsList[$i]['location_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$locationsList[$i]['location_title']}<span>{$locationsList[$i]['state_name']}</span></p>
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
                                    <label class="form-lable">Feature image ( size 1200px x 400px )</label>
                                    <input type="file" id="icon" name="icon" accept="image/*" />
                                </div>

                                <?php
                                $months = [
                                    'january',
                                    'february',
                                    'march',
                                    'april',
                                    'may',
                                    'june',
                                    'july',
                                    'august',
                                    'september',
                                    'october',
                                    'november',
                                    'december'
                                ];
                                ?>

                                <div class="form-group">
                                    <p class="form-lable">Best time to Visit<sup>*</sup></p>

                                    <div class="col-5">
                                        <select name="fromMonth" class="selectJS form-control" id="fromMonth">
                                            <option value="#">Start from</option>
                                            <?php foreach ($months as $month) { ?>
                                                <option value="<?php echo $month ?>">
                                                    <?php echo ucfirst($month) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-5">
                                        <select name="toMonth" class="selectJS form-control" id="toMonth">
                                            <option value="#">Start from</option>
                                            <?php foreach ($months as $month) { ?>
                                                <option value="<?php echo $month ?>">
                                                    <?php echo ucfirst($month) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
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
                                    <label class="form-lable">Feature image ( size 1200px x 400px )</label>
                                    <input type="file" id="editIcon" name="icon" accept="image/*" />
                                </div>

                                <div class="form-group">
                                    <p class="form-lable">Best time to Visit<sup>*</sup></p>

                                    <div class="col-5">
                                        <select name="fromMonth" class="selectJS form-control" id="editFromMonth">
                                            <option value="#">Start from</option>
                                            <?php foreach ($months as $month) { ?>
                                                <option value="<?php echo $month ?>">
                                                    <?php echo ucfirst($month) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-5">
                                        <select name="toMonth" class="selectJS form-control" id="editToMonth">
                                            <option value="#">Start from</option>
                                            <?php foreach ($months as $month) { ?>
                                                <option value="<?php echo $month ?>">
                                                    <?php echo ucfirst($month) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
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
                    <p>Feature image</p>
                    <img src="" id="locationsIcon" alt="">
                </div>
            </div>
        </div>
    </div>
</div>