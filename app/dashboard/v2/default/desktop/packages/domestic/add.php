<h2 class="heading"><?php echo 'Add New ' . ucwords($urlStructure[1]) ?><span>Create new domestic tour package</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Destination details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" id="addDomesticPackageForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1 style="float:left; width:100%">Create new domestic tour package</h1>
                        </div>
                        <div class="form-group">
                            <!-- <label class="form-lable" id="Label">Package name<sup>*</sup></label> -->
                            <p class="form-lable" id="Label">Package name<sup>*</sup></p>
                            <input id="name" name="name" type="text" />
                        </div>
                        <div class="form-group">
                            <p>Destination<sup>*</sup></p>
                            <select class="selectJS form-control" name="destination" id="destination">
                                <option value="select">Select destination</option>
                                <?php
                                if ($locations = mysqli_query($connection, "SELECT location_title, state_name FROM " . TABLE_PREFIX . "locations INNER JOIN " . TABLE_PREFIX . "state ON location_state=state_id WHERE location_country=101 AND state_id!=0")) {
                                    if (mysqli_num_rows($locations)) {
                                        while ($locationData = mysqli_fetch_assoc($locations)) {
                                            echo "<option value='{$locationData['location_title']}'>{$locationData['location_title']}, {$locationData['state_name']}</option>";
                                        }
                                    }
                                } else {
                                    echo mysqli_error($connection);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Starting city<sup>*</sup></p>
                            <select class="selectJS form-control" name="startingCity" id="startingCity">
                                <option value="select">Select starting city</option>
                                <?php
                                if ($locations = mysqli_query($connection, "SELECT state_name, country_name FROM " . TABLE_PREFIX . "country INNER JOIN " . TABLE_PREFIX . "state ON state_country_id=country_id WHERE state_country_id=101")) {
                                    if (mysqli_num_rows($locations)) {
                                        while ($locationData = mysqli_fetch_assoc($locations)) {
                                            echo "<option value='{$locationData['state_name']}' " . ((strtolower($locationData['state_name']) == 'delhi') ? 'selected' : '')
                                                . ">{$locationData['state_name']}, {$locationData['country_name']}</option>";
                                        }
                                    }
                                } else {
                                    echo mysqli_error($connection);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p class="form-lable" id="Label">Trip duration ( in Night )<sup>*</sup></p>
                            <input id="duration" name="duration" type="number" />
                        </div>
                        <div class="form-group">
                            <p>Travel style</p>
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
                            <select class="selectJS form-control" name="localSighseeignVehicle" id="localSighseeignVehicle">
                                <option value="select">Select vehicle</option>
                                <?php
                                if ($vehicles = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
                                    if (mysqli_num_rows($vehicles)) {
                                        while ($vehiclesData = mysqli_fetch_assoc($vehicles)) {
                                            echo "<option value='{$vehiclesData['vehicle_title']}' " . ((strtolower($vehiclesData['vehicle_title']) == 'alto') ? 'selected' : '') . ">{$vehiclesData['vehicle_title']}, " . ucwords(str_replace('_', ' ', $vehiclesData['vehicle_type'])) . "</option>";
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
                                <input type="submit" class="btn btn-green" id="addDomesticPackageBtn" value="Generate package">
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>