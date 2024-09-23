<h2>Add New Clinic or Hospital</h2>

<div class="form-box">

    <form id="publishForm">
        <div class="box-2">
            <div class="form-group">
                <label>Clinic/Hospital Name*</label>
                <input type="text" class="form-control inside-shadow" id="name" name="name" placeholder="Clinic/Hospital Name">
            </div>
            <div class="form-group">
                <label>Clinic/Hospital Contact Number*</label>
                <input type="text" class="form-control inside-shadow" id="mobile" name="mobile" placeholder="10-Digit Mobile Number without country code">
            </div>
            <div class="form-group">
                <label>Flat, House no., Building, Company, Apartment*</label>
                <input type="text" class="form-control inside-shadow" id="house_number" name="house_number" placeholder="">
            </div>
            <div class="form-group">
                <label>Area, Colony, Street, Sector, Village*</label>
                <input type="text" class="form-control inside-shadow" id="area" name="area" placeholder="">
            </div>
            <div class="form-group">
                <label>Landmark*</label>
                <input type="text" class="form-control inside-shadow" id="landmark" name="landmark" placeholder="Ex. Near Rajiv Chowk Metro Station, etc.">
            </div>
            <div class="form-group">
                <label>Country*</label>
                <select name="country" id="countryID" class="form-control inside-shadow">
                    <option value="">Select Country</option>
                    <option value="101" selected>India</option>
                </select>
            </div>
            <div class="form-group">
                <label>State*</label>
                <select name="state" id="stateID" class="form-control inside-shadow">
                    <?php

                    if ($checkState = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id='101'")) {

                        if (mysqli_num_rows($checkState) != 0) {

                            echo "<option value=''>Select State</option>";

                            while ($stateData = mysqli_fetch_assoc($checkState)) {

                                echo "<option value='{$stateData['state_id']}_{$stateData['state_name']}' >{$stateData['state_name']}</option>";
                            }
                        } else {

                            echo "<option>No State Information Found</option>";
                        }
                    }

                    ?>
                </select>
            </div>
            <div class="form-group" id="cityBox" style="display: none">
                <label>City*</label>
                <select name="city" id="cityID" class="form-control inside-shadow">
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="form-group" id="pinCodeBox" style="display: none">
                <label>Pin Code*</label>
                <input type="number" onwheel="this.blur()" id="pincode" name="pincode" class="form-control inside-shadow" placeholder="6 digit numeric Pin Code">
            </div>
            <div class="form-group">
                <label>Select Building Type*</label>
                <select name="building_type" id="buildingType" class="form-control inside-shadow">
                    <option value="">Select Building Type</option>
                    <option value="clinic">Clinic</option>
                    <option value="hospital">Hospital</option>
                </select>
            </div>
            <div class="form-group">
                <input type="hidden" name="type" value="clinic">
                <input type="hidden" name="action" value="add">
                <input type="submit" id="publishBtn" value='Add New Clinic' class='btn btn-green'>
            </div>
        </div>
    </form>

</div>