<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Enter block details</h1>
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
        <label class="form-lable" id="Label">Block name<sup>*</sup></label>
    </div>
    <div class="form-group">
        <textarea name="description" id="description" cols="30" rows="10"></textarea>
        <label class="form-lable" id="Label">About block</label>
    </div>
    <div class="form-group">
        <input id="itemCode" name="itemCode" type="text" />
        <label class="form-lable" id="Label">Block code or Item code<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="totalItem" name="totalItem" type="number" />
        <label class="form-lable" id="Label">Total slabs in Block<sup>*</sup></label>
    </div>
    <div class="full-width">
        <div class="col-8">
            <div class="form-group">
                <input id="thickness" name="thickness" type="number" />
                <label class="form-lable" id="Label">Thickness<sup>*</sup></label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <!-- <p>Size<sup>*</sup></p> -->
                <select class="selectJS form-control" name="thicknessSize" id="thicknessSize">
                    <option value="mm">Millimeter</option>
                    <option value="cm">Centimeter</option>
                    <option value="in">Inches</option>
                </select>
                <label class="form-lable" id="Label">Size<sup>*</sup></label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <!-- <p>BloxSize<sup>*</sup></p> -->
        <select class="selectJS form-control" name="blockSize" id="blockSize">
            <option value="mm">Millimeter</option>
            <option value="cm">Centimeter</option>
            <option value="in">Inches</option>
            <option value="ft" selected>Feet</option>
            <option value="mt">Meter</option>
        </select>
        <label class="form-lable" id="Label">Block Size calculation<sup>*</sup></label>
    </div>
    <!-- <div class="full-width">
        <div class="form-header" style="margin-bottom: 20px">
            <h2>Enter block item details<di2>
        </div>
        <div class="col-2">
            <div class="form-group">
                <input id="thickness" name="thickness" type="text" />
                <label class="form-lable" id="Label">Width<sup>*</sup></label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <input id="thickness" name="thickness" type="text" />
                <label class="form-lable" id="Label">Height<sup>*</sup></label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <input id="thickness" name="thickness" type="text" />
                <label class="form-lable" id="Label">Total item qty.<sup>*</sup></label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <input id="thickness" name="thickness" type="text" />
                <label class="form-lable" id="Label">Thickness<sup>*</sup></label>
            </div>
        </div>
    </div> -->
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
        <label class="form-lable" id="Label">Block feature image</label>
    </div>
    <div class="form-group">
        <div class="input-field">
            <div class="addGallery" style="padding-top: .5rem;"></div>
            <label class="form-lable" id="Label">Block image gallery</label>
        </div>
    </div>
    <div class="box-1">
        <div class="form-group">
            <input type="submit" class="btn btn-green" id="addBlock" value="Add new block">
        </div>
    </div>
</div>