<?php

$id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));

if ($vehicleData = mysqli_query($connection, "SELECT vehicle_id, vehicle_title, vehicle_seating, vehicle_description, vehicle_thumbnail, vehicle_is_deletable, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
    $vehicleList = array();
    if (mysqli_num_rows($vehicleData) != 0) {
        while ($data = mysqli_fetch_assoc($vehicleData)) {
            $vehicleList[] = $data;
        }
    } else {

        $vehicle_title = '';
        $vehicle_description = '';
        $vehicle_parent_id = '';
        $link_url = '';
    }
}

$typeDropDown = '
<option value="">Select vehicle type</option>
<option value="mpv">MPV</option>
<option value="suv">SUV</option>
<option value="bus" selected>Bus</option>
<option value="traveller">Traveller</option>
<option value="micro">Micro</option>
<option value="hatchback">Hatchback</option>
<option value="sedan">Sedan</option>
<option value="light_commercial_vehicle">Light Commercial Vehicle</option>
<option value="flight">Flight</option>
<option value="rail">Rail</option>
';

?>
<h2 class="heading">Vehicles list<span>Complete vehicles list</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                if (count($vehicleList) > 0) {
                    for ($i = 0; $i < sizeof($vehicleList); $i++) {
                        echo "
                    <div class='featureList editVehicle' id='" . $vehicleList[$i]['vehicle_id'] . "' data_name='" . $vehicleList[$i]['vehicle_title'] . "' data_seating='{$vehicleList[$i]['vehicle_seating']}' data_image='" . (($vehicleList[$i]['vehicle_thumbnail'] != '') ?
                            $vehicleList[$i]['vehicle_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' data_desc='" . $vehicleList[$i]['vehicle_description'] . "' data_type='" . $vehicleList[$i]['vehicle_type'] . "' >
                        <img src='" . (($vehicleList[$i]['vehicle_thumbnail'] != '') ?
                            $vehicleList[$i]['vehicle_thumbnail'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$vehicleList[$i]['vehicle_title']}</p>
                    </div>
                    ";
                    }
                }

                ?>
                <div class="shortcut">
                    <a href="#" class="addNewVehicle"><i class="fi fi-rr-plus"></i></a>
                </div>
            </section>
        </div>
    </div>

    <div class="popUp vehicleBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addVehicleForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new vehicle</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="description" placeholder="Description" class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Maximum seating<sup>*</sup></label>
                                    <input id="seating" name="seating" type="text" />
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <input type="file" id="icon" name="icon" accept="image/*" />
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category" id="type"><?php echo $typeDropDown ?></select>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addVehicle"><i class="fi-rr-check"></i> Add new vehicle</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editVehicleBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="updateVehicleForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit vehicle</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="editName" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="editDescription" placeholder="Description" class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Maximum seating<sup>*</sup></label>
                                    <input id="vehicleSeating" name="seating" type="text" />
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <input type="file" id="editIcon" name="icon" accept="image/*" />
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category" id="editType"><?php echo $typeDropDown ?></select>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateVehicle"><i class="fi-rr-check"></i> Update vehicle</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                    <p>Current icon</p>
                    <img src="" id="vehicleIcon" alt="">
                </div>
            </div>
        </div>
    </div>
</div>