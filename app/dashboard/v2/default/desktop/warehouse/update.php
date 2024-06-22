<?php

if (!isset($id)) { ?>
    <script>
        window.location.href = '/dashboard/warehouse/list/';
    </script>
<?php
} else if ($id == '') { ?>
    <script>
        window.location.href = '/dashboard/warehouse/list/';
    </script>
<?php
}

$warehouseId = base64_decode($id);

if ($checkWarehouse = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON city_state_id=state_id WHERE warehouse_id='{$warehouseId}'")) {
    if (mysqli_num_rows($checkWarehouse) > 0) {
        extract(mysqli_fetch_assoc($checkWarehouse));
    }
}

?>
<h2 class="heading"><?php echo 'Update warehouse details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Warehouse details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateWarehouseForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update warehouse details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" value="<?php echo $warehouse_title ?>" type="text" />
                            <label class="form-lable" id="Label">Warehouse name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"><?php echo $warehouse_description ?></textarea>
                            <label class="form-lable" id="Label">About warehouse</label>
                        </div>
                        <div class="form-group">
                            <input id="address" name="address" value="<?php echo $address_area ?>" type="text" />
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group" id="stateDropDown">
                            <p>State<sup>*</sup></p>
                            <select class="selectJS form-control" name="state" id="state">
                                <option value="select">Select state</option>
                                <?php

                                if ($stateList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id=101 AND state_status=1")) {
                                    if (mysqli_num_rows($stateList) > 0) {
                                        while ($state = mysqli_fetch_assoc($stateList)) {
                                            echo "<option value='{$state['state_id']}' " . ((strtolower($state['state_name']) == strtolower($state_name)) ? 'selected' : '') . " >{$state['state_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="cityBox">
                            <p>City<sup>*</sup></p>
                            <select class="selectJS form-control" name="city" id="city">
                                <option value="select">Select city</option>
                                <?php

                                if ($cityList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "city WHERE city_state_id={$address_state_id} AND city_status=1")) {
                                    if (mysqli_num_rows($cityList) > 0) {
                                        while ($city = mysqli_fetch_assoc($cityList)) {
                                            echo "<option value='{$city['city_id']}' " . ((strtolower($city['city_name']) == strtolower($city_name)) ? 'selected' : '') . " >{$city['city_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" value="<?php echo $address_pin_code ?>" type="number" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" value="<?php echo $warehouse_email ?>" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" value="<?php echo $warehouse_mobile ?>" type="number" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="warehouseRegistrationNumber" value="<?php echo $warehouse_cin ?>" name="cin" type="text" />
                            <label class="form-lable" id="Label">Registration no. ( CIN )</label>
                        </div>
                        <div class="form-group">
                            <input id="gstNumber" name="gst" value="<?php echo $warehouse_gst ?>" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="form-group">
                            <input id="apnNumber" name="pan" value="<?php echo $warehouse_pan ?>" type="text" />
                            <label class="form-lable" id="Label">PAN Number</label>
                        </div>
                        <div class="form-group">
                            <input id="warehouseCode" name="warehouseCode" value="<?php echo $warehouse_code ?>" type="text" />
                            <label class="form-lable" id="Label">Warehouse Code or ID</label>
                        </div>
                        <div class="form-group">
                            <p>Warehouse type</p>
                            <select class="selectJS form-control" name="warehouseType" id="warehouseType">
                                <option value="select">Select registration type</option>
                                <option value="private" <?php echo (($warehouse_type == 'private') ? 'selected' : '') ?>>
                                    Private Limited Warehouse</option>
                                <option value="public" <?php echo (($warehouse_type == 'public') ? 'selected' : '') ?>>
                                    Public Limited Warehouse</option>
                                <option value="partnerships" <?php echo (($warehouse_type == 'partnerships') ? 'selected' : '') ?>>Partnerships
                                </option>
                                <option value="llp" <?php echo (($warehouse_type == 'llp') ? 'selected' : '') ?>>LLP
                                    Registration</option>
                                <option value="opc" <?php echo (($warehouse_type == 'opc') ? 'selected' : '') ?>>One
                                    Person
                                    Warehouse ( OPC )</option>
                                <option value="proprietorship" <?php echo (($warehouse_type == 'proprietorship') ? 'selected' : '') ?>>Sole
                                    Proprietorship</option>
                                <option value="section8" <?php echo (($warehouse_type == 'section8') ? 'selected' : '') ?>>
                                    Section 8 Warehouse</option>
                            </select>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="warehouseId" value="<?php echo $warehouseId ?>">
                                <input type="submit" class="btn btn-green" id="updateWarehouse" value="Update warehouse">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>