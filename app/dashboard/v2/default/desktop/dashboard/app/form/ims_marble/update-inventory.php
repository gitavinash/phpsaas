<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (!isset($id)) { ?>
<script>
window.location.href = '/dashboard/inventory/list/';
</script>
<?php
} else if ($id == '') { ?>
<script>
window.location.href = '/dashboard/inventory/list/';
</script>
<?php
}

$blockId = base64_decode($id);

if ($checkInventory = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkInventory) > 0) {
        $validId = true;
        extract(mysqli_fetch_assoc($checkInventory));
    }
}

if (isset($validId)) { ?>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Update block details</h1>
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
                                echo "<option value='{$row['warehouse_id']}'" . (($row['warehouse_id'] == $block_warehouse_id) ? 'selected' : '') . ">{$row['warehouse_title']}</option>";
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
        <input id="name" name="name" value="<?php echo $block_title ?>" type="text" />
        <label class="form-lable" id="Label">Block name<sup>*</sup></label>
    </div>
    <div class="form-group">
        <textarea name="description" id="description" cols="30" rows="10"><?php echo $block_description ?></textarea>
        <label class="form-lable" id="Label">About block</label>
    </div>
    <div class="form-group">
        <input id="itemCode" name="itemCode" value="<?php echo $block_code ?>" type="text" />
        <label class="form-lable" id="Label">Block code or Item code<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="totalItem" name="totalItem" value="<?php echo $block_total_item ?>" type="number" />
        <label class="form-lable" id="Label">Total item in Block<sup>*</sup></label>
    </div>
    <div class="full-width">
        <div class="col-8">
            <div class="form-group">
                <input id="thickness" name="thickness" value="<?php echo $block_thickness_size ?>" type="number" />
                <label class="form-lable" id="Label">Thickness<sup>*</sup></label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <!-- <p>Size<sup>*</sup></p> -->
                <select class="selectJS form-control" name="thicknessSize" id="thicknessSize">
                    <option value="mm" <?php echo (($block_thickness_dimension == 'mm') ? 'selected' : '') ?>>Millimeter
                    </option>
                    <option value="cm" <?php echo (($block_thickness_dimension == 'cm') ? 'selected' : '') ?>>Centimeter
                    </option>
                    <option value="in" <?php echo (($block_thickness_dimension == 'in') ? 'selected' : '') ?>>Inches
                    </option>
                </select>
                <label class="form-lable" id="Label">Size<sup>*</sup></label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <select class="selectJS form-control" name="productType" id="productType">
            <?php
                if ($measurementList = mysqli_query($connection, "SELECT site_configuration_key FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%measurement_%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($measurementList) != 0) {
                        while ($data = mysqli_fetch_assoc($measurementList)) {
                            $measurementName = ucwords(str_replace('measurement_', '', $data['site_configuration_key']));
                            echo "<option value='" . strtolower($measurementName) . "' " . (($block_size_dimension == strtolower($measurementName)) ? 'selected' : '') . " >{$measurementName}</option>";
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
                            echo "<option value='{$row['supplier_id']}' " . (($row['supplier_id'] == $block_supplier_id) ? 'selected' : '') . ">{$row['supplier_title']}</option>";
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
            <div class="updateGallery" style="padding-top: .5rem;"></div>
            <label class="form-lable" id="Label">Block image gallery</label>
        </div>

        <?php

            if ($block_gallery != '') {

                $blockGalleryList = json_decode($block_gallery, true);

                $oldGalleryImages = array();

                for ($i = 0; $i < sizeof($blockGalleryList); $i++) {

                    $oldGalleryImages[] = array(
                        'id' => $i + 1,
                        'name' => $blockGalleryList[$i]['name'],
                        'src' => $blockGalleryList[$i]['path'],
                        'type' => $blockGalleryList[$i]['type'],
                    );
                }

                $oldGalleryImages = json_encode($oldGalleryImages, TRUE);
            } else {

                $oldGalleryImages = json_encode('', TRUE);
            }

            echo "<script> oldGalleryImages = {$oldGalleryImages}</script>";

            ?>
    </div>
    <div class="box-1">
        <div class="form-group">
            <input type="hidden" name="blockId" value="<?php echo $block_id ?>">
            <input type="hidden" name="oldTotalItem" value="<?php echo $block_total_item ?>">
            <input type="hidden" name="oldItemCode" value="<?php echo $block_code ?>">
            <input type="hidden" name="oldFeatureImage" value="<?php echo $block_feature_image ?>">
            <input type="hidden" name="oldGalleryImages" value="<?php echo $block_gallery ?>">
            <input type="submit" class="btn btn-green" id="updateBlock" value="Update block">
        </div>
    </div>
</div>
<?php
} else { ?>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h2>Invalid block request received</h2>
        <img src="<?php echo IMG_LIBRARY ?>storyset/missing.png">
    </div>

    <?php
}