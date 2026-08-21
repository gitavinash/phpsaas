<?php

if (!isset($id)) { ?>
    <script>
        window.location.href = '/dashboard/supplier/list/';
    </script>
<?php
} else if ($id == '') { ?>
    <script>
        window.location.href = '/dashboard/supplier/list/';
    </script>
<?php
}

$supplierId = base64_decode($id);

if ($checkSupplier = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "supplier INNER JOIN " . TABLE_PREFIX . "address ON supplier_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON city_state_id=state_id WHERE supplier_id='{$supplierId}'")) {
    if (mysqli_num_rows($checkSupplier) > 0) {
        extract(mysqli_fetch_assoc($checkSupplier));
    }
}

?>
<h2 class="heading"><?php echo 'Update supplier details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Supplier details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateSupplierForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update supplier details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" value="<?php echo $supplier_title ?>" type="text" />
                            <label class="form-lable" id="Label">Supplier name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"><?php echo $supplier_description ?></textarea>
                            <label class="form-lable" id="Label">About supplier</label>
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
                            <input id="pinCode" name="pinCode" value="<?php echo $address_pin_code ?>" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" value="<?php echo $supplier_email ?>" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" value="<?php echo $supplier_mobile ?>" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="supplierRegistrationNumber" value="<?php echo $supplier_cin ?>" name="cin" type="text" />
                            <label class="form-lable" id="Label">Registration no. ( CIN )</label>
                        </div>
                        <div class="form-group">
                            <input id="gstNumber" name="gst" value="<?php echo $supplier_gst ?>" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="form-group">
                            <input id="apnNumber" name="pan" value="<?php echo $supplier_pan ?>" type="text" />
                            <label class="form-lable" id="Label">PAN Number</label>
                        </div>
                        <div class="form-group">
                            <input id="supplierCode" name="supplierCode" value="<?php echo $supplier_code ?>" type="text" />
                            <label class="form-lable" id="Label">Supplier Code or ID</label>
                        </div>
                        <div class="form-group">
                            <p>Company type</p>
                            <select class="selectJS form-control" name="supplierType" id="supplierType">
                                <option value="select">Select registration type</option>
                                <option value="private" <?php echo (($supplier_type == 'private') ? 'selected' : '') ?>>
                                    Private Limited Supplier</option>
                                <option value="public" <?php echo (($supplier_type == 'public') ? 'selected' : '') ?>>
                                    Public Limited Supplier</option>
                                <option value="partnerships" <?php echo (($supplier_type == 'partnerships') ? 'selected' : '') ?>>Partnerships
                                </option>
                                <option value="llp" <?php echo (($supplier_type == 'llp') ? 'selected' : '') ?>>LLP
                                    Registration</option>
                                <option value="opc" <?php echo (($supplier_type == 'opc') ? 'selected' : '') ?>>One
                                    Person
                                    Supplier ( OPC )</option>
                                <option value="proprietorship" <?php echo (($supplier_type == 'proprietorship') ? 'selected' : '') ?>>Sole
                                    Proprietorship</option>
                                <option value="section8" <?php echo (($supplier_type == 'section8') ? 'selected' : '') ?>>
                                    Section 8 Supplier</option>
                            </select>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="supplierId" value="<?php echo $supplierId ?>">
                                <input type="button" class="btn btn-green" id="updateSupplier" value="Update supplier">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>