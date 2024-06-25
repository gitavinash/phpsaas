<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Enter block details</h1>
        <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
    </div>
    <?php

    if ($siteAccessMeta['warehouse'] == 'all') { ?>

    <div class="form-group">
        <p>Warehouse </p>
        <select class="selectJS form-control" name="warehouseId" id="warehouseId">
            <option value="select">Select warehouse</option>
            <?php

                if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($warehouseList) > 0) {
                        while ($row = mysqli_fetch_array($warehouseList)) {
                            echo "<option value='{$row['warehouse_id']}'>{$row['warehouse_title']}</option>";
                        }
                    }
                }

                ?>
        </select>
    </div>

    <?php
    } else {
        echo "<input type='hidden' name='warehouseId' value='{$siteAccessMeta['warehouse']}' id='warehouseId'>";
    }

    ?>
    <div class="form-group">
        <input id="name" name="name" type="text" />
        <label class="form-lable" id="Label">Block name<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="blockCode" name="blockCode" type="text" />
        <label class="form-lable" id="Label">Block Code or ID</label>
    </div>
    <div class="form-group">
        <textarea name="description" id="description" cols="30" rows="10"></textarea>
        <label class="form-lable" id="Label">About block</label>
    </div>
    <div class="form-group">
        <input id="address" name="address" type="text" />
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
                        echo "<option value='{$state['state_id']}' >{$state['state_name']}</option>";
                    }
                }
            }

            ?>
        </select>
    </div>
    <div class="form-group" id="cityBox" style="display: none">
        <p>City<sup>*</sup></p>
        <select class="selectJS form-control" name="city" id="city">
        </select>
    </div>
    <div class="form-group">
        <input id="pinCode" name="pinCode" type="number" />
        <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="email" name="email" type="email" />
        <label class="form-lable" id="Label">Email<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="mobile" name="mobile" type="number" />
        <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="gst" name="gst" type="text" />
        <label class="form-lable" id="Label">GST Number</label>
    </div>
    <div class="box-1">
        <div class="form-group">
            <input type="submit" class="btn btn-green" id="addBlock" value="Add new block">
        </div>
    </div>
</div>