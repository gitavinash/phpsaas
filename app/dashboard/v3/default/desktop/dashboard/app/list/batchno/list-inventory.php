<?php
$tableNamePrefix = TABLE_PREFIX;

if (isset($_GET['warehouse'])) {
    if ($_GET['warehouse'] != '') {
        $warehouseFilterValue = $_GET['warehouse'];
        $warehouseFilter = "AND inventory_warehouse_id='{$warehouseFilterValue}'";
    } else {
        $warehouseFilterValue = '';
        $warehouseFilter = '';
    }
} else {
    $warehouseFilterValue = '';
    $warehouseFilter = '';
}

if (isset($_GET['product_type'])) {
    if ($_GET['product_type'] != '') {
        $typeFilterValue = $_GET['product_type'];
        $typeFilter = "AND product_category='{$typeFilterValue}'";
    } else {
        $typeFilterValue = '';
        $typeFilter = '';
    }
} else {
    $typeFilterValue = '';
    $typeFilter = '';
}

if (isset($_GET['status'])) {
    if ($_GET['status'] != '') {
        $statusFilterValue = $_GET['status'];
        $statusFilter = "AND product_status='{$statusFilterValue}'";
    } else {
        $statusFilterValue = '';
        $statusFilter = 'AND product_status!=2';
    }
} else {
    $statusFilterValue = '';
    $statusFilter = 'AND product_status!=2';
}

?>
<div class="tableFilter">
    <div class="filterBox">
        <label>Filter by</label>
    </div>
    <div class="filterBox">
        <form action="" method="GET">
            <select id="warehouseFilter" name="warehouse">
                <option value="">Warehouse</option>
                <?php

                if ($checkWarehouse = mysqli_query($connection, "SELECT warehouse_id, warehouse_title FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND warehouse_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($checkWarehouse) != 0) {
                        while ($warehouseData = mysqli_fetch_assoc($checkWarehouse)) {
                            echo "<option value='{$warehouseData['warehouse_id']}' " . (($warehouseData['warehouse_id'] == $warehouseFilterValue) ? 'selected' : '') . " >{$warehouseData['warehouse_title']}</option>";
                        }
                    }
                }

                ?>
            </select>
    </div>
    <div class="filterBox">
        <select id="typeFilter" name="product_type">
            <option value="">Product category</option>
            <?php

            if ($checkProductType = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_status=1 AND category_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkProductType) != 0) {
                    while ($productData = mysqli_fetch_assoc($checkProductType)) {
                        echo "<option value='{$productData['category_id']}'" . (($productData['category_id'] == $typeFilterValue) ? 'selected' : '') . ">" . ucwords($productData['category_title']) . "</option>";
                    }
                }
            }

            ?>
        </select>
    </div>
    <div class="filterBox">
        <select id="statusFilter" name="status">
            <option value="">Status</option>
            <option value="1" <?php echo (($statusFilterValue == '1') ? 'selected' : '') ?>>Active</option>
            <option value="3" <?php echo (($statusFilterValue == '3') ? 'selected' : '') ?>>Sold out</option>
            <option value="4" <?php echo (($statusFilterValue == '4') ? 'selected' : '') ?>>Return</option>
            <option value="2" <?php echo (($statusFilterValue == '2') ? 'selected' : '') ?>>Delete</option>
            <option value="5" <?php echo (($statusFilterValue == '5') ? 'selected' : '') ?>>Transfer request pending
            </option>
            <option value="0" <?php echo (($statusFilterValue == '0') ? 'selected' : '') ?>>Pending</option>
        </select>
    </div>
    <div class="filterBox">
        <input type="submit" class="btn-sm btn btn-green date-btn" value="Apply filter">
    </div>
    </form>
</div>
<table class="showInventoryTableList display nowrap" style="width: 100%;">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Name</th>
            <th>Warehouse</th>
            <th>Category</th>
            <th>Total Qty.</th>
            <th>Avl. Qty.</th>
            <th>MRP</th>
            <th>Price</th>
            <th>Status</th>
            <th style="width:160px"></th>
        </tr>
    </thead>
    <?php

    if ($site_access_type != 'administrator' && $site_access_type != 'both') {
        $userFilter = 'AND product_user_id="' . USER_ID . '" ';
    } else {
        $userFilter = '';
    }

    $inventorQuery = "SELECT * FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id AND inventory_site_id='" . SITE_ID . "' AND inventory_status=1 INNER JOIN " . TABLE_PREFIX . "warehouse ON inventory_warehouse_id=warehouse_id LEFT JOIN " . TABLE_PREFIX . "supplier ON inventory_supplier_id=supplier_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "category ON product_category=category_id AND category_site_id=" . SITE_ID . " WHERE inventory_site_id='" . SITE_ID . "' {$userFilter} {$statusFilter} {$typeFilter} {$warehouseFilter} ORDER BY product_title ASC";

    if ($inventoryList = mysqli_query($connection, $inventorQuery)) {
        echo "<tbody>";
        if (mysqli_num_rows($inventoryList) > 0) {
            $serialNo = 1;
            while ($inventoryData = mysqli_fetch_assoc($inventoryList)) {
                extract($inventoryData);

                switch ($inventory_status) {
                    case 1:
                        $productStatus = 'Active';
                        break;
                    case 3:
                        $productStatus = 'Sold out';
                        break;
                    case 4:
                        $productStatus = 'Return';
                        break;
                    case 2:
                        $productStatus = 'Delete';
                        break;
                    case 5:
                        $productStatus = 'Transfer request pending';
                        break;
                    default:
                        $productStatus = 'Pending';
                        break;
                }

                echo "<tr>
                                
                                <td>" . $inventory_sku . "</td>
                                <td>" . $product_title . "</td>
                                <td>" . $warehouse_title . "</td>
                                <td>" . $category_title . "</td>
                                <td>" . $inventory_stock . "</td>
                                <td>" . $inventory_current_stock . "</td>
                                <td>" . $inventory_mrp . "</td>
                                <td>" . $inventory_price . "</td>
                                <td>" . $productStatus . "</td>
                                <td>
                                    <div class='tableActionButton'>
                                        <a href='/dashboard/inventory/update/?id=" . base64_encode($product_id) . "' class='tableActionIconBtn' title='Edit product' data-title='Edit product'><i class='fi fi-rr-edit bgWhite'></i></a>
                                            <a href='/download/?downloadUrl={$inventory_qr}' class='tableActionIconBtn' target='_blank' title='Show QR Code' data-title='Download QR code' target='_blank'><i class='fi fi-rr-qr-scan bgWhite'></i></a>
                                            " . (($inventory_status == 1) ? "
                                            <a href='#' title='Mark as sold out' data-title='Mark as Sold Out' class='tableActionIconBtn markAsSold' data-type='product' data-id='{$product_id}'  target='_blank'><i class='fi fi-rr-sold-signal bgRed'></i></a>" : "") . "
                                            <a href='#' data-type='inventory' data-id='{$product_id}' title='Delete product' data-title='Delete product' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false' ><i class='fi fi-rr-trash bgRed'></i></a>
                                    </div>            
                                </td>";
                echo "</tr>";
            }
        }
        echo "</tbody>";
    } else {
        if (DEVELOPMENT_MODE == TRUE) {
            echo "Debugging: " . mysqli_error($connection);
        }
        $msg = "Something went wrong";
    }

    ?>
</table>