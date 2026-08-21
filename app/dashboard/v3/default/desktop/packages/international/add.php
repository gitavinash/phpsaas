<?php
if ($locationsData = mysqli_query($connection, "SELECT location_id, location_title, location_description, location_thumbnail, location_is_deletable, location_state, location_country, country_name FROM " . TABLE_PREFIX . "locations INNER JOIN " . TABLE_PREFIX . "country ON location_country=country_id WHERE location_country!=101")) {
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

    $countryDropDown = "<option value=''></option>";

    if ($countryData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "country")) {
        if (mysqli_num_rows($countryData) != 0) {
            while ($countryInfo = mysqli_fetch_assoc($countryData)) {
                $countryDropDown .= "<option value='" . str_replace(' ', '_', strtolower($countryInfo['country_id'])) . "'>{$countryInfo['country_name']}</option>";
            }
        } else {
            $countryDropDown .= "<option value='No data'>No Data</option>";
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
<h2 class="heading"><?php echo 'Add New ' . ucwords($urlStructure[1]) ?><span>Create new international tour
        package</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Destination details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" id="addInternationalPackageForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1 style="float:left; width:100%">Create new international tour package</h1>
                        </div>
                        <div class="form-group">
                            <!-- <label class="form-lable" id="Label">Package name<sup>*</sup></label> -->
                            <p class="form-lable" id="Label">Package name<sup>*</sup></p>
                            <input id="name" name="name" type="text" />
                        </div>
                        <div class="form-group">
                            <p>Country</p>
                            <select class="selectJS form-control" name="country"
                                id="type"><?php echo $countryDropDown ?></select>
                        </div>
                        <div id="locationDurationContainer">
                            <div class="locationDurationGroup">
                                <div class="form-group">
                                    <p>Destination<sup>*</sup></p>
                                    <select class="internationalCityAutoComplete form-control" name="destination[]"
                                        id="destination-1">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <p class="form-lable" id="Label">Trip duration (in Nights)<sup>*</sup></p>
                                    <input id="duration" name="duration[]" type="number" onwheel="this.blur()" />
                                </div>
                                <div class="form-group">
                                    <div class="box-2">
                                        <button type="button" class="btn btn-success addLocation">+</button>
                                    </div>
                                    <div class="box-2">
                                        <button type="button" class="btn-red removeLocation"
                                            style="display:none;">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <p>Pickup city<sup>*</sup></p>
                            <select class="internationalCityAutoComplete form-control" name="startingCity"
                                id="startingCity">
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Drop-off city<sup>*</sup></p>
                            <select class="internationalCityAutoComplete form-control" name="endCity" id="endCity">
                            </select>
                        </div>
                        <!-- <div class="form-group">
                            <p>Drop city<sup>*</sup></p>
                            <select class="selectJS form-control" name="dropCity" id="dropCity">
                                <option value="select">Select drop city</option>

                                <?php

                                if ($locations = mysqli_query($connection, "SELECT city_name, state_name, country_name FROM " . TABLE_PREFIX . "country INNER JOIN " . TABLE_PREFIX . "state ON state_country_id=country_id INNER JOIN " . TABLE_PREFIX . "city ON city_state_id=state_id WHERE state_country_id=101")) {
                                    if (mysqli_num_rows($locations)) {
                                        while ($locationData = mysqli_fetch_assoc($locations)) {
                                            echo "<option value='{$locationData['city_name']}, {$locationData['state_name']}' " . ((strtolower($locationData['city_name']) == 'new delhi') ? 'selected' : '')
                                                . ">{$locationData['city_name']}, {$locationData['state_name']}, {$locationData['country_name']}</option>";
                                        }
                                    }
                                } else {
                                    echo mysqli_error($connection);
                                }
                                ?>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <p>Travel style<sup>*</sup></p>
                            <select class="selectJS form-control" name="travelStyle" id="travelStyle">
                                <option value="select">Select travel style</option>
                                <option value="adventure">Adventure Travel</option>
                                <option value="cultural" selected>Cultural Travel</option>
                                <option value="luxury">Luxury Travel</option>
                                <option value="solo">Solo Travel</option>
                                <option value="group">Group Travel</option>
                                <option value="eco">Eco/Responsible Travel</option>
                                <option value="honeymoon">Honeymoon Travel</option>
                                <option value="couple">Couple Travel</option>
                                <option value="family">Family Travel</option>
                                <option value="wellness">Wellness/Retreat Travel</option>
                                <option value="budget">Budget Travel</option>
                                <option value="roadtrip">Road Trip</option>
                                <option value="cruise">Cruise Travel</option>
                                <option value="backpacking">Backpacking Travel</option>
                                <option value="religious">Religious Travel</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Outstataion vehicle<sup>*</sup></p>
                            <select class="selectJS form-control" name="outstationVehicle" id="outstationVehicle">
                                <option value="select">Select vehicle</option>
                                <?php
                                if ($vehicles = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
                                    if (mysqli_num_rows($vehicles)) {
                                        while ($vehiclesData = mysqli_fetch_assoc($vehicles)) {
                                            echo "<option value='{$vehiclesData['vehicle_title']}' " . ((strtolower($vehiclesData['vehicle_title']) == 'volvo') ? 'selected' : '') . ">{$vehiclesData['vehicle_title']}, " . ucwords(str_replace('_', ' ', $vehiclesData['vehicle_type'])) . "</option>";
                                        }
                                    }
                                } else {
                                    echo mysqli_error($connection);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Local Sightseeing vehicle<sup>*</sup></p>
                            <select class="selectJS form-control" name="localSighseeignVehicle"
                                id="localSighseeignVehicle">
                                <option value="select">Select vehicle</option>
                                <?php
                                if ($vehicles = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
                                    if (mysqli_num_rows($vehicles)) {
                                        while ($vehiclesData = mysqli_fetch_assoc($vehicles)) {
                                            echo "<option value='{$vehiclesData['vehicle_title']}' " . ((strtolower($vehiclesData['vehicle_title']) == 'maruti alto') ? 'selected' : '') . ">{$vehiclesData['vehicle_title']}, " . ucwords(str_replace('_', ' ', $vehiclesData['vehicle_type'])) . "</option>";
                                        }
                                    }
                                } else {
                                    echo mysqli_error($connection);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="button" class="btn btn-green" id="addInternationalPackageBtn"
                                    value="Generate package">
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>