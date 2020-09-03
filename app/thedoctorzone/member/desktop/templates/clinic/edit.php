<?php

    if($clincInfo=mysqli_query($connection, "SELECT company_id, company_title, address_mobile, address_house_no, address_area, address_landmark, address_pin_code, address_type, city_id, state_id, country_id FROM ".TABLE_PREFIX."company INNER JOIN ".TABLE_PREFIX."address ON address_id=company_address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON address_state_id=state_id INNER JOIN ".TABLE_PREFIX."country ON country_id=address_country_id WHERE company_type='clinic' AND company_status=1 AND company_id='{$id}'")) {

        if(mysqli_num_rows($clincInfo)!=0) {

            extract(mysqli_fetch_assoc($clincInfo)); ?>

                <h2>Edit Clinic or Hospital</h2>

                <div class="form-box">

                    <form id="updateForm">
                        <div class="box-2">
                            <div class="form-group">
                                <label>Clinic/Hospital Name*</label>
                                <input type="text" class="form-control inside-shadow" id="name" name="name" value="<?php echo $company_title ?>" placeholder="Clinic/Hospital Name" >
                            </div>
                            <div class="form-group">
                                <label>Clinic/Hospital Contact Number ( without country code )*</label>
                                <input type="text" class="form-control inside-shadow" id="mobile" name="mobile" value="<?php echo $address_mobile ?>" placeholder="10-Digit Mobile Number without country code" >
                            </div>
                            <div class="form-group">
                                <label>Flat, House no., Building, Company, Apartment*</label>
                                <input type="text" class="form-control inside-shadow" id="house_number" name="house_number" value="<?php echo $address_house_no ?>" placeholder="" >
                            </div>
                            <div class="form-group">
                                <label>Area, Colony, Street, Sector, Village*</label>
                                <input type="text" class="form-control inside-shadow" id="area" name="area" value="<?php echo $address_area ?>" placeholder="" >
                            </div>
                            <div class="form-group">
                                <label>Landmark*</label>
                                <input type="text" class="form-control inside-shadow" id="landmark" name="landmark" value="<?php echo $address_landmark ?>" placeholder="Ex. Near Rajiv Chowk Metro Station, etc." >
                            </div>
                            <div class="form-group">
                                <label>Country*</label>
                                <select name="country" id="countryID" class="form-control inside-shadow" >
                                    <option value="">Select Country</option>
                                    <option value="101" selected >India</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>State* ( click on state if you want to change city or pin code )</label>
                                <select name="state" id="stateID" class="form-control inside-shadow" >
                                    <?php
                                    
                                        if($checkState=mysqli_query($connection, "SELECT state_id, state_name FROM ".TABLE_PREFIX."state WHERE state_country_id='101'")) {

                                            if(mysqli_num_rows($checkState)!=0) {

                                                echo "<option value=''>Select State</option>";

                                                while($stateData=mysqli_fetch_assoc($checkState)) {

                                                    echo "<option value='{$stateData['state_id']}_{$stateData['state_name']}' ".(($state_id==$stateData['state_id']) ? 'selected' : '' )." >{$stateData['state_name']}</option>";

                                                }

                                            } else {

                                                echo "<option>No State Information Found</option>";

                                            }

                                        }

                                    ?>
                                </select>
                            </div>
                            <div class="form-group" id="cityBox" style="display: none" >
                                <label>City*</label>
                                <select name="city" id="cityID" class="form-control inside-shadow">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="form-group" id="pinCodeBox" style="display: none" >
                                <label>Pin Code*</label>
                                <input type="number" id="pincode" name="pincode" class="form-control inside-shadow" placeholder="6 digit numeric Pin Code" >
                            </div>
                            <div class="form-group" >
                                <label>Select Building Type*</label>
                                <select name="building_type" id="buildingType" class="form-control inside-shadow">
                                    <option value="">Select Building Type</option>
                                    <option value="clinic" <?php echo (($address_type=='clinic') ? 'selected' : '' ) ?> >Clinic</option>
                                    <option value="hospital" <?php echo (($address_type=='hospital') ? 'selected' : '' ) ?>>Hospital</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="id" value="<?php echo $company_id ?>" >
                                <input type="hidden" name="type" value="clinic" >
                                <input type="hidden" name="action" value="update" >
                                <input type="submit" id="updateBtn" value='Update Clinic' class='btn btn-green'>
                            </div>
                        </div>
                    </form>

                </div>

        <?php
        } else {

            echo 'No Information Found';

        }

    }