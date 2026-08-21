<?php

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

$productId = base64_decode($id);

if ($checkInventory = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id AND inventory_site_id=" . SITE_ID . " AND inventory_status=1 AND inventory_type='product' INNER JOIN " . TABLE_PREFIX . "warehouse ON warehouse_id=inventory_warehouse_id AND warehouse_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "company ON company_id=warehouse_company_id AND company_site_id=" . SITE_ID . " WHERE product_id='{$productId}' AND product_status=1 AND product_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkInventory) > 0) {
        $validId = true;
        extract(mysqli_fetch_assoc($checkInventory));
    }
}

if (isset($validId)) { ?>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Update product details</h1>
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
                                echo "<option value='{$row['warehouse_id']}'" . (($row['warehouse_id'] == $inventory_warehouse_id) ? 'selected' : '') . ">{$row['warehouse_title']}</option>";
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
        <input id="name" name="name" value="<?php echo $product_title ?>" type="text" />
        <label class="form-lable" id="Label">Product name<sup>*</sup></label>
    </div>
    <div class="form-group">
        <textarea name="description" id="description" cols="30" rows="10"><?php echo $product_description ?></textarea>
        <label class="form-lable" id="Label">About product</label>
    </div>
    <div class="form-group">
        <input id="itemCode" name="itemCode" value="<?php echo $inventory_sku ?>" type="text" />
        <label class="form-lable" id="Label">Product code or SKU<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="purchaseCost" name="purchaseCost" value="<?php echo $inventory_purchase_price ?>" type="number" />
        <label class="form-lable" id="Label">Purchase cost</label>
    </div>
    <div class="form-group">
        <input id="purchaseMRP" name="purchaseMRP" value="<?php echo $inventory_mrp ?>" type="number" />
        <label class="form-lable" id="Label">MRP</label>
    </div>
    <div class="form-group">
        <input id="sellingPrice" name="sellingPrice" value="<?php echo $inventory_price ?>" type="number" />
        <label class="form-lable" id="Label">Selling price</label>
    </div>
    <div class="form-group">
        <input id="totalItem" name="totalItem" value="<?php echo $inventory_stock ?>" type="number"
            onwheel="this.blur()" />
        <label class="form-lable" id="Label">Total stock<sup>*</sup></label>
    </div>
    <div class="form-group">
        <input id="minStock" name="minStock" value="<?php echo $inventory_stock_notice ?>" type="number"
            onwheel="this.blur()" />
        <label class="form-lable" id="Label">Stock alert</label>
    </div>
    <div class="form-group">
        <select class="selectJS form-control" name="productType" id="productType">
            <?php
                if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_status=1 AND category_site_id=" . SITE_ID . " ORDER BY category_title ASC")) {
                    if (mysqli_num_rows($categoryList) != 0) {
                        while ($data = mysqli_fetch_assoc($categoryList)) {
                            echo "<option value='{$data['category_id']}' " . (($product_category == $data['category_id']) ? 'selected' : '') . ">{$data['category_title']}</option>";
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
        <select class="selectJS form-control" name="supplierId" id="supplier">
            <option value="">Select supplier</option>
            <?php

                if ($supplierList = mysqli_query($connection, "SELECT supplier_id, supplier_code, supplier_title, city_name, state_name FROM " . TABLE_PREFIX . "supplier INNER JOIN " . TABLE_PREFIX . "address ON supplier_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE supplier_status=1 AND supplier_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($row = mysqli_fetch_array($supplierList)) {
                            echo "<option value='{$row['supplier_id']}' " . (($row['supplier_id'] == $inventory_supplier_id) ? 'selected' : '') . ">{$row['supplier_title']}</option>";
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
            <div class="updateGallery" style="padding-top: .5rem;"></div>
            <label class="form-lable" id="Label">Product image gallery</label>
        </div>

        <?php

            if ($product_gallery != '') {

                $productGalleryList = json_decode($product_gallery, true);

                $oldGalleryImages = array();

                for ($i = 0; $i < sizeof($productGalleryList); $i++) {

                    $oldGalleryImages[] = array(
                        'id' => $i + 1,
                        'name' => $productGalleryList[$i]['name'],
                        'src' => $productGalleryList[$i]['path'],
                        'type' => $productGalleryList[$i]['type'],
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
            <input type="hidden" name="productId" value="<?php echo $productId ?>">
            <input type="hidden" name="oldItemCode" value="<?php echo $inventory_sku ?>">
            <input type="hidden" name="oldFeatureImage" value="<?php echo $product_image ?>">
            <input type="hidden" name="oldGalleryImages" value="<?php echo $product_gallery ?>">
            <input type="button" class="btn btn-green" id="updateInventory" value="Update product">
        </div>
    </div>
</div>
<?php
} else { ?>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h2>Invalid product request received</h2>
        <img src="<?php echo IMG_LIBRARY ?>storyset/missing.png">
    </div>

    <?php
}