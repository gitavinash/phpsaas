<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Enter product details</h1>
        <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
    </div>
    <?php

    if (isset($siteAccessMeta['warehouse'])) {
        if ($siteAccessMeta['warehouse'] == 'all') {
            $showWarehouse = 'all';
        } else {
            $showWarehouse = $siteAccessMeta['warehouse'];
        }
    } else {
        $showWarehouse = 'all';
    }

    if ($showWarehouse == "all") {
    ?>

    <div class="form-group">
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
        <label class="form-lable" id="Label">Warehouse<sup>*</sup></label>
    </div>

    <?php
    } else {
        echo "<input type='hidden' name='warehouseId' value='{$showWarehouse}' id='warehouseId'>";
    }

    ?>
    <div class="form-group">
        <input id="name" name="name" type="text" />
        <label class="form-lable" id="Label">Product name<sup>*</sup></label>
    </div>
    <div class="form-group">
        <textarea name="description" id="description" cols="30" rows="10"></textarea>
        <label class="form-lable" id="Label">About product</label>
    </div>
    <div class="form-group">
        <input id="itemCode" name="itemCode" type="text" />
        <label class="form-lable" id="Label">Product code or SKU code<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="totalItem" name="totalItem" type="number" onwheel="this.blur()" />
        <label class="form-lable" id="Label">Current stock<sup>*</sup></label>
    </div>
    <div class="form-group">
        <select class="selectJS form-control" name="productType" id="productType">
            <option value="select">Select product category</option>
            <?php
            if ($measurementList = mysqli_query($connection, "SELECT site_configuration_key FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%measurement_%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID . " ORDER BY site_configuration_key ASC")) {
                if (mysqli_num_rows($measurementList) != 0) {
                    while ($data = mysqli_fetch_assoc($measurementList)) {
                        $measurementName = ucwords(str_replace('measurement_', '', $data['site_configuration_key']));
                        echo "<option value='" . strtolower($measurementName) . "'>{$measurementName}</option>";
                    }
                } else {
                    echo "<option>No product type measurement found</option>";
                }
            }

            ?>
        </select>
        <label class="form-lable" id="Label">Product type<sup>*</sup></label>
    </div>
    <div class="form-group">
        <select class="selectJS form-control" name="supplier" id="supplier">
            <option value="select">Select supplier</option>
            <?php

            if ($supplierList = mysqli_query($connection, "SELECT supplier_id, supplier_code, supplier_title, city_name, state_name FROM " . TABLE_PREFIX . "supplier INNER JOIN " . TABLE_PREFIX . "address ON supplier_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE supplier_status=1 AND supplier_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($supplierList) > 0) {
                    while ($row = mysqli_fetch_array($supplierList)) {
                        echo "<option value='{$row['supplier_id']}'>{$row['supplier_title']}</option>";
                    }
                }
            }

            ?>
        </select>
        <label class="form-lable" id="Label">Supplier</label>
    </div>
    <div class="form-group">
        <input id="totalItem" name="featureImage" type="file" accept="image/x-png, image/gif, image/jpeg" />
        <label class="form-lable" id="Label">Product feature image</label>
    </div>
    <div class="form-group">
        <div class="input-field">
            <div class="addGallery" style="padding-top: .5rem;"></div>
            <label class="form-lable" id="Label">Product image gallery</label>
        </div>
    </div>
    <div class="box-1">
        <div class="form-group">
            <input type="button" class="btn btn-green" id="addInventory" value="Add new product">
        </div>
    </div>
</div>